<template>
  <div class="h-screen w-screen overflow-hidden flex flex-col lg:flex-row bg-slate-50 text-text-primary font-sans">
    <!-- Sidebar Kiri -->
    <Sidebar @open-report="reportModalOpen = true" />

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
            Dokumentasi, kunci otentikasi API, dan playground interaktif integrasi sistem kampus.
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
            class="w-full py-3 px-5 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-60 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"
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
              <p class="text-[11px] text-text-secondary">Anda memiliki otorisasi untuk mengakses API Publik Interoperabilitas NODUS v1.0.</p>
            </div>
          </div>
          <span class="text-[11px] text-emerald-700 bg-emerald-100 font-bold px-3 py-1 rounded-full border border-emerald-200">
            Sanctum Bearer Token
          </span>
        </div>

        <!-- Section 1: Kunci API & Terminal Code Block -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-sm space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-bold text-navy-900">Kredensial &amp; API Token</h3>
              <p class="text-xs text-text-secondary">Gunakan token otentikasi Sanctum Anda pada header Authorization Bearer.</p>
            </div>
            <button
              @click="copyToken"
              class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
              </svg>
              <span>{{ copiedToken ? 'Disalin!' : 'Salin Token' }}</span>
            </button>
          </div>

          <!-- UI Code Block Bergaya Terminal Gelap -->
          <div class="bg-slate-950 rounded-2xl p-4 font-mono text-xs text-slate-200 shadow-inner border border-slate-800 overflow-x-auto">
            <!-- Terminal Header -->
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800 text-slate-500 text-[11px]">
              <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                <span class="ml-2 font-semibold text-slate-400">bash — nodus api authentication</span>
              </div>
              <span class="text-[10px] text-slate-500">bearer auth</span>
            </div>

            <!-- Terminal Content -->
            <div class="space-y-2">
              <div class="text-slate-500"># 1. Simpan Token ke Environment Variable</div>
              <div class="text-emerald-400 break-all select-all">
                <span class="text-purple-400">export</span> NODUS_API_TOKEN="<span class="text-amber-300">{{ apiToken || 'login-to-get-sanctum-token' }}</span>"
              </div>

              <div class="text-slate-500 pt-2"># 2. Contoh Request menggunakan cURL</div>
              <div class="text-slate-300 leading-relaxed whitespace-pre overflow-x-auto select-all">curl -X GET "{{ apiBaseUrl }}/api/public/items?limit=10" \
  -H "Authorization: Bearer $NODUS_API_TOKEN" \
  -H "Accept: application/json"</div>
            </div>
          </div>
        </div>

        <!-- Section 2: Dokumentasi & Interactive Playground -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-sm space-y-5">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">
                  GET
                </span>
                <code class="text-xs font-bold text-navy-900 font-mono">/api/public/items</code>
              </div>
              <h3 class="text-sm font-bold text-navy-900">Public Items &amp; Statistics API</h3>
              <p class="text-xs text-text-secondary mt-0.5">
                Mengembalikan agregat statistik dan riwayat barang resolved tanpa data sensitif/pribadi.
              </p>
            </div>

            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="runTestEndpoint"
                :disabled="testingEndpoint"
                class="px-4 py-2 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5"
              >
                <div v-if="testingEndpoint" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ testingEndpoint ? 'Memanggil API...' : 'Test Endpoint' }}</span>
              </button>
            </div>
          </div>

          <!-- Parameter Playground Controls -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200/60">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Limit Data (1-50):</label>
              <input
                v-model.number="playgroundLimit"
                type="number"
                min="1"
                max="50"
                class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-brand-teal"
              />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Filter Tipe:</label>
              <select
                v-model="playgroundType"
                class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-brand-teal"
              >
                <option value="">Semua (Lost &amp; Found)</option>
                <option value="lost">Lost (Hilang)</option>
                <option value="found">Found (Temuan)</option>
              </select>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Filter Kategori (Opsional):</label>
              <input
                v-model="playgroundCategory"
                type="text"
                placeholder="Contoh: Dompet &amp; Tas"
                class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-brand-teal"
              />
            </div>
          </div>

          <!-- Live Output JSON Mentah -->
          <div class="space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-navy-900 flex items-center gap-2">
                <span>Response Output</span>
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
              </span>

              <button
                v-if="rawResponse"
                @click="copyResponseJson"
                class="text-[11px] font-semibold text-brand-teal hover:underline flex items-center gap-1"
              >
                <span>{{ copiedJson ? 'JSON Disalin!' : 'Salin JSON' }}</span>
              </button>
            </div>

            <!-- Terminal Output Window -->
            <div class="bg-slate-950 rounded-2xl p-4 font-mono text-xs text-emerald-400 shadow-inner border border-slate-800 max-h-[380px] overflow-y-auto">
              <pre v-if="rawResponse" class="whitespace-pre-wrap leading-relaxed">{{ rawResponse }}</pre>
              <div v-else class="text-center py-12 text-slate-500 font-sans text-xs">
                Klik tombol <strong class="text-brand-teal font-mono">Test Endpoint</strong> di atas untuk menjalankan request dan melihat respon JSON mentah.
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Report User / Fraud (Opsional) -->
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

const playgroundLimit = ref(5);
const playgroundType = ref('');
const playgroundCategory = ref('');
const testingEndpoint = ref(false);
const rawResponse = ref('');
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
  responseStatus.value = null;
  responseStatusText.value = '';
  executionTime.value = null;

  const startTime = performance.now();

  try {
    const params = {
      limit: playgroundLimit.value || 5,
    };
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
    store.notify('Request API Publik berhasil dieksekusi!', 'success');
  } catch (err) {
    const endTime = performance.now();
    executionTime.value = Math.round(endTime - startTime);
    responseStatus.value = err.response?.status || 500;
    responseStatusText.value = err.response?.statusText || 'Error';
    rawResponse.value = JSON.stringify(err.response?.data || { error: err.message }, null, 2);
    store.notify('Panggilan endpoint menghasilkan galat (error).', 'error');
  } finally {
    testingEndpoint.value = false;
  }
}
</script>
