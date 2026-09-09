@extends('layouts.app')
@section('content')

<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

@push('styles')
  <link href="{{ asset('assets/css/data-file.css') }}" rel="stylesheet">
@endpush

<section id="data-File" class="data-File-bg pt-5">
    @if(!empty($isLocked))
    <!-- Lock Screen Modal Overlay -->
    <div class="sharefile-lock-overlay">
        <div class="lock-card">
            <div class="lock-icon-wrapper">
                <i class="bi bi-lock-fill"></i>
            </div>
            <h2 class="h5 fw-bold text-dark mb-1">Akses Berkas Terkunci</h2>
            <p class="text-muted small mb-4">
                Halaman ini dilindungi kata sandi. Silakan masukkan kata sandi untuk mengakses berkas dan folder.
            </p>

            @if(session('lock_error'))
            <div class="alert alert-danger py-2 px-3 rounded-3 small mb-3 border-0 d-flex align-items-center gap-2" style="background-color: #fee2e2; color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                <div class="text-start">{{ session('lock_error') }}</div>
            </div>
            @endif

            <form action="{{ url('/data-File/unlock') }}" method="POST">
                @csrf
                <div class="mb-3 text-start">
                    <label class="form-label small fw-semibold text-secondary">Kata Sandi Akses</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                            <i class="bi bi-key-fill"></i>
                        </span>
                        <input 
                            type="password" 
                            name="password" 
                            id="sharefileUnlockInput"
                            class="form-control rounded-end-3 py-2 border-start-0 pe-5" 
                            placeholder="Ketik kata sandi..." 
                            required 
                            autofocus>
                        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 text-muted pe-3 z-3" onclick="toggleUnlockPassword()" aria-label="Lihat Password">
                            <i class="bi bi-eye" id="unlockEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-unlock-submit w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
                    <i class="bi bi-unlock-fill"></i>
                    <span>Buka Akses Berkas</span>
                </button>
            </form>

            <div class="mt-3 pt-3 border-top text-center text-muted small">
                <a href="{{ url('/') }}" class="text-decoration-none text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda Utama
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="container py-4 {{ !empty($isLocked) ? 'sharefile-content-locked' : '' }}" data-aos="fade-up" data-aos-duration="800">

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
                        <h3 class="fw-bold mb-0 text-dark">Share File</h3>
                        <p class="text-muted mb-0 fs-6">Kelola dan pantau dokumen Anda</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @if(empty($isLocked) && session('sharefile_unlocked'))
                    <form action="{{ url('/data-File/lock') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-3 py-2 d-flex align-items-center gap-1 shadow-sm" title="Kunci Kembali Akses Berkas">
                            <i class="bi bi-lock-fill"></i> <span class="d-none d-sm-inline">Kunci Akses</span>
                        </button>
                    </form>
                    @endif

                    <!-- New Button -->
                    <div class="dropdown">
                        <button class="btn btn-gradient px-4 py-2 d-flex align-items-center justify-content-center shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-plus-lg me-2 fw-bold"></i> Baru
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end glass-dropdown mt-3 border-0 shadow">
                            <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#folderModal">
                                    <div class="bg-light rounded p-2 me-3"><i class="bi bi-folder-plus fs-5 text-warning"></i></div>
                                    <div>
                                        <div class="fw-bold">Buat Folder</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Buat folder baru</div>
                                    </div>
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <div class="bg-light rounded p-2 me-3"><i class="bi bi-cloud-arrow-up-fill fs-5 text-primary"></i></div>
                                    <div>
                                        <div class="fw-bold">Upload File</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Unggah dari komputer</div>
                                    </div>
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item py-2 px-3 d-flex align-items-center" href="{{ url('data-File/download-drive-bat') }}">
                                    <div class="bg-light rounded p-2 me-3"><i class="bi bi-hdd-network-fill fs-5 text-success"></i></div>
                                    <div>
                                        <div class="fw-bold">Hubungkan Drive Z:</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Download skrip auto-connect folder server</div>
                                    </div>
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar (Mirrors Admin Filter) -->
            <div class="filter-card p-3 mb-4 rounded-4 shadow-sm" style="background: rgba(255, 255, 255, 0.75); border: 1px solid rgba(255, 255, 255, 0.6); backdrop-filter: blur(8px);">
                <form action="{{ url('/data-File') }}" method="GET" class="row g-2 align-items-center">
                    @if(request('folder'))
                    <input type="hidden" name="folder" value="{{ request('folder') }}">
                    @endif

                    <!-- Search Input -->
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-3">
                                <i class="bi bi-search"></i>
                            </span>
                            <input 
                                type="text" 
                                name="q" 
                                class="form-control border-start-0 rounded-end-pill bg-white shadow-none" 
                                placeholder="Cari nama berkas, folder, atau pengunggah..." 
                                value="{{ request('q') }}">
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <select name="type" class="form-select rounded-pill bg-white shadow-none" onchange="this.form.submit()">
                            <option value="" {{ !request('type') ? 'selected' : '' }}>Semua Tipe File</option>
                            <option value="folder" {{ request('type') == 'folder' ? 'selected' : '' }}>Folder Direktori</option>
                            <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Dokumen (PDF / Word / Teks)</option>
                            <option value="spreadsheet" {{ request('type') == 'spreadsheet' ? 'selected' : '' }}>Lembar Kerja (Excel / CSV)</option>
                            <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Gambar (PNG / JPG / WEBP)</option>
                            <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>Arsip (ZIP / RAR)</option>
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <select name="sort" class="form-select rounded-pill bg-white shadow-none" onchange="this.form.submit()">
                            <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="original_name" {{ request('sort') == 'original_name' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="file_size" {{ request('sort') == 'file_size' ? 'selected' : '' }}>Ukuran Terbesar</option>
                        </select>
                    </div>

                    <!-- Submit & Reset -->
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill w-100 fw-semibold shadow-sm" style="background: var(--primary-gradient); border: none;">
                            <i class="bi bi-funnel-fill me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['q', 'type', 'sort']))
                            <a href="{{ url('/data-File') }}{{ request('folder') ? '?folder='.request('folder') : '' }}" class="btn btn-light bg-white border rounded-pill shadow-sm" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Section -->
            @if($currentFolder)
            <div class="d-flex align-items-center mb-3">
                <a href="{{ $currentFolder->parent_id ? url('data-File?folder='.$currentFolder->parent_id) : url('data-File') }}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm me-2 hover-primary folder-row" data-doc-id="{{ $currentFolder->parent_id ?? '' }}">
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
                        $ext = strtolower(pathinfo($doc->filename, PATHINFO_EXTENSION));
                        $isWord = Str::contains($doc->mime_type, 'word') || in_array($ext, ['docx', 'doc']);
                        $isExcel = Str::contains($doc->mime_type, 'excel') || Str::contains($doc->mime_type, 'spreadsheet') || in_array($ext, ['xlsx', 'xls', 'csv']);

                        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $doc->path)));
                        $fileStorageUrl = asset('storage/'.$encodedPath);
                        $smbHost = parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost();
                        if ($smbHost === '127.0.0.1' || $smbHost === 'localhost') {
                            $smbHost = '192.168.110.121';
                        }
                        $shareName = env('SMB_SHARE_NAME', 'Share-Budidaya');
                        $smbPath = '\\\\' . $smbHost . '\\' . $shareName . '\\' . str_replace('/', '\\', $doc->path);

                        $webdavUrl = url('webdav/' . $encodedPath);

                        if ($isWord) {
                            $desktopOfficeUrl = 'ms-word:ofe|u|' . $webdavUrl;
                        } elseif ($isExcel) {
                            $desktopOfficeUrl = 'ms-excel:ofe|u|' . $webdavUrl;
                        } else {
                            $desktopOfficeUrl = null;
                        }

                        if($doc->is_folder) {
                            $iconClass = 'bi-folder-fill text-warning';
                        } else {
                            $iconClass = 'bi-file-earmark-fill icon-gradient-default';
                            if(Str::contains($doc->mime_type, 'pdf')) $iconClass = 'bi-file-earmark-pdf-fill icon-gradient-pdf';
                            elseif(Str::contains($doc->mime_type, 'image')) $iconClass = 'bi-file-earmark-image-fill icon-gradient-image';
                            elseif($isWord) $iconClass = 'bi-file-earmark-word-fill icon-gradient-word';
                            elseif($isExcel) $iconClass = 'bi-file-earmark-excel-fill icon-gradient-excel';
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
                                        <a href="{{ url('data-File?folder='.$doc->id) }}" class="text-decoration-none fw-semibold text-dark hover-primary">{{ $doc->original_name }}</a>
                                    @elseif($desktopOfficeUrl)
                                        <a href="{{ $desktopOfficeUrl }}" class="text-decoration-none fw-semibold text-dark hover-primary" title="Klik untuk Buka & Edit di Aplikasi Office Desktop (Auto-Save ke Server)">{{ $doc->original_name }}</a>
                                    @elseif(Str::contains($doc->mime_type, 'pdf') || Str::contains($doc->mime_type, 'image') || Str::contains($doc->mime_type, 'text'))
                                        <a href="{{ $fileStorageUrl }}" target="_blank" class="text-decoration-none fw-semibold text-dark hover-primary" title="Buka di Tab Baru">{{ $doc->original_name }}</a>
                                    @else
                                        <a href="{{ url('data-File/download/'.$doc->id) }}" class="text-decoration-none fw-semibold text-dark hover-primary" title="Download File">{{ $doc->original_name }}</a>
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
                                    @if($doc->is_folder)
                                        <a href="{{ url('data-File?folder='.$doc->id) }}" class="action-btn-modern btn-hover-info" title="Buka Folder"><i class="bi bi-folder2-open"></i></a>
                                        <a href="{{ url('data-File/folder-download/'.$doc->id) }}" class="action-btn-modern btn-hover-success" title="Download Folder (ZIP)"><i class="bi bi-file-earmark-zip-fill"></i></a>
                                    @else
                                        @if($isWord)
                                            <!-- Buka di Aplikasi Word Desktop (WebDAV - Auto-save ke Server) -->
                                            <a href="{{ $desktopOfficeUrl }}" class="action-btn-modern btn-hover-primary" title="Edit di Microsoft Word Desktop (Auto-Save ke Server)"><i class="bi bi-file-earmark-word-fill text-primary"></i></a>
                                        @elseif($isExcel)
                                            <!-- Buka di Aplikasi Excel Desktop (WebDAV - Auto-save ke Server) -->
                                            <a href="{{ $desktopOfficeUrl }}" class="action-btn-modern btn-hover-success" title="Edit di Microsoft Excel Desktop (Auto-Save ke Server)"><i class="bi bi-file-earmark-excel-fill text-success"></i></a>
                                        @elseif(Str::contains($doc->mime_type, 'pdf') || Str::contains($doc->mime_type, 'image') || Str::contains($doc->mime_type, 'text'))
                                            <a href="{{ $fileStorageUrl }}" target="_blank" class="action-btn-modern btn-hover-info" title="Lihat di Browser"><i class="bi bi-box-arrow-up-right"></i></a>
                                        @endif

                                        <!-- Download Langsung -->
                                        <a href="{{ url('data-File/download/'.$doc->id) }}" class="action-btn-modern btn-hover-secondary" title="Download File"><i class="bi bi-cloud-arrow-down-fill"></i></a>
                                    @endif

                                    <button type="button" class="action-btn-modern btn-hover-primary" title="Ganti Nama" data-bs-toggle="modal" data-bs-target="#renameModal" data-doc-id="{{ $doc->id }}" data-doc-name="{{ $doc->original_name }}">
                                        <i class="bi bi-input-cursor-text"></i>
                                    </button>

                                    <form action="{{ url('data-File/'.$doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pindahkan {{ $doc->is_folder ? 'folder ini dan seluruh isinya' : 'berkas ini' }} ke Tempat Sampah (Recycle Bin)?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn-modern btn-hover-danger" title="Pindahkan ke Tempat Sampah"><i class="bi bi-trash3-fill"></i></button>
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

            <!-- Pagination Section -->
            @if($documents->hasPages())
            <div class="pt-3 mt-2 border-top" style="border-color: rgba(0,0,0,0.06) !important;">
                {{ $documents->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Floating Upload Progress Widget -->
    <div id="uploadProgressWidget" class="upload-progress-widget p-3 d-none">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm text-primary me-2" id="uploadSpinner" role="status"></div>
                <span class="fw-bold text-dark small" id="uploadWidgetTitle">Mengunggah file & folder...</span>
            </div>
            <span class="badge bg-primary rounded-pill small px-2 py-1" id="uploadWidgetCount">0 / 0</span>
        </div>
        <div class="progress mb-2" style="height: 8px; border-radius: 4px; background: #e2e8f0;">
            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background: var(--primary-gradient);"></div>
        </div>
        <div class="d-flex justify-content-between align-items-center small text-muted">
            <span class="text-truncate me-2 font-monospace" id="uploadWidgetFilename" style="max-width: 250px; font-size: 0.78rem;">Menyiapkan...</span>
            <span class="fw-semibold" id="uploadWidgetPercent" style="font-size: 0.78rem;">0%</span>
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

                    if (newTbody && currentTbody) {
                        // Hanya update DOM jika ada perubahan HTML (untuk menghemat resource)
                        if (newTbody.innerHTML !== currentTbody.innerHTML) {
                            currentTbody.innerHTML = newTbody.innerHTML;
                        }
                    }
                })
                .catch(error => console.error('Gagal auto-refresh:', error));
        }, 3000);

        // Setup Rename Modal
        var renameModal = document.getElementById('renameModal');
        if (renameModal) {
            renameModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var docId = button.getAttribute('data-doc-id');
                var docName = button.getAttribute('data-doc-name');
                var form = renameModal.querySelector('form');
                form.action = "{{ url('data-File/rename') }}/" + docId;
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

        let dragCounter = 0;

        window.addEventListener('dragenter', function(e) {
            if (!draggedRow && e.dataTransfer && Array.from(e.dataTransfer.types).includes('Files')) {
                dragCounter++;
                if (glassCard) glassCard.classList.add('drag-over-global');
            }
        });

        window.addEventListener('dragleave', function(e) {
            if (!draggedRow) {
                dragCounter--;
                if (dragCounter <= 0) {
                    dragCounter = 0;
                    if (glassCard) glassCard.classList.remove('drag-over-global');
                }
            }
        });

        document.addEventListener('dragover', function(e) {
            e.preventDefault(); // allow drop

            // Handle moving documents into folders
            var targetFolder = e.target.closest('.folder-row');
            if (targetFolder && draggedRow && draggedRow !== targetFolder) {
                targetFolder.classList.add('drag-over');
            }

            if (!draggedRow && e.dataTransfer && Array.from(e.dataTransfer.types).includes('Files')) {
                e.dataTransfer.dropEffect = 'copy';
            }
        });

        document.addEventListener('drop', async function(e) {
            e.preventDefault();
            dragCounter = 0;

            if (glassCard) glassCard.classList.remove('drag-over-global');

            var targetFolder = e.target.closest('.folder-row');
            if (targetFolder) {
                targetFolder.classList.remove('drag-over');
            }

            // Case 1: Moving existing document into another folder inside web UI
            if (draggedRow && targetFolder && draggedRow !== targetFolder) {
                var draggedId = draggedRow.getAttribute('data-doc-id');
                var targetFolderId = targetFolder.getAttribute('data-doc-id');

                fetch("{{ url('data-File/move') }}/" + draggedId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            parent_id: targetFolderId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            draggedRow.style.display = 'none';
                        } else {
                            alert(data.message || 'Gagal memindahkan file.');
                        }
                    })
                    .catch(err => console.error(err));
                return;
            }

            // Case 2: Dropping files or folders from OS
            if (!draggedRow && e.dataTransfer) {
                const files = await getFilesFromDataTransfer(e.dataTransfer);
                if (files && files.length > 0) {
                    startBatchUpload(files);
                }
            }
        });

        // Helper: Extract all files and folder hierarchy from DataTransfer
        async function getFilesFromDataTransfer(dataTransfer) {
            const files = [];
            const items = dataTransfer.items;

            if (items && items.length > 0 && items[0].webkitGetAsEntry) {
                const promises = [];
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    if (item.kind === 'file') {
                        const entry = item.webkitGetAsEntry();
                        if (entry) {
                            promises.push(traverseEntry(entry, ''));
                        }
                    }
                }
                const results = await Promise.all(promises);
                results.forEach(arr => files.push(...arr));
            } else if (dataTransfer.files && dataTransfer.files.length > 0) {
                for (let i = 0; i < dataTransfer.files.length; i++) {
                    const f = dataTransfer.files[i];
                    f.relativePath = f.name;
                    files.push(f);
                }
            }
            return files;
        }

        // Recursive directory traversal using FileSystemEntry API
        function traverseEntry(entry, path) {
            path = path || '';
            if (entry.isFile) {
                return new Promise((resolve) => {
                    entry.file(file => {
                        file.relativePath = path + file.name;
                        resolve([file]);
                    }, () => resolve([]));
                });
            } else if (entry.isDirectory) {
                return new Promise((resolve) => {
                    const dirReader = entry.createReader();
                    const entries = [];

                    function readBatch() {
                        dirReader.readEntries(async (batch) => {
                            if (batch.length === 0) {
                                const subPromises = entries.map(child => traverseEntry(child, path + entry.name + '/'));
                                const subResults = await Promise.all(subPromises);
                                const subFiles = [];
                                subResults.forEach(r => subFiles.push(...r));
                                resolve(subFiles);
                            } else {
                                entries.push(...batch);
                                readBatch();
                            }
                        }, () => resolve([]));
                    }
                    readBatch();
                });
            }
            return Promise.resolve([]);
        }

        // Batch upload runner with real-time floating progress widget
        async function startBatchUpload(files) {
            if (!files || files.length === 0) return;

            // Batas maksimal 200MB per file (209,715,200 bytes)
            const MAX_SIZE = 209715200;
            const validFiles = [];
            const oversizedFiles = [];

            files.forEach(f => {
                if (f.size > MAX_SIZE) {
                    oversizedFiles.push(f.name + ' (' + (f.size / (1024 * 1024)).toFixed(1) + 'MB)');
                } else {
                    validFiles.push(f);
                }
            });

            if (oversizedFiles.length > 0) {
                alert('File berikut melebihi batas maksimal 200MB dan dilewati:\n- ' + oversizedFiles.join('\n- '));
            }

            if (validFiles.length === 0) return;

            // Close upload modal if open
            var uploadModalEl = document.getElementById('uploadModal');
            if (uploadModalEl) {
                var modalInstance = bootstrap.Modal.getInstance(uploadModalEl);
                if (modalInstance) modalInstance.hide();
            }

            // Show floating upload progress widget
            const widget = document.getElementById('uploadProgressWidget');
            const widgetTitle = document.getElementById('uploadWidgetTitle');
            const widgetCount = document.getElementById('uploadWidgetCount');
            const widgetFilename = document.getElementById('uploadWidgetFilename');
            const widgetPercent = document.getElementById('uploadWidgetPercent');
            const progressBar = document.getElementById('uploadProgressBar');
            const spinner = document.getElementById('uploadSpinner');

            widget.classList.remove('d-none');
            widgetTitle.textContent = 'Mengunggah file & folder...';
            spinner.classList.remove('d-none');

            const total = validFiles.length;
            let completed = 0;
            let failed = 0;

            for (let i = 0; i < validFiles.length; i++) {
                const file = validFiles[i];
                const relPath = file.relativePath || file.webkitRelativePath || file.name;

                widgetCount.textContent = `${i + 1} / ${total}`;
                widgetFilename.textContent = relPath;
                const baseProgress = (i / total) * 100;
                progressBar.style.width = `${Math.round(baseProgress)}%`;
                widgetPercent.textContent = `${Math.round(baseProgress)}%`;

                try {
                    await uploadSingleFileWithProgress(file, relPath, (percent) => {
                        const overall = Math.round(((i + (percent / 100)) / total) * 100);
                        progressBar.style.width = `${overall}%`;
                        widgetPercent.textContent = `${overall}%`;
                    });
                    completed++;
                } catch (err) {
                    console.error('Gagal upload file:', relPath, err);
                    failed++;
                }
            }

            progressBar.style.width = '100%';
            widgetPercent.textContent = '100%';
            spinner.classList.add('d-none');

            if (failed === 0) {
                widgetTitle.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Selesai!';
                widgetFilename.textContent = `${completed} file & folder berhasil diunggah.`;
            } else {
                widgetTitle.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Selesai Sebagian';
                widgetFilename.textContent = `${completed} berhasil, ${failed} gagal.`;
            }

            setTimeout(() => {
                window.location.reload();
            }, 1200);
        }

        // Single file upload via XMLHttpRequest for progress tracking
        function uploadSingleFileWithProgress(file, relPath, onProgress) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                const formData = new FormData();
                const parentId = '{{ $currentFolder ? $currentFolder->id : '' }}';

                formData.append('file', file);
                formData.append('relative_path', relPath);
                if (parentId) formData.append('parent_id', parentId);
                formData.append('_token', '{{ csrf_token() }}');

                xhr.open('POST', '{{ url("data-File/upload") }}', true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable && onProgress) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        onProgress(percent);
                    }
                };

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.success) {
                                resolve(res);
                            } else {
                                reject(new Error(res.message || 'Gagal'));
                            }
                        } catch (e) {
                            resolve(xhr.responseText);
                        }
                    } else {
                        reject(new Error('HTTP ' + xhr.status));
                    }
                };

                xhr.onerror = function() {
                    reject(new Error('Koneksi terputus'));
                };

                xhr.send(formData);
            });
        }

        // Setup File Inputs inside Modal
        var modalFileInput = document.getElementById('modalFileInput');
        if (modalFileInput) {
            modalFileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    const files = Array.from(this.files).map(f => {
                        f.relativePath = f.name;
                        return f;
                    });
                    startBatchUpload(files);
                }
            });
        }

        var modalFolderInput = document.getElementById('modalFolderInput');
        if (modalFolderInput) {
            modalFolderInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    const files = Array.from(this.files).map(f => {
                        f.relativePath = f.webkitRelativePath || f.name;
                        return f;
                    });
                    startBatchUpload(files);
                }
            });
        }
    });
</script>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-glass border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i> Upload File & Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Nav Tabs for File vs Folder -->
                <ul class="nav nav-pills nav-fill mb-3" id="uploadTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill py-2 fw-semibold" id="files-tab" data-bs-toggle="tab" data-bs-target="#files-tab-pane" type="button" role="tab">
                            <i class="bi bi-files me-1"></i> Banyak File
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-2 fw-semibold" id="folder-tab" data-bs-toggle="tab" data-bs-target="#folder-tab-pane" type="button" role="tab">
                            <i class="bi bi-folder-symlink me-1"></i> Folder & Isinya
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="uploadTabContent">
                    <!-- Tab File -->
                    <div class="tab-pane fade show active" id="files-tab-pane" role="tabpanel">
                        <div class="modal-dropzone mb-3" id="modalFileDropzone" onclick="document.getElementById('modalFileInput').click()">
                            <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 2.5rem;"></i>
                            <div class="fw-bold text-dark mt-2">Pilih atau Tarik File ke Sini</div>
                            <div class="small text-muted">Bisa memilih sekaligus banyak file (Maks. 200MB/file)</div>
                            <input class="d-none" type="file" id="modalFileInput" multiple>
                        </div>
                    </div>

                    <!-- Tab Folder -->
                    <div class="tab-pane fade" id="folder-tab-pane" role="tabpanel">
                        <div class="modal-dropzone mb-3" id="modalFolderDropzone" onclick="document.getElementById('modalFolderInput').click()">
                            <i class="bi bi-folder-fill text-warning" style="font-size: 2.5rem;"></i>
                            <div class="fw-bold text-dark mt-2">Pilih Folder untuk Diunggah</div>
                            <div class="small text-muted">Semua file dan subfolder di dalamnya akan otomatis diunggah</div>
                            <input class="d-none" type="file" id="modalFolderInput" webkitdirectory directory multiple>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between p-2 px-3 bg-light rounded-3 small text-muted">
                    <span><i class="bi bi-shield-check text-success me-1"></i> Batas ukuran per file:</span>
                    <span class="badge bg-primary rounded-pill fw-bold">Maks. 200 MB</span>
                </div>
            </div>
            <div class="modal-footer pb-4 px-4 border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
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
            <form action="{{ url('data-File/folder') }}" method="POST">
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

<!-- Network Guide Modal -->
<div class="modal fade" id="networkGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-glass border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-hdd-network-fill text-primary me-2 fs-4"></i>
                    Panduan: Edit File di Komputer Lain & Tetap Tersimpan di Server
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Method 1 -->
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-4 shadow-sm h-100 border border-primary border-opacity-25">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-primary rounded-pill me-2">Metode 1</span>
                                <h6 class="fw-bold mb-0 text-dark">Buka Langsung di Word / Excel</h6>
                            </div>
                            <p class="text-muted small mb-2">
                                Klik nama file atau ikon aplikasi Office (Word/Excel) pada tabel dokumen.
                            </p>
                            <ul class="small text-muted ps-3 mb-0">
                                <li>Dokumen akan otomatis terbuka di aplikasi Microsoft Word / Excel di komputer Anda.</li>
                                <li>Saat Anda menekan <strong>Ctrl + S (Simpan)</strong>, perubahan otomatis tersimpan langsung ke server via protokol WebDAV.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Method 2 -->
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-4 shadow-sm h-100 border border-success border-opacity-25">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success rounded-pill me-2">Metode 2</span>
                                <h6 class="fw-bold mb-0 text-dark">Drive Jaringan Z: (Paling Praktis)</h6>
                            </div>
                            <p class="text-muted small mb-2">
                                Hubungkan folder server sebagai Drive <strong>Z:\</strong> di File Explorer komputer Anda.
                            </p>
                            <a href="{{ url('data-File/download-drive-bat') }}" class="btn btn-sm btn-success rounded-pill w-100 mb-2 shadow-sm">
                                <i class="bi bi-download me-1"></i> Download Skrip Hubungkan_Drive_Z.bat
                            </a>
                            <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                <em>Klik dua kali file .bat yang diunduh. Folder server akan langsung muncul sebagai Drive Z:\ di File Explorer laptop Anda!</em>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-4 px-4 border-0">
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleUnlockPassword() {
    const input = document.getElementById('sharefileUnlockInput');
    const icon = document.getElementById('unlockEyeIcon');
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

@if(empty($isLocked) && session('sharefile_unlocked') && (!Auth::check() || Auth::user()->role !== 'admin'))
(function() {
    // 5 menit tanpa aktivitas = 300.000 ms
    const IDLE_TIMEOUT_MS = 5 * 60 * 1000;
    const PING_INTERVAL_MS = 60 * 1000; // Kirim heartbeat per 60 detik bila ada aktivitas pengguna
    const LOCK_URL = "{{ url('/data-File/lock') }}";
    const PING_URL = "{{ url('/data-File/ping-activity') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    let lastActivityTime = Date.now();
    let isLockedTriggered = false;
    let hasInteractedSinceLastPing = false;

    function resetActivity() {
        if (isLockedTriggered) return;
        lastActivityTime = Date.now();
        hasInteractedSinceLastPing = true;
    }

    // Pantau berbagai aktivitas pengguna
    const activityEvents = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];
    activityEvents.forEach(function(evt) {
        window.addEventListener(evt, resetActivity, { passive: true });
    });

    // Pengecekan timer setiap 1 detik
    const checkInterval = setInterval(function() {
        if (isLockedTriggered) return;

        const now = Date.now();
        if (now - lastActivityTime >= IDLE_TIMEOUT_MS) {
            triggerAutoLock();
        }
    }, 1000);

    // Kirim heartbeat ping ke server jika ada aktivitas agar sesi backend tidak expire mendadak
    const pingInterval = setInterval(function() {
        if (isLockedTriggered) return;
        if (hasInteractedSinceLastPing) {
            hasInteractedSinceLastPing = false;
            fetch(PING_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.locked) {
                    triggerAutoLock();
                }
            })
            .catch(function() {});
        }
    }, PING_INTERVAL_MS);

    // Tangani saat tab kembali aktif setelah diminimize / laptop sleep
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && !isLockedTriggered) {
            if (Date.now() - lastActivityTime >= IDLE_TIMEOUT_MS) {
                triggerAutoLock();
            }
        }
    });

    function triggerAutoLock() {
        if (isLockedTriggered) return;
        isLockedTriggered = true;
        clearInterval(checkInterval);
        clearInterval(pingInterval);

        // Submit form POST /data-File/lock dengan reason=idle
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = LOCK_URL;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = CSRF_TOKEN;
        form.appendChild(csrfInput);

        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = 'idle';
        form.appendChild(reasonInput);

        document.body.appendChild(form);
        form.submit();
    }
})();
@endif
</script>

@endsection