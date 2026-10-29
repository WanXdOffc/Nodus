<template>
  <div class="h-screen w-screen overflow-hidden flex flex-col lg:flex-row bg-slate-50 text-text-primary font-sans">
    <!-- Sidebar Kiri -->
    <Sidebar />

    <!-- Area Konten Utama Portal Developer -->
    <main class="flex-1 flex flex-col h-full overflow-hidden p-6 sm:p-8 space-y-6">
      <!-- Header Halaman -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/60 flex-shrink-0">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-mint-wash text-brand-teal text-[11px] font-bold mb-1">
            <span class="w-2 h-2 rounded-full bg-brand-teal animate-pulse"></span>
            Interoperabilitas &amp; Developer Hub
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2.5">
            <span>Developer API Portal</span>
            <span
              v-if="isDeveloper"
              class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-extrabold border border-emerald-200"
            >
              Active
            </span>
            <span
              v-else
              class="text-xs px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-600 font-extrabold border border-slate-300"
            >
              Restricted
            </span>
          </h1>
          <p class="text-xs text-text-secondary mt-0.5">
            Dokumentasi ramah pemula, kunci otentikasi API, dan playground interaktif untuk integrasi sistem kampus.
          </p>
        </div>

        <!-- Tombol Aksi Cepat / Status -->
        <div class="flex items-center gap-2">
          <router-link
            to="/dashboard"
            class="px-3.5 py-2 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 shadow-2xs transition-all flex items-center gap-1.5"
          >
            <span>&larr; Kembali ke Dashboard</span>
          </router-link>
        </div>
      </div>

      <!-- STATE 1: is_developer === false (Belum Memiliki Akses) -->
      <div
        v-if="!isDeveloper"
        class="flex-1 overflow-y-auto flex items-center justify-center p-4"
      >
        <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-slate-200/80 shadow-card text-center space-y-6">
          <!-- Ilustrasi Ikon Kunci / Terminal -->
          <div class="w-20 h-20 rounded-3xl bg-slate-100 text-slate-600 flex items-center justify-center mx-auto border border-slate-200 shadow-inner">
            <svg class="w-10 h-10 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
          </div>

          <div class="space-y-2">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-3 py-1 rounded-full">
              Izin Khusus Dibutuhkan
            </span>
            <h2 class="text-xl font-extrabold text-navy-900 tracking-tight">
              Akses Developer API Belum Aktif
            </h2>
            <p class="text-xs text-text-secondary leading-relaxed">
              Akun Anda belum terdaftar sebagai pengembang (developer). Dapatkan akses kunci API untuk integrasi sistem informasi kampus, sinkronisasi data temuan, dan statistik resolusi NODUS.
            </p>
          </div>

          <!-- Alert Petunjuk -->
          <div class="p-4 bg-teal-50/70 border border-teal-100/90 rounded-2xl text-left space-y-1.5 text-xs text-teal-950">
            <p class="font-bold flex items-center gap-1.5 text-brand-teal">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Alur Verifikasi Developer:
            </p>
            <p class="text-[11px] text-teal-800 leading-relaxed">
              1. Klik tombol di bawah untuk mengajukan permohonan akses.<br>
              2. Tim Administrator akan memverifikasi identitas pengembang atau unit kampus Anda.<br>
              3. Setelah disetujui, halaman ini otomatis menjadi portal API dan Playground aktif.
            </p>
          </div>

          <!-- Tombol Request Developer Access -->
          <button
            type="button"
            @click="handleRequestAccess"
            :disabled="requesting || requestSent"
            class="w-full py-3 px-5 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-60 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ requestSent ? 'Permintaan Terkirim (Menunggu Review)' : 'Request Developer Access' }}</span>
          </button>
        </div>
      </div>

      <!-- STATE 2: is_developer === true (Portal API & Playground Aktif) -->
      <div v-else class="flex-1 overflow-y-auto space-y-6 pr-1">
        <!-- Banner Sukses Developer -->
        <div class="bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-transparent border border-emerald-200/80 rounded-2xl p-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <div>
              <p class="text-xs font-bold text-navy-900">Akses Pengembang Aktif</p>
              <p class="text-[11px] text-text-secondary">Anda memiliki otorisasi penuh untuk mengakses API Publik Interoperabilitas NODUS v1.0.</p>
            </div>
          </div>
          <span class="text-[11px] text-emerald-700 bg-emerald-100 font-bold px-3 py-1 rounded-full border border-emerald-200">
            Sanctum Bearer Token
          </span>
        </div>

        <!-- PANDUAN CEPAT ORANG AWAM (Quickstart 3 Langkah) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-sm space-y-4">
          <div class="space-y-1">
            <h3 class="text-sm font-bold text-navy-900 flex items-center gap-2">
              <span>💡 Panduan Mudah: Apa itu Developer API NODUS?</span>
            </h3>
            <p class="text-xs text-text-secondary leading-relaxed">
              API ini berfungsi sebagai jembatan data agar web kampus, mading digital, bot WhatsApp/Telegram, atau sistem satpam dapat membaca daftar barang hilang dan temuan secara otomatis tanpa harus membuka web satu per satu.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
              <div class="w-6 h-6 rounded-full bg-brand-teal text-white flex items-center justify-center text-xs font-bold">1</div>
              <p class="text-xs font-bold text-navy-900">Gunakan API Token</p>
              <p class="text-[11px] text-text-secondary leading-relaxed">
                Salin token Sanctum Anda di bawah sebagai kunci izin agar server mengenali aplikasi Anda.
              </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
              <div class="w-6 h-6 rounded-full bg-brand-orange text-white flex items-center justify-center text-xs font-bold">2</div>
              <p class="text-xs font-bold text-navy-900">Panggil Endpoint</p>
              <p class="text-[11px] text-text-secondary leading-relaxed">
                Kirim HTTP GET ke <code class="text-brand-teal font-bold">/api/public/items</code> menggunakan aplikasi atau skrip favorit Anda.
              </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
              <div class="w-6 h-6 rounded-full bg-navy-900 text-white flex items-center justify-center text-xs font-bold">3</div>
              <p class="text-xs font-bold text-navy-900">Terima Data Real-Time</p>
              <p class="text-[11px] text-text-secondary leading-relaxed">
                Server mengembalikan data barang publik dan statistik teragregasi yang siap Anda tampilkan.
              </p>
            </div>
          </div>
        </div>

        <!-- Section 1: Kunci API & Terminal Code Block -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-sm space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-bold text-navy-900">Kredensial &amp; API Token Anda</h3>
              <p class="text-xs text-text-secondary">Sertakan token ini pada header <code class="bg-slate-100 px-1 py-0.5 rounded text-[11px]">Authorization: Bearer &lt;token&gt;</code>.</p>
            </div>
            <button
              @click="copyToken"
              class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5 cursor-pointer"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
              </svg>
              <span>{{ copiedToken ? 'Disalin!' : 'Salin Token' }}</span>
            </button>
          </div>

          <!-- UI Code Block Bergaya Terminal Gelap dengan Pilihan Bahasa -->
          <div class="bg-slate-950 rounded-2xl p-4 font-mono text-xs text-slate-200 shadow-inner border border-slate-800 overflow-x-auto">
            <!-- Terminal Header & Language Tabs -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-3 border-b border-slate-800 gap-2">
              <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                <span class="ml-2 font-semibold text-slate-400">code snippet — {{ activeCodeTab.toUpperCase() }}</span>
              </div>
              <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 self-start">
                <button
                  type="button"
                  @click="activeCodeTab = 'curl'"
                  class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold transition-all"
                  :class="activeCodeTab === 'curl' ? 'bg-brand-teal text-white' : 'text-slate-400 hover:text-white'"
                >
                  cURL
                </button>
                <button
                  type="button"
                  @click="activeCodeTab = 'javascript'"
                  class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold transition-all"
                  :class="activeCodeTab === 'javascript' ? 'bg-brand-teal text-white' : 'text-slate-400 hover:text-white'"
                >
                  JavaScript
                </button>
                <button
                  type="button"
                  @click="activeCodeTab = 'python'"
                  class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold transition-all"
                  :class="activeCodeTab === 'python' ? 'bg-brand-teal text-white' : 'text-slate-400 hover:text-white'"
                >
                  Python
                </button>
                <button
                  type="button"
                  @click="activeCodeTab = 'php'"
                  class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold transition-all"
                  :class="activeCodeTab === 'php' ? 'bg-brand-teal text-white' : 'text-slate-400 hover:text-white'"
                >
                  PHP
                </button>
              </div>
            </div>

            <!-- Terminal Snippets -->
            <div class="space-y-2">
              <div v-if="activeCodeTab === 'curl'" class="text-slate-300 leading-relaxed whitespace-pre overflow-x-auto select-all">curl -X GET "{{ apiBaseUrl }}/api/public/items?limit=10" \
  -H "Authorization: Bearer {{ apiToken }}" \
  -H "Accept: application/json"</div>

              <div v-else-if="activeCodeTab === 'javascript'" class="text-slate-300 leading-relaxed whitespace-pre overflow-x-auto select-all">// Ambil data barang publik dengan Fetch API
const response = await fetch("{{ apiBaseUrl }}/api/public/items?limit=10", {
  headers: {
    "Authorization": "Bearer {{ apiToken }}",
    "Accept": "application/json"
  }
});
const data = await response.json();
console.log("Barang Ditemukan:", data.items);</div>

              <div v-else-if="activeCodeTab === 'python'" class="text-slate-300 leading-relaxed whitespace-pre overflow-x-auto select-all">import requests

url = "{{ apiBaseUrl }}/api/public/items"
headers = {
    "Authorization": "Bearer {{ apiToken }}",
    "Accept": "application/json"
}
params = {"limit": 10}

response = requests.get(url, headers=headers, params=params)
print(response.json())</div>

              <div v-else-if="activeCodeTab === 'php'" class="text-slate-300 leading-relaxed whitespace-pre overflow-x-auto select-all">&lt;?php
$ch = curl_init("{{ apiBaseUrl }}/api/public/items?limit=10");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {{ apiToken }}",
    "Accept: application/json"
]);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

print_r($response['items']);</div>
            </div>
          </div>
        </div>

        <!-- Section 2: Tabel Kamus Parameter Query (Super Ramah Orang Awam) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-sm space-y-4">
          <div class="space-y-1">
            <h3 class="text-sm font-bold text-navy-900">📖 Kamus Parameter Permintaan (Query Options)</h3>
            <p class="text-xs text-text-secondary">Anda dapat menambahkan parameter di ujung URL untuk menyaring barang yang ingin ditampilkan.</p>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border border-slate-200/80 rounded-2xl overflow-hidden">
              <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200/80">
                <tr>
                  <th class="p-3">Parameter</th>
                  <th class="p-3">Tipe</th>
                  <th class="p-3">Pilihan Nilai</th>
                  <th class="p-3">Penjelasan Sederhana</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-600">
                <tr>
                  <td class="p-3 font-mono font-bold text-brand-teal">limit</td>
                  <td class="p-3">Angka</td>
                  <td class="p-3">1 - 50 (Default: 15)</td>
                  <td class="p-3">Menentukan berapa banyak kartu barang yang ingin diambil dalam satu kali permintaan.</td>
                </tr>
                <tr>
                  <td class="p-3 font-mono font-bold text-brand-teal">status</td>
                  <td class="p-3">Teks</td>
                  <td class="p-3"><code class="bg-slate-100 px-1 py-0.5 rounded">all</code>, <code class="bg-slate-100 px-1 py-0.5 rounded">searching</code>, <code class="bg-slate-100 px-1 py-0.5 rounded">matched</code>, <code class="bg-slate-100 px-1 py-0.5 rounded">resolved</code></td>
                  <td class="p-3">Menyaring barang berdasarkan statusnya (contoh: hanya yang masih dicari, atau yang sudah berhasil dikembalikan).</td>
                </tr>
                <tr>
                  <td class="p-3 font-mono font-bold text-brand-teal">type</td>
                  <td class="p-3">Teks</td>
                  <td class="p-3"><code class="bg-slate-100 px-1 py-0.5 rounded">lost</code> atau <code class="bg-slate-100 px-1 py-0.5 rounded">found</code></td>
                  <td class="p-3">Memilih hanya barang hilang (<code class="text-brand-orange font-bold">lost</code>) atau hanya barang temuan (<code class="text-brand-teal font-bold">found</code>).</td>
                </tr>
                <tr>
                  <td class="p-3 font-mono font-bold text-brand-teal">category</td>
                  <td class="p-3">Teks</td>
                  <td class="p-3">Kunci &amp; Kendaraan, Dompet &amp; Tas, dll.</td>
                  <td class="p-3">Menyaring barang sesuai kategori benda.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Section 3: Interactive Playground & Visual Response Cards -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-sm space-y-5">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">
                  GET
                </span>
                <code class="text-xs font-bold text-navy-900 font-mono">/api/public/items</code>
              </div>
              <h3 class="text-sm font-bold text-navy-900">Uji Coba Langsung (API Playground)</h3>
              <p class="text-xs text-text-secondary mt-0.5">
                Pilih filter di bawah lalu klik "Test Endpoint" untuk melihat respons nyata dari server NODUS.
              </p>
            </div>

            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="runTestEndpoint"
                :disabled="testingEndpoint"
                class="px-5 py-2.5 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer"
              >
                <div v-if="testingEndpoint" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ testingEndpoint ? 'Memanggil API...' : 'Test Endpoint Sekarang' }}</span>
              </button>
            </div>
          </div>

          <!-- Parameter Playground Controls -->
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200/60">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Limit Data (1-50):</label>
              <input
                v-model.number="playgroundLimit"
                type="number"
                min="1"
                max="50"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-brand-teal"
              />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Filter Status:</label>
              <select
                v-model="playgroundStatus"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-brand-teal"
              >
                <option value="">Semua Status Publik</option>
                <option value="searching">Sedang Dicari (searching)</option>
                <option value="matched">Cocok AI (matched)</option>
                <option value="resolved">Selesai (resolved)</option>
              </select>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Filter Tipe:</label>
              <select
                v-model="playgroundType"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-brand-teal"
              >
                <option value="">Semua (Lost &amp; Found)</option>
                <option value="lost">Lost (Hilang)</option>
                <option value="found">Found (Temuan)</option>
              </select>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Filter Kategori:</label>
              <input
                v-model="playgroundCategory"
                type="text"
                placeholder="Contoh: Dompet &amp; Tas"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-brand-teal"
              />
            </div>
          </div>

          <!-- Tampilan Hasil Uji Coba: Toggle Visual Cards vs Raw JSON -->
          <div class="space-y-3">
            <div class="flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <span class="font-bold text-navy-900">Hasil Respon API:</span>
                <span
                  v-if="responseStatus"
                  class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                  :class="responseStatus === 200 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                >
                  HTTP {{ responseStatus }} {{ responseStatusText }}
                </span>
                <span v-if="executionTime" class="text-[10px] text-slate-400 font-normal">
                  ⚡ {{ executionTime }}ms
                </span>
                <span v-if="returnedItems.length" class="text-[11px] font-bold text-brand-teal">
                  ({{ returnedItems.length }} barang ditemukan)
                </span>
              </div>

              <!-- Switch Mode Tampilan -->
              <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
                <button
                  type="button"
                  @click="viewMode = 'visual'"
                  class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer"
                  :class="viewMode === 'visual' ? 'bg-white text-navy-900 shadow-2xs' : 'text-slate-500 hover:text-navy-900'"
                >
                  Pratinjau Visual (Orang Awam)
                </button>
                <button
                  type="button"
                  @click="viewMode = 'json'"
                  class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer"
                  :class="viewMode === 'json' ? 'bg-white text-navy-900 shadow-2xs' : 'text-slate-500 hover:text-navy-900'"
                >
                  JSON Mentah (Developer)
                </button>
              </div>
            </div>

            <!-- MODE 1: Visual Cards untuk orang awam -->
            <div v-if="viewMode === 'visual'">
              <div v-if="returnedItems.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <div
                  v-for="item in returnedItems"
                  :key="item.id"
                  class="bg-slate-50 rounded-2xl p-4 border border-slate-200 hover:border-brand-teal/40 transition-all space-y-2.5 shadow-2xs"
                >
                  <div class="flex items-center justify-between">
                    <span
                      class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full"
                      :class="item.type === 'lost' ? 'bg-orange-100 text-brand-orange' : 'bg-mint-wash text-brand-teal'"
                    >
                      {{ item.type_label || item.type }}
                    </span>
                    <span
                      class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                      :class="[
                        item.status === 'resolved' ? 'bg-emerald-100 text-emerald-700' :
                        item.status === 'matched' ? 'bg-indigo-100 text-indigo-700' :
                        'bg-amber-100 text-amber-700'
                      ]"
                    >
                      {{ item.status_label || item.status }}
                    </span>
                  </div>

                  <div>
                    <h4 class="text-xs font-bold text-navy-900 line-clamp-1">{{ item.title }}</h4>
                    <p class="text-[11px] text-text-secondary line-clamp-2 mt-0.5">{{ item.description }}</p>
                  </div>

                  <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[10px] text-slate-500">
                    <span class="flex items-center gap-1">
                      📍 <span>{{ item.location_area }}</span>
                    </span>
                    <span>📅 {{ item.item_date }}</span>
                  </div>
                </div>
              </div>

              <div
                v-else-if="rawResponse"
                class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-500 text-xs"
              >
                Tidak ada barang yang cocok dengan filter yang Anda masukkan. Coba ubah opsi filter menjadi "Semua".
              </div>

              <div
                v-else
                class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-500 text-xs"
              >
                Klik tombol <strong class="text-brand-teal font-semibold">Test Endpoint Sekarang</strong> di atas untuk memuat daftar barang.
              </div>
            </div>

            <!-- MODE 2: JSON Mentah untuk developer -->
            <div v-else class="space-y-2">
              <div class="flex justify-end">
                <button
                  v-if="rawResponse"
                  @click="copyResponseJson"
                  class="text-[11px] font-semibold text-brand-teal hover:underline flex items-center gap-1 cursor-pointer"
                >
                  <span>{{ copiedJson ? 'JSON Disalin!' : 'Salin JSON Mentah' }}</span>
                </button>
              </div>
              <div class="bg-slate-950 rounded-2xl p-4 font-mono text-xs text-emerald-400 shadow-inner border border-slate-800 max-h-[380px] overflow-y-auto">
                <pre v-if="rawResponse" class="whitespace-pre-wrap leading-relaxed">{{ rawResponse }}</pre>
                <div v-else class="text-center py-12 text-slate-500 font-sans text-xs">
                  Klik tombol <strong class="text-brand-teal font-mono">Test Endpoint Sekarang</strong> di atas untuk melihat respon JSON.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Report User (Opsional) -->
    <ReportUserModal
      v-model="reportModalOpen"
      :target-user="null"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useAppStore } from '@/store/appStore';
import Sidebar from '@/components/Sidebar.vue';
import ReportUserModal from '@/components/ReportUserModal.vue';

const store = useAppStore();
const reportModalOpen = ref(false);
const requesting = ref(false);
const requestSent = ref(false);
const copiedToken = ref(false);
const copiedJson = ref(false);

const activeCodeTab = ref('curl');
const viewMode = ref('visual'); // 'visual' atau 'json'
const playgroundLimit = ref(10);
const playgroundStatus = ref('');
const playgroundType = ref('');
const playgroundCategory = ref('');
const testingEndpoint = ref(false);
const rawResponse = ref('');
const returnedItems = ref([]);
const responseStatus = ref(null);
const responseStatusText = ref('');
const executionTime = ref(null);

onMounted(async () => {
  if (!store.user) {
    await store.fetchCurrentUser();
  }
});

const isDeveloper = computed(() => {
  return Boolean(store.user?.is_developer);
});

const apiToken = computed(() => {
  return store.token || localStorage.getItem('nodus_token') || 'nodus_sanctum_token_active';
});

const apiBaseUrl = computed(() => {
  return window.location.origin;
});

async function handleRequestAccess() {
  if (requesting.value || requestSent.value) return;
  requesting.value = true;
  try {
    const res = await axios.post('/api/user/request-developer');
    requestSent.value = true;
    store.notify(res.data.message || 'Permintaan akses developer berhasil dikirim ke Admin.', 'success');
  } catch (err) {
    store.notify('Gagal mengirimkan permintaan akses developer.', 'error');
  } finally {
    requesting.value = false;
  }
}

function copyToken() {
  if (!apiToken.value) return;
  navigator.clipboard.writeText(apiToken.value);
  copiedToken.value = true;
  store.notify('API Token berhasil disalin ke clipboard!', 'success');
  setTimeout(() => {
    copiedToken.value = false;
  }, 2000);
}

function copyResponseJson() {
  if (!rawResponse.value) return;
  navigator.clipboard.writeText(rawResponse.value);
  copiedJson.value = true;
  store.notify('Respon JSON berhasil disalin!', 'success');
  setTimeout(() => {
    copiedJson.value = false;
  }, 2000);
}

async function runTestEndpoint() {
  if (testingEndpoint.value) return;
  testingEndpoint.value = true;
  rawResponse.value = '';
  returnedItems.value = [];
  responseStatus.value = null;
  responseStatusText.value = '';
  executionTime.value = null;

  const startTime = performance.now();

  try {
    const params = {
      limit: playgroundLimit.value || 10,
    };
    if (playgroundStatus.value) {
      params.status = playgroundStatus.value;
    }
    if (playgroundType.value) {
      params.type = playgroundType.value;
    }
    if (playgroundCategory.value) {
      params.category = playgroundCategory.value;
    }

    const res = await axios.get('/api/public/items', {
      params,
      headers: {
        Authorization: `Bearer ${apiToken.value}`,
        Accept: 'application/json',
      },
    });

    const endTime = performance.now();
    executionTime.value = Math.round(endTime - startTime);
    responseStatus.value = res.status;
    responseStatusText.value = res.statusText || 'OK';

    rawResponse.value = JSON.stringify(res.data, null, 2);
    returnedItems.value = res.data.items || res.data.data || [];

    store.notify(`Berhasil mengambil ${returnedItems.value.length} barang dari API NODUS.`, 'success');
  } catch (err) {
    const endTime = performance.now();
    executionTime.value = Math.round(endTime - startTime);
    responseStatus.value = err.response?.status || 500;
    responseStatusText.value = err.response?.statusText || 'Error';
    rawResponse.value = JSON.stringify(err.response?.data || { error: err.message }, null, 2);
    returnedItems.value = [];
    store.notify('Gagal memanggil API: ' + (err.response?.data?.message || err.message), 'error');
  } finally {
    testingEndpoint.value = false;
  }
}
</script>
