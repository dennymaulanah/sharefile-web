@extends('layouts.app')
@section('content')

<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* Premium Aesthetic Variables & Base */
:root {
  --primary-gradient: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
  --success-gradient: linear-gradient(135deg, #10B981 0%, #059669 100%);
  --danger-gradient:  linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
  --info-gradient:    linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
  --glass-bg: rgba(255, 255, 255, 0.7);
  --glass-border: rgba(255, 255, 255, 0.4);
  --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #f3f4f6; /* Fallback */
    caret-color: transparent; /* Sembunyikan kursor ketik pada teks biasa */
}

input, textarea, [contenteditable] {
    caret-color: auto; /* Kembalikan kursor ketik pada input */
}

/* Stunning Animated Background */
.data-budidaya-bg {
    position: relative;
    background: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
    min-height: 100vh;
    overflow: hidden;
    z-index: 1;
}

.data-budidaya-bg::before {
    content: '';
    position: absolute;
    top: -10%;
    left: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(124,58,237,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    z-index: -1;
    animation: float 8s ease-in-out infinite;
}

.data-budidaya-bg::after {
    content: '';
    position: absolute;
    bottom: -10%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    z-index: -1;
    animation: float 10s ease-in-out infinite reverse;
}

@keyframes float {
    0% { transform: translateY(0px) translateX(0px); }
    50% { transform: translateY(20px) translateX(20px); }
    100% { transform: translateY(0px) translateX(0px); }
}

/* Glassmorphism Containers */
.glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
    border-radius: 24px;
}

/* Typography & Links */
.text-gradient {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent;
}

.hover-primary {
    transition: all 0.3s ease;
}
.hover-primary:hover {
    color: #4F46E5 !important;
    transform: translateX(2px);
}

/* Search Bar */
.search-wrapper {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(8px);
    border-radius: 50px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02), 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}
.search-wrapper:focus-within {
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02), 0 4px 15px rgba(79, 70, 229, 0.15);
    border-color: #818cf8;
}
.search-wrapper input {
    background: transparent;
}
.search-wrapper input:focus {
    box-shadow: none;
    background: transparent;
}

/* Primary Button */
.btn-gradient {
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 10px 24px;
    font-weight: 500;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
    color: white;
}

/* Dropdown Menu */
.glass-dropdown {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.8);
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border-radius: 16px;
    padding: 10px;
}
.glass-dropdown .dropdown-item {
    border-radius: 10px;
    transition: all 0.2s ease;
    font-weight: 500;
    color: #4b5563;
}
.glass-dropdown .dropdown-item:hover {
    background: rgba(79, 70, 229, 0.08);
    color: #4F46E5;
    transform: translateX(4px);
}

/* Custom Table */
.modern-table {
    border-collapse: separate;
    border-spacing: 0 12px;
}
.modern-table th {
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
    border: none;
    padding: 0 20px;
}
.modern-table td {
    background: rgba(255, 255, 255, 0.6);
    border: none;
    padding: 16px 20px;
    vertical-align: middle;
    transition: all 0.3s ease;
}
.modern-table td:first-child {
    border-top-left-radius: 16px;
    border-bottom-left-radius: 16px;
}
.modern-table td:last-child {
    border-top-right-radius: 16px;
    border-bottom-right-radius: 16px;
}
.modern-table tr {
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
}
.modern-table tr:hover {
    transform: translateY(-3px) scale(1.005);
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}
.modern-table tr:hover td {
    background: rgba(255, 255, 255, 0.9);
}
.folder-row.drag-over {
    background: rgba(79, 70, 229, 0.1) !important;
    outline: 2px dashed #4F46E5;
    transform: scale(1.02);
}

/* Action Buttons in Table */
.action-btn-modern {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(255,255,255,0.8);
    color: #6b7280;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.2s ease;
    text-decoration: none;
}
.action-btn-modern:hover {
    transform: translateY(-2px);
}
.btn-hover-primary:hover { background: var(--primary-gradient); color: white !important; border-color: transparent;}
.btn-hover-info:hover { background: var(--info-gradient); color: white !important; border-color: transparent;}
.btn-hover-danger:hover { background: var(--danger-gradient); color: white !important; border-color: transparent;}
.btn-hover-success:hover { background: var(--success-gradient); color: white !important; border-color: transparent;}

/* Icon Gradients */
.icon-gradient-word { background: linear-gradient(135deg, #1E3A8A, #3B82F6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.icon-gradient-excel { background: linear-gradient(135deg, #064E3B, #10B981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.icon-gradient-pdf { background: linear-gradient(135deg, #991B1B, #EF4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.icon-gradient-image { background: linear-gradient(135deg, #075985, #0EA5E9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.icon-gradient-zip { background: linear-gradient(135deg, #B45309, #F59E0B); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.icon-gradient-default { background: linear-gradient(135deg, #4B5563, #9CA3AF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

/* Modal Glassmorphism */
.modal-glass {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.6);
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}
.modal-glass .modal-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.modal-glass .modal-footer {
    border-top: 1px solid rgba(0,0,0,0.05);
}
.form-control-modern {
    background: rgba(255,255,255,0.7);
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 12px;
    padding: 12px 16px;
    transition: all 0.3s ease;
}
.form-control-modern:focus {
    background: #fff;
    border-color: #818cf8;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

/* Custom Alert */
.alert-glass {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: none;
    border-left: 5px solid;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.alert-glass-success { border-left-color: #10B981; }
.alert-glass-danger { border-left-color: #EF4444; }

/* Global Drag Over Styling */
.drag-over-global::after {
    content: 'Drop files to upload...';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(5px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    color: #4F46E5;
    border: 4px dashed #4F46E5;
    border-radius: 24px;
}

</style>

<section id="data-budidaya" class="data-budidaya-bg pt-5">
  <div class="container py-4" data-aos="fade-up" data-aos-duration="800">
    
    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-glass alert-glass-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
            <div class="fw-medium text-dark">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-glass alert-glass-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-3"></i>
            <div class="fw-medium text-dark">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-glass alert-glass-danger alert-dismissible fade show" role="alert">
            <div class="d-flex">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-3 mt-1"></i>
                <div>
                    <div class="fw-bold text-dark mb-1">Terjadi Kesalahan</div>
                    <ul class="mb-0 text-dark opacity-75 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main App Container -->
    <div class="glass-card p-4 p-md-5">
      
      <!-- Top Header -->
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-5 gap-4">
        
        <!-- Brand / Title -->
        <div class="d-flex align-items-center">
           <div class="bg-white rounded-circle p-3 shadow-sm me-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
               <i class="bi bi-cloud-check-fill fs-3 text-gradient"></i>
           </div>
           <div>
               <h3 class="fw-bold mb-0 text-dark">Share Budidaya</h3>
               <p class="text-muted mb-0 fs-6">Kelola dan pantau dokumen perikanan Anda</p>
           </div>
        </div>
        
        <!-- Actions & Search -->
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
           <!-- Search -->
           <form action="{{ url('/data-budidaya') }}" method="GET" class="search-wrapper d-flex align-items-center px-3 py-1">
             <i class="bi bi-search text-muted"></i>
             <input type="text" name="q" class="form-control border-0 shadow-none py-2 px-3" placeholder="Cari dokumen..." value="{{ request('q') }}">
             @if(request('q'))
                <a href="{{ url('/data-budidaya') }}" class="text-muted hover-primary ms-2"><i class="bi bi-x-circle-fill"></i></a>
             @endif
           </form>
           
           <!-- New Button -->
           <div class="dropdown">
               <button class="btn btn-gradient px-4 py-2 d-flex align-items-center w-100 justify-content-center" data-bs-toggle="dropdown" aria-expanded="false">
                 <i class="bi bi-plus-lg me-2 fw-bold"></i> Baru
               </button>
               <ul class="dropdown-menu dropdown-menu-end glass-dropdown mt-3 border-0">
                 <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#folderModal">
                    <div class="bg-light rounded p-2 me-3"><i class="bi bi-folder-plus fs-5 text-warning"></i></div>
                    <div>
                        <div class="fw-bold">Buat Folder</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Buat folder baru</div>
                    </div>
                 </a></li>
                 <li><hr class="dropdown-divider"></li>
                 <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <div class="bg-light rounded p-2 me-3"><i class="bi bi-cloud-arrow-up-fill fs-5 text-primary"></i></div>
                    <div>
                        <div class="fw-bold">Upload File</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Unggah dari komputer</div>
                    </div>
                 </a></li>
               </ul>
           </div>
        </div>
      </div>

      <!-- Table Section -->
      @if($currentFolder)
      <div class="d-flex align-items-center mb-3">
          <a href="{{ $currentFolder->parent_id ? url('data-budidaya?folder='.$currentFolder->parent_id) : url('data-budidaya') }}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm me-2 hover-primary folder-row" data-doc-id="{{ $currentFolder->parent_id ?? '' }}">
              <i class="bi bi-arrow-left me-1"></i> Kembali
          </a>
          <span class="text-muted fw-bold">/ {{ $currentFolder->original_name }}</span>
      </div>
      @endif
      <div class="table-responsive px-1 pb-3">
        <table class="table modern-table w-100">
          <thead>
            <tr>
              <th scope="col" style="min-width: 250px;">Nama File</th>
              <th scope="col">Pemilik</th>
              <th scope="col">Dimodifikasi</th>
              <th scope="col">Ukuran</th>
              <th scope="col" class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody id="document-table-body">
            @forelse($documents as $doc)
            @php
                $isEditable = false;
                $ext = pathinfo($doc->filename, PATHINFO_EXTENSION);
                if(!$doc->is_folder && in_array(strtolower($ext), ['html', 'json', 'docx', 'xlsx', 'xls', 'csv'])) {
                    $isEditable = true;
                }

                if($doc->is_folder) {
                    $iconClass = 'bi-folder-fill text-warning';
                } else {
                    $iconClass = 'bi-file-earmark-fill icon-gradient-default';
                    if(Str::contains($doc->mime_type, 'pdf')) $iconClass = 'bi-file-earmark-pdf-fill icon-gradient-pdf';
                    elseif(Str::contains($doc->mime_type, 'image')) $iconClass = 'bi-file-earmark-image-fill icon-gradient-image';
                    elseif(Str::contains($doc->mime_type, 'word') || $doc->mime_type == 'text/html' || $ext == 'docx') { $iconClass = 'bi-file-earmark-word-fill icon-gradient-word'; }
                    elseif(Str::contains($doc->mime_type, 'excel') || Str::contains($doc->mime_type, 'spreadsheet') || $doc->mime_type == 'application/json' || in_array($ext, ['xlsx', 'xls', 'csv'])) { $iconClass = 'bi-file-earmark-excel-fill icon-gradient-excel'; }
                    elseif(Str::contains($doc->mime_type, 'zip') || Str::contains($doc->mime_type, 'rar')) $iconClass = 'bi-file-earmark-zip-fill icon-gradient-zip';
                }
            @endphp
            <tr draggable="true" data-doc-id="{{ $doc->id }}" data-is-folder="{{ $doc->is_folder ? 'true' : 'false' }}" class="document-row {{ $doc->is_folder ? 'folder-row' : '' }}">
              <td>
                <div class="d-flex align-items-center">
                  <div class="p-2 bg-white rounded-3 shadow-sm me-3">
                      <i class="bi {{ $iconClass }} fs-4"></i>
                  </div>
                  @if($doc->is_folder)
                      <a href="{{ url('data-budidaya?folder='.$doc->id) }}" class="text-decoration-none fw-semibold text-dark hover-primary">{{ $doc->original_name }}</a>
                  @else
                      <span class="fw-semibold text-dark">{{ $doc->original_name }}</span>
                  @endif
                </div>
              </td>
              <td>
                  <div class="d-flex align-items-center">
                      <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 28px; height: 28px; background: var(--primary-gradient); font-size: 0.7rem;">
                          {{ substr($doc->owner_name ?? 'A', 0, 1) }}
                      </div>
                      <span class="text-dark fw-medium">{{ $doc->owner_name ?? 'Admin' }}</span>
                  </div>
              </td>
              <td>
                  <span class="text-muted fw-medium"><i class="bi bi-clock me-1 opacity-50"></i> {{ $doc->created_at->format('d M, Y') }}</span>
              </td>
              <td>
                  <span class="badge bg-light text-dark border px-2 py-1">
                      @php
                          $bytes = $doc->is_folder ? $doc->getFolderSize() : $doc->file_size;
                          $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                          $bytes = max($bytes, 0);
                          $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                          $pow = min($pow, count($units) - 1);
                          $bytes /= pow(1024, $pow);
                          echo round($bytes, 2) . ' ' . $units[$pow];
                      @endphp
                  </span>
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-1">
                    @php
                        $fileUrl = asset('storage/'.$doc->path);
                        $host = request()->getHost();
                        $isLocal = in_array($host, ['127.0.0.1', 'localhost', '::1']);
                        
                        // Construct file:// path for Office to open via SMB / Local Disk (allows direct save)
                        if ($isLocal) {
                            $officePath = 'file:///' . str_replace('\\', '/', Storage::disk('public')->path($doc->path));
                        } else {
                            $officePath = 'file://' . $host . '/budidaya_documents/' . $doc->filename;
                        }

                        $openLink = $fileUrl;
                        $openTarget = '_blank';
                        $iconOpen = 'bi-box-arrow-up-right';
                        $titleOpen = 'Buka File di Browser';

                        if(Str::contains($doc->mime_type, 'word') || $ext == 'docx' || $ext == 'doc') { 
                            $openLink = 'ms-word:ofe|u|' . $officePath;
                            $openTarget = '_self';
                            $titleOpen = 'Buka & Edit di MS Word';
                        }
                        elseif(Str::contains($doc->mime_type, 'excel') || Str::contains($doc->mime_type, 'spreadsheet') || in_array($ext, ['xlsx', 'xls'])) { 
                            $openLink = 'ms-excel:ofe|u|' . $officePath;
                            $openTarget = '_self';
                            $titleOpen = 'Buka & Edit di MS Excel';
                        }
                    @endphp
                    @if(!$doc->is_folder)
                    <a href="{{ $openLink }}" target="{{ $openTarget }}" class="action-btn-modern btn-hover-info" title="{{ $titleOpen }}">
                      <i class="bi {{ $iconOpen }}"></i>
                    </a>
                    
                    <a href="{{ url('data-budidaya/download/'.$doc->id) }}" class="action-btn-modern btn-hover-success" title="Download Dokumen"><i class="bi bi-cloud-arrow-down-fill"></i></a>
                    @else
                    <a href="{{ url('data-budidaya/folder-download/'.$doc->id) }}" class="action-btn-modern btn-hover-success" title="Download Folder (ZIP)"><i class="bi bi-file-earmark-zip-fill"></i></a>
                    @endif
                    <button type="button" class="action-btn-modern btn-hover-primary" title="Ganti Nama" data-bs-toggle="modal" data-bs-target="#renameModal" data-doc-id="{{ $doc->id }}" data-doc-name="{{ $doc->original_name }}">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    
                    <form action="{{ url('data-budidaya/'.$doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokumen ini secara permanen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn-modern btn-hover-danger" title="Hapus"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5">
                  <div class="text-center py-5 my-4">
                      <div class="d-inline-block p-4 rounded-circle bg-white shadow-sm mb-4">
                          <i class="bi bi-folder-x text-muted" style="font-size: 3rem;"></i>
                      </div>
                      <h4 class="fw-bold text-dark">Ruang Kerja Kosong</h4>
                      <p class="text-muted">Belum ada dokumen yang diunggah. Mulai tambahkan file baru.</p>
                      <button class="btn btn-gradient mt-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                          <i class="bi bi-cloud-upload me-2"></i> Upload File Pertama
                      </button>
                  </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Scripts -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    @if(session('open_upload_modal'))
      var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
      uploadModal.show();
    @endif
    
    // Auto-refresh mekanisme (Polling setiap 3 detik)
    setInterval(function() {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newTbody = doc.getElementById('document-table-body');
            const currentTbody = document.getElementById('document-table-body');
            
            if(newTbody && currentTbody) {
                // Hanya update DOM jika ada perubahan HTML (untuk menghemat resource)
                if(newTbody.innerHTML !== currentTbody.innerHTML) {
                    currentTbody.innerHTML = newTbody.innerHTML;
                }
            }
        })
        .catch(error => console.error('Gagal auto-refresh:', error));
    }, 3000);
    
    // Setup Rename Modal
    var renameModal = document.getElementById('renameModal');
    if(renameModal) {
        renameModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var docId = button.getAttribute('data-doc-id');
            var docName = button.getAttribute('data-doc-name');
            var form = renameModal.querySelector('form');
            form.action = "{{ url('data-budidaya/rename') }}/" + docId;
            var input = renameModal.querySelector('input[name="new_name"]');
            input.value = docName;
        });
    }

    // Setup Drag and Drop via Event Delegation
    var draggedRow = null;
    var glassCard = document.querySelector('.glass-card');

    document.addEventListener('dragstart', function(e) {
        var target = e.target.closest('.document-row');
        if (target) {
            draggedRow = target;
            e.dataTransfer.effectAllowed = 'move';
            setTimeout(() => target.style.opacity = '0.5', 0);
        }
    });

    document.addEventListener('dragend', function(e) {
        var target = e.target.closest('.document-row');
        if (target) {
            target.style.opacity = '1';
            document.querySelectorAll('.folder-row').forEach(f => f.classList.remove('drag-over'));
            draggedRow = null;
        }
    });

    document.addEventListener('dragover', function(e) {
        e.preventDefault(); // allow drop
        
        // Handle moving documents into folders
        var targetFolder = e.target.closest('.folder-row');
        if (targetFolder && draggedRow && draggedRow !== targetFolder) {
            targetFolder.classList.add('drag-over');
        }
        
        // Handle dragging OS files
        if (!draggedRow && e.dataTransfer.types.includes('Files')) {
            if (glassCard) glassCard.classList.add('drag-over-global');
        }
    });

    document.addEventListener('dragleave', function(e) {
        var targetFolder = e.target.closest('.folder-row');
        if (targetFolder) {
            targetFolder.classList.remove('drag-over');
        }
        
        // Remove global drag style if leaving the glass-card
        if (!draggedRow && glassCard && e.target === glassCard || glassCard.contains(e.target)) {
            // Check if relatedTarget is outside the glassCard
            if (e.relatedTarget && !glassCard.contains(e.relatedTarget)) {
                glassCard.classList.remove('drag-over-global');
            }
        }
    });

    document.addEventListener('drop', function(e) {
        e.preventDefault();
        
        if (glassCard) glassCard.classList.remove('drag-over-global');
        
        var targetFolder = e.target.closest('.folder-row');
        if (targetFolder) {
            targetFolder.classList.remove('drag-over');
        }
        
        // Case 1: Moving existing document to a folder
        if (draggedRow && targetFolder && draggedRow !== targetFolder) {
            var draggedId = draggedRow.getAttribute('data-doc-id');
            var targetFolderId = targetFolder.getAttribute('data-doc-id');
            
            fetch("{{ url('data-budidaya/move') }}/" + draggedId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ parent_id: targetFolderId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    draggedRow.style.display = 'none';
                } else {
                    alert(data.message || 'Gagal memindahkan file.');
                }
            })
            .catch(err => console.error(err));
            return;
        }
        
        // Case 2: Dropping files from OS
        if (!draggedRow && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            var files = e.dataTransfer.files;
            
            // Populate the upload modal form and submit it automatically
            var uploadForm = document.querySelector('#uploadModal form');
            var fileInput = uploadForm.querySelector('input[type="file"]');
            
            if (fileInput && files.length > 0) {
                // Use DataTransfer object to set file input files
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(files[0]); // Upload first file
                fileInput.files = dataTransfer.files;
                
                // Show modal temporarily or just submit
                // We'll just submit directly
                uploadForm.submit();
            }
        }
    });
  });
</script>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-glass border-0">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i> Upload File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ url('data-budidaya/upload') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body p-4">
            <input type="hidden" name="parent_id" value="{{ $currentFolder ? $currentFolder->id : '' }}">
            <div class="mb-3">
              <label class="form-label fw-bold text-dark small">Pilih Dokumen</label>
              <input class="form-control form-control-modern bg-white" type="file" name="file" required>
              <div class="form-text mt-2 opacity-75"><i class="bi bi-info-circle me-1"></i> Maks. 50MB (Word, Excel, PDF, dll)</div>
            </div>
          </div>
          <div class="modal-footer pb-4 px-4 border-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-gradient px-4 shadow-sm">Mulai Upload</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Folder Modal -->
<div class="modal fade" id="folderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-glass border-0">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-folder-plus text-warning me-2"></i> Buat Folder Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ url('data-budidaya/folder') }}" method="POST">
          @csrf
          <div class="modal-body p-4">
            <input type="hidden" name="parent_id" value="{{ $currentFolder ? $currentFolder->id : '' }}">
            <div class="mb-3">
              <label class="form-label fw-bold text-dark small">Nama Folder</label>
              <input class="form-control form-control-modern bg-white" type="text" name="folder_name" placeholder="Misal: Laporan 2026" required>
            </div>
          </div>
          <div class="modal-footer pb-4 px-4 border-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-gradient px-4 shadow-sm">Buat Folder</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Rename Modal -->
<div class="modal fade" id="renameModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-glass border-0">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i> Ganti Nama</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold text-dark small">Nama Baru</label>
              <input class="form-control form-control-modern bg-white" type="text" name="new_name" required>
            </div>
          </div>
          <div class="modal-footer pb-4 px-4 border-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-gradient px-4 shadow-sm">Simpan</button>
          </div>
      </form>
    </div>
  </div>
</div>

@endsection
