<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $currentFolder = null;
        
        if ($request->has('q') && $request->q != '') {
            // Jika sedang mencari, tampilkan semua hasil tanpa mempedulikan struktur folder
            $searchTerm = $request->q;
            $query = Document::where(function($q) use ($searchTerm) {
                $q->where('original_name', 'like', "%{$searchTerm}%")
                  ->orWhere('owner_name', 'like', "%{$searchTerm}%");
            });
        } else {
            if ($request->has('folder') && $request->folder != '') {
                $currentFolder = Document::where('is_folder', true)->findOrFail($request->folder);
                $query = Document::where('parent_id', $currentFolder->id);
            } else {
                $query = Document::whereNull('parent_id');
            }
        }

        $query->orderBy('is_folder', 'desc')->orderBy('created_at', 'desc');
        $documents = $query->get();
        return view('pages.data-budidaya', compact('documents', 'currentFolder'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB max
            'parent_id' => 'nullable|exists:documents,id'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('budidaya_documents', $filename, 'public');

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

        Document::create([
            'original_name' => $request->folder_name,
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

        $uuid = Str::uuid();
        $isWord = $request->type == 'word';
        $filename = $uuid . ($isWord ? '.html' : '.json');
        $mimeType = $isWord ? 'text/html' : 'application/json';
        $path = 'budidaya_documents/' . $filename;
        
        $initialContent = $isWord ? '<h1>' . e($request->title) . '</h1><p>Mulai mengetik di sini...</p>' : '{}';
        Storage::disk('public')->put($path, $initialContent);

        $doc = Document::create([
            'original_name' => $request->title,
            'filename' => $filename,
            'path' => $path,
            'file_size' => strlen($initialContent),
            'mime_type' => $mimeType,
            'owner_name' => $request->owner_name
        ]);

        return redirect('data-budidaya/editor/' . $doc->id);
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
        $document->update([
            'original_name' => $request->new_name
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
