@extends('admin.layout')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola akun yang memiliki akses ke dalam sistem')

@section('content')
<div class="card-custom p-3 p-sm-4 mb-3 mb-md-4">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
    <!-- Search Form -->
    <form action="{{ route('admin.users') }}" method="GET" class="d-flex flex-wrap flex-sm-nowrap align-items-center gap-2 flex-grow-1 w-100 w-lg-auto" style="max-width: 500px;">
      <div class="input-group">
        <span class="input-group-text bg-light border-end-0 text-muted">
          <i class="bi bi-search"></i>
        </span>
        <input 
          type="text" 
          name="q" 
          class="form-control border-start-0 ps-0" 
          placeholder="Cari nama, username, atau email..." 
          value="{{ request('q') }}">
      </div>
      <select name="role" class="form-select flex-shrink-0" style="max-width: 150px;" onchange="this.form.submit()">
        <option value="" {{ !request('role') ? 'selected' : '' }}>Semua Peran</option>
        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
      </select>
      @if(request()->hasAny(['q', 'role']))
        <a href="{{ route('admin.users') }}" class="btn btn-light border rounded-pill" title="Reset">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      @endif
    </form>

    <!-- Add User Button -->
    <button type="button" class="btn btn-teal text-white rounded-pill px-4 d-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto" style="background-color: #0d9488; border: none;" data-bs-toggle="modal" data-bs-target="#addUserModal">
      <i class="bi bi-person-plus-fill"></i>
      <span>Tambah Pengguna</span>
    </button>
  </div>
</div>

<!-- Users Table Card -->
<div class="card-custom p-3 p-sm-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h3 class="h6 fw-bold mb-0 text-dark">Daftar Akun Pengguna</h3>
      <small class="text-muted">Total {{ $users->total() }} akun terdaftar dalam sistem</small>
    </div>
  </div>

  @if($users->count() > 0)
    <div class="table-responsive">
      <table class="table table-custom table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Pengguna</th>
            <th class="d-none d-sm-table-cell">Username</th>
            <th class="d-none d-md-table-cell">Email</th>
            <th>Peran (Role)</th>
            <th class="d-none d-lg-table-cell">Tanggal Terdaftar</th>
            <th class="text-end">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
            @php
              $isSelf = (Auth::id() === $user->id);
            @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="user-avatar flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.95rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                  </div>
                  <div class="overflow-hidden min-w-0">
                    <div class="fw-semibold text-dark text-truncate">
                      {{ $user->name }}
                      @if($isSelf)
                        <span class="badge bg-teal ms-1" style="background-color: #0d9488; font-size: 0.65rem;">Anda</span>
                      @endif
                    </div>
                    <!-- Mobile secondary metadata -->
                    <div class="small text-muted d-sm-none text-truncate" style="font-size: 0.72rem;">
                      {{ '@' . $user->username }} &bull; {{ $user->email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="d-none d-sm-table-cell">
                <code class="text-teal fw-bold">@ {{ $user->username ?? '-' }}</code>
              </td>
              <td class="d-none d-md-table-cell">
                <span class="text-muted small">{{ $user->email }}</span>
              </td>
              <td>
                @if($user->role === 'admin')
                  <span class="badge badge-soft-success">
                    <i class="bi bi-shield-check me-1"></i> Admin
                  </span>
                @else
                  <span class="badge badge-soft-primary">
                    <i class="bi bi-person me-1"></i> User
                  </span>
                @endif
              </td>
              <td class="d-none d-lg-table-cell">
                <span class="text-muted small">{{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <!-- Edit Button -->
                  <button type="button" class="btn btn-sm btn-light border p-1 px-2" title="Edit Pengguna" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                    <i class="bi bi-pencil-square text-primary"></i>
                  </button>

                  <!-- Delete Button -->
                  @if(!$isSelf)
                    <button type="button" class="btn btn-sm btn-light border p-1 px-2 text-danger" title="Hapus Pengguna" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                      <i class="bi bi-trash3"></i>
                    </button>
                  @else
                    <button type="button" class="btn btn-sm btn-light border p-1 px-2 text-muted" title="Akun Anda Sedang Digunakan" disabled>
                      <i class="bi bi-lock-fill"></i>
                    </button>
                  @endif

                  <!-- Modal Edit User -->
                  <div class="modal fade text-start" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="editUserModalLabel{{ $user->id }}"><i class="bi bi-pencil-square me-2 text-teal"></i> Edit Akun {{ $user->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-3">
                              <label class="form-label small fw-semibold">Nama Lengkap</label>
                              <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                            <div class="mb-3">
                              <label class="form-label small fw-semibold">Username</label>
                              <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                            </div>
                            <div class="mb-3">
                              <label class="form-label small fw-semibold">Alamat Email</label>
                              <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                            <div class="mb-3">
                              <label class="form-label small fw-semibold">Peran (Role)</label>
                              <select name="role" class="form-select" required>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Pengguna Biasa</option>
                              </select>
                            </div>
                            <hr class="my-3 text-muted">
                            <div class="mb-3">
                              <label class="form-label small fw-semibold">Password Baru (Opsional)</label>
                              <input type="password" name="password" class="form-control" placeholder="Kosongkan bila tidak ingin diubah">
                              <small class="text-muted" style="font-size: 0.72rem;">Minimal 6 karakter jika ingin mengganti password</small>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-teal text-white rounded-pill px-4" style="background-color: #0d9488; border: none;">Simpan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                  <!-- Modal Delete User -->
                  @if(!$isSelf)
                    <div class="modal fade text-start" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                          <div class="modal-body text-center p-4">
                            <div class="text-danger mb-3">
                              <i class="bi bi-person-x-fill display-5"></i>
                            </div>
                            <h6 class="fw-bold">Hapus Pengguna?</h6>
                            <p class="small text-muted mb-4">
                              Apakah Anda yakin ingin menghapus akun <strong>{{ $user->name }}</strong> ({{ $user->username }})?
                            </p>
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-3">Hapus</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
      <small class="text-muted">Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna</small>
      <div>
        {{ $users->links('pagination::bootstrap-5') }}
      </div>
    </div>
  @else
    <div class="text-center py-5 text-muted">
      <i class="bi bi-person-x display-4 opacity-50 mb-2"></i>
      <p class="mb-0">Tidak ditemukan pengguna yang sesuai.</p>
    </div>
  @endif
</div>

<!-- Modal Tambah Pengguna Baru -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.users.create') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="addUserModalLabel"><i class="bi bi-person-plus me-2 text-teal"></i> Tambah Akun Pengguna Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Contoh: budi_s" required>
            <small class="text-muted" style="font-size: 0.72rem;">Hanya huruf, angka, garis bawah, tanpa spasi</small>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Alamat Email</label>
            <input type="email" name="email" class="form-control" placeholder="budi@example.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Peran (Role)</label>
            <select name="role" class="form-select" required>
              <option value="user" selected>Pengguna Biasa</option>
              <option value="admin">Administrator</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Password Awal</label>
            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-teal text-white rounded-pill px-4" style="background-color: #0d9488; border: none;">Simpan Pengguna</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
