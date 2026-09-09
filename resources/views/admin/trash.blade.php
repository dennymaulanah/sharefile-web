@extends('admin.layout')

@section('title', 'Tempat Sampah')
@section('page-title', 'Tempat Sampah (Recycle Bin)')
@section('page-subtitle', 'Daftar berkas dan folder yang dihapus. Hanya admin yang dapat menghapus permanen atau memulihkannya.')

@section('content')
<div class="card-custom p-4 mb-4">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
    <!-- Filter & Search Form -->
    <form action="{{ route('admin.trash') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
      <div class="input-group" style="max-width: 380px;">
        <span class="input-group-text bg-light border-end-0 text-muted">
          <i class="bi bi-search"></i>
        </span>
        <input 
          type="text" 
          name="q" 
          class="form-control border-start-0 ps-0" 
          placeholder="Cari berkas yang dihapus..." 
          value="{{ request('q') }}">
      </div>

      <select name="type" class="form-select" style="max-width: 180px;" onchange="this.form.submit()">
        <option value="" {{ !request('type') ? 'selected' : '' }}>Semua Tipe File</option>
        <option value="folder" {{ request('type') == 'folder' ? 'selected' : '' }}>Folder Direktori</option>
        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Dokumen (PDF / Word)</option>
        <option value="spreadsheet" {{ request('type') == 'spreadsheet' ? 'selected' : '' }}>Lembar Kerja (Excel)</option>
        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Gambar (PNG / JPG)</option>
        <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>Arsip (ZIP / RAR)</option>
      </select>

      @if(request()->hasAny(['q', 'type']))
        <a href="{{ route('admin.trash') }}" class="btn btn-light border rounded-pill" title="Reset Filter">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      @endif
    </form>

    <!-- Empty Trash Button -->
    @if($trashCount > 0)
      <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">
        <i class="bi bi-trash3-fill"></i>
        <span>Kosongkan Tempat Sampah ({{ $trashCount }})</span>
      </button>

      <!-- Modal Kosongkan Trash -->
      <div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
          <div class="modal-content">
            <div class="modal-body text-center p-4">
              <div class="text-danger mb-3">
                <i class="bi bi-exclamation-triangle-fill display-5"></i>
              </div>
              <h6 class="fw-bold">Kosongkan Tempat Sampah?</h6>
              <p class="small text-muted mb-4">
                Tindakan ini akan <strong>menghapus permanen seluruh ({{ $trashCount }}) berkas dan folder</strong> di Tempat Sampah dari hard disk server. Tindakan ini tidak dapat dibatalkan!
              </p>
              <form action="{{ route('admin.trash.empty') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center gap-2">
                  <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-danger rounded-pill px-3">Ya, Kosongkan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>

<!-- Trashed Items Table Card -->
<div class="card-custom p-4">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
      <h3 class="h6 fw-bold mb-0 text-dark"><i class="bi bi-trash3-fill text-danger me-2"></i> Berkas di Tempat Sampah</h3>
      <small class="text-muted">Total {{ $trashedDocuments->total() }} berkas/folder berada di tempat penampungan sementara</small>
    </div>
  </div>

  @if($trashedDocuments->count() > 0)
    <div class="table-responsive">
      <table class="table table-custom table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Nama Berkas / Folder</th>
            <th>Tipe</th>
            <th>Lokasi Asal</th>
            <th>Ukuran</th>
            <th>Waktu Dihapus</th>
            <th class="text-end">Tindakan Admin</th>
          </tr>
        </thead>
        <tbody>
          @foreach($trashedDocuments as $doc)
            @php
              $isFolder = $doc->is_folder;
              $ext = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION));
            @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  @if($isFolder)
                    <div class="p-2 rounded bg-warning bg-opacity-10 text-warning">
                      <i class="bi bi-folder-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['pdf']))
                    <div class="p-2 rounded bg-danger bg-opacity-10 text-danger">
                      <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['doc', 'docx']))
                    <div class="p-2 rounded bg-primary bg-opacity-10 text-primary">
                      <i class="bi bi-file-earmark-word-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                    <div class="p-2 rounded bg-success bg-opacity-10 text-success">
                      <i class="bi bi-file-earmark-excel-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    <div class="p-2 rounded bg-warning bg-opacity-10 text-warning">
                      <i class="bi bi-file-earmark-image-fill fs-5"></i>
                    </div>
                  @elseif(in_array($ext, ['zip', 'rar', '7z']))
                    <div class="p-2 rounded bg-purple bg-opacity-10" style="color: #7c3aed;">
                      <i class="bi bi-file-earmark-zip-fill fs-5"></i>
                    </div>
                  @else
                    <div class="p-2 rounded bg-secondary bg-opacity-10 text-secondary">
                      <i class="bi bi-file-earmark-fill fs-5"></i>
                    </div>
                  @endif

                  <div class="overflow-hidden" style="max-width: 280px;">
                    <div class="fw-semibold text-dark text-truncate" title="{{ $doc->original_name }}">
                      {{ $doc->original_name }}
                    </div>
                    <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">
                      Pengunggah: {{ $doc->owner_name ?? 'Sistem' }}
                    </small>
                  </div>
                </div>
              </td>
              <td>
                @if($isFolder)
                  <span class="badge badge-soft-warning">Folder</span>
                @else
                  <span class="badge badge-soft-primary text-uppercase">{{ $ext ?: 'File' }}</span>
                @endif
              </td>
              <td>
                <span class="text-muted small">
                  @if($doc->parent)
                    <i class="bi bi-folder2 me-1 text-warning"></i> {{ $doc->parent->original_name }}
                  @else
                    <i class="bi bi-hdd me-1 text-secondary"></i> Direktori Utama
                  @endif
                </span>
              </td>
              <td>
                <span class="small font-monospace">
                  {{ $isFolder ? '-' : \App\Http\Controllers\AdminController::formatBytes($doc->file_size) }}
                </span>
              </td>
              <td>
                <span class="small text-danger fw-semibold d-block">
                  <i class="bi bi-clock-history me-1"></i> {{ $doc->deleted_at ? $doc->deleted_at->diffForHumans() : '-' }}
                </span>
                <small class="text-muted" style="font-size: 0.7rem;">{{ $doc->deleted_at ? $doc->deleted_at->format('d/m/Y H:i') : '' }}</small>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-2">
                  <!-- Restore Form -->
                  <form action="{{ route('admin.trash.restore', $doc->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 d-flex align-items-center gap-1 shadow-sm" title="Pulihkan berkas ke lokasi asal">
                      <i class="bi bi-arrow-counterclockwise"></i>
                      <span class="small">Pulihkan</span>
                    </button>
                  </form>

                  <!-- Permanent Delete Button -->
                  <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 d-flex align-items-center gap-1" title="Hapus Permanen dari Server" data-bs-toggle="modal" data-bs-target="#forceDeleteModal{{ $doc->id }}">
                    <i class="bi bi-trash3-fill"></i>
                    <span class="small">Hapus Permanen</span>
                  </button>

                  <!-- Modal Force Delete -->
                  <div class="modal fade" id="forceDeleteModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                      <div class="modal-content text-start">
                        <div class="modal-body text-center p-4">
                          <div class="text-danger mb-3">
                            <i class="bi bi-exclamation-octagon-fill display-5"></i>
                          </div>
                          <h6 class="fw-bold">Hapus Permanen?</h6>
                          <p class="small text-muted mb-4">
                            Yakin ingin menghapus <strong>{{ $doc->original_name }}</strong> secara permanen? {{ $isFolder ? 'Seluruh subfolder dan isi berkas di dalamnya akan dimusnahkan secara permanen dari hard disk server!' : 'Berkas fisik akan dihapus permanen dari hard disk server!' }}
                          </p>
                          <form action="{{ route('admin.trash.force', $doc->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="d-flex justify-content-center gap-2">
                              <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-danger rounded-pill px-3">Hapus Selamanya</button>
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
      <small class="text-muted">Menampilkan {{ $trashedDocuments->firstItem() ?? 0 }} - {{ $trashedDocuments->lastItem() ?? 0 }} dari {{ $trashedDocuments->total() }} berkas terhapus</small>
      <div>
        {{ $trashedDocuments->links('pagination::bootstrap-5') }}
      </div>
    </div>
  @else
    <div class="text-center py-5 text-muted">
      <div class="p-3 bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
        <i class="bi bi-trash3 text-muted display-6"></i>
      </div>
      <h6 class="fw-bold text-dark">Tempat Sampah Kosong</h6>
      <p class="small text-muted mb-0">Tidak ada berkas atau folder yang terhapus di dalam sistem.</p>
    </div>
  @endif
</div>
@endsection
