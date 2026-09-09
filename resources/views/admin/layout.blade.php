<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Dashboard') - ShareFile</title>

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.svg') }}" rel="icon" type="image/svg+xml">
  <link href="{{ asset('assets/img/favicon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- Bootstrap & Bootstrap Icons -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

  <!-- Admin Custom CSS -->
  <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body>

  <div class="admin-wrapper">
    <!-- Sidebar Backdrop (Mobile) -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
      <div class="sidebar-brand">
        <div class="brand-icon">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2">
            <h1 class="brand-title">ShareFile</h1>
            <span class="brand-badge">Admin</span>
          </div>
          <small style="color: #64748b; font-size: 0.72rem;">Control & Monitoring</small>
        </div>
      </div>

      <div class="sidebar-menu">
        <div class="menu-label">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dashboard</span>
        </a>

        <div class="menu-label">Akses Cepat</div>
        <a href="{{ url('/data-File') }}" class="nav-link-custom" target="_blank">
          <i class="bi bi-cloud-arrow-up-fill text-info"></i>
          <span>Buka Aplikasi File</span>
          <i class="bi bi-box-arrow-up-right ms-auto" style="font-size: 0.75rem; opacity: 0.7;"></i>
        </a>
        <a href="{{ url('/') }}" class="nav-link-custom" target="_blank">
          <i class="bi bi-house-door-fill text-warning"></i>
          <span>Halaman Beranda</span>
          <i class="bi bi-box-arrow-up-right ms-auto" style="font-size: 0.75rem; opacity: 0.7;"></i>
        </a>

        <div class="menu-label">Manajemen Sistem</div>
        <a href="{{ route('admin.files') }}" class="nav-link-custom {{ request()->routeIs('admin.files') ? 'active' : '' }}">
          <i class="bi bi-folder-fill"></i>
          <span>Manajemen Berkas</span>
        </a>
        
        <a href="{{ route('admin.users') }}" class="nav-link-custom {{ request()->routeIs('admin.users') ? 'active' : '' }}">
          <i class="bi bi-people-fill"></i>
          <span>Manajemen User</span>
        </a>

        <a href="{{ route('admin.trash') }}" class="nav-link-custom {{ request()->routeIs('admin.trash*') ? 'active' : '' }}">
          <i class="bi bi-trash3-fill text-danger"></i>
          <span>Tempat Sampah</span>
          @php $sidebarTrashCount = \App\Models\Document::onlyTrashed()->count(); @endphp
          @if($sidebarTrashCount > 0)
            <span class="badge bg-danger rounded-pill ms-auto small" style="font-size: 0.68rem;">{{ $sidebarTrashCount }}</span>
          @endif
        </a>

      </div>

      <div class="sidebar-footer">
        <div class="user-pill d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2 overflow-hidden">
            <div class="user-avatar">
              {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="text-truncate">
              <div class="fw-bold text-white text-truncate" style="font-size: 0.85rem;">{{ Auth::user()->name }}</div>
              <div style="font-size: 0.7rem; color: #94a3b8;">{{ '@' . Auth::user()->username }}</div>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-light p-1 border-0" data-bs-toggle="modal" data-bs-target="#profileModal" title="Pengaturan Profil">
            <i class="bi bi-gear-fill"></i>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
      <!-- Topbar -->
      <header class="admin-topbar">
        <div class="topbar-left">
          <button class="btn-toggle-sidebar" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
            <i class="bi bi-list"></i>
          </button>
          <div>
            <h2 class="h5 mb-0 fw-bold text-dark">@yield('page-title', 'Dashboard')</h2>
            <small class="text-muted" style="font-size: 0.75rem;">@yield('page-subtitle', 'Ringkasan & Pengelolaan Dokumen')</small>
          </div>
        </div>

        <div class="d-flex align-items-center gap-3">
          <div class="dropdown">
            <button class="btn btn-light d-flex align-items-center gap-2 rounded-pill px-3 py-1 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="badge bg-teal rounded-circle p-1" style="background-color: #0d9488; width: 10px; height: 10px; display: inline-block;"></span>
              <span class="fw-semibold text-dark small">{{ Auth::user()->name }}</span>
              <i class="bi bi-chevron-down text-muted small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
              <li class="px-3 py-2 border-bottom">
                <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                <div class="small text-muted">{{ Auth::user()->email }}</div>
                <span class="badge bg-success mt-1" style="font-size: 0.65rem;">{{ strtoupper(Auth::user()->role ?? 'ADMIN') }}</span>
              </li>
              <li>
                <button class="dropdown-item py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#profileModal">
                  <i class="bi bi-person-gear text-primary"></i> Edit Profil & Password
                </button>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="dropdown-item text-danger py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Keluar (Logout)
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="content-area">
        <!-- Flash Alerts -->
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-3 shadow-sm border-0" role="alert" style="background-color: #ecfdf5; color: #065f46;">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 rounded-3 shadow-sm border-0" role="alert" style="background-color: #fef2f2; color: #991b1b;">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Terjadi kesalahan:</div>
            <ul class="mb-0 ps-3 small">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @yield('content')
      </main>

      <!-- Footer -->
      <footer class="py-3 px-4 border-top bg-white text-muted small d-flex flex-wrap justify-content-between align-items-center">
        <div>&copy; {{ date('Y') }} <strong>ShareFile Server</strong> - Panel Administrasi Terpusat</div>
        <div>Laravel v{{ app()->version() }} &bull; PHP v{{ PHP_VERSION }}</div>
      </footer>
    </div>
  </div>

  <!-- Profile & Password Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('admin.profile.update') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="profileModalLabel"><i class="bi bi-person-circle me-2 text-teal"></i> Pengaturan Akun Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label small fw-semibold">Nama Lengkap</label>
              <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">Username</label>
              <input type="text" name="username" class="form-control" value="{{ Auth::user()->username }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">Alamat Email</label>
              <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
            </div>
            <hr class="my-3 text-muted">
            <p class="small text-muted mb-2"><i class="bi bi-shield-lock me-1"></i> Ubah Password (Kosongkan bila tidak ingin diubah)</p>
            <div class="mb-3">
              <label class="form-label small fw-semibold">Password Saat Ini</label>
              <input type="password" name="current_password" class="form-control" placeholder="Masukkan password saat ini jika ingin ganti password">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">Password Baru</label>
              <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">Ulangi Password Baru</label>
              <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #0d9488; border: none;">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('adminSidebar');
      const backdrop = document.getElementById('sidebarBackdrop');
      sidebar.classList.toggle('show');
      backdrop.classList.toggle('show');
    }
  </script>
  @stack('scripts')
</body>
</html>
