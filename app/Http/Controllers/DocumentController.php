<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private function generateUniqueName($originalName, $parentId, $isFolder = false, $ignoreId = null)
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newName = $originalName;
        $counter = 1;

        while (true) {
            $query = Document::where('original_name', $newName)
                ->where('parent_id', $parentId)
                ->where('is_folder', $isFolder);
                
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                break;
            }

            if ($extension && !$isFolder) {
                $newName = $name . ' (' . $counter . ').' . $extension;
            } else {
                $newName = $originalName . ' (' . $counter . ')';
            }
            $counter++;
        }

        return $newName;
    }

    private function getAllDescendantIds($folderId)
    {
        $ids = [];
        $children = Document::where('parent_id', $folderId)->where('is_folder', true)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getAllDescendantIds($childId));
        }
        return $ids;
    }

    public function index(Request $request)
    {
        $currentFolder = null;
        
        if ($request->has('folder') && $request->folder != '') {
            $currentFolder = Document::where('is_folder', true)->findOrFail($request->folder);
        }
        
        if ($request->has('q') && $request->q != '') {
            $searchTerm = $request->q;
            $query = Document::where(function($q) use ($searchTerm) {
                $q->where('original_name', 'like', "%{$searchTerm}%")
                  ->orWhere('owner_name', 'like', "%{$searchTerm}%");
            });

            if ($currentFolder) {
                $descendantFolderIds = $this->getAllDescendantIds($currentFolder->id);
                $allowedParentIds = array_merge([$currentFolder->id], $descendantFolderIds);
                
                $query->where(function($q) use ($allowedParentIds, $currentFolder) {
                    $q->whereIn('parent_id', $allowedParentIds)
                      ->orWhere('id', $currentFolder->id); // Optionally include the folder itself if it matches
                });
            }
        } else {
            if ($currentFolder) {
                $query = Document::where('parent_id', $currentFolder->id);
            } else {
                $query = Document::whereNull('parent_id');
            }
        }

        $this->syncPhysicalFilesWithDatabase($currentFolder);

        if ($request->filled('type')) {
            $type = $request->type;
            if ($type === 'folder') {
                $query->where('is_folder', true);
            } elseif ($type === 'file') {
                $query->where('is_folder', false);
            } elseif ($type === 'document') {
                $query->where('is_folder', false)->where(function ($q) {
                    $exts = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'webdoc'];
                    foreach ($exts as $e) {
                        $q->orWhere('original_name', 'like', "%.{$e}");
                    }
                });
            } elseif ($type === 'spreadsheet') {
                $query->where('is_folder', false)->where(function ($q) {
                    $exts = ['xls', 'xlsx', 'csv', 'ods'];
                    foreach ($exts as $e) {
                        $q->orWhere('original_name', 'like', "%.{$e}");
                    }
                });
            } elseif ($type === 'image') {
                $query->where('is_folder', false)->where(function ($q) {
                    $exts = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'];
                    foreach ($exts as $e) {
                        $q->orWhere('original_name', 'like', "%.{$e}");
                    }
                });
            } elseif ($type === 'archive') {
                $query->where('is_folder', false)->where(function ($q) {
                    $exts = ['zip', 'rar', '7z', 'tar', 'gz'];
                    foreach ($exts as $e) {
                        $q->orWhere('original_name', 'like', "%.{$e}");
                    }
                });
            }
        }

        $sort = $request->get('sort', 'created_at');
        if ($sort === 'original_name') {
            $query->orderBy('is_folder', 'desc')->orderBy('original_name', 'asc');
        } elseif ($sort === 'file_size') {
            $query->orderBy('is_folder', 'desc')->orderBy('file_size', 'desc');
        } else {
            $query->orderBy('is_folder', 'desc')->orderBy('created_at', 'desc');
        }

        $documents = $query->paginate(20)->withQueryString();

        $isPasswordEnabled = Setting::get('sharefile_password_enabled', '1') === '1';
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $isUnlocked = session('sharefile_unlocked', false);

        if ($isUnlocked && !$isAdmin) {
            $lastActivity = session('sharefile_last_activity');
            $timeoutSeconds = 300; // 5 menit

            if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
                session()->forget(['sharefile_unlocked', 'sharefile_last_activity']);
                $isUnlocked = false;
                session()->flash('lock_error', 'Sesi Anda telah terkunci otomatis karena tidak ada aktivitas selama 5 menit. Silakan masukkan kata sandi kembali.');
            } else {
                session(['sharefile_last_activity' => time()]);
            }
        }

        $isLocked = $isPasswordEnabled && !$isAdmin && !$isUnlocked;

        return view('pages.data-file', compact('documents', 'currentFolder', 'isLocked'));
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $savedPassword = Setting::get('sharefile_password', 'budidaya123');

        if ($request->password === $savedPassword) {
            session([
                'sharefile_unlocked' => true,
                'sharefile_last_activity' => time(),
            ]);
            return redirect()->back()->with('success', 'Akses berkas berhasil dibuka.');
        }

        return redirect()->back()->with('lock_error', 'Kata sandi yang Anda masukkan salah. Silakan coba lagi.');
    }

    public function lock(Request $request)
    {
        session()->forget(['sharefile_unlocked', 'sharefile_last_activity']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Akses berkas berhasil dikunci.'
            ]);
        }

        $reason = $request->input('reason');
        $message = ($reason === 'idle')
            ? 'Sesi Anda telah terkunci otomatis karena tidak ada aktivitas selama 5 menit. Silakan masukkan kata sandi kembali.'
            : 'Akses berkas telah berhasil dikunci.';

        $flashKey = ($reason === 'idle') ? 'lock_error' : 'info';

        return redirect('/data-File')->with($flashKey, $message);
    }

    public function pingActivity(Request $request)
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $enabled = Setting::get('sharefile_password_enabled', '1') === '1';

        if (!$enabled || $isAdmin) {
            return response()->json(['success' => true]);
        }

        if (!session('sharefile_unlocked')) {
            return response()->json([
                'success' => false,
                'locked' => true,
                'message' => 'Akses berkas terkunci.'
            ], 403);
        }

        $lastActivity = session('sharefile_last_activity');
        $timeoutSeconds = 300; // 5 menit

        if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
            session()->forget(['sharefile_unlocked', 'sharefile_last_activity']);
            return response()->json([
                'success' => false,
                'locked' => true,
                'message' => 'Sesi telah berakhir karena tidak ada aktivitas selama 5 menit.'
            ], 403);
        }

        session(['sharefile_last_activity' => time()]);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil diperbarui.'
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'nullable|file|max:204800', // Maks 200MB per file
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|max:204800',
            'relative_path' => 'nullable|string',
            'relative_paths' => 'nullable|array',
            'parent_id' => 'nullable|exists:documents,id'
        ]);

        $uploadedDocs = [];
        $parentId = $request->parent_id ?: null;
        $clientIp = $request->ip();

        // 1. Single file upload (AJAX drag-and-drop / single form)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file && $file->isValid()) {
                $doc = $this->storeSingleUploadedFile(
                    $file,
                    $parentId,
                    $request->input('relative_path'),
                    $clientIp
                );
                if ($doc) $uploadedDocs[] = $doc;
            }
        }

        // 2. Multiple files upload (e.g. standard multi-file form)
        if ($request->hasFile('files')) {
            $relativePaths = $request->input('relative_paths', []);
            foreach ($request->file('files') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $relPath = $relativePaths[$idx] ?? null;
                    $doc = $this->storeSingleUploadedFile(
                        $file,
                        $parentId,
                        $relPath,
                        $clientIp
                    );
                    if ($doc) $uploadedDocs[] = $doc;
                }
            }
        }

        if (empty($uploadedDocs)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada file valid yang diunggah atau file melebihi 200MB.'], 400);
            }
            return redirect()->back()->with('error', 'Tidak ada file yang diunggah.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedDocs) . ' file berhasil diunggah.',
                'documents' => $uploadedDocs
            ]);
        }

        return redirect()->back()->with('success', count($uploadedDocs) . ' file berhasil diunggah.');
    }

    private function storeSingleUploadedFile($file, $parentId, $relativePath = null, $clientIp = null)
    {
        $targetParentId = $parentId;

        // Jika ada relative_path dari drag folder (contoh: "FolderUtama/Subfolder/dokumen.docx")
        if (!empty($relativePath)) {
            $normalizedPath = str_replace('\\', '/', trim($relativePath, '/'));
            $dirPart = dirname($normalizedPath);

            if ($dirPart && $dirPart !== '.' && $dirPart !== '/') {
                $segments = explode('/', $dirPart);
                $currParentId = $parentId;

                foreach ($segments as $segment) {
                    $segment = trim($segment);
                    if ($segment === '' || $segment === '.') continue;

                    // Cari folder dengan nama ini di parent saat ini
                    $folder = Document::where('is_folder', true)
                        ->where('parent_id', $currParentId)
                        ->where('original_name', $segment)
                        ->first();

                    if (!$folder) {
                        // Tentukan path folder fisik di Storage
                        $currParentDoc = $currParentId ? Document::find($currParentId) : null;
                        $folderPhysicalPath = $currParentDoc && !empty($currParentDoc->path)
                            ? trim($currParentDoc->path, '/') . '/' . $segment
                            : $segment;

                        // Buat direktori di storage disk jika belum ada
                        Storage::disk('public')->makeDirectory($folderPhysicalPath);

                        $folder = Document::create([
                            'original_name' => $segment,
                            'filename' => $segment,
                            'path' => $folderPhysicalPath,
                            'file_size' => 0,
                            'mime_type' => 'folder',
                            'owner_name' => $clientIp,
                            'is_folder' => true,
                            'parent_id' => $currParentId
                        ]);
                    }

                    $currParentId = $folder->id;
                }

                $targetParentId = $currParentId;
            }
        }

        $originalName = $this->generateUniqueName($file->getClientOriginalName(), $targetParentId, false);

        $targetDir = '';
        if ($targetParentId) {
            $parent = Document::find($targetParentId);
            if ($parent && !empty($parent->path)) {
                $targetDir = trim($parent->path, '/');
            }
        }

        $filename = $originalName;
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $counter = 1;

        $checkPath = $targetDir ? $targetDir . '/' . $filename : $filename;
        while (Storage::disk('public')->exists($checkPath)) {
            $filename = $extension ? $name . '_' . $counter . '.' . $extension : $name . '_' . $counter;
            $checkPath = $targetDir ? $targetDir . '/' . $filename : $filename;
            $counter++;
        }

        $path = $targetDir ? $file->storeAs($targetDir, $filename, 'public') : $file->storeAs('', $filename, 'public');

        return Document::create([
            'original_name' => $originalName,
            'filename' => $filename,
            'path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'owner_name' => $clientIp,
            'is_folder' => false,
            'parent_id' => $targetParentId
        ]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:documents,id'
        ]);

        $folderName = $this->generateUniqueName($request->folder_name, $request->parent_id, true);

        $folderPath = $folderName;
        if ($request->parent_id) {
            $parent = Document::find($request->parent_id);
            if ($parent && !empty($parent->path)) {
                $folderPath = trim($parent->path, '/') . '/' . $folderName;
            }
        }

        // Buat folder fisik di Storage (D:\Share-Budidaya\...)
        Storage::disk('public')->makeDirectory($folderPath);

        Document::create([
            'original_name' => $folderName,
            'filename' => $folderName,
            'path' => $folderPath,
            'file_size' => 0,
            'mime_type' => 'folder',
            'owner_name' => $request->ip(),
            'is_folder' => true,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->back()->with('success', 'Folder berhasil dibuat.');
    }

    public function createWebDoc(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'type' => 'required|in:word,excel',
            'parent_id' => 'nullable|exists:documents,id'
        ]);

        $title = $this->generateUniqueName($request->title, $request->parent_id, false);
        
        $isWord = $request->type == 'word';
        $filename = $title . ($isWord ? '.html' : '.json');
        
        $targetDir = '';
        if ($request->parent_id) {
            $parent = Document::find($request->parent_id);
            if ($parent && !empty($parent->path)) {
                $targetDir = trim($parent->path, '/');
            }
        }

        $name = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $counter = 1;
        $checkPath = $targetDir ? $targetDir . '/' . $filename : $filename;
        while (Storage::disk('public')->exists($checkPath)) {
            $filename = $name . '_' . $counter . '.' . $extension;
            $checkPath = $targetDir ? $targetDir . '/' . $filename : $filename;
            $counter++;
        }
        
        $mimeType = $isWord ? 'text/html' : 'application/json';
        $path = $targetDir ? $targetDir . '/' . $filename : $filename;
        
        $initialContent = $isWord ? '<h1>' . e($title) . '</h1><p>Mulai mengetik di sini...</p>' : '{}';
        Storage::disk('public')->put($path, $initialContent);

        $doc = Document::create([
            'original_name' => $title,
            'filename' => $filename,
            'path' => $path,
            'file_size' => strlen($initialContent),
            'mime_type' => $mimeType,
            'owner_name' => $request->owner_name,
            'is_folder' => false,
            'parent_id' => $request->parent_id
        ]);

        return redirect('data-File/editor/' . $doc->id);
    }

    public function editor($id)
    {
        $document = Document::findOrFail($id);
        
        $allowedExtensions = ['html', 'json', 'docx', 'xlsx', 'xls', 'csv'];
        $ext = pathinfo($document->filename, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($ext), $allowedExtensions)) {
            return redirect()->back()->with('error', 'Tipe file ini tidak bisa diedit secara langsung di Web.');
        }

        $content = '';
        if ($ext == 'html' || $ext == 'json') {
            $content = Storage::disk('public')->get($document->path);
        }

        return view('pages.editor', compact('document', 'content', 'ext'));
    }

    public function updateWebDoc(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $ext = pathinfo($document->filename, PATHINFO_EXTENSION);
        
        if ($request->hasFile('file')) {
            // Binary overwrite (for DOCX, XLSX)
            $file = $request->file('file');
            $fileContent = file_get_contents($file->getRealPath());
            Storage::disk('public')->put($document->path, $fileContent);
            $size = strlen($fileContent);
        } else {
            // Text overwrite (for HTML, JSON)
            $content = $request->input('content');
            Storage::disk('public')->put($document->path, $content);
            $size = strlen($content);
        }
        
        $document->update([
            'file_size' => $size
        ]);

        return response()->json(['success' => true]);
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        $filePath = Storage::disk('public')->path($document->path);

        if (file_exists($filePath)) {
            return response()->download($filePath, $document->original_name);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan di server.');
    }

    public function downloadFolder($id)
    {
        $folder = Document::where('is_folder', true)->findOrFail($id);
        
        $zip = new \ZipArchive();
        $zipFileName = $folder->original_name . '.zip';
        $zipFilePath = Storage::disk('public')->path($zipFileName);

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $folderPath = trim($folder->path, '/');
            $fullFolderPath = Storage::disk('public')->path($folderPath);

            if (is_dir($fullFolderPath)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($fullFolderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                $count = 0;
                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($fullFolderPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                        $count++;
                    }
                }

                if ($count === 0) {
                    $zip->close();
                    if (file_exists($zipFilePath)) unlink($zipFilePath);
                    return redirect()->back()->with('error', 'Folder kosong, tidak ada yang bisa didownload.');
                }
            } else {
                $zip->close();
                if (file_exists($zipFilePath)) unlink($zipFilePath);
                return redirect()->back()->with('error', 'Folder tidak ditemukan di server.');
            }

            $zip->close();
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
    }

    public function rename(Request $request, $id)
    {
        $request->validate([
            'new_name' => 'required|string|max:255'
        ]);

        $document = Document::findOrFail($id);
        $newName = $this->generateUniqueName($request->new_name, $document->parent_id, $document->is_folder, $document->id);
        
        $oldPath = $document->path;
        $parentPath = '';
        if ($document->parent_id) {
            $parent = Document::find($document->parent_id);
            if ($parent && !empty($parent->path)) {
                $parentPath = trim($parent->path, '/');
            }
        }

        $newPath = $parentPath ? $parentPath . '/' . $newName : $newName;

        if ($document->is_folder) {
            if (!empty($oldPath) && $oldPath !== $newPath) {
                $oldFullPath = Storage::disk('public')->path($oldPath);
                $newFullPath = Storage::disk('public')->path($newPath);
                if (is_dir($oldFullPath)) {
                    @rename($oldFullPath, $newFullPath);
                }

                // Update path semua descendant
                $descendants = Document::where('path', 'like', $oldPath . '/%')->get();
                foreach ($descendants as $descendant) {
                    $descendant->update([
                        'path' => $newPath . substr($descendant->path, strlen($oldPath))
                    ]);
                }
            }

            $document->update([
                'original_name' => $newName,
                'filename' => $newName,
                'path' => $newPath
            ]);
        } else {
            if (!empty($oldPath) && $oldPath !== $newPath) {
                $oldFullPath = Storage::disk('public')->path($oldPath);
                $newFullPath = Storage::disk('public')->path($newPath);
                if (file_exists($oldFullPath)) {
                    @rename($oldFullPath, $newFullPath);
                }
            }

            $document->update([
                'original_name' => $newName,
                'filename' => $newName,
                'path' => $newPath
            ]);
        }

        return redirect()->back()->with('success', 'Nama berhasil diubah.');
    }

    public function move(Request $request, $id)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:documents,id'
        ]);

        $document = Document::findOrFail($id);

        if ($document->is_folder && $document->id == $request->parent_id) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat memindahkan folder ke dalam dirinya sendiri.']);
        }

        $oldPath = $document->path;
        $targetDir = '';
        if ($request->parent_id) {
            $newParent = Document::find($request->parent_id);
            if ($newParent && !empty($newParent->path)) {
                $targetDir = trim($newParent->path, '/');
            }
        }

        $itemName = $document->is_folder ? $document->original_name : $document->filename;
        $newPath = $targetDir ? $targetDir . '/' . $itemName : $itemName;

        if ($document->is_folder) {
            if (!empty($oldPath) && $oldPath !== $newPath) {
                $oldFullPath = Storage::disk('public')->path($oldPath);
                $newFullPath = Storage::disk('public')->path($newPath);
                if (is_dir($oldFullPath)) {
                    @rename($oldFullPath, $newFullPath);
                }

                // Update path semua descendant
                $descendants = Document::where('path', 'like', $oldPath . '/%')->get();
                foreach ($descendants as $descendant) {
                    $descendant->update([
                        'path' => $newPath . substr($descendant->path, strlen($oldPath))
                    ]);
                }
            }

            $document->update([
                'parent_id' => $request->parent_id,
                'path' => $newPath
            ]);
        } else {
            if (!empty($oldPath) && $oldPath !== $newPath) {
                $oldFullPath = Storage::disk('public')->path($oldPath);
                $newFullPath = Storage::disk('public')->path($newPath);
                if (file_exists($oldFullPath)) {
                    @rename($oldFullPath, $newFullPath);
                }
            }

            $document->update([
                'parent_id' => $request->parent_id,
                'path' => $newPath
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $isFolder = $document->is_folder;
        $name = $document->original_name;

        $this->softDeleteRecursively($document);

        return redirect()->back()->with('success', $isFolder ? "Folder '$name' dan seluruh isinya berhasil dipindahkan ke Tempat Sampah." : "File '$name' berhasil dipindahkan ke Tempat Sampah.");
    }

    private function softDeleteRecursively($document)
    {
        if ($document->is_folder) {
            $children = Document::where('parent_id', $document->id)->get();
            foreach ($children as $child) {
                $this->softDeleteRecursively($child);
            }
        }

        self::movePhysicalToTrash($document);
        $document->delete();
    }

    public static function movePhysicalToTrash($document)
    {
        try {
            $disk = Storage::disk('public');
            if (!$disk->exists('.trash')) {
                $disk->makeDirectory('.trash');
            }

            if (!empty($document->path)) {
                $src = $disk->path($document->path);
                $destName = $document->id . '_' . basename($document->path);
                $dest = $disk->path('.trash/' . $destName);

                if ($document->is_folder) {
                    if (is_dir($src)) {
                        @rename($src, $dest);
                    }
                } else {
                    if (file_exists($src)) {
                        @rename($src, $dest);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently proceed
        }
    }

    public static function restorePhysicalFromTrash($document)
    {
        try {
            $disk = Storage::disk('public');
            if (!empty($document->path)) {
                $dest = $disk->path($document->path);
                $destDir = dirname($dest);
                if (!is_dir($destDir)) {
                    @mkdir($destDir, 0777, true);
                }

                $trashName = $document->id . '_' . basename($document->path);
                $src = $disk->path('.trash/' . $trashName);

                if ($document->is_folder) {
                    if (is_dir($src)) {
                        @rename($src, $dest);
                    }
                } else {
                    if (file_exists($src)) {
                        @rename($src, $dest);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently proceed
        }
    }

    public static function forceDeletePhysical($document)
    {
        try {
            $disk = Storage::disk('public');
            $trashName = $document->id . '_' . basename($document->path);
            $trashPath = $disk->path('.trash/' . $trashName);
            $originalPath = !empty($document->path) ? $disk->path($document->path) : null;

            if ($document->is_folder) {
                if (is_dir($trashPath)) {
                    File::deleteDirectory($trashPath);
                }
                if ($originalPath && is_dir($originalPath)) {
                    File::deleteDirectory($originalPath);
                }
            } else {
                if (file_exists($trashPath)) {
                    @unlink($trashPath);
                }
                if ($originalPath && file_exists($originalPath)) {
                    @unlink($originalPath);
                }
            }
        } catch (\Throwable $e) {
            // Silently proceed
        }
    }

    public function replaceFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB max
        ]);

        $document = Document::findOrFail($id);

        if ($document->is_folder) {
            return redirect()->back()->with('error', 'Tidak dapat menimpa folder dengan file.');
        }

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            
            // Simpan langsung ke path yang sama untuk menimpa file
            $path = $document->path;
            $fileContent = file_get_contents($uploadedFile->getRealPath());
            Storage::disk('public')->put($path, $fileContent);

            $document->update([
                'file_size' => $uploadedFile->getSize(),
                'mime_type' => $uploadedFile->getMimeType(),
            ]);

            return redirect()->back()->with('success', 'File "' . $document->original_name . '" berhasil diperbarui dengan versi terbaru.');
        }

        return redirect()->back()->with('error', 'Tidak ada file yang dipilih.');
    }

    public function downloadDriveBat(Request $request)
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: $request->getHost();
        if ($host === '127.0.0.1' || $host === 'localhost') {
            $host = '192.168.110.121';
        }
        $shareName = env('SMB_SHARE_NAME', 'Share-Budidaya');
        $uncPath = "\\\\{$host}\\{$shareName}";

        $batContent = "@echo off\r\n" .
            "title Hubungkan Folder Server ShareFile ke Drive Z:\r\n" .
            "chcp 65001 >nul\r\n" .
            "cls\r\n" .
            "echo =========================================================\r\n" .
            "echo    MENGHUBUNGKAN DRIVE JARINGAN KE SERVER SHAREFILE\r\n" .
            "echo =========================================================\r\n" .
            "echo.\r\n" .
            "echo Server IP   : {$host}\r\n" .
            "echo Folder Share: {$uncPath}\r\n" .
            "echo Drive Tujuan: Z:\r\n" .
            "echo.\r\n" .
            "echo Sedang memutuskan koneksi drive Z: lama jika ada...\r\n" .
            "net use Z: /delete /yes >nul 2>&1\r\n" .
            "echo Sedang menghubungkan drive Z: ke server...\r\n" .
            "net use Z: \"{$uncPath}\" /persistent:yes\r\n" .
            "if %errorlevel% equ 0 (\r\n" .
            "    echo.\r\n" .
            "    echo [BERHASIL] Folder server berhasil dihubungkan ke Drive Z:!\r\n" .
            "    echo File Word & Excel di Drive Z: langsung tersimpan di server saat Anda tekan Ctrl+S.\r\n" .
            "    echo.\r\n" .
            "    echo Membuka Drive Z: di File Explorer...\r\n" .
            "    start explorer Z:\\\r\n" .
            ") else (\r\n" .
            "    echo.\r\n" .
            "    echo [GAGAL] Tidak dapat terhubung ke server.\r\n" .
            "    echo Pastikan komputer Anda terhubung ke jaringan Wi-Fi/LAN yang sama dengan server.\r\n" .
            ")\r\n" .
            "echo.\r\n" .
            "pause\r\n";

        return response($batContent, 200, [
            'Content-Type' => 'application/x-bat',
            'Content-Disposition' => 'attachment; filename="Hubungkan_Drive_Z.bat"',
        ]);
    }

    private function syncPhysicalFilesWithDatabase($currentFolder = null)
    {
        try {
            $disk = Storage::disk('public');
            $folderPath = $currentFolder ? trim($currentFolder->path, '/') : '';

            // Abaikan folder .trash sistem
            if ($folderPath === '.trash' || str_starts_with($folderPath, '.trash/') || str_starts_with($folderPath, '.trash\\')) {
                return;
            }

            $parentId = $currentFolder ? $currentFolder->id : null;

            // 1. Sync ukuran file yang ada di DB dengan file fisik di storage
            $existingDocs = Document::where('parent_id', $parentId)->where('is_folder', false)->get();
            foreach ($existingDocs as $doc) {
                if (!empty($doc->path) && $disk->exists($doc->path)) {
                    $actualSize = $disk->size($doc->path);
                    if ($actualSize !== $doc->file_size) {
                        $doc->update(['file_size' => $actualSize]);
                    }
                }
            }

            // 2. Scan file baru di disk yang belum tercatat di database
            $filesOnDisk = $disk->files($folderPath);
            foreach ($filesOnDisk as $filePath) {
                $fileName = basename($filePath);
                if (in_array($fileName, ['.gitignore', '.DS_Store', 'Thumbs.db', '.trash'])) {
                    continue;
                }

                $alreadyInDb = Document::withTrashed()->where('parent_id', $parentId)->where('filename', $fileName)->exists();
                if (!$alreadyInDb) {
                    $size = $disk->size($filePath);
                    $mime = $disk->mimeType($filePath) ?: 'application/octet-stream';
                    Document::create([
                        'original_name' => $fileName,
                        'filename' => $fileName,
                        'path' => $filePath,
                        'file_size' => $size,
                        'mime_type' => $mime,
                        'owner_name' => 'SMB/Server',
                        'is_folder' => false,
                        'parent_id' => $parentId,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore sync errors to not disrupt web loading
        }
    }
}
