@extends('layouts.app')
@section('content')

<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

@push('styles')
  <link href="{{ asset('assets/css/data-file.css') }}?v={{ file_exists(public_path('assets/css/data-file.css')) ? filemtime(public_path('assets/css/data-file.css')) : time() }}" rel="stylesheet">
  <style>
    /* ==========================================================================
       Floating Batch Action Bar & Mobile Responsive Styling
       ========================================================================== */
    .batch-action-bar {
        position: fixed !important;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        z-index: 1080 !important;
        opacity: 0;
        pointer-events: none;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
        max-width: 95vw;
        display: none;
    }

    .batch-action-bar.show {
        transform: translateX(-50%) translateY(0) !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .batch-bar-inner {
        background: rgba(15, 23, 42, 0.94) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        border-radius: 50px !important;
        padding: 8px 14px 8px 12px !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35), 0 2px 10px rgba(0, 0, 0, 0.2) !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        color: #ffffff !important;
    }

    .batch-counter-badge {
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%) !important;
        color: #ffffff !important;
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        padding: 6px 12px !important;
        border-radius: 30px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4) !important;
        white-space: nowrap !important;
    }

    .batch-drag-hint {
        font-size: 0.78rem !important;
        color: #cbd5e1 !important;
        border-left: 1px solid rgba(255, 255, 255, 0.18) !important;
        padding-left: 10px !important;
        white-space: nowrap !important;
    }

    .batch-actions-btns {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin-left: auto !important;
    }

    .batch-btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 30px !important;
        padding: 7px 16px !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 3px 10px rgba(239, 68, 68, 0.35) !important;
        cursor: pointer !important;
        white-space: nowrap !important;
    }

    .batch-btn-delete:hover,
    .batch-btn-delete:active {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 5px 14px rgba(239, 68, 68, 0.5) !important;
        color: #ffffff !important;
    }

    .batch-btn-cancel {
        background: rgba(255, 255, 255, 0.12) !important;
        color: #e2e8f0 !important;
        border: none !important;
        border-radius: 30px !important;
        padding: 7px 14px !important;
        font-size: 0.82rem !important;
        font-weight: 500 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        white-space: nowrap !important;
    }

    .batch-btn-cancel:hover,
    .batch-btn-cancel:active {
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
    }

    /* Modern Checkbox Column */
    .th-checkbox,
    .td-checkbox {
        width: 44px;
        min-width: 44px;
        max-width: 48px;
        text-align: center;
        vertical-align: middle !important;
        padding-left: 12px !important;
        padding-right: 6px !important;
    }

    .custom-checkbox-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
        user-select: none;
        width: 32px;
        height: 32px;
    }

    .form-check-input-modern {
        width: 19px;
        height: 19px;
        margin: 0;
        cursor: pointer;
        background-color: #fff;
        border: 1.8px solid #cbd5e1;
        border-radius: 6px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .form-check-input-modern:hover {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .form-check-input-modern:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.35);
    }

    .form-check-input-modern:indeterminate {
        background-color: #6366f1;
        border-color: #6366f1;
    }

    /* Selected Table Row Highlighting without column shift */
    .document-row.table-row-selected {
        background-color: rgba(99, 102, 241, 0.08) !important;
    }

    .document-row.table-row-selected td {
        background-color: transparent !important;
    }

    .document-row.table-row-selected td:first-child {
        box-shadow: inset 4px 0 0 #4f46e5 !important;
    }

    /* Mobile Specific Optimizations */
    @media (max-width: 767.98px) {
        .batch-action-bar {
            bottom: 16px !important;
            width: calc(100% - 24px) !important;
            max-width: 460px !important;
            left: 50% !important;
            transform: translateX(-50%) translateY(100px) !important;
        }

        .batch-action-bar.show {
            transform: translateX(-50%) translateY(0) !important;
        }

        .batch-bar-inner {
            width: 100% !important;
            padding: 7px 10px !important;
            justify-content: space-between !important;
            gap: 6px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
        }

        .batch-counter-badge {
            font-size: 0.75rem !important;
            padding: 5px 10px !important;
        }

        .batch-btn-delete {
            padding: 6px 12px !important;
            font-size: 0.78rem !important;
        }

        .batch-btn-cancel {
            padding: 6px 10px !important;
            font-size: 0.78rem !important;
        }

        .th-checkbox,
        .td-checkbox {
            width: 36px !important;
            min-width: 36px !important;
            max-width: 40px !important;
            padding-left: 8px !important;
            padding-right: 2px !important;
        }

        .custom-checkbox-wrapper {
            width: 28px !important;
            height: 28px !important;
        }

        .form-check-input-modern {
            width: 18px !important;
            height: 18px !important;
        }

        .file-title-container {
            max-width: calc(100vw - 165px) !important;
        }
    }
  </style>
@endpush

<section id="data-File" class="data-File-bg py-2 py-sm-3 py-md-4">
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

    <div class="container-fluid px-2 px-sm-3 px-md-4 py-2 py-md-3 {{ !empty($isLocked) ? 'sharefile-content-locked' : '' }}" style="max-width: 1750px;" data-aos="fade-up" data-aos-duration="800">

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
        <div class="glass-card p-3 p-sm-4 p-md-4 p-lg-5">

            <!-- Top Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 mb-md-4 gap-3 w-100">

                <!-- Brand / Title & Stats -->
                <div class="flex-shrink-0">
                    <div class="d-flex align-items-center">
                        <div class="header-icon bg-white rounded-circle p-2 p-sm-3 shadow-sm me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.15) !important;">
                            <i class="bi bi-cloud-check-fill fs-2 text-gradient"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h3 class="fw-bold mb-0 text-dark header-title">Share File</h3>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small" style="font-size: 0.72rem;">
                                    <i class="bi bi-files me-1"></i> {{ $documents->total() }} Item
                                </span>
                                @if(empty($isLocked) && session('sharefile_unlocked'))
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small" style="font-size: 0.72rem;">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Auto-Lock 5 Menit
                                </span>
                                @endif
                            </div>
                            <p class="text-muted mb-0 small header-subtitle">Kelola, unggah, dan pantau berkas Anda secara terpusat di server</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Buttons (Aligned to the Right) -->
                <div class="header-actions d-flex flex-wrap gap-2 align-items-center justify-content-start justify-content-md-end ms-md-auto">
                    <!-- Upload File Button -->
                    <button type="button" class="btn-action-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
                        <span>Upload File</span>
                    </button>

                    <!-- Buat Folder Button -->
                    <button type="button" class="btn-action-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#folderModal">
                        <i class="bi bi-folder-plus text-warning fs-5"></i>
                        <span>Folder Baru</span>
                    </button>

                    <!-- More Options Dropdown -->
                    <div class="dropdown">
                        <button class="btn-action-secondary shadow-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear-fill text-muted"></i>
                            <span>Lainnya</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end glass-dropdown mt-2 border-0 shadow">
                            <li>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="{{ url('data-File/download-drive-bat') }}">
                                    <div class="bg-success-subtle text-success rounded p-2 me-3"><i class="bi bi-hdd-network-fill fs-5"></i></div>
                                    <div>
                                        <div class="fw-bold">Hubungkan Drive Z:</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Akses folder via File Explorer Windows</div>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#networkGuideModal">
                                    <div class="bg-primary-subtle text-primary rounded p-2 me-3"><i class="bi bi-info-circle-fill fs-5"></i></div>
                                    <div>
                                        <div class="fw-bold">Panduan Edit Office</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Cara auto-save Word & Excel ke server</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                    @if(empty($isLocked) && session('sharefile_unlocked'))
                    <form action="{{ url('/data-File/lock') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-3 py-2 d-flex align-items-center justify-content-center gap-1 shadow-sm small fw-semibold" title="Kunci Kembali Akses Berkas">
                            <i class="bi bi-lock-fill"></i> <span>Kunci Akses</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Breadcrumb Navigation Bar -->
            <div class="breadcrumb-bar d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3 mb-md-4">
                <nav aria-label="breadcrumb" class="overflow-x-auto w-100 py-1" style="scrollbar-width: thin;">
                    <ul class="breadcrumb-modern flex-nowrap">
                        <li>
                            <a href="{{ url('/data-File') }}" class="breadcrumb-item-link {{ !$currentFolder ? 'fw-bold text-primary' : '' }}">
                                <i class="bi bi-house-door-fill text-primary"></i>
                                <span>Beranda</span>
                            </a>
                        </li>
                        @if($currentFolder)
                            @php
                                $breadcrumbs = [];
                                $temp = $currentFolder;
                                while ($temp) {
                                    array_unshift($breadcrumbs, $temp);
                                    $temp = $temp->parent;
                                }
                            @endphp
                            @foreach($breadcrumbs as $crumb)
                                <li class="breadcrumb-separator"><i class="bi bi-chevron-right"></i></li>
                                <li>
                                    @if($loop->last)
                                        <span class="breadcrumb-item-link fw-bold text-dark text-nowrap" style="background: rgba(0,0,0,0.05);">
                                            <i class="bi bi-folder2-open text-warning"></i>
                                            <span>{{ $crumb->original_name }}</span>
                                        </span>
                                    @else
                                        <a href="{{ url('data-File?folder='.$crumb->id) }}" class="breadcrumb-item-link text-nowrap">
                                            <i class="bi bi-folder text-muted"></i>
                                            <span>{{ $crumb->original_name }}</span>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </nav>

                @if($currentFolder)
                <a href="{{ $currentFolder->parent_id ? url('data-File?folder='.$currentFolder->parent_id) : url('data-File') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-secondary hover-primary d-inline-flex align-items-center justify-content-center gap-1 shadow-sm text-nowrap flex-shrink-0 align-self-start align-self-sm-auto">
                    <i class="bi bi-arrow-left"></i> Naik Satu Level
                </a>
                @endif
            </div>

            <!-- Unified Card: Filter Bar + Table Section -->
            <div class="unified-table-card rounded-4 shadow-sm overflow-hidden mb-4">
                <!-- Filter & Search Bar Header -->
                <div class="table-filter-header p-3 p-md-4">
                    <form action="{{ url('/data-File') }}" method="GET" class="row g-2 align-items-center">
                        @if(request('folder'))
                        <input type="hidden" name="folder" value="{{ request('folder') }}">
                        @endif

                        <!-- Search Input -->
                        <div class="col-12 col-lg-5">
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
                        <div class="col-6 col-sm-6 col-lg-3">
                            <select name="type" class="form-select rounded-pill bg-white shadow-none text-truncate" onchange="this.form.submit()">
                                <option value="" {{ !request('type') ? 'selected' : '' }}>Semua Tipe</option>
                                <option value="folder" {{ request('type') == 'folder' ? 'selected' : '' }}>Folder Direktori</option>
                                <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Dokumen (PDF/Word)</option>
                                <option value="spreadsheet" {{ request('type') == 'spreadsheet' ? 'selected' : '' }}>Excel / CSV</option>
                                <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Gambar</option>
                                <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>Arsip (ZIP/RAR)</option>
                            </select>
                        </div>

                        <!-- Sort Filter -->
                        <div class="col-6 col-sm-6 col-lg-2">
                            <select name="sort" class="form-select rounded-pill bg-white shadow-none text-truncate" onchange="this.form.submit()">
                                <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                                <option value="original_name" {{ request('sort') == 'original_name' ? 'selected' : '' }}>Nama (A-Z)</option>
                                <option value="file_size" {{ request('sort') == 'file_size' ? 'selected' : '' }}>Ukuran</option>
                            </select>
                        </div>

                        <!-- Submit & Reset -->
                        <div class="col-12 col-lg-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 fw-semibold shadow-sm" style="background: var(--primary-gradient); border: none;">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['q', 'type', 'sort']))
                                <a href="{{ url('/data-File') }}{{ request('folder') ? '?folder='.request('folder') : '' }}" class="btn btn-light bg-white border rounded-pill shadow-sm px-3" title="Reset Filter">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                <table class="table modern-table w-100">
                    <thead>
                        <tr>
                            <th scope="col" class="th-checkbox">
                                <div class="custom-checkbox-wrapper" title="Pilih Semua">
                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input-modern" aria-label="Pilih Semua Berkas dan Folder">
                                </div>
                            </th>
                            <th scope="col"><i class="bi bi-file-earmark-text me-1"></i> Nama Berkas / Folder</th>
                            <th scope="col" class="d-none d-md-table-cell"><i class="bi bi-person me-1"></i> Pengunggah</th>
                            <th scope="col" class="d-none d-lg-table-cell"><i class="bi bi-calendar3 me-1"></i> Dimodifikasi</th>
                            <th scope="col" class="d-none d-sm-table-cell"><i class="bi bi-hdd me-1"></i> Ukuran</th>
                            <th scope="col" class="text-end"><i class="bi bi-sliders me-1"></i> Aksi</th>
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
                            $typeClass = 'folder';
                            $iconClass = 'bi-folder-fill';
                        } else {
                            if(Str::contains($doc->mime_type, 'pdf') || $ext === 'pdf') {
                                $typeClass = 'pdf';
                                $iconClass = 'bi-file-earmark-pdf-fill';
                            } elseif(Str::contains($doc->mime_type, 'image') || in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'])) {
                                $typeClass = 'image';
                                $iconClass = 'bi-file-earmark-image-fill';
                            } elseif($isWord) {
                                $typeClass = 'word';
                                $iconClass = 'bi-file-earmark-word-fill';
                            } elseif($isExcel) {
                                $typeClass = 'excel';
                                $iconClass = 'bi-file-earmark-excel-fill';
                            } elseif(in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
                                $typeClass = 'archive';
                                $iconClass = 'bi-file-earmark-zip-fill';
                            } else {
                                $typeClass = 'default';
                                $iconClass = 'bi-file-earmark-fill';
                            }
                        }

                        $bytes = $doc->is_folder ? $doc->getFolderSize() : $doc->file_size;
                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                        $bytes = max($bytes, 0);
                        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                        $pow = min($pow, count($units) - 1);
                        $formattedSize = round($bytes / pow(1024, $pow), 1) . ' ' . $units[$pow];
                        @endphp
                        <tr draggable="true" data-doc-id="{{ $doc->id }}" data-is-folder="{{ $doc->is_folder ? 'true' : 'false' }}" data-doc-name="{{ $doc->original_name }}" class="document-row {{ $doc->is_folder ? 'folder-row' : '' }}">
                            <td class="td-checkbox">
                                <div class="custom-checkbox-wrapper">
                                    <input type="checkbox" class="form-check-input-modern doc-checkbox" value="{{ $doc->id }}" data-doc-id="{{ $doc->id }}" data-doc-name="{{ $doc->original_name }}" data-is-folder="{{ $doc->is_folder ? 'true' : 'false' }}" aria-label="Pilih {{ $doc->original_name }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="file-icon-box {{ $typeClass }} me-2 me-sm-3 flex-shrink-0">
                                        <i class="bi {{ $iconClass }}"></i>
                                    </div>
                                    <div class="d-flex flex-column overflow-hidden min-w-0 flex-grow-1 file-title-container">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            @if($doc->is_folder)
                                                <a href="{{ url('data-File?folder='.$doc->id) }}" class="text-decoration-none fw-bold text-dark hover-primary text-truncate fs-6" title="Buka Folder {{ $doc->original_name }}">{{ $doc->original_name }}</a>
                                                <span class="badge-ext folder d-none d-sm-inline-block">Folder</span>
                                            @elseif($desktopOfficeUrl)
                                                <a href="{{ $desktopOfficeUrl }}" class="text-decoration-none fw-bold text-dark hover-primary text-truncate fs-6" title="Klik untuk Buka & Edit di Aplikasi Office Desktop (Auto-Save ke Server)">{{ $doc->original_name }}</a>
                                                <span class="badge-ext {{ $isWord ? 'word' : 'excel' }} d-none d-sm-inline-block">{{ $ext ?: ($isWord ? 'docx' : 'xlsx') }}</span>
                                            @elseif(Str::contains($doc->mime_type, 'pdf'))
                                                <a href="{{ $fileStorageUrl }}" target="_blank" class="text-decoration-none fw-bold text-dark hover-primary text-truncate fs-6" title="Buka PDF di Tab Baru">{{ $doc->original_name }}</a>
                                                <span class="badge-ext pdf d-none d-sm-inline-block">PDF</span>
                                            @elseif(Str::contains($doc->mime_type, 'image'))
                                                <a href="{{ $fileStorageUrl }}" target="_blank" class="text-decoration-none fw-bold text-dark hover-primary text-truncate fs-6" title="Lihat Gambar">{{ $doc->original_name }}</a>
                                                <span class="badge-ext image d-none d-sm-inline-block">{{ $ext ?: 'IMG' }}</span>
                                            @elseif(Str::contains($doc->mime_type, 'zip') || Str::contains($doc->mime_type, 'rar'))
                                                <a href="{{ url('data-File/download/'.$doc->id) }}" class="text-decoration-none fw-bold text-dark hover-primary text-truncate fs-6" title="Download Arsip">{{ $doc->original_name }}</a>
                                                <span class="badge-ext archive d-none d-sm-inline-block">{{ $ext ?: 'ZIP' }}</span>
                                            @else
                                                <a href="{{ url('data-File/download/'.$doc->id) }}" class="text-decoration-none fw-bold text-dark hover-primary text-truncate fs-6" title="Download File">{{ $doc->original_name }}</a>
                                                <span class="badge-ext default d-none d-sm-inline-block">{{ $ext ?: 'FILE' }}</span>
                                            @endif
                                        </div>
                                        <!-- Mobile secondary metadata -->
                                        <div class="d-flex align-items-center gap-1 flex-wrap text-muted small" style="font-size: 0.72rem;">
                                            <span class="badge-ext {{ $typeClass }} py-0 px-1 d-sm-none" style="font-size: 0.65rem;">{{ $doc->is_folder ? 'DIR' : strtoupper($ext ?: 'FILE') }}</span>
                                            <span class="d-sm-none">&bull; {{ $doc->is_folder ? 'Folder' : $formattedSize }}</span>
                                            <span class="d-md-none">&bull; {{ $doc->owner_name ?? 'Admin' }}</span>
                                            <span class="d-lg-none">&bull; {{ $doc->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if(!$doc->is_folder && $desktopOfficeUrl)
                                        <small class="text-muted d-none d-sm-flex align-items-center gap-1 mt-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-cloud-check-fill text-success"></i> Auto-save langsung ke server
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 30px; height: 30px; background: var(--primary-gradient); font-size: 0.72rem; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);">
                                        {{ strtoupper(substr($doc->owner_name ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="text-dark fw-medium small text-nowrap">{{ $doc->owner_name ?? 'Admin' }}</span>
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="text-muted small fw-medium text-nowrap"><i class="bi bi-clock me-1 opacity-50"></i> {{ $doc->created_at->format('d M, Y') }}</span>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="badge bg-white text-secondary border px-2 py-1 shadow-sm text-nowrap" style="font-size: 0.75rem;">
                                    {{ $doc->is_folder ? 'Folder' : $formattedSize }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap pe-3 pe-md-4">
                                <div class="dropdown d-inline-block">
                                    <button class="action-btn-modern rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end glass-dropdown border-0 shadow-lg py-2">
                                        @if($doc->is_folder)
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ url('data-File?folder='.$doc->id) }}">
                                                    <i class="bi bi-folder2-open text-primary fs-6"></i>
                                                    <span>Buka Folder</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ url('data-File/folder-download/'.$doc->id) }}">
                                                    <i class="bi bi-file-earmark-zip-fill text-success fs-6"></i>
                                                    <span>Download Folder (ZIP)</span>
                                                </a>
                                            </li>
                                        @else
                                            @if($isWord)
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ $desktopOfficeUrl }}">
                                                        <i class="bi bi-file-earmark-word-fill text-primary fs-6"></i>
                                                        <span>Edit di Microsoft Word</span>
                                                    </a>
                                                </li>
                                            @elseif($isExcel)
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ $desktopOfficeUrl }}">
                                                        <i class="bi bi-file-earmark-excel-fill text-success fs-6"></i>
                                                        <span>Edit di Microsoft Excel</span>
                                                    </a>
                                                </li>
                                            @elseif(Str::contains($doc->mime_type, 'pdf') || Str::contains($doc->mime_type, 'image') || Str::contains($doc->mime_type, 'text'))
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ $fileStorageUrl }}" target="_blank">
                                                        <i class="bi bi-box-arrow-up-right text-info fs-6"></i>
                                                        <span>Buka di Browser</span>
                                                    </a>
                                                </li>
                                            @endif

                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ url('data-File/download/'.$doc->id) }}">
                                                    <i class="bi bi-cloud-arrow-down-fill text-secondary fs-6"></i>
                                                    <span>Download Berkas</span>
                                                </a>
                                            </li>
                                        @endif

                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" onclick="copySmbPath('{{ addslashes($smbPath) }}')">
                                                <i class="bi bi-link-45deg text-success fs-6"></i>
                                                <span>Salin Link Drive (SMB)</span>
                                            </button>
                                        </li>

                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" data-bs-toggle="modal" data-bs-target="#renameModal" data-doc-id="{{ $doc->id }}" data-doc-name="{{ $doc->original_name }}">
                                                <i class="bi bi-input-cursor-text text-warning fs-6"></i>
                                                <span>Ganti Nama</span>
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider my-1 opacity-10"></li>

                                        <li>
                                            <button type="button" 
                                                class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" 
                                                onclick="openDeleteConfirm('{{ url('data-File/'.$doc->id) }}', '{{ addslashes($doc->original_name) }}', {{ $doc->is_folder ? 'true' : 'false' }})">
                                                <i class="bi bi-trash3-fill fs-6"></i>
                                                <span>Pindahkan ke Sampah</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center py-5 my-3">
                                    <div class="d-inline-flex p-4 rounded-4 bg-white shadow-sm mb-3 text-muted" style="border: 1px solid rgba(0,0,0,0.05);">
                                        <i class="bi bi-folder2-open display-4 opacity-50 text-warning"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Belum Ada Berkas di Sini</h5>
                                    <p class="text-muted small mb-3">Folder ini masih kosong atau belum ada berkas yang cocok dengan filter pencarian Anda.</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn-action-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                            <i class="bi bi-cloud-arrow-up-fill"></i> Upload File
                                        </button>
                                        <button type="button" class="btn-action-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#folderModal">
                                            <i class="bi bi-folder-plus text-warning"></i> Folder Baru
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            @if($documents->hasPages())
            <div class="p-3 border-top bg-light bg-opacity-25" style="border-color: rgba(0,0,0,0.06) !important;">
                {{ $documents->links() }}
            </div>
            @endif
            </div>
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

    <!-- Floating Batch Action Bar -->
    <div id="batchActionBar" class="batch-action-bar" style="display: none;">
        <div class="batch-bar-inner">
            <div class="batch-counter-badge">
                <i class="bi bi-check2-circle fs-6"></i>
                <span id="batchCountText">0 Terpilih</span>
            </div>
            <div class="batch-drag-hint d-none d-lg-flex align-items-center gap-1">
                <i class="bi bi-arrows-move text-primary"></i>
                <span>Tarik ke folder tujuan untuk pindah</span>
            </div>
            <div class="batch-actions-btns">
                <button type="button" class="batch-btn-delete" onclick="openBatchDeleteConfirm()">
                    <i class="bi bi-trash3-fill"></i>
                    <span class="d-none d-sm-inline">Pindahkan ke Sampah</span>
                    <span class="d-sm-none">Hapus</span>
                </button>
                <button type="button" class="batch-btn-cancel" onclick="clearBatchSelection()" title="Batalkan Pilihan">
                    <i class="bi bi-x-lg"></i>
                    <span class="d-none d-sm-inline">Batal</span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<script>
    // State global untuk batch selection
    window.selectedDocIds = new Set();

    function updateBatchSelectionUI() {
        const totalSelected = window.selectedDocIds.size;
        const bar = document.getElementById('batchActionBar');
        const countText = document.getElementById('batchCountText');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const allCheckboxes = document.querySelectorAll('.doc-checkbox');

        if (countText) {
            countText.textContent = totalSelected + ' Terpilih';
        }

        if (totalSelected > 0) {
            if (bar) {
                bar.style.display = 'block';
                void bar.offsetHeight; // Force reflow
                bar.classList.add('show');
            }
        } else {
            if (bar) {
                bar.classList.remove('show');
                setTimeout(() => {
                    if (!bar.classList.contains('show')) {
                        bar.style.display = 'none';
                    }
                }, 300);
            }
        }

        // Update status master checkbox (semua terpilih / sebagian / tidak ada)
        if (selectAllCheckbox && allCheckboxes.length > 0) {
            const checkedCount = document.querySelectorAll('.doc-checkbox:checked').length;
            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === allCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
    }

    function clearBatchSelection() {
        window.selectedDocIds.clear();
        document.querySelectorAll('.doc-checkbox').forEach(cb => {
            cb.checked = false;
            const row = cb.closest('.document-row');
            if (row) row.classList.remove('table-row-selected');
        });
        updateBatchSelectionUI();
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('open_upload_modal'))
        var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        uploadModal.show();
        @endif

        // Listener untuk checkbox per baris dan master checkbox
        document.addEventListener('change', function(e) {
            if (e.target.matches('.doc-checkbox')) {
                const cb = e.target;
                const id = cb.value;
                const row = cb.closest('.document-row');

                if (cb.checked) {
                    window.selectedDocIds.add(id);
                    if (row) row.classList.add('table-row-selected');
                } else {
                    window.selectedDocIds.delete(id);
                    if (row) row.classList.remove('table-row-selected');
                }
                updateBatchSelectionUI();
            } else if (e.target.id === 'selectAllCheckbox') {
                const isChecked = e.target.checked;
                const allCheckboxes = document.querySelectorAll('.doc-checkbox');
                allCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                    const id = cb.value;
                    const row = cb.closest('.document-row');
                    if (isChecked) {
                        window.selectedDocIds.add(id);
                        if (row) row.classList.add('table-row-selected');
                    } else {
                        window.selectedDocIds.delete(id);
                        if (row) row.classList.remove('table-row-selected');
                    }
                });
                updateBatchSelectionUI();
            }
        });

        // Auto-refresh mekanisme (Polling setiap 3 detik)
        setInterval(function() {
            // Jeda auto-refresh jika user sedang memilih item, sedang drag, atau modal/dropdown sedang aktif
            if (window.selectedDocIds && window.selectedDocIds.size > 0) return;
            if (draggedRow != null || (draggedIds && draggedIds.length > 0)) return;
            if (document.querySelector('.modal.show') || document.querySelector('.dropdown-menu.show')) return;

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
                        if (document.querySelector('.dropdown-menu.show') || (window.selectedDocIds && window.selectedDocIds.size > 0)) {
                            return;
                        }
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

        // Setup Drag and Drop via Event Delegation (Support Single & Multi Drag)
        var draggedRow = null;
        var draggedIds = [];
        var glassCard = document.querySelector('.glass-card');

        document.addEventListener('dragstart', function(e) {
            var target = e.target.closest('.document-row');
            if (target) {
                draggedRow = target;
                var currentId = target.getAttribute('data-doc-id');

                // Jika baris yang ditarik adalah bagian dari checkbox yang dicentang, pindahkan SEMUA item yang dicentang!
                if (window.selectedDocIds && window.selectedDocIds.has(currentId)) {
                    draggedIds = Array.from(window.selectedDocIds);
                } else {
                    // Jika ditarik langsung tanpa centang, hanya pindahkan baris tersebut
                    draggedIds = [currentId];
                }

                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', JSON.stringify(draggedIds));

                // Jika memindahkan lebih dari 1 item, buat preview drag ghost yang rapi
                if (draggedIds.length > 1) {
                    const ghost = document.createElement('div');
                    ghost.className = 'drag-multi-ghost';
                    ghost.innerHTML = `<i class="bi bi-files text-warning fs-6"></i> <span>Memindahkan ${draggedIds.length} item</span>`;
                    document.body.appendChild(ghost);
                    e.dataTransfer.setDragImage(ghost, 15, 15);
                    setTimeout(() => ghost.remove(), 0);

                    draggedIds.forEach(id => {
                        const r = document.querySelector(`tr[data-doc-id="${id}"]`);
                        if (r) r.style.opacity = '0.45';
                    });
                } else {
                    setTimeout(() => target.style.opacity = '0.5', 0);
                }
            }
        });

        document.addEventListener('dragend', function(e) {
            if (draggedIds && draggedIds.length > 0) {
                draggedIds.forEach(id => {
                    const r = document.querySelector(`tr[data-doc-id="${id}"]`);
                    if (r) r.style.opacity = '1';
                });
            }
            if (draggedRow) {
                draggedRow.style.opacity = '1';
            }
            document.querySelectorAll('.folder-row').forEach(f => f.classList.remove('drag-over'));
            draggedRow = null;
            draggedIds = [];
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

            // Handle hover ke folder target untuk memindahkan berkas
            var targetFolder = e.target.closest('.folder-row');
            if (targetFolder && draggedIds && draggedIds.length > 0) {
                var targetFolderId = targetFolder.getAttribute('data-doc-id');
                // Jangan sorot folder jika folder tujuan adalah salah satu dari item yang sedang dipindahkan
                if (!draggedIds.includes(targetFolderId)) {
                    targetFolder.classList.add('drag-over');
                }
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

            // Kasus 1: Memindahkan berkas/folder di dalam web UI (Single atau Batch)
            if (draggedIds && draggedIds.length > 0 && targetFolder) {
                var targetFolderId = targetFolder.getAttribute('data-doc-id');

                // Validasi: tidak boleh memindahkan ke folder yang termasuk dalam daftar yang dipindah
                if (draggedIds.includes(targetFolderId)) {
                    return;
                }

                const targetFolderName = targetFolder.getAttribute('data-doc-name') || 'folder';
                const movingCount = draggedIds.length;
                const movingIdsCopy = [...draggedIds];

                fetch("{{ url('data-File/batch-move') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            doc_ids: movingIdsCopy,
                            parent_id: targetFolderId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            movingIdsCopy.forEach(id => {
                                const row = document.querySelector(`tr[data-doc-id="${id}"]`);
                                if (row) {
                                    row.style.transition = 'all 0.3s ease';
                                    row.style.opacity = '0';
                                    row.style.transform = 'scale(0.95)';
                                    setTimeout(() => row.remove(), 300);
                                }
                            });
                            clearBatchSelection();
                            showActionToast(`${data.count || movingCount} item berhasil dipindahkan ke folder "${targetFolderName}".`);
                        } else {
                            alert(data.message || 'Gagal memindahkan berkas.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan koneksi saat memindahkan berkas.');
                    });
                return;
            }

            // Kasus 2: Upload file / folder dari sistem operasi lokal
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

<!-- Modern Centered Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content modal-glass border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-body text-center p-4 pt-4 pb-3">
                <div class="delete-icon-pulse mb-3 mx-auto">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="deleteConfirmTitle">Pindahkan ke Tempat Sampah?</h5>
                <p class="text-muted small mb-3">
                    Apakah Anda yakin ingin memindahkan <span id="deleteConfirmItemType" class="text-dark fw-medium">item ini</span> ke Tempat Sampah:
                </p>
                <div class="delete-item-pill mb-3 text-start shadow-sm mx-auto">
                    <i class="bi bi-file-earmark-text text-danger fs-5 flex-shrink-0" id="deleteConfirmIcon"></i>
                    <span class="fw-semibold text-dark text-truncate small" id="deleteConfirmItemName" style="max-width: 270px; display: inline-block; vertical-align: middle;"></span>
                </div>
                <div class="alert alert-light border py-2 px-3 rounded-3 small text-muted d-flex align-items-center justify-content-center gap-2 mb-0" style="font-size: 0.78rem; background-color: #f8fafc;">
                    <i class="bi bi-info-circle-fill text-primary flex-shrink-0"></i>
                    <span class="text-start">Item dapat dipulihkan kembali oleh Administrator melalui menu Recycle Bin jika diperlukan.</span>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-1 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 py-2 text-secondary fw-medium shadow-sm" data-bs-dismiss="modal">
                    Batal
                </button>
                <form id="deleteConfirmForm" action="" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
                        <i class="bi bi-trash3-fill"></i>
                        <span>Ya, Pindahkan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modern Centered Batch Delete Confirmation Modal -->
<div class="modal fade" id="batchDeleteConfirmModal" tabindex="-1" aria-labelledby="batchDeleteConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content modal-glass border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-body text-center p-4 pt-4 pb-3">
                <div class="delete-icon-pulse mb-3 mx-auto">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="batchDeleteConfirmTitle">Pindahkan Item Terpilih ke Sampah?</h5>
                <p class="text-muted small mb-3">
                    Apakah Anda yakin ingin memindahkan <strong id="batchDeleteCountNotice" class="text-danger">0 item</strong> sekaligus ke Tempat Sampah:
                </p>
                <div class="batch-delete-list-container text-start mb-3" id="batchDeleteItemsList">
                    <!-- Dinamis terisi via JavaScript -->
                </div>
                <div class="alert alert-light border py-2 px-3 rounded-3 small text-muted d-flex align-items-center justify-content-center gap-2 mb-0" style="font-size: 0.78rem; background-color: #f8fafc;">
                    <i class="bi bi-info-circle-fill text-primary flex-shrink-0"></i>
                    <span class="text-start">Item dapat dipulihkan kembali oleh Administrator melalui menu Recycle Bin jika diperlukan.</span>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-1 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 py-2 text-secondary fw-medium shadow-sm" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" id="confirmBatchDeleteBtn" onclick="submitBatchDelete()" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
                    <i class="bi bi-trash3-fill"></i>
                    <span id="batchDeleteBtnLabel">Ya, Pindahkan Semua</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openBatchDeleteConfirm() {
    if (!window.selectedDocIds || window.selectedDocIds.size === 0) return;

    const count = window.selectedDocIds.size;
    const countNoticeEl = document.getElementById('batchDeleteCountNotice');
    const itemsListEl = document.getElementById('batchDeleteItemsList');
    const labelEl = document.getElementById('batchDeleteBtnLabel');

    if (countNoticeEl) countNoticeEl.textContent = count + ' item';
    if (labelEl) labelEl.textContent = 'Ya, Pindahkan ' + count + ' Item';

    if (itemsListEl) {
        itemsListEl.innerHTML = '';
        window.selectedDocIds.forEach(id => {
            const checkbox = document.querySelector(`.doc-checkbox[value="${id}"]`);
            const name = checkbox ? checkbox.getAttribute('data-doc-name') : 'Item #' + id;
            const isFolder = checkbox && checkbox.getAttribute('data-is-folder') === 'true';

            const itemEl = document.createElement('div');
            itemEl.className = 'batch-delete-item-pill';
            itemEl.innerHTML = `
                <i class="bi ${isFolder ? 'bi-folder-fill text-warning' : 'bi-file-earmark-text text-danger'} fs-6 flex-shrink-0"></i>
                <span class="text-truncate flex-grow-1 fw-medium text-dark">${escapeHtml(name)}</span>
                <span class="badge ${isFolder ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-light text-muted border'} rounded-pill" style="font-size: 0.68rem;">${isFolder ? 'Folder' : 'Berkas'}</span>
            `;
            itemsListEl.appendChild(itemEl);
        });
    }

    const modalEl = document.getElementById('batchDeleteConfirmModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

function submitBatchDelete() {
    if (!window.selectedDocIds || window.selectedDocIds.size === 0) return;

    const btn = document.getElementById('confirmBatchDeleteBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses...';
    }

    const ids = Array.from(window.selectedDocIds);

    fetch("{{ url('data-File/batch-delete') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            doc_ids: ids
        })
    })
    .then(res => res.json())
    .then(data => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash3-fill"></i> <span>Ya, Pindahkan Semua</span>';
        }

        const modalEl = document.getElementById('batchDeleteConfirmModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        if (data.success) {
            ids.forEach(id => {
                const row = document.querySelector(`tr[data-doc-id="${id}"]`);
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    setTimeout(() => row.remove(), 300);
                }
            });

            clearBatchSelection();
            showActionToast(data.message || `${ids.length} item berhasil dipindahkan ke Tempat Sampah.`);

            // Jika semua baris di tabel sudah habis terhapus, reload setelah sedikit jeda
            setTimeout(() => {
                const remainingRows = document.querySelectorAll('#document-table-body tr.document-row');
                if (remainingRows.length === 0) {
                    window.location.reload();
                }
            }, 600);
        } else {
            alert(data.message || 'Gagal menghapus beberapa berkas.');
        }
    })
    .catch(err => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash3-fill"></i> <span>Ya, Pindahkan Semua</span>';
        }
        console.error(err);
        alert('Terjadi kesalahan saat memindahkan berkas.');
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showActionToast(message, isSuccess = true) {
    const existing = document.getElementById('actionToastWidget');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'actionToastWidget';
    toast.className = 'position-fixed bottom-0 start-50 translate-middle-x mb-4 px-4 py-2 bg-dark text-white rounded-pill shadow-lg small fw-medium d-flex align-items-center gap-2';
    toast.style.zIndex = '9999';
    toast.style.animation = 'fadeIn 0.25s ease';
    toast.innerHTML = `<i class="bi ${isSuccess ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'}"></i> <span>${escapeHtml(message)}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}

function openDeleteConfirm(actionUrl, itemName, isFolder) {
    const form = document.getElementById('deleteConfirmForm');
    const nameEl = document.getElementById('deleteConfirmItemName');
    const typeEl = document.getElementById('deleteConfirmItemType');
    const titleEl = document.getElementById('deleteConfirmTitle');
    const iconEl = document.getElementById('deleteConfirmIcon');

    if (form) form.action = actionUrl;
    if (nameEl) nameEl.textContent = itemName;
    if (typeEl) typeEl.textContent = isFolder ? 'folder beserta seluruh isinya' : 'berkas ini';
    if (titleEl) titleEl.textContent = isFolder ? 'Pindahkan Folder ke Sampah?' : 'Pindahkan Berkas ke Sampah?';
    if (iconEl) {
        if (isFolder) {
            iconEl.className = 'bi bi-folder-fill text-warning fs-5 flex-shrink-0';
        } else {
            iconEl.className = 'bi bi-file-earmark-text text-danger fs-5 flex-shrink-0';
        }
    }

    const modalEl = document.getElementById('deleteConfirmModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

function copySmbPath(path) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(path).then(showCopySuccess).catch(() => fallbackCopy(path));
    } else {
        fallbackCopy(path);
    }
}

function fallbackCopy(path) {
    const ta = document.createElement('textarea');
    ta.value = path;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        showCopySuccess();
    } catch (err) {
        alert('Gagal menyalin link.');
    }
    document.body.removeChild(ta);
}

function showCopySuccess() {
    const existing = document.getElementById('copySuccessToast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'copySuccessToast';
    toast.className = 'position-fixed bottom-0 start-50 translate-middle-x mb-4 px-4 py-2 bg-dark text-white rounded-pill shadow small fw-medium d-flex align-items-center gap-2';
    toast.style.zIndex = '9999';
    toast.style.animation = 'fadeIn 0.2s ease';
    toast.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> Link Drive berhasil disalin ke clipboard!';
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

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