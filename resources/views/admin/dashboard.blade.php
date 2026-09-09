@extends('admin.layout')

@section('title', 'Dashboard Utama')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan sistem penyimpanan dan aktivitas file')

@section('content')
<!-- KPI Metric Cards -->
<div class="row g-2 g-sm-3 g-xl-4 mb-3 mb-md-4">
  <!-- Total Files -->
  <div class="col-6 col-md-6 col-xl-3">
    <div class="card-custom metric-card h-100">
      <div class="metric-icon" style="background-color: #ecfeff; color: #0891b2;">
        <i class="bi bi-file-earmark-text-fill"></i>
      </div>
      <div>
        <div class="metric-title">Total Berkas</div>
        <div class="metric-value text-dark">{{ number_format($totalFiles) }}</div>
        <small class="text-muted d-none d-sm-inline" style="font-size: 0.75rem;">Berkas tersimpan</small>
      </div>
    </div>
  </div>

  <!-- Total Folders -->
  <div class="col-6 col-md-6 col-xl-3">
    <div class="card-custom metric-card h-100">
      <div class="metric-icon" style="background-color: #fef3c7; color: #d97706;">
        <i class="bi bi-folder-fill"></i>
      </div>
      <div>
        <div class="metric-title">Total Folder</div>
        <div class="metric-value text-dark">{{ number_format($totalFolders) }}</div>
        <small class="text-muted d-none d-sm-inline" style="font-size: 0.75rem;">Direktori arsip</small>
      </div>
    </div>
  </div>

  <!-- Total Storage Used -->
  <div class="col-6 col-md-6 col-xl-3">
    <div class="card-custom metric-card h-100">
      <div class="metric-icon" style="background-color: #f0fdf4; color: #16a34a;">
        <i class="bi bi-hdd-fill"></i>
      </div>
      <div>
        <div class="metric-title">Kapasitas</div>
        <div class="metric-value text-dark" style="font-size: 1.5rem;">{{ $totalSizeFormatted }}</div>
        <small class="text-muted d-none d-sm-inline" style="font-size: 0.75rem;">Storage file publik</small>
      </div>
    </div>
  </div>

  <!-- Total Users -->
  <div class="col-6 col-md-6 col-xl-3">
    <div class="card-custom metric-card h-100">
      <div class="metric-icon" style="background-color: #f5f3ff; color: #7c3aed;">
        <i class="bi bi-people-fill"></i>
      </div>
      <div>
        <div class="metric-title">Pengguna</div>
        <div class="metric-value text-dark">{{ number_format($totalUsers) }}</div>
        <small class="text-muted d-none d-sm-inline" style="font-size: 0.75rem;">Akun terdaftar</small>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Storage & Category Breakdown -->
  <div class="col-12 col-lg-7">
    <div class="card-custom p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <h3 class="h6 fw-bold mb-1 text-dark"><i class="bi bi-pie-chart-fill text-teal me-2"></i> Distribusi Jenis Berkas</h3>
          <small class="text-muted">Pembagian dokumen berdasarkan kategori format file</small>
        </div>
        <a href="{{ route('admin.files') }}" class="btn btn-sm btn-light border rounded-pill px-3">
          Lihat Semua <i class="bi bi-arrow-right small"></i>
        </a>
      </div>

      <!-- Segmented Bar -->
      <div class="progress mb-4 rounded-pill overflow-hidden" style="height: 14px; background-color: #e2e8f0;">
        @php
          $totalCountForBar = max($totalFiles, 1);
        @endphp
        @foreach($categories as $cat)
          @php
            $pct = round(($cat['count'] / $totalCountForBar) * 100, 1);
          @endphp
          @if($pct > 0)
            <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%; background-color: {{ $cat['color'] }};" title="{{ $cat['label'] }}: {{ $cat['count'] }} berkas ({{ $pct }}%)"></div>
          @endif
        @endforeach
      </div>

      <!-- Category List -->
      <div class="row g-3">
        @foreach($categories as $cat)
          @php
            $pct = round(($cat['count'] / $totalCountForBar) * 100, 1);
          @endphp
          <div class="col-12 col-sm-6">
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light bg-opacity-50">
              <div class="d-flex align-items-center gap-2 overflow-hidden">
                <span class="rounded-circle d-inline-block flex-shrink-0" style="width: 10px; height: 10px; background-color: {{ $cat['color'] }};"></span>
                <span class="small fw-semibold text-truncate">{{ $cat['label'] }}</span>
              </div>
              <div class="text-end flex-shrink-0 ps-2">
                <span class="badge bg-white text-dark border small fw-bold">{{ $cat['count'] }}</span>
                <div class="text-muted" style="font-size: 0.7rem;">{{ \App\Http\Controllers\AdminController::formatBytes($cat['size']) }}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <!-- Quick Actions -->
      <div class="dashboard-quick-actions d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
        <a href="{{ url('/data-File') }}" class="btn btn-teal text-white rounded-pill px-3 py-2 small fw-semibold" style="background-color: #0d9488; border: none;" target="_blank">
          <i class="bi bi-cloud-arrow-up-fill me-1"></i> Buka File Explorer
        </a>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
          <i class="bi bi-person-plus-fill me-1"></i> Kelola Pengguna
        </a>
        <a href="{{ url('/data-File/download-drive-bat') }}" class="btn btn-outline-dark rounded-pill px-3 py-2 small fw-semibold">
          <i class="bi bi-windows me-1"></i> Unduh Script Drive Z
        </a>
      </div>
    </div>
  </div>

  <!-- Server & Environment Card -->
  <div class="col-12 col-lg-5">
    <div class="card-custom p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <h3 class="h6 fw-bold mb-1 text-dark"><i class="bi bi-cpu-fill text-primary me-2"></i> Status Server & Konfigurasi</h3>
          <small class="text-muted">Informasi teknis hosting ShareFile</small>
        </div>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
          <i class="bi bi-check-circle-fill me-1"></i> Online
        </span>
      </div>

      <ul class="list-group list-group-flush small">
        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
          <span class="text-muted"><i class="bi bi-code-slash me-2"></i> Versi PHP</span>
          <span class="fw-semibold text-dark">{{ $serverInfo['php_version'] }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
          <span class="text-muted"><i class="bi bi-box me-2"></i> Framework Laravel</span>
          <span class="fw-semibold text-dark">v{{ $serverInfo['laravel_version'] }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
          <span class="text-muted"><i class="bi bi-database me-2"></i> Database Engine</span>
          <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ strtoupper($serverInfo['db_driver']) }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
          <span class="text-muted"><i class="bi bi-arrow-up-circle me-2"></i> Max File Upload</span>
          <span class="fw-semibold text-dark">{{ $serverInfo['max_upload'] }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
          <span class="text-muted"><i class="bi bi-memory me-2"></i> Memory Limit</span>
          <span class="fw-semibold text-dark">{{ $serverInfo['memory_limit'] }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
          <span class="text-muted"><i class="bi bi-hdd-network me-2"></i> Protocol WebDAV</span>
          <span class="badge bg-info-subtle text-info px-2 py-1">Aktif (/webdav)</span>
        </li>
      </ul>

      <div class="mt-3 p-2 bg-light rounded-3 border">
        <div class="d-flex align-items-center justify-content-between mb-1">
          <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-folder-symlink me-1"></i> Root Direktori Storage:</span>
        </div>
        <div class="text-dark font-monospace text-truncate small" title="{{ $serverInfo['storage_root'] }}">
          {{ $serverInfo['storage_root'] }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ShareFile Access Password Settings Card -->
<div class="card-custom p-3 p-sm-4 mb-3 mb-md-4" style="border-left: 4px solid #0d9488;">
  <div class="row align-items-center gy-3">
    <div class="col-12 col-xl-6">
      <div class="d-flex align-items-center gap-3">
        <div class="metric-icon flex-shrink-0" style="background-color: #ccfbf1; color: #0d9488; width: 44px; height: 44px; font-size: 1.3rem; border-radius: 12px;">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <h3 class="h6 fw-bold mb-0 text-dark">Keamanan & Password Akses Menu Share File</h3>
            @if($sharefilePasswordEnabled)
              <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small" style="font-size: 0.72rem;">
                <i class="bi bi-lock-fill me-1"></i> Proteksi Aktif
              </span>
            @else
              <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill small" style="font-size: 0.72rem;">
                <i class="bi bi-unlock-fill me-1"></i> Proteksi Nonaktif
              </span>
            @endif
          </div>
          <small class="text-muted d-block mt-1">Pengunjung wajib memasukkan password ini untuk mengakses berkas dan folder di menu Share File.</small>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-6">
      <form action="{{ route('admin.settings.sharefile_password') }}" method="POST" class="sharefile-pw-form d-flex flex-wrap align-items-center gap-2 justify-content-xl-end">
        @csrf
        <div class="form-check form-switch me-2 mb-0">
          <input class="form-check-input" type="checkbox" role="switch" id="enableProtection" name="sharefile_password_enabled" value="1" {{ $sharefilePasswordEnabled ? 'checked' : '' }}>
          <label class="form-check-label small fw-semibold text-dark" for="enableProtection">Wajibkan Password</label>
        </div>
        <div class="input-group" style="max-width: 240px;">
          <input type="password" name="sharefile_password" id="adminShareFilePassword" class="form-control form-control-sm" value="{{ $sharefilePassword }}" placeholder="Password ShareFile" required>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAdminPw()" title="Lihat Password">
            <i class="bi bi-eye" id="adminPwEye"></i>
          </button>
        </div>
        <button type="submit" class="btn btn-sm btn-teal text-white rounded-pill px-3 py-1 fw-semibold" style="background-color: #0d9488; border: none;">
          <i class="bi bi-check2 me-1"></i> Simpan
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Recent Uploads / Files -->
<div class="card-custom p-4">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
      <h3 class="h6 fw-bold mb-1 text-dark"><i class="bi bi-clock-history text-teal me-2"></i> Aktivitas Dokumen & Berkas Terbaru</h3>
      <small class="text-muted">10 berkas atau folder terakhir yang ditambahkan ke dalam sistem</small>
    </div>
    <a href="{{ route('admin.files') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
      Buka Semua Berkas ({{ $totalFiles + $totalFolders }})
    </a>
  </div>

  @if($recentDocuments->count() > 0)
    <div class="table-responsive">
      <table class="table table-custom table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Nama Berkas / Folder</th>
            <th class="d-none d-sm-table-cell">Tipe</th>
            <th class="d-none d-md-table-cell">Pemilik / Uploader</th>
            <th class="d-none d-sm-table-cell">Ukuran</th>
            <th class="d-none d-lg-table-cell">Tanggal Unggah</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($recentDocuments as $doc)
            @php
              $isFolder = $doc->is_folder;
              $ext = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION));
            @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  @if($isFolder)
                    <div class="p-2 rounded bg-warning bg-opacity-10 text-warning flex-shrink-0">
                      <i class="bi bi-folder-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['pdf']))
                    <div class="p-2 rounded bg-danger bg-opacity-10 text-danger flex-shrink-0">
                      <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['doc', 'docx']))
                    <div class="p-2 rounded bg-primary bg-opacity-10 text-primary flex-shrink-0">
                      <i class="bi bi-file-earmark-word-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                    <div class="p-2 rounded bg-success bg-opacity-10 text-success flex-shrink-0">
                      <i class="bi bi-file-earmark-excel-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    <div class="p-2 rounded bg-warning bg-opacity-10 text-warning flex-shrink-0">
                      <i class="bi bi-file-earmark-image-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['zip', 'rar', '7z']))
                    <div class="p-2 rounded bg-purple bg-opacity-10 flex-shrink-0" style="color: #7c3aed;">
                      <i class="bi bi-file-earmark-zip-fill fs-5"></i>
                    </div>
                  @else
                    <div class="p-2 rounded bg-secondary bg-opacity-10 text-secondary flex-shrink-0">
                      <i class="bi bi-file-earmark-fill fs-5"></i>
                    </div>
                  @endif

                  <div class="overflow-hidden min-w-0 flex-grow-1">
                    <div class="fw-semibold text-dark text-truncate" style="max-width: 260px;">
                      {{ $doc->original_name }}
                    </div>
                    @if($doc->parent)
                      <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">
                        <i class="bi bi-folder me-1"></i> {{ $doc->parent->original_name }}
                      </small>
                    @endif
                    <!-- Mobile secondary metadata -->
                    <div class="d-flex align-items-center gap-1 flex-wrap text-muted small d-sm-none mt-1" style="font-size: 0.7rem;">
                      <span class="badge bg-light text-dark border py-0 px-1">{{ $isFolder ? 'DIR' : strtoupper($ext ?: 'FILE') }}</span>
                      <span>&bull; {{ $isFolder ? '-' : \App\Http\Controllers\AdminController::formatBytes($doc->file_size) }}</span>
                      <span>&bull; {{ $doc->owner_name ?? 'Sistem' }}</span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="d-none d-sm-table-cell">
                @if($isFolder)
                  <span class="badge badge-soft-warning">Folder</span>
                @else
                  <span class="badge badge-soft-primary text-uppercase">{{ $ext ?: 'File' }}</span>
                @endif
              </td>
              <td class="d-none d-md-table-cell">
                <span class="text-muted small"><i class="bi bi-person me-1"></i> {{ $doc->owner_name ?? 'Sistem' }}</span>
              </td>
              <td class="d-none d-sm-table-cell">
                <span class="small font-monospace">
                  {{ $isFolder ? '-' : \App\Http\Controllers\AdminController::formatBytes($doc->file_size) }}
                </span>
              </td>
              <td class="d-none d-lg-table-cell">
                <span class="text-muted small">{{ $doc->created_at ? $doc->created_at->format('d M Y H:i') : '-' }}</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  @if(!$isFolder)
                    <a href="{{ url('/data-File/download/' . $doc->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="Unduh File">
                      <i class="bi bi-download text-primary"></i>
                    </a>
                  @else
                    <a href="{{ url('/data-File?folder=' . $doc->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="Buka di Explorer">
                      <i class="bi bi-box-arrow-up-right text-teal"></i>
                    </a>
                  @endif

                  <button type="button" class="btn btn-sm btn-light border p-1 px-2 text-danger" title="Hapus Berkas" data-bs-toggle="modal" data-bs-target="#deleteDocModal{{ $doc->id }}">
                    <i class="bi bi-trash3"></i>
                  </button>

                  <!-- Modal Delete Document -->
                  <div class="modal fade" id="deleteDocModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                      <div class="modal-content">
                        <div class="modal-body text-center p-4">
                          <div class="text-danger mb-3">
                            <i class="bi bi-exclamation-circle-fill display-5"></i>
                          </div>
                          <h6 class="fw-bold">Pindahkan ke Tempat Sampah?</h6>
                          <p class="small text-muted mb-4">
                            Pindahkan <strong>{{ $doc->original_name }}</strong> ke Tempat Sampah? Anda dapat memulihkannya kapan saja melalui menu <strong>Tempat Sampah</strong>.
                          </p>
                          <form action="{{ route('admin.files.destroy', $doc->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="d-flex justify-content-center gap-2">
                              <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-danger rounded-pill px-3">Pindahkan ke Sampah</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="text-center py-5 text-muted">
      <i class="bi bi-folder2-open display-4 opacity-50 mb-2"></i>
      <p class="mb-0">Belum ada file atau folder yang tersimpan.</p>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  function toggleAdminPw() {
    const input = document.getElementById('adminShareFilePassword');
    const icon = document.getElementById('adminPwEye');
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
</script>
@endpush
