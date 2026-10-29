<template>
  <div class="h-screen w-screen overflow-hidden flex flex-col lg:flex-row bg-slate-50 text-text-primary font-sans">
    <!-- Toast Notification -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="store.toast.show"
        class="fixed top-5 right-5 z-50 px-4 py-3 rounded-2xl shadow-lg border text-sm font-semibold flex items-center gap-2"
        :class="[
          store.toast.type === 'error' ? 'bg-rose-50 border-rose-200 text-rose-800' :
          store.toast.type === 'info' ? 'bg-sky-50 border-sky-200 text-sky-800' :
          'bg-emerald-50 border-emerald-200 text-emerald-800'
        ]"
      >
        <span class="w-2 h-2 rounded-full" :class="store.toast.type === 'error' ? 'bg-rose-500' : 'bg-brand-teal'"></span>
        <span>{{ store.toast.message }}</span>
      </div>
    </transition>

    <!-- ========================================== -->
    <!-- KOLOM 1: SIDEBAR (20% Lebar Layar) -->
    <!-- ========================================== -->
    <Sidebar />

    <!-- ========================================== -->
    <!-- KOLOM 2: MAIN FEED (50% Lebar Layar) -->
    <!-- ========================================== -->
    <main class="flex-1 lg:w-[50%] bg-slate-50 flex flex-col h-full overflow-hidden p-5 sm:p-7 space-y-5">
      <!-- Bagian Atas: Ucapan Selamat Datang & 3 Widget Kotak Berjejer -->
      <div class="space-y-4 flex-shrink-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-navy-900 tracking-tight">
              Selamat datang kembali, {{ store.user?.name || 'Sobat Nodus' }}! 👋
            </h1>
            <p class="text-xs text-text-secondary mt-0.5">
              Pantau radar barang dan temukan kecocokan otomatis di area Singaraja.
            </p>
          </div>
          <!-- Quick Filter Buttons -->
          <div class="flex items-center gap-1.5 bg-white p-1 rounded-2xl border border-slate-200/60 shadow-xs self-start">
            <button
              @click="store.selectedTypeFilter = ''"
              class="px-3 py-1 rounded-xl text-xs font-semibold transition-all"
              :class="store.selectedTypeFilter === '' ? 'bg-navy-900 text-white' : 'text-slate-600 hover:bg-slate-100'"
            >
              Semua
            </button>
            <button
              @click="store.selectedTypeFilter = 'lost'"
              class="px-3 py-1 rounded-xl text-xs font-semibold transition-all"
              :class="store.selectedTypeFilter === 'lost' ? 'bg-brand-orange text-white' : 'text-slate-600 hover:bg-slate-100'"
            >
              Hilang
            </button>
            <button
              @click="store.selectedTypeFilter = 'found'"
              class="px-3 py-1 rounded-xl text-xs font-semibold transition-all"
              :class="store.selectedTypeFilter === 'found' ? 'bg-brand-teal text-white' : 'text-slate-600 hover:bg-slate-100'"
            >
              Temuan
            </button>
          </div>
        </div>

        <!-- 3 Widget Kotak Berjejer -->
        <div class="grid grid-cols-3 gap-3.5">
          <!-- Widget 1: Total Kehilangan -->
          <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-xs flex items-center justify-between">
            <div>
              <p class="text-[11px] font-semibold text-text-secondary">Total Kehilangan</p>
              <p class="text-xl sm:text-2xl font-extrabold text-brand-orange mt-0.5">{{ store.stats.totalLost }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-50 text-brand-orange flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Widget 2: Total Penemuan -->
          <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-xs flex items-center justify-between">
            <div>
              <p class="text-[11px] font-semibold text-text-secondary">Total Penemuan</p>
              <p class="text-xl sm:text-2xl font-extrabold text-brand-teal mt-0.5">{{ store.stats.totalFound }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-mint-wash text-brand-teal flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
          </div>

          <!-- Widget 3: Menunggu AI Match -->
          <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-xs flex items-center justify-between">
            <div>
              <p class="text-[11px] font-semibold text-text-secondary">Menunggu AI Match</p>
              <p class="text-xl sm:text-2xl font-extrabold text-navy-900 mt-0.5">{{ store.stats.waitingAi }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-navy-900 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Bagian Tengah (Map Component) -->
      <div class="flex-shrink-0">
        <MapWidget />
      </div>

      <!-- Bagian Bawah: Scrollable List untuk Kartu Barang (ItemCard.vue) -->
      <div class="flex-1 flex flex-col min-h-0">
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-xs font-bold text-navy-900 uppercase tracking-wider">
            Daftar Barang Terkini (Singaraja)
          </h3>
          <span class="text-[11px] text-text-secondary">
            {{ store.filteredItems.length }} Barang Terdeteksi
          </span>
        </div>

        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
          <div v-if="store.itemsLoading" class="text-center py-12 text-xs text-slate-400">
            Mengambil data barang...
          </div>
          <div v-else-if="!store.filteredItems.length" class="text-center py-12 bg-white rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
            Belum ada barang yang cocok dengan filter ini.
          </div>
          <ItemCard
            v-for="item in store.filteredItems"
            :key="item.id"
            :item="item"
            :isSelected="store.activeItem?.id === item.id"
            @select="handleSelectItem"
          />
        </div>
      </div>
    </main>

    <!-- ========================================== -->
    <!-- KOLOM 3: SECURE CLAIM TICKET (30% Lebar Layar) -->
    <!-- ========================================== -->
    <aside
      class="w-full lg:w-[30%] lg:min-w-[340px] h-full flex-shrink-0 transition-all duration-300"
      :class="store.activeTicket ? 'block' : 'hidden lg:block'"
    >
      <!-- Panel Aktif jika ada tiket yang dipilih -->
      <SecureClaimTicketPanel
        v-if="store.activeTicket"
        :ticket="store.activeTicket"
        @close="store.activeTicket = null"
      />

      <!-- Empty State jika belum ada item AI Match yang diklik -->
      <div
        v-else
        class="h-full flex flex-col items-center justify-center p-8 bg-white border-l border-slate-200/80 text-center space-y-4"
      >
        <div class="w-16 h-16 rounded-3xl bg-mint-wash text-brand-teal flex items-center justify-center shadow-xs">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
        </div>
        <div class="max-w-xs space-y-1">
          <h3 class="text-sm font-bold text-navy-900">Secure Claim Ticket</h3>
          <p class="text-xs text-text-secondary leading-relaxed">
            Pilih barang berlabel <span class="text-brand-teal font-semibold">AI MATCH</span> pada daftar untuk membuka perbandingan detail dan jalur obrolan klaim terverifikasi.
          </p>
        </div>
      </div>
    </aside>

    <!-- Modal Report User (Anti-ID-Hafal & Bukti Gambar) -->
    <ReportUserModal
      v-model="reportModalOpen"
      :target-user="activeReportUser"
    />

    <!-- Modal Buat Laporan Barang (Kehilangan / Ditemukan) -->
    <CreateReportModal
      v-model="showCreateReportModal"
      :initial-type="initialReportType"
      @created="handleReportCreated"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAppStore } from '@/store/appStore';
import Sidebar from '@/components/Sidebar.vue';
import MapWidget from '@/components/MapWidget.vue';
import ItemCard from '@/components/ItemCard.vue';
import SecureClaimTicketPanel from '@/components/SecureClaimTicketPanel.vue';
import ReportUserModal from '@/components/ReportUserModal.vue';
import CreateReportModal from '@/components/CreateReportModal.vue';

const store = useAppStore();
const router = useRouter();
const route = useRoute();

const activeMenu = ref('dashboard');
const reportModalOpen = ref(false);
const showCreateReportModal = ref(false);
const initialReportType = ref('lost');

const activeReportUser = computed(() => {
  if (store.activeTicket) {
    const currentId = store.user?.id;
    if (store.activeTicket.lost_item?.user_id === currentId) {
      return store.activeTicket.found_item?.user;
    }
    return store.activeTicket.lost_item?.user;
  }
  return null;
});

const userInitial = computed(() => {
  return store.user?.name ? store.user.name.charAt(0).toUpperCase() : 'U';
});

onMounted(async () => {
  // Ambil profil jika ada token
  if (store.token && !store.user) {
    await store.fetchCurrentUser();
  }

  // Ambil daftar barang dari /api/items menggunakan axios (di store)
  await store.fetchItems();
  await store.fetchClaimTickets();

  // Hubungkan status tiket yang ada ke item cards
  enrichItemsWithTicketMatches();

  // Buka modal laporan jika diarahkan dengan query atau session
  checkPendingReport();
});

function checkPendingReport() {
  const targetReport = route.query.report || sessionStorage.getItem('nodus_redirect_report');
  if (targetReport === 'lost' || targetReport === 'found') {
    sessionStorage.removeItem('nodus_redirect_report');
    initialReportType.value = targetReport;
    showCreateReportModal.value = true;
  }
}

watch(
  () => route.query.report,
  (val) => {
    if (val === 'lost' || val === 'found') {
      initialReportType.value = val;
      showCreateReportModal.value = true;
    }
  }
);

async function handleReportCreated() {
  await store.fetchItems();
  await store.fetchClaimTickets();
  enrichItemsWithTicketMatches();
  store.notify('Laporan barang Anda berhasil dipublikasikan!', 'success');
}

function enrichItemsWithTicketMatches() {
  if (!store.tickets.length || !store.items.length) return;

  store.items = store.items.map((item) => {
    const match = store.tickets.find(
      (t) => t.lost_item_id === item.id || t.found_item_id === item.id
    );
    if (match) {
      return {
        ...item,
        has_ai_match: true,
        match_score: match.ai_match_score || 95,
        claim_ticket_id: match.id,
      };
    }
    return item;
  });
}

async function handleSelectItem(item) {
  await store.selectItem(item);

  // Jika item memiliki kecocokan tiket klaim, otomatis buka panel kanan
  if (item.claim_ticket_id) {
    await store.loadActiveTicket(item.claim_ticket_id);
  }
}

function openReportModal() {
  reportModalOpen.value = true;
}

async function handleLogout() {
  await store.logout();
  router.push('/');
}
</script>
