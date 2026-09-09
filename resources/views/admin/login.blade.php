<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - ShareFile</title>

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.svg') }}" rel="icon" type="image/svg+xml">
  <link href="{{ asset('assets/img/favicon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap & Bootstrap Icons -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

  <!-- Admin Login Custom CSS -->
  <link href="{{ asset('assets/css/admin-login.css') }}" rel="stylesheet">
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      <div class="text-center mb-4">
        <div class="brand-badge">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1 class="h4 fw-bold text-dark mb-1">Admin Panel</h1>
        <p class="text-muted small">Masuk untuk mengelola dokumen dan sistem ShareFile</p>
      </div>

      <!-- Flash Message -->
      @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 rounded-3 small mb-3 border-0" style="background-color: #ecfdf5; color: #065f46;">
          <i class="bi bi-check-circle-fill"></i>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 rounded-3 small mb-3 border-0" style="background-color: #fef2f2; color: #991b1b;">
          <i class="bi bi-exclamation-circle-fill"></i>
          <div>{{ $errors->first() }}</div>
        </div>
      @endif

      <form action="{{ route('admin.login.submit') }}" method="POST">
        @csrf

        <div class="mb-3">
          <label class="form-label small fw-semibold text-secondary">Username atau Email</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
              <i class="bi bi-person-fill"></i>
            </span>
            <input 
              type="text" 
              name="login" 
              class="form-control rounded-end-3 py-2 border-start-0 @error('login') is-invalid @enderror" 
              placeholder="Username atau email" 
              value="{{ old('login') }}" 
              required 
              autofocus>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-semibold text-secondary">Password</label>
          <div class="input-group position-relative">
            <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
              <i class="bi bi-key-fill"></i>
            </span>
            <input 
              type="password" 
              name="password" 
              id="passwordInput"
              class="form-control rounded-end-3 py-2 border-start-0 pe-5 @error('password') is-invalid @enderror" 
              placeholder="Password admin" 
              required>
            <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility()" aria-label="Lihat Password">
              <i class="bi bi-eye" id="toggleIcon"></i>
            </button>
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
            <label class="form-check-label small text-muted" for="rememberMe">
              Ingat Saya
            </label>
          </div>
          <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.72rem;">Role: Administrator</span>
        </div>

        <button type="submit" class="btn btn-login w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
          <span>Masuk ke Dashboard</span>
          <i class="bi bi-arrow-right-short fs-5"></i>
        </button>
      </form>

      <div class="text-center mt-4">
        <a href="{{ url('/') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
          <i class="bi bi-arrow-left"></i> Kembali ke Beranda Utama
        </a>
      </div>
    </div>

    <div class="text-center mt-3 text-muted small">
      &copy; {{ date('Y') }} ShareFile Application System
    </div>
  </div>

  <script>
    function togglePasswordVisibility() {
      const input = document.getElementById('passwordInput');
      const icon = document.getElementById('toggleIcon');
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
</body>
</html>
