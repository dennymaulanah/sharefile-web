<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
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

        $query->orderBy('is_folder', 'desc')->orderBy('created_at', 'desc');
        $documents = $query->get();
        return view('pages.data-file', compact('documents', 'currentFolder'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB max
            'parent_id' => 'nullable|exists:documents,id'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $this->generateUniqueName($file->getClientOriginalName(), $request->parent_id, false);
            
            $filename = $originalName;
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $counter = 1;
            while (Storage::disk('public')->exists('file_documents/' . $filename)) {
                $filename = $name . '_' . $counter . '.' . $extension;
                $counter++;
            }

            $path = $file->storeAs('file_documents', $filename, 'public');

            Document::create([
                'original_name' => $originalName,
                'filename' => $filename,
                'path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'owner_name' => $request->ip(),
                'is_folder' => false,
                'parent_id' => $request->parent_id
            ]);

            return redirect()->back()->with('success', 'File berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Tidak ada file yang diunggah.');
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:documents,id'
        ]);

        $folderName = $this->generateUniqueName($request->folder_name, $request->parent_id, true);

        Document::create([
            'original_name' => $folderName,
            'filename' => Str::uuid(),
            'path' => '',
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
            'type' => 'required|in:word,excel'
        ]);

        $title = $this->generateUniqueName($request->title, null, false);
        
        $isWord = $request->type == 'word';
        $filename = $title . ($isWord ? '.html' : '.json');
        
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $counter = 1;
        while (Storage::disk('public')->exists('file_documents/' . $filename)) {
            $filename = $name . '_' . $counter . '.' . $extension;
            $counter++;
        }
        
        $mimeType = $isWord ? 'text/html' : 'application/json';
        $path = 'file_documents/' . $filename;
        
        $initialContent = $isWord ? '<h1>' . e($title) . '</h1><p>Mulai mengetik di sini...</p>' : '{}';
        Storage::disk('public')->put($path, $initialContent);

        $doc = Document::create([
            'original_name' => $title,
            'filename' => $filename,
            'path' => $path,
            'file_size' => strlen($initialContent),
            'mime_type' => $mimeType,
            'owner_name' => $request->owner_name
        ]);

        return redirect('data-file/editor/' . $doc->id);
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
        $filePath = storage_path('app/public/' . $document->path);

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
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $files = Document::where('parent_id', $folder->id)->where('is_folder', false)->get();
            
            if ($files->count() == 0) {
                $zip->close();
                if(file_exists($zipFilePath)) unlink($zipFilePath);
                return redirect()->back()->with('error', 'Folder kosong, tidak ada yang bisa didownload.');
            }

            foreach ($files as $file) {
                $filePath = storage_path('app/public/' . $file->path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file->original_name);
                }
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
        
        $document->update([
            'original_name' => $newName
        ]);

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

        $document->update([
            'parent_id' => $request->parent_id
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        if ($document->is_folder && $document->children()->count() > 0) {
            return redirect()->back()->with('error', 'Folder tidak kosong, silakan hapus isinya terlebih dahulu.');
        }

        // Hapus file fisik
        if (!$document->is_folder && Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }

        $document->delete();

        return redirect()->back()->with('success', $document->is_folder ? 'Folder berhasil dihapus.' : 'File berhasil dihapus.');
    }
}
