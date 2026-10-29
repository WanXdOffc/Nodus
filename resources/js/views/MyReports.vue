<template>
  <div class="h-screen w-screen overflow-hidden flex flex-col lg:flex-row bg-slate-50 text-text-primary font-sans">
    <!-- Sidebar Kiri -->
    <Sidebar @open-report="reportModalOpen = true" />

    <!-- Area Utama Konten Laporanku -->
    <main class="flex-1 flex flex-col h-full overflow-hidden p-6 sm:p-8 space-y-6">
      <!-- Header Halaman -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/60">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-mint-wash text-brand-teal text-[11px] font-bold mb-1">
            <span class="w-2 h-2 rounded-full bg-brand-teal animate-pulse"></span>
            Manajemen Laporan Pribadi
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight">
            Laporanku
          </h1>
          <p class="text-xs text-text-secondary mt-0.5">
            Kelola dan pantau status seluruh barang hilang maupun temuan yang telah Anda laporkan.
          </p>
        </div>

        <!-- Tombol Aksi & Filter -->
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1 bg-white p-1 rounded-2xl border border-slate-200 shadow-xs">
            <button
              @click="activeFilter = 'all'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all"
              :class="activeFilter === 'all' ? 'bg-navy-900 text-white' : 'text-slate-600 hover:bg-slate-100'"
            >
              Semua ({{ myItems.length }})
            </button>
            <button
              @click="activeFilter = 'lost'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all"
              :class="activeFilter === 'lost' ? 'bg-brand-orange text-white' : 'text-slate-600 hover:bg-slate-100'"
            >
              Kehilangan
            </button>
            <button
              @click="activeFilter = 'found'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all"
              :class="activeFilter === 'found' ? 'bg-brand-teal text-white' : 'text-slate-600 hover:bg-slate-100'"
            >
              Temuan
            </button>
          </div>

          <router-link
            to="/dashboard"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-teal hover:bg-[#00ab94] text-white text-xs font-bold rounded-xl shadow-xs transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Buat Laporan</span>
          </router-link>
        </div>
      </div>

      <!-- Feed / List Laporan Pribadi -->
      <div class="flex-1 overflow-y-auto space-y-4 pr-1">
        <div v-if="loading" class="text-center py-16 text-xs text-slate-400">
          Mengambil data laporan Anda...
        </div>

        <div
          v-else-if="!filteredMyItems.length"
          class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-200 p-8 space-y-3"
        >
          <div class="w-14 h-14 rounded-2xl bg-mint-wash text-brand-teal mx-auto flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <h3 class="text-sm font-bold text-navy-900">Belum Ada Laporan</h3>
          <p class="text-xs text-text-secondary max-w-sm mx-auto">
            Anda belum membuat laporan pada kategori ini. Laporan baru akan otomatis dicocokkan oleh algoritma AI Nodus.
          </p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="item in filteredMyItems"
            :key="item.id"
            class="bg-white rounded-3xl p-5 border border-slate-200/70 shadow-xs hover:shadow-card transition-all flex flex-col justify-between space-y-4"
          >
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span
                  class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full"
                  :class="item.type === 'lost' ? 'bg-orange-100 text-brand-orange' : 'bg-mint-wash text-brand-teal'"
                >
                  {{ item.type === 'lost' ? 'Kehilangan' : 'Ditemukan' }}
                </span>

                <span
                  class="text-[10px] font-semibold px-2 py-0.5 rounded-md"
                  :class="[
                    item.status === 'matched' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' :
                    item.status === 'resolved' ? 'bg-slate-100 text-slate-500' :
                    'bg-sky-50 text-sky-600 border border-sky-200'
                  ]"
                >
                  {{ item.status.toUpperCase() }}
                </span>
              </div>

              <div>
                <h4 class="text-sm font-bold text-navy-900 line-clamp-1">{{ item.title }}</h4>
                <p class="text-xs text-text-secondary line-clamp-2 mt-1 leading-relaxed">{{ item.description }}</p>
              </div>

              <div class="text-[11px] text-slate-500 space-y-1 pt-1">
                <p class="flex items-center gap-1.5">
                  <span>📍</span>
                  <span class="truncate">{{ item.location_name }}</span>
                </p>
                <p class="flex items-center gap-1.5 text-[10px] text-slate-400">
                  <span>📅 Dilaporkan: {{ formatDate(item.item_date) }}</span>
                </p>
              </div>

              <!-- AI Tags -->
              <div v-if="item.ai_tags && item.ai_tags.length" class="flex flex-wrap gap-1 pt-1">
                <span
                  v-for="tag in item.ai_tags.slice(0, 3)"
                  :key="tag"
                  class="text-[10px] text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100"
                >
                  #{{ tag }}
                </span>
              </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
              <span class="text-[11px] text-slate-400">Kategori: {{ item.category || 'Umum' }}</span>
              <router-link
                to="/dashboard"
                class="text-xs font-bold text-brand-teal hover:underline flex items-center gap-1"
              >
                <span>Lihat Radar</span>
                <span>&rarr;</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Report User -->
    <div
      v-if="reportModalOpen"
      class="fixed inset-0 z-50 bg-navy-900/40 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-base font-bold text-navy-900 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
            Laporkan Pengguna (Report User)
          </h3>
          <button @click="reportModalOpen = false" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <p class="text-xs text-text-secondary">
          Bantu kami menjaga keamanan komunitas Nodus. Laporan Anda akan ditinjau langsung oleh tim admin.
        </p>
        <form @submit.prevent="submitReport" class="space-y-3.5">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">ID Pengguna yang Dilaporkan</label>
            <input
              v-model="reportForm.reported_user_id"
              type="number"
              placeholder="Contoh: 2"
              required
              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pelaporan</label>
            <textarea
              v-model="reportForm.reason"
              rows="3"
              placeholder="Jelaskan alasan pelaporan..."
              required
              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
            ></textarea>
          </div>
          <div class="flex items-center justify-end gap-2 pt-2">
            <button
              type="button"
              @click="reportModalOpen = false"
              class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm"
            >
              Kirim Laporan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useAppStore } from '@/store/appStore';
import Sidebar from '@/components/Sidebar.vue';

const store = useAppStore();
const loading = ref(false);
const activeFilter = ref('all');
const reportModalOpen = ref(false);
const reportForm = ref({ reported_user_id: '', reason: '' });

onMounted(async () => {
  loading.value = true;
  if (!store.user) {
    await store.fetchCurrentUser();
  }
  await store.fetchItems();
  loading.value = false;
});

const myItems = computed(() => {
  const currentUserId = store.user?.id;
  if (!currentUserId) return store.items;
  return store.items.filter((item) => item.user_id === currentUserId);
});

const filteredMyItems = computed(() => {
  if (activeFilter.value === 'all') return myItems.value;
  return myItems.value.filter((i) => i.type === activeFilter.value);
});

function formatDate(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

async function submitReport() {
  if (!reportForm.value.reported_user_id || !reportForm.value.reason) return;
  try {
    await store.submitUserReport(reportForm.value.reported_user_id, reportForm.value.reason);
    reportForm.value = { reported_user_id: '', reason: '' };
    reportModalOpen.value = false;
  } catch (e) {
    // handled
  }
}
</script>
