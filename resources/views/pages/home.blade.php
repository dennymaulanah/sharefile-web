@extends('layouts.app')
@section('content')

<style>
/* Calm & Attractive Colors */
.hero-calm {
    background: linear-gradient(135deg, #e0f7fa 0%, #e8f5e9 100%);
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}
.hero-calm::after {
    content: '';
    position: absolute;
    bottom: -50px;
    left: 0;
    width: 100%;
    height: 100px;
    background: #ffffff;
    transform: skewY(-2deg);
    z-index: 1;
}
.hero-calm .container {
    position: relative;
    z-index: 2;
}
.hero-calm h1 {
    color: #006064;
    font-weight: 700;
    font-size: 3rem;
    line-height: 1.2;
}
.hero-calm p {
    color: #004d40;
    font-size: 1.15rem;
    opacity: 0.85;
}
.img-calm {
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 150, 136, 0.15);
    transition: transform 0.3s ease;
}
.img-calm:hover {
    transform: translateY(-10px);
}
.section-title h2 {
    color: #00796b;
}
.section-title span {
    color: #4db6ac;
}
.about-calm {
    background: #ffffff;
    padding: 80px 0;
}
.about-calm h3 {
    color: #00695c;
    font-weight: 600;
    margin-bottom: 20px;
}
.about-calm p {
    color: #546e7a;
    line-height: 1.8;
}
.btn-calm {
    background: linear-gradient(135deg, #26a69a, #4db6ac);
    color: white;
    border: none;
    transition: all 0.3s;
}
.btn-calm:hover {
    background: linear-gradient(135deg, #00897b, #26a69a);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(38, 166, 154, 0.4);
}
</style>

<!-- Hero Section -->
<section id="hero" class="hero-calm section">
  <div class="container">
    <div class="row gy-4 align-items-center">
      <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
        <h1>Bidang Perikanan Budidaya</h1>
        <p class="mt-3">Selamat datang di portal resmi Perikanan Budidaya dari Dinas Perikanan dan Kelautan Kabupaten Indramayu. Kami berkomitmen untuk memajukan sektor budidaya perikanan yang berkelanjutan dan menyejahterakan masyarakat pesisir.</p>
        <div class="d-flex mt-4">
              <a href="{{ url('/data-budidaya') }}" class="btn btn-calm rounded-pill px-4 py-2 shadow-sm fw-medium"><i class="bi bi-cloud-arrow-up-fill me-2"></i> Mulai Berbagi Data</a>
        </div>
      </div>
      <div class="col-lg-6 order-1 order-lg-2 text-center" data-aos="zoom-out" data-aos-delay="100">
        <img src="{{ asset('assets/img/aquaculture_hero.jpg') }}" class="img-fluid img-calm" alt="Ilustrasi Perikanan Budidaya">
      </div>
    </div>
  </div>
</section>

<!-- About / Budidaya Section -->
<section id="budidaya" class="about-calm section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <span>Aktivitas Kami</span>
    <h2>Pengembangan Budidaya</h2>
  </div><!-- End Section Title -->

  <div class="container">
    <div class="row gy-5 align-items-center">
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
        <img src="{{ asset('assets/img/aquaculture_about.jpg') }}" class="img-fluid img-calm" alt="Aktivitas Perikanan">
      </div>
      <div class="col-lg-6 content ps-lg-5" data-aos="fade-up" data-aos-delay="200">
        <h3>Ekosistem yang Sehat dan Produktif</h3>
        <p>
          Kami memantau secara berkala kondisi perairan dan lahan budidaya untuk memastikan hasil panen yang maksimal dengan tetap menjaga keseimbangan ekosistem alam yang berkelanjutan.
        </p>
        <ul class="list-unstyled mt-4">
          <li class="d-flex mb-3"><i class="bi bi-check-circle-fill fs-5 me-3" style="color: #26a69a !important;"></i> <span>Penerapan teknologi modern dan ramah lingkungan pada tambak.</span></li>
          <li class="d-flex mb-3"><i class="bi bi-check-circle-fill fs-5 me-3" style="color: #26a69a !important;"></i> <span>Pemberdayaan dan pendampingan Kelompok Pembudidaya Ikan (Pokdakan).</span></li>
          <li class="d-flex mb-3"><i class="bi bi-check-circle-fill fs-5 me-3" style="color: #26a69a !important;"></i> <span>Sistem pelaporan, manajemen, dan pengelolaan data yang terintegrasi.</span></li>
        </ul>
        <p class="mt-4">
          Dengan integrasi pendataan melalui portal <strong>Share Budidaya</strong>, setiap kegiatan operasional dapat dipantau, dievaluasi, dan dikembangkan secara lebih transparan dan efisien.
        </p>
      </div>
    </div>
  </div>
</section>

@endsection