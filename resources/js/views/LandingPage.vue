<template>
  <div class="min-h-screen bg-surface-white font-sans text-text-primary selection:bg-brand-teal selection:text-white">
    <!-- Navbar Melayang -->
    <Navbar @open-login="authModalOpen = true" />

    <!-- Auth Modal -->
    <AuthModal v-model="authModalOpen" />

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

            <!-- Dua Tombol CTA Menuju Dashboard / Lapor -->
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
              <!-- Tombol Lapor Kehilangan (Orange) -->
              <button
                type="button"
                id="btn-lapor-kehilangan"
                @click="handleActionClick('lost')"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand-orange hover:bg-[#e85b27] text-white font-semibold px-8 py-3.5 rounded-full shadow-lg shadow-orange-500/25 hover:shadow-orange-500/35 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Lapor Kehilangan</span>
              </button>

              <!-- Tombol Lapor Ditemukan (Outline Teal) -->
              <button
                type="button"
                id="btn-lapor-ditemukan"
                @click="handleActionClick('found')"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border-2 border-brand-teal text-brand-teal hover:bg-mint-wash font-semibold px-8 py-3.5 rounded-full bg-white transition-all duration-200 cursor-pointer"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Lapor Ditemukan</span>
              </button>
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

    <!-- Section Riwayat Sukses (Clean Background Off-White) -->
    <section id="riwayat-sukses" class="py-20 sm:py-28 bg-slate-50 border-b border-slate-200/60">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-4">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Riwayat Sukses Terbaru
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">
            Kisah Rekonsiliasi Barang yang Telah Selesai
          </h2>
          <p class="text-sm sm:text-base text-text-secondary leading-relaxed">
            Setiap barang yang berhasil kembali adalah bukti nyata kekuatan komunitas yang saling peduli dan akurasi pencocokan AI.
          </p>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="historyLoading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="i in 3" :key="i" class="bg-white rounded-3xl p-5 border border-slate-100 shadow-card animate-pulse space-y-4">
            <div class="w-full aspect-[4/3] bg-slate-200 rounded-2xl"></div>
            <div class="h-4 bg-slate-200 rounded-md w-3/4"></div>
            <div class="h-3 bg-slate-200 rounded-md w-1/2"></div>
          </div>
        </div>

        <!-- Horizontal Grid Kartu Barang -->
        <div v-else-if="historyItems.length">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="item in displayedHistoryItems"
              :key="item.id"
              class="bg-white rounded-3xl p-5 border border-slate-100 shadow-card hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group"
            >
              <div>
                <!-- Gambar Foto Barang Asli -->
                <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 mb-4 flex items-center justify-center">
                  <img
                    v-if="item.image_path"
                    :src="item.image_path.startsWith('http') ? item.image_path : '/storage/' + item.image_path"
                    :alt="item.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                  <img
                    v-else
                    src="https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80"
                    :alt="item.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />

                  <!-- Badge Status Resolved -->
                  <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] font-bold text-emerald-700 shadow-xs border border-emerald-100 flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Reunited</span>
                  </div>

                  <div class="absolute top-3 right-3 bg-navy-900/80 backdrop-blur-sm px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white uppercase">
                    {{ item.category }}
                  </div>
                </div>

                <!-- Judul Barang & Lokasi -->
                <h3 class="text-base font-extrabold text-navy-900 group-hover:text-brand-teal transition-colors line-clamp-1">
                  {{ item.title }}
                </h3>
                <p class="text-xs text-text-secondary mt-1 flex items-center gap-1 line-clamp-1">
                  <span>📍</span>
                  <span>{{ item.location_name || 'Singaraja, Buleleng' }}</span>
                </p>
              </div>

              <!-- Meta Waktu Selesai -->
              <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-text-secondary font-medium">
                  ⏱️ Berhasil dipertemukan {{ item.resolved_human }}
                </span>
                <span class="text-[11px] font-bold text-brand-teal bg-mint-wash px-2.5 py-0.5 rounded-full">
                  Selesai
                </span>
              </div>
            </div>
          </div>

          <!-- Tombol Show More / Show Less Riwayat -->
          <div v-if="historyItems.length > 3" class="mt-10 text-center">
            <button
              type="button"
              @click="showAllHistory = !showAllHistory"
              class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold border border-slate-200 shadow-sm hover:shadow transition-all group"
            >
              <span>{{ showAllHistory ? 'Tampilkan Lebih Sedikit (Show Less)' : `Lihat Semua Riwayat (${historyItems.length}) (Show More)` }}</span>
              <svg
                class="w-4 h-4 text-brand-teal transition-transform duration-200"
                :class="showAllHistory ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Fallback jika belum ada data riwayat di database (Foto Asli) -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-card hover:shadow-xl transition-all flex flex-col justify-between group">
            <div>
              <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 mb-4">
                <img
                  src="https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&w=600&q=80"
                  alt="Dompet Kulit Pria"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-2.5 py-0.5 rounded-full text-[10px] font-bold text-emerald-700 shadow-xs border border-emerald-100">Reunited</div>
                <div class="absolute top-3 right-3 bg-navy-900/80 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white uppercase">Dompet &amp; Aksesoris</div>
              </div>
              <h3 class="text-base font-extrabold text-navy-900">Dompet Kulit Pria</h3>
              <p class="text-xs text-text-secondary mt-1">📍 Taman Kota Singaraja</p>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 text-xs text-brand-teal font-semibold flex items-center justify-between">
              <span>⏱️ Dipertemukan dalam 4 jam</span>
              <span class="text-[11px] font-bold text-brand-teal bg-mint-wash px-2 py-0.5 rounded-full">Selesai</span>
            </div>
          </div>

          <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-card hover:shadow-xl transition-all flex flex-col justify-between group">
            <div>
              <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 mb-4">
                <img
                  src="https://images.unsplash.com/photo-1589134763158-7c8a6fcf7c61?auto=format&fit=crop&w=600&q=80"
                  alt="Kunci Motor Smartkey"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-2.5 py-0.5 rounded-full text-[10px] font-bold text-emerald-700 shadow-xs border border-emerald-100">Reunited</div>
                <div class="absolute top-3 right-3 bg-navy-900/80 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white uppercase">Kunci &amp; Kendaraan</div>
              </div>
              <h3 class="text-base font-extrabold text-navy-900">Kunci Motor Vario Smartkey</h3>
              <p class="text-xs text-text-secondary mt-1">📍 Pantai Penimbangan, Singaraja</p>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 text-xs text-brand-teal font-semibold flex items-center justify-between">
              <span>⏱️ Dipertemukan dalam 6 jam</span>
              <span class="text-[11px] font-bold text-brand-teal bg-mint-wash px-2 py-0.5 rounded-full">Selesai</span>
            </div>
          </div>

          <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-card hover:shadow-xl transition-all flex flex-col justify-between group">
            <div>
              <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 mb-4">
                <img
                  src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=600&q=80"
                  alt="Smartphone Android"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-2.5 py-0.5 rounded-full text-[10px] font-bold text-emerald-700 shadow-xs border border-emerald-100">Reunited</div>
                <div class="absolute top-3 right-3 bg-navy-900/80 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white uppercase">Elektronik &amp; Gadget</div>
              </div>
              <h3 class="text-base font-extrabold text-navy-900">Smartphone Android</h3>
              <p class="text-xs text-text-secondary mt-1">📍 Perpustakaan Buleleng</p>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 text-xs text-brand-teal font-semibold flex items-center justify-between">
              <span>⏱️ Dipertemukan dalam 12 jam</span>
              <span class="text-[11px] font-bold text-brand-teal bg-mint-wash px-2 py-0.5 rounded-full">Selesai</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Grid (3 Kolom Bento-Box, Latar Putih, Soft Shadow) -->
    <section id="cara-kerja" class="py-20 sm:py-28 bg-white">
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

    <!-- Section Testimoni (Data Real dari API /api/reviews/featured) -->
    <section id="testimoni" class="py-20 sm:py-28 bg-white border-b border-slate-100">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-orange-50 text-brand-orange text-xs font-bold">
            <svg class="w-3.5 h-3.5 fill-brand-orange" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Ulasan Komunitas Nodus
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">
            Apa Kata Pengguna yang Telah Terbantu
          </h2>
          <p class="text-sm sm:text-base text-text-secondary leading-relaxed">
            Testimoni otentik dari pengguna yang menemukan dan mengembalikan barang dengan aman melalui Nodus.
          </p>
        </div>

        <!-- Real Testimonials Cards -->
        <div v-if="reviewsLoading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="i in 3" :key="i" class="bg-slate-50 rounded-3xl p-6 border border-slate-100 animate-pulse space-y-4">
            <div class="h-4 bg-slate-200 rounded w-1/3"></div>
            <div class="h-12 bg-slate-200 rounded"></div>
            <div class="h-6 bg-slate-200 rounded w-1/2"></div>
          </div>
        </div>

        <div v-else-if="featuredReviews.length">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
              v-for="rev in displayedReviews"
              :key="rev.id"
              class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 shadow-card hover:shadow-lg transition-all duration-300 flex flex-col justify-between"
            >
              <div class="space-y-4">
                <!-- Bintang Rating -->
                <div class="flex items-center gap-1">
                  <svg
                    v-for="star in 5"
                    :key="star"
                    class="w-4 h-4"
                    :class="star <= rev.rating ? 'text-brand-orange fill-brand-orange' : 'text-slate-200 fill-slate-200'"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  <span class="text-xs font-bold text-slate-700 ml-1.5">({{ rev.rating }}.0)</span>
                </div>

                <!-- Komentar Pengguna -->
                <p class="text-sm text-slate-700 leading-relaxed italic">
                  "{{ rev.comment }}"
                </p>
              </div>

              <!-- Profil Penulis Ulasan -->
              <div class="pt-6 mt-6 border-t border-slate-200/80 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-teal text-white flex items-center justify-center font-bold text-xs">
                  {{ rev.user?.name ? rev.user.name.charAt(0).toUpperCase() : 'U' }}
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-bold text-navy-900 truncate">{{ rev.user?.name || 'Pengguna Nodus' }}</p>
                  <p class="text-[10px] text-brand-teal font-semibold">
                    ★ {{ rev.user?.trust_score || '5.0' }} Trust Score &bull; Terverifikasi
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Tombol Show More / Show Less Testimoni -->
          <div v-if="featuredReviews.length > 3" class="mt-10 text-center">
            <button
              type="button"
              @click="showAllReviews = !showAllReviews"
              class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-slate-50 hover:bg-slate-100 text-navy-900 text-xs font-bold border border-slate-200 shadow-sm hover:shadow transition-all group"
            >
              <span>{{ showAllReviews ? 'Tampilkan Lebih Sedikit (Show Less)' : `Lihat Semua Ulasan (${featuredReviews.length}) (Show More)` }}</span>
              <svg
                class="w-4 h-4 text-brand-orange transition-transform duration-200"
                :class="showAllReviews ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Fallback Default Testimoni jika belum ada review featured di database -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 shadow-card flex flex-col justify-between">
            <div class="space-y-4">
              <div class="flex items-center gap-1 text-brand-orange">
                <span v-for="i in 5" :key="i">★</span>
                <span class="text-xs font-bold text-slate-700 ml-1.5">(5.0)</span>
              </div>
              <p class="text-sm text-slate-700 leading-relaxed italic">
                "Dompet saya yang jatuh di Pantai Penimbangan kembali hanya dalam waktu 3 jam berkat pencocokan AI Nodus. Luar biasa aman dan terpercaya!"
              </p>
            </div>
            <div class="pt-6 mt-6 border-t border-slate-200/80 flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-brand-teal text-white flex items-center justify-center font-bold text-xs">D</div>
              <div>
                <p class="text-xs font-bold text-navy-900">Dharmawan</p>
                <p class="text-[10px] text-brand-teal font-semibold">★ 5.0 Trust Score &bull; Terverifikasi</p>
              </div>
            </div>
          </div>

          <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 shadow-card flex flex-col justify-between">
            <div class="space-y-4">
              <div class="flex items-center gap-1 text-brand-orange">
                <span v-for="i in 5" :key="i">★</span>
                <span class="text-xs font-bold text-slate-700 ml-1.5">(5.0)</span>
              </div>
              <p class="text-sm text-slate-700 leading-relaxed italic">
                "Fitur chat terenkripsi dan verifikasi kepemilikan sangat membantu. Saya tidak perlu khawatir nomor pribadi tersebar saat mengembalikan tas."
              </p>
            </div>
            <div class="pt-6 mt-6 border-t border-slate-200/80 flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-brand-orange text-white flex items-center justify-center font-bold text-xs">A</div>
              <div>
                <p class="text-xs font-bold text-navy-900">Aryasuta</p>
                <p class="text-[10px] text-brand-teal font-semibold">★ 5.0 Trust Score &bull; Terverifikasi</p>
              </div>
            </div>
          </div>

          <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 shadow-card flex flex-col justify-between">
            <div class="space-y-4">
              <div class="flex items-center gap-1 text-brand-orange">
                <span v-for="i in 5" :key="i">★</span>
                <span class="text-xs font-bold text-slate-700 ml-1.5">(5.0)</span>
              </div>
              <p class="text-sm text-slate-700 leading-relaxed italic">
                "Sistem reputasi dinamis membuat orang terdorong untuk jujur. Senang sekali kunci motor saya bisa ketemu dengan cepat!"
              </p>
            </div>
            <div class="pt-6 mt-6 border-t border-slate-200/80 flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-navy-900 text-white flex items-center justify-center font-bold text-xs">P</div>
              <div>
                <p class="text-xs font-bold text-navy-900">Putu Ayu</p>
                <p class="text-[10px] text-brand-teal font-semibold">★ 5.0 Trust Score &bull; Terverifikasi</p>
              </div>
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
            <div class="flex items-center justify-center md:justify-start gap-2.5">
              <LogoN class="w-7 h-7" />
              <span class="text-xl font-bold tracking-wider text-white">NODUS</span>
            </div>
            <p class="text-xs text-slate-400 max-w-sm">
              Platform pintar lost and found untuk menghubungkan barang hilang dengan penemunya secara aman dan transparan.
            </p>
          </div>

          <!-- Tautan Cepat -->
          <div class="flex items-center gap-8 text-xs font-medium">
            <a href="#hero" class="hover:text-white transition-colors">Home</a>
            <a href="#riwayat-sukses" class="hover:text-white transition-colors">Riwayat</a>
            <a href="#cara-kerja" class="hover:text-white transition-colors">Cara Kerja</a>
            <a href="#testimoni" class="hover:text-white transition-colors">Testimoni</a>
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
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAppStore } from '@/store/appStore';
import Navbar from '@/components/Navbar.vue';
import AuthModal from '@/components/AuthModal.vue';
import LogoN from '@/components/LogoN.vue';

const route = useRoute();
const router = useRouter();
const store = useAppStore();
const authModalOpen = ref(false);

function handleActionClick(type) {
  const token = store.token || localStorage.getItem('nodus_token');
  if (token) {
    router.push({ path: '/dashboard', query: { report: type } });
  } else {
    sessionStorage.setItem('nodus_redirect_report', type);
    authModalOpen.value = true;
  }
}

const historyItems = ref([]);
const historyLoading = ref(true);
const showAllHistory = ref(false);

const displayedHistoryItems = computed(() => {
  return showAllHistory.value ? historyItems.value : historyItems.value.slice(0, 3);
});

const featuredReviews = ref([]);
const reviewsLoading = ref(true);
const showAllReviews = ref(false);

const displayedReviews = computed(() => {
  return showAllReviews.value ? featuredReviews.value : featuredReviews.value.slice(0, 3);
});

onMounted(async () => {
  if (route.query.login === '1') {
    authModalOpen.value = true;
  }

  fetchLandingData();
});

async function fetchLandingData() {
  try {
    const [historyRes, reviewsRes] = await Promise.allSettled([
      axios.get('/api/items/history'),
      axios.get('/api/reviews/featured'),
    ]);

    if (historyRes.status === 'fulfilled') {
      historyItems.value = historyRes.value.data || [];
    }
    if (reviewsRes.status === 'fulfilled') {
      featuredReviews.value = reviewsRes.value.data || [];
    }
  } catch (err) {
    console.error('Failed to fetch landing data:', err);
  } finally {
    historyLoading.value = false;
    reviewsLoading.value = false;
  }
}

watch(
  () => route.query.login,
  (val) => {
    if (val === '1') {
      authModalOpen.value = true;
    }
  }
);
</script>
