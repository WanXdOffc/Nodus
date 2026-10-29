<template>
  <div class="min-h-screen bg-surface-white font-sans text-text-primary selection:bg-brand-teal selection:text-white">
    <!-- Navbar Melayang -->
    <Navbar />

    <!-- Hero Section -->
    <section id="hero" class="relative pt-32 sm:pt-40 pb-20 sm:pb-28 overflow-hidden">
      <!-- Background subtle gradient glow -->
      <div class="absolute top-10 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-mint-wash/60 blur-3xl -z-10 rounded-full pointer-events-none"></div>

      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10 items-center">
          <!-- Kolom Kiri: Headline & Call To Action -->
          <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-mint-wash border border-brand-teal/20 text-brand-teal text-xs sm:text-sm font-semibold tracking-wide">
              <span class="w-2 h-2 rounded-full bg-brand-teal animate-pulse"></span>
              Platform Pencarian Barang Cerdas Berbasis AI
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy-900 tracking-tight leading-[1.12]">
              Reconnecting <br class="hidden sm:inline" />
              <span class="text-brand-teal">What's Lost</span>
            </h1>

            <p class="text-base sm:text-lg text-text-secondary leading-relaxed max-w-xl mx-auto lg:mx-0">
              Temukan barang hilang atau bantu kembalikan barang temuan secara cepat dan terverifikasi. Didukung teknologi AI Gemini dan geolokasi pintar untuk akurasi pencocokan maksimal.
            </p>

            <!-- Dua Tombol CTA -->
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
              <!-- Tombol Lapor Kehilangan (Orange) -->
              <a
                href="#lapor-kehilangan"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand-orange hover:bg-[#e85b27] text-white font-semibold px-8 py-3.5 rounded-full shadow-lg shadow-orange-500/25 hover:shadow-orange-500/35 hover:-translate-y-0.5 transition-all duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Lapor Kehilangan</span>
              </a>

              <!-- Tombol Lapor Ditemukan (Outline Teal) -->
              <a
                href="#lapor-ditemukan"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border-2 border-brand-teal text-brand-teal hover:bg-mint-wash font-semibold px-8 py-3.5 rounded-full bg-white transition-all duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Lapor Ditemukan</span>
              </a>
            </div>

            <!-- Trust highlights -->
            <div class="pt-4 flex items-center justify-center lg:justify-start gap-6 text-xs text-text-secondary">
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Privasi Terjaga</span>
              </div>
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Sistem Verifikasi Tiket</span>
              </div>
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Komunitas Terpercaya</span>
              </div>
            </div>
          </div>

          <!-- Kolom Kanan: Placeholder Mockup UI AI (rounded-3xl, bg-slate-100) -->
          <div class="lg:col-span-5">
            <div class="relative w-full aspect-[4/5] sm:aspect-square rounded-3xl bg-slate-100 border border-slate-200/80 p-6 sm:p-8 flex flex-col justify-between overflow-hidden shadow-card group">
              <!-- Decorative background glow -->
              <div class="absolute -top-16 -right-16 w-52 h-52 bg-brand-teal/15 rounded-full blur-2xl"></div>
              <div class="absolute -bottom-16 -left-16 w-52 h-52 bg-brand-orange/10 rounded-full blur-2xl"></div>

              <!-- Header Card Mockup -->
              <div class="relative z-10 flex items-center justify-between bg-white/90 backdrop-blur-sm px-4 py-3 rounded-2xl border border-slate-200/60 shadow-sm">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-brand-teal text-white flex items-center justify-center font-bold text-xs">
                    AI
                  </div>
                  <div>
                    <p class="text-xs font-semibold text-navy-900">Gemini Match Engine</p>
                    <p class="text-[10px] text-text-secondary">Pencocokan Real-Time Aktif</p>
                  </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-mint-wash text-brand-teal">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-teal animate-ping"></span>
                  SCANNING
                </span>
              </div>

              <!-- Center Interactive Visual Mockup -->
              <div class="relative z-10 my-auto space-y-4">
                <!-- Match Preview Card -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-brand-orange bg-orange-50 px-2.5 py-0.5 rounded-full">
                      Lost Item Match
                    </span>
                    <span class="text-xs font-bold text-brand-teal">
                      94% Match Score
                    </span>
                  </div>
                  <div class="space-y-1">
                    <p class="text-sm font-bold text-navy-900">Dompet Kulit Hitam Fossil</p>
                    <p class="text-xs text-text-secondary line-clamp-2">
                      Ditemukan di dekat koridor perpustakaan lantai 2 dengan gantungan kunci perak.
                    </p>
                  </div>
                  <!-- Tags -->
                  <div class="flex flex-wrap gap-1.5 pt-1">
                    <span class="text-[10px] font-medium bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">#dompet</span>
                    <span class="text-[10px] font-medium bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">#kulit</span>
                    <span class="text-[10px] font-medium bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">#hitam</span>
                    <span class="text-[10px] font-medium bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">#fossil</span>
                  </div>
                </div>

                <!-- Match Progress Bar -->
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 border border-slate-200/60 flex items-center justify-between text-xs">
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-brand-teal"></div>
                    <span class="text-slate-600 font-medium">Tiket Klaim Dibuat</span>
                  </div>
                  <span class="font-bold text-navy-900">#TK-2894</span>
                </div>
              </div>

              <!-- Footer Placeholder Note -->
              <div class="relative z-10 text-center">
                <span class="text-[11px] font-medium text-slate-400">
                  Tempat Mockup UI AI & Peta Interaktif
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Bar (Full Width, Dark Navy bg-navy-900, Teks Putih) -->
    <section class="w-full bg-navy-900 text-white py-12 sm:py-16">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 text-center divide-y md:divide-y-0 md:divide-x divide-slate-800">
          <!-- Stat 1 -->
          <div class="pt-6 md:pt-0">
            <p class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-brand-teal tracking-tight">
              98%
            </p>
            <p class="text-sm sm:text-base font-medium text-slate-300 mt-2">
              Match Rate
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
              Akurasi pencocokan semantik AI
            </p>
          </div>

          <!-- Stat 2 -->
          <div class="pt-6 md:pt-0">
            <p class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight">
              12.4k
            </p>
            <p class="text-sm sm:text-base font-medium text-slate-300 mt-2">
              Items Returned
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
              Barang berhasil dikembalikan
            </p>
          </div>

          <!-- Stat 3 -->
          <div class="pt-6 md:pt-0">
            <p class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-brand-teal tracking-tight">
              &lt; 2 Jam
            </p>
            <p class="text-sm sm:text-base font-medium text-slate-300 mt-2">
              Rata-rata Temu
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
              Waktu deteksi kecocokan awal
            </p>
          </div>

          <!-- Stat 4 -->
          <div class="pt-6 md:pt-0">
            <p class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight">
              100%
            </p>
            <p class="text-sm sm:text-base font-medium text-slate-300 mt-2">
              Secure Claim
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
              Klaim terverifikasi & terenkripsi
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Grid (3 Kolom Bento-Box, Latar Putih, Soft Shadow) -->
    <section id="cara-kerja" class="py-20 sm:py-28 bg-slate-50/50">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
          <span class="inline-block text-xs font-bold uppercase tracking-wider text-brand-teal bg-mint-wash px-3.5 py-1.5 rounded-full">
            Cara Kerja Nodus
          </span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">
            Tiga Langkah Mudah Menemukan Barang
          </h2>
          <p class="text-base text-text-secondary leading-relaxed">
            Alur pelaporan terintegrasi dengan kecerdasan buatan untuk memastikan barang kembali ke pemilik sah dengan aman.
          </p>
        </div>

        <!-- 3 Bento-Box Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Bento 1: Report -->
          <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-card border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
            <div>
              <div class="w-12 h-12 rounded-2xl bg-orange-50 text-brand-orange flex items-center justify-center font-bold text-lg mb-6 group-hover:scale-110 transition-transform">
                01
              </div>
              <h3 class="text-xl font-bold text-navy-900 mb-3">
                Report (Laporkan)
              </h3>
              <p class="text-sm text-text-secondary leading-relaxed mb-6">
                Unggah detail barang yang hilang atau Anda temukan. Sertakan foto, perkiraan lokasi, dan tanggal kejadian dengan formulir yang mudah.
              </p>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-brand-orange font-semibold">
              <span>Formulir Cepat 1 Menit</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>

          <!-- Bento 2: AI Match -->
          <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-card border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
            <div>
              <div class="w-12 h-12 rounded-2xl bg-mint-wash text-brand-teal flex items-center justify-center font-bold text-lg mb-6 group-hover:scale-110 transition-transform">
                02
              </div>
              <h3 class="text-xl font-bold text-navy-900 mb-3">
                AI Match (Pencocokan)
              </h3>
              <p class="text-sm text-text-secondary leading-relaxed mb-6">
                Algoritma Gemini AI mengekstrak kata kunci semantik dan mengomparasi database barang hilang vs temuan secara otomatis dalam hitungan detik.
              </p>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-brand-teal font-semibold">
              <span>Akurasi &gt; 60% Auto Ticket</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>

          <!-- Bento 3: Claim -->
          <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-card border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
            <div>
              <div class="w-12 h-12 rounded-2xl bg-slate-100 text-navy-900 flex items-center justify-center font-bold text-lg mb-6 group-hover:scale-110 transition-transform">
                03
              </div>
              <h3 class="text-xl font-bold text-navy-900 mb-3">
                Claim (Klaim Aman)
              </h3>
              <p class="text-sm text-text-secondary leading-relaxed mb-6">
                Kedua belah pihak berkomunikasi lewat ruang pesan terenkripsi. Verifikasi kepemilikan sebelum serah terima dilakukan tanpa menyebarkan nomor pribadi.
              </p>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-navy-900 font-semibold">
              <span>Secure Ticket Chat</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Keamanan & Trust Score Section -->
    <section id="keamanan" class="py-20 sm:py-24 bg-white">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="bg-slate-50 border border-slate-200/60 rounded-3xl p-8 sm:p-14 flex flex-col lg:flex-row items-center justify-between gap-8">
          <div class="space-y-4 max-w-xl text-center lg:text-left">
            <span class="text-xs font-bold uppercase tracking-wider text-brand-teal bg-mint-wash px-3.5 py-1.5 rounded-full">
              Sistem Kepercayaan Pengguna
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight">
              Dilindungi Indikator Trust Score & Moderasi Terpadu
            </h2>
            <p class="text-sm sm:text-base text-text-secondary leading-relaxed">
              Setiap pengguna memiliki peringkat kepercayaan (Trust Score) yang dihitung dari riwayat pengembalian barang dan laporan pengguna lain untuk menjamin lingkungan komunitas yang aman.
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 text-center flex-1 sm:w-44">
              <p class="text-3xl font-extrabold text-brand-teal">5.0</p>
              <p class="text-xs font-medium text-slate-500 mt-1">Starting Trust Score</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 text-center flex-1 sm:w-44">
              <p class="text-3xl font-extrabold text-navy-900">24/7</p>
              <p class="text-xs font-medium text-slate-500 mt-1">Admin Moderation</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer Minimalis (Dark Navy bg-navy-900) -->
    <footer class="w-full bg-navy-900 text-slate-400 py-12 sm:py-16 border-t border-slate-800">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8 pb-8 border-b border-slate-800">
          <!-- Logo & Deskripsi -->
          <div class="space-y-3 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-2">
              <div class="w-7 h-7 rounded-full bg-brand-teal/20 flex items-center justify-center">
                <span class="w-2.5 h-2.5 rounded-full bg-brand-teal"></span>
              </div>
              <span class="text-xl font-bold tracking-wider text-white">NODUS</span>
            </div>
            <p class="text-xs text-slate-400 max-w-sm">
              Platform pintar lost and found untuk menghubungkan barang hilang dengan penemunya secara aman dan transparan.
            </p>
          </div>

          <!-- Tautan Cepat -->
          <div class="flex items-center gap-8 text-xs font-medium">
            <a href="#hero" class="hover:text-white transition-colors">Home</a>
            <a href="#cara-kerja" class="hover:text-white transition-colors">Cara Kerja</a>
            <a href="#keamanan" class="hover:text-white transition-colors">Keamanan</a>
            <a href="/admin" class="hover:text-white transition-colors">Admin Portal</a>
          </div>
        </div>

        <!-- Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
          <p>&copy; 2026 Nodus Platform. All rights reserved.</p>
          <p class="flex items-center gap-4">
            <span>Kebijakan Privasi</span>
            <span>&bull;</span>
            <span>Syarat &amp; Ketentuan</span>
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
</script>
