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
        <h1>ShareFile</h1>
        <p class="mt-3">Selamat datang di ShareFile, platform manajemen dokumen terpusat yang memudahkan Anda menyimpan, memantau, dan membagikan berkas secara aman dan efisien. Terorganisir, transparan, dan mudah diakses dari mana saja.</p>
        <div class="d-flex mt-4">
              <a href="{{ url('/data-file') }}" class="btn btn-calm rounded-pill px-4 py-2 shadow-sm fw-medium"><i class="bi bi-cloud-arrow-up-fill me-2"></i> Mulai Kelola File</a>
        </div>
      </div>
      <div class="col-lg-6 order-1 order-lg-2 text-center" data-aos="zoom-out" data-aos-delay="100">
        <img src="{{ asset('assets/img/sharefile_hero.svg') }}" class="img-fluid img-calm" alt="Ilustrasi ShareFile">
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="about-calm section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <span>Fitur Kami</span>
    <h2>Mengapa Memilih ShareFile?</h2>
  </div><!-- End Section Title -->

  <div class="container">
    <div class="row gy-5 align-items-center">
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
        <img src="{{ asset('assets/img/sharefile_about.svg') }}" class="img-fluid img-calm" alt="Aktivitas ShareFile">
      </div>
      <div class="col-lg-6 content ps-lg-5" data-aos="fade-up" data-aos-delay="200">
        <h3>Ekosistem Dokumen yang Cerdas & Terorganisir</h3>
        <p>
          Kami memastikan seluruh dokumen Anda tersimpan dengan rapi, mudah dicari, dan dapat dikelola kapan saja tanpa kendala, meningkatkan produktivitas tim dan individu.
        </p>
        <ul class="list-unstyled mt-4">
          <li class="d-flex mb-3"><i class="bi bi-check-circle-fill fs-5 me-3" style="color: #26a69a !important;"></i> <span>Unggah file super cepat melalui mekanisme drag-and-drop langsung dari OS Anda.</span></li>
          <li class="d-flex mb-3"><i class="bi bi-check-circle-fill fs-5 me-3" style="color: #26a69a !important;"></i> <span>Sistem manajemen folder tak terbatas untuk pengelompokan arsip yang optimal.</span></li>
          <li class="d-flex mb-3"><i class="bi bi-check-circle-fill fs-5 me-3" style="color: #26a69a !important;"></i> <span>Integrasi canggih dengan Office Lokal (Word, Excel) dan Editor Web internal.</span></li>
        </ul>
        <p class="mt-4">
          Dengan integrasi pendataan melalui portal <strong>ShareFile</strong>, setiap pencarian dan kolaborasi dokumen dapat dilakukan secara lebih transparan, aman, dan efisien.
        </p>
      </div>
    </div>
  </div>
</section>

@endsection
