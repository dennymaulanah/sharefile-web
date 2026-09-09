@extends('admin.layout')

@section('title', 'Manajemen Berkas')
@section('page-title', 'Manajemen Berkas & Folder')
@section('page-subtitle', 'Kelola seluruh berkas dan arsip yang tersimpan di server')

@section('content')
<div class="card-custom p-4 mb-4">
  <!-- Filter & Search Bar -->
  <form action="{{ route('admin.files') }}" method="GET" class="row g-3 align-items-center">
    <!-- Search Input -->
    <div class="col-12 col-md-5">
      <div class="input-group">
        <span class="input-group-text bg-light border-end-0 text-muted">
          <i class="bi bi-search"></i>
        </span>
        <input 
          type="text" 
          name="q" 
          class="form-control border-start-0 ps-0" 
          placeholder="Cari nama berkas, folder, atau pengunggah..." 
          value="{{ request('q') }}">
      </div>
    </div>

    <!-- Category Filter -->
    <div class="col-6 col-md-3">
      <select name="type" class="form-select" onchange="this.form.submit()">
        <option value="" {{ !request('type') ? 'selected' : '' }}>Semua Tipe File</option>
        <option value="folder" {{ request('type') == 'folder' ? 'selected' : '' }}>Folder Direktori</option>
        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Dokumen (PDF / Word / Teks)</option>
        <option value="spreadsheet" {{ request('type') == 'spreadsheet' ? 'selected' : '' }}>Lembar Kerja (Excel / CSV)</option>
        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Gambar (PNG / JPG / WEBP)</option>
        <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>Arsip (ZIP / RAR)</option>
      </select>
    </div>

    <!-- Sort Filter -->
    <div class="col-6 col-md-2">
      <select name="sort" class="form-select" onchange="this.form.submit()">
        <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
        <option value="original_name" {{ request('sort') == 'original_name' ? 'selected' : '' }}>Nama (A-Z)</option>
        <option value="file_size" {{ request('sort') == 'file_size' ? 'selected' : '' }}>Ukuran Terbesar</option>
      </select>
    </div>

    <!-- Submit / Reset -->
    <div class="col-12 col-md-2 d-flex gap-2">
      <button type="submit" class="btn btn-teal text-white w-100 rounded-pill" style="background-color: #0d9488; border: none;">
        Filter
      </button>
      @if(request()->hasAny(['q', 'type', 'sort']))
        <a href="{{ route('admin.files') }}" class="btn btn-light border rounded-pill" title="Reset Filter">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      @endif
    </div>
  </form>
</div>

<!-- Files Table Card -->
<div class="card-custom p-3 p-sm-4">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
      <h3 class="h6 fw-bold mb-0 text-dark">Daftar Berkas Terdata</h3>
      <small class="text-muted">Menampilkan total {{ $documents->total() }} item dalam penyimpanan</small>
    </div>
    <a href="{{ url('/data-File') }}" class="btn btn-sm btn-outline-teal rounded-pill px-3" style="color: #0d9488; border-color: #0d9488;" target="_blank">
      <i class="bi bi-cloud-arrow-up me-1"></i> Buka Tampilan Pengguna
    </a>
  </div>

  @if($documents->count() > 0)
    <div class="table-responsive">
      <table class="table table-custom table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Nama Berkas / Folder</th>
            <th class="d-none d-sm-table-cell">Tipe</th>
            <th class="d-none d-lg-table-cell">Lokasi Direktori</th>
            <th class="d-none d-md-table-cell">Pemilik</th>
            <th class="d-none d-sm-table-cell">Ukuran</th>
            <th class="d-none d-lg-table-cell">Dibuat</th>
            <th class="text-end">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($documents as $doc)
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
                    <div class="fw-semibold text-dark text-truncate" style="max-width: 260px;" title="{{ $doc->original_name }}">
                      {{ $doc->original_name }}
                    </div>
                    @if($doc->filename && $doc->filename !== $doc->original_name)
                      <small class="text-muted font-monospace d-block text-truncate" style="font-size: 0.7rem;">
                        {{ $doc->filename }}
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
              <td class="d-none d-lg-table-cell">
                <span class="text-muted small">
                  @if($doc->parent)
                    <i class="bi bi-folder2 me-1 text-warning"></i> {{ $doc->parent->original_name }}
                  @else
                    <i class="bi bi-hdd me-1 text-secondary"></i> Root
                  @endif
                </span>
              </td>
              <td class="d-none d-md-table-cell">
                <span class="small text-muted"><i class="bi bi-person me-1"></i> {{ $doc->owner_name ?? 'Sistem' }}</span>
              </td>
              <td class="d-none d-sm-table-cell">
                <span class="small font-monospace">
                  {{ $isFolder ? '-' : \App\Http\Controllers\AdminController::formatBytes($doc->file_size) }}
                </span>
              </td>
              <td class="d-none d-lg-table-cell">
                <span class="small text-muted">{{ $doc->created_at ? $doc->created_at->format('d/m/Y H:i') : '-' }}</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  @if(!$isFolder)
                    <a href="{{ url('/data-File/download/' . $doc->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="Unduh File">
                      <i class="bi bi-download text-primary"></i>
                    </a>
                  @else
                    <a href="{{ url('/data-File?folder=' . $doc->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="Buka Folder di Web" target="_blank">
                      <i class="bi bi-box-arrow-up-right text-teal"></i>
                    </a>
                  @endif

                  <button type="button" class="btn btn-sm btn-light border p-1 px-2 text-danger" title="Hapus Permanen" data-bs-toggle="modal" data-bs-target="#deleteDocModal{{ $doc->id }}">
                    <i class="bi bi-trash3"></i>
                  </button>

                  <!-- Modal Delete Document -->
                  <div class="modal fade" id="deleteDocModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                      <div class="modal-content">
                        <div class="modal-body text-center p-4">
                          <div class="text-danger mb-3">
                            <i class="bi bi-trash3-fill display-5"></i>
                          </div>
                          <h6 class="fw-bold">Pindahkan ke Tempat Sampah?</h6>
                          <p class="small text-muted mb-4">
                            Pindahkan <strong>{{ $doc->original_name }}</strong> ke Tempat Sampah? Berkas/folder ini dapat dipulihkan kembali atau dihapus secara permanen melalui menu <strong>Tempat Sampah</strong>.
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

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
      <small class="text-muted">Menampilkan {{ $documents->firstItem() ?? 0 }} - {{ $documents->lastItem() ?? 0 }} dari {{ $documents->total() }} berkas</small>
      <div>
        {{ $documents->links('pagination::bootstrap-5') }}
      </div>
    </div>
  @else
    <div class="text-center py-5 text-muted">
      <i class="bi bi-search display-4 opacity-50 mb-2"></i>
      <p class="mb-0">Tidak ditemukan berkas yang sesuai dengan kriteria filter.</p>
    </div>
  @endif
</div>
@endsection
