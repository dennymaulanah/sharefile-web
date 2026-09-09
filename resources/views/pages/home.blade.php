@extends('layouts.app')
@section('content')

@push('styles')
  <link href="{{ asset('assets/css/home.css') }}" rel="stylesheet">
@endpush

<!-- Hero Section -->
<section id="hero" class="hero-calm section">
  <div class="container">
    <div class="row gy-4 align-items-center">
      <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
        <h1>ShareFile</h1>
        <p class="mt-3">Selamat datang di ShareFile, platform manajemen dokumen terpusat yang memudahkan Anda menyimpan, memantau, dan membagikan berkas secara aman dan efisien. Terorganisir, transparan, dan mudah diakses dari mana saja.</p>
        <div class="d-flex mt-4">
              <a href="{{ url('/data-File') }}" class="btn btn-calm rounded-pill px-4 py-2 shadow-sm fw-medium"><i class="bi bi-cloud-arrow-up-fill me-2"></i> Mulai Kelola File</a>
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
          <li class="d-flex mb-3"><i class="bi bi-check-circle-fill fs-5 me-3" style="color: #26a69a !important;"></i> <span>Integrasi canggih dengan Office Lokal (Word, Excel) & Sinkronisasi Server Otomatis.</span></li>
        </ul>
        <p class="mt-4">
          Dengan integrasi pendataan melalui portal <strong>ShareFile</strong>, setiap pencarian dan kolaborasi dokumen dapat dilakukan secara lebih transparan, aman, dan efisien.
        </p>
      </div>
    </div>
  </div>
</section>

@endsection
