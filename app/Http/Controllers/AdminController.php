<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Helper to format bytes to human readable size
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        if (!$bytes || $bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Admin Dashboard Overview
     */
    public function index()
    {
        $totalFiles = Document::where('is_folder', false)->count();
        $totalFolders = Document::where('is_folder', true)->count();
        $totalSizeBytes = Document::where('is_folder', false)->sum('file_size');
        $totalSizeFormatted = self::formatBytes($totalSizeBytes);
        $totalUsers = User::count();

        // Categorize file statistics
        $allFiles = Document::where('is_folder', false)->select('original_name', 'file_size')->get();
        
        $categories = [
            'documents' => ['count' => 0, 'size' => 0, 'label' => 'Dokumen (PDF, Word, Teks)', 'color' => '#0d6efd', 'extensions' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'webdoc']],
            'spreadsheets' => ['count' => 0, 'size' => 0, 'label' => 'Lembar Kerja (Excel, CSV)', 'color' => '#198754', 'extensions' => ['xls', 'xlsx', 'csv', 'ods']],
            'images' => ['count' => 0, 'size' => 0, 'label' => 'Gambar & Media', 'color' => '#fd7e14', 'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp']],
            'archives' => ['count' => 0, 'size' => 0, 'label' => 'Arsip & Kompresi', 'color' => '#6f42c1', 'extensions' => ['zip', 'rar', '7z', 'tar', 'gz', 'iso']],
            'others' => ['count' => 0, 'size' => 0, 'label' => 'Lainnya', 'color' => '#6c757d', 'extensions' => []],
        ];

        foreach ($allFiles as $file) {
            $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
            $matched = false;
            foreach ($categories as $key => &$cat) {
                if ($key === 'others') continue;
                if (in_array($ext, $cat['extensions'])) {
                    $cat['count']++;
                    $cat['size'] += $file->file_size;
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $categories['others']['count']++;
                $categories['others']['size'] += $file->file_size;
            }
        }

        // Recent items
        $recentDocuments = Document::with('parent')->latest()->take(10)->get();

        // System environment info
        $storageRoot = config('filesystems.disks.public.root', storage_path('app/public'));
        $diskFree = @disk_free_space($storageRoot);
        $diskTotal = @disk_total_space($storageRoot);
        $diskUsedPercent = ($diskTotal && $diskFree) ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : null;

        $serverInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'storage_root' => $storageRoot,
            'disk_free' => $diskFree ? self::formatBytes($diskFree) : 'N/A',
            'disk_total' => $diskTotal ? self::formatBytes($diskTotal) : 'N/A',
            'disk_used_percent' => $diskUsedPercent,
            'max_upload' => ini_get('upload_max_filesize'),
            'post_max' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'db_driver' => config('database.default'),
        ];

        $sharefilePassword = Setting::get('sharefile_password', 'budidaya123');
        $sharefilePasswordEnabled = Setting::get('sharefile_password_enabled', '1') === '1';

        return view('admin.dashboard', compact(
            'totalFiles',
            'totalFolders',
            'totalSizeBytes',
            'totalSizeFormatted',
            'totalUsers',
            'categories',
            'recentDocuments',
            'serverInfo',
            'sharefilePassword',
            'sharefilePasswordEnabled'
        ));
    }

    /**
     * File Management (All Files & Folders)
     */
    public function files(Request $request)
    {
        $query = Document::with('parent');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('path', 'like', "%{$search}%");
            });
        }

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
        $direction = $request->get('direction', 'desc');
        if (in_array($sort, ['original_name', 'file_size', 'created_at', 'owner_name'])) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $documents = $query->paginate(20)->withQueryString();

        return view('admin.files', compact('documents'));
    }

    /**
     * Delete a file or folder permanently
     */
    public function destroyFile($id)
    {
        $document = Document::findOrFail($id);
        $name = $document->original_name;
        $isFolder = $document->is_folder;

        if ($document->is_folder) {
            $children = Document::where('parent_id', $document->id)->get();
            foreach ($children as $child) {
                DocumentController::movePhysicalToTrash($child);
                $child->delete();
            }
        }

        DocumentController::movePhysicalToTrash($document);
        $document->delete();

        return redirect()->back()->with('success', ($isFolder ? 'Folder' : 'File') . " '{$name}' berhasil dipindahkan ke Tempat Sampah.");
    }

    /**
     * Recycle Bin (Trashed Documents)
     */
    public function trash(Request $request)
    {
        $query = Document::onlyTrashed()->with('parent');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('path', 'like', "%{$search}%");
            });
        }

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

        $trashedDocuments = $query->orderBy('deleted_at', 'desc')->paginate(20)->withQueryString();
        $trashCount = Document::onlyTrashed()->count();

        return view('admin.trash', compact('trashedDocuments', 'trashCount'));
    }

    /**
     * Restore a trashed file or folder
     */
    public function restoreFile($id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        $name = $document->original_name;
        $isFolder = $document->is_folder;

        if ($isFolder) {
            $descendants = Document::onlyTrashed()->where('path', 'like', $document->path . '/%')->get();
            foreach ($descendants as $desc) {
                DocumentController::restorePhysicalFromTrash($desc);
                $desc->restore();
            }
        }

        DocumentController::restorePhysicalFromTrash($document);
        $document->restore();

        return redirect()->back()->with('success', ($isFolder ? 'Folder' : 'File') . " '{$name}' berhasil dipulihkan.");
    }

    /**
     * Permanently delete a file or folder (Admin Only)
     */
    public function forceDeleteFile($id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        $name = $document->original_name;
        $isFolder = $document->is_folder;

        if ($isFolder) {
            $descendants = Document::onlyTrashed()->where('path', 'like', $document->path . '/%')->get();
            foreach ($descendants as $desc) {
                DocumentController::forceDeletePhysical($desc);
                $desc->forceDelete();
            }
        }

        DocumentController::forceDeletePhysical($document);
        $document->forceDelete();

        return redirect()->back()->with('success', ($isFolder ? 'Folder' : 'File') . " '{$name}' telah dihapus secara permanen dari server.");
    }

    /**
     * Empty entire Recycle Bin (Admin Only)
     */
    public function emptyTrash()
    {
        $trashed = Document::onlyTrashed()->get();
        $count = $trashed->count();

        foreach ($trashed as $doc) {
            DocumentController::forceDeletePhysical($doc);
            $doc->forceDelete();
        }

        $trashDir = Storage::disk('public')->path('.trash');
        if (is_dir($trashDir)) {
            File::cleanDirectory($trashDir);
        }

        return redirect()->back()->with('success', "Tempat Sampah berhasil dikosongkan. Sebanyak {$count} item telah dihapus secara permanen.");
    }

    /**
     * User Management
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => strtolower($request->username),
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'Pengguna ' . $request->name . ' berhasil ditambahkan.');
    }

    /**
     * Update user details or reset password
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,user',
            'password' => 'nullable|string|min:6',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        // Prevent self-demotion if only one admin left
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak dapat mencabut peran admin dari akun Anda sendiri.');
        }

        $data = [
            'name' => $request->name,
            'username' => strtolower($request->username),
            'email' => strtolower($request->email),
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Data pengguna ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('success', "Pengguna '{$name}' berhasil dihapus.");
    }

    /**
     * Update Admin's own profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->username = strtolower($request->username);
        $user->email = strtolower($request->email);
        $user->save();

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Update ShareFile Access Password Settings
     */
    public function updateSharefilePassword(Request $request)
    {
        $request->validate([
            'sharefile_password' => 'required|string|min:4',
        ], [
            'sharefile_password.required' => 'Password akses ShareFile wajib diisi.',
            'sharefile_password.min' => 'Password minimal 4 karakter.',
        ]);

        Setting::set('sharefile_password', $request->sharefile_password);
        Setting::set('sharefile_password_enabled', $request->has('sharefile_password_enabled') ? '1' : '0');

        return redirect()->back()->with('success', 'Pengaturan password akses ShareFile berhasil diperbarui.');
    }
}
