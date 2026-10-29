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
            <button
              @click="activeFilter = 'charity'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all"
              :class="activeFilter === 'charity' ? 'bg-slate-700 text-white' : 'text-slate-600 hover:bg-slate-100'"
            >
              Charity
            </button>
          </div>

          <button
            type="button"
            @click="showReportModal = true"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-teal hover:bg-[#00ab94] text-white text-xs font-bold rounded-xl shadow-xs transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Buat Laporan</span>
          </button>
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
            class="rounded-3xl p-5 border transition-all flex flex-col justify-between space-y-4"
            :class="[
              item.status === 'expired_charity'
                ? 'bg-slate-50/90 border-slate-300 shadow-sm ring-1 ring-slate-200'
                : 'bg-white border-slate-200/70 shadow-xs hover:shadow-card'
            ]"
          >
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span
                  class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full"
                  :class="item.type === 'lost' ? 'bg-orange-100 text-brand-orange' : 'bg-mint-wash text-brand-teal'"
                >
                  {{ item.type === 'lost' ? 'Kehilangan' : 'Ditemukan' }}
                </span>

                <!-- Badge Status Abu-Abu untuk Expired Charity -->
                <span
                  class="text-[10px] font-semibold px-2.5 py-0.5 rounded-md"
                  :class="[
                    item.status === 'expired_charity' ? 'bg-slate-200 text-slate-700 border border-slate-300 font-bold' :
                    item.status === 'matched' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' :
                    item.status === 'resolved' ? 'bg-slate-100 text-slate-500' :
                    'bg-sky-50 text-sky-600 border border-sky-200'
                  ]"
                >
                  {{ item.status === 'expired_charity' ? 'EXPIRED CHARITY' : item.status.toUpperCase() }}
                </span>
              </div>

              <!-- Peringatan Khusus untuk Barang Expired Charity -->
              <div
                v-if="item.status === 'expired_charity'"
                class="p-3.5 bg-slate-200/80 border border-slate-300/90 rounded-2xl flex items-start gap-2.5 text-xs text-slate-800 leading-relaxed shadow-xs"
              >
                <div class="w-6 h-6 rounded-lg bg-slate-300 text-slate-700 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                  ⚠️
                </div>
                <div>
                  <span class="font-bold block text-slate-900 text-[11px] mb-0.5">Program Donasi &amp; Charity</span>
                  <p class="text-[11px] text-slate-700 leading-normal">
                    Masa pencarian 30 hari habis. Tolong serahkan barang ini ke Drop Point NODUS (Satpam Kampus) untuk program Charity.
                  </p>
                </div>
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

    <!-- Modal Buat Laporan Baru -->
    <CreateReportModal
      v-model="showReportModal"
      @created="handleItemCreated"
    />

    <!-- Modal Report User -->
    <ReportUserModal
      v-model="reportModalOpen"
      :target-user="selectedTargetUser"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useAppStore } from '@/store/appStore';
import Sidebar from '@/components/Sidebar.vue';
import CreateReportModal from '@/components/CreateReportModal.vue';
import ReportUserModal from '@/components/ReportUserModal.vue';

const store = useAppStore();
const loading = ref(false);
const activeFilter = ref('all');
const showReportModal = ref(false);
const reportModalOpen = ref(false);
const selectedTargetUser = ref(null);

onMounted(async () => {
  loading.value = true;
  if (!store.user) {
    await store.fetchCurrentUser();
  }
  await store.fetchItems({ user_id: store.user?.id, include_expired: true });
  loading.value = false;
});

const myItems = computed(() => {
  const currentUserId = store.user?.id;
  if (!currentUserId) return store.items;
  return store.items.filter((item) => item.user_id === currentUserId);
});

const filteredMyItems = computed(() => {
  if (activeFilter.value === 'all') return myItems.value;
  if (activeFilter.value === 'charity') return myItems.value.filter((i) => i.status === 'expired_charity');
  return myItems.value.filter((i) => i.type === activeFilter.value);
});

function formatDate(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function handleItemCreated() {
  store.fetchItems({ user_id: store.user?.id, include_expired: true });
}
</script>
