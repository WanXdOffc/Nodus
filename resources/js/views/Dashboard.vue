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
    <aside class="w-full lg:w-[20%] lg:min-w-[220px] bg-white shadow-[4px_0_24px_rgba(0,0,0,0.03)] z-20 flex flex-col justify-between p-6 border-r border-slate-100 flex-shrink-0">
      <div class="space-y-8">
        <!-- Logo NODUS -->
        <div class="flex items-center justify-between">
          <router-link to="/" class="flex items-center gap-2.5 group">
            <div class="w-9 h-9 rounded-2xl bg-brand-teal/10 flex items-center justify-center">
              <span class="w-3.5 h-3.5 rounded-full bg-brand-teal"></span>
            </div>
            <span class="text-2xl font-bold tracking-wider text-navy-900 group-hover:text-brand-teal transition-colors">
              NODUS
            </span>
          </router-link>
        </div>

        <!-- Profil Singkat Pengguna -->
        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-brand-teal text-white flex items-center justify-center font-bold text-sm">
            {{ userInitial }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-navy-900 truncate">{{ store.user?.name || 'Pengguna Nodus' }}</p>
            <div class="flex items-center gap-1 text-[11px] text-brand-teal font-semibold">
              <span>⭐ {{ store.user?.trust_score || '5.0' }}</span>
              <span class="text-slate-400 font-normal">Score</span>
            </div>
          </div>
        </div>

        <!-- Navigasi Menu Utama -->
        <nav class="space-y-1.5">
          <button
            @click="activeMenu = 'dashboard'"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all text-left"
            :class="[
              activeMenu === 'dashboard'
                ? 'bg-mint-wash text-brand-teal'
                : 'text-slate-600 hover:bg-slate-50 hover:text-navy-900'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Dashboard</span>
          </button>

          <button
            @click="activeMenu = 'laporanku'"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all text-left"
            :class="[
              activeMenu === 'laporanku'
                ? 'bg-mint-wash text-brand-teal'
                : 'text-slate-600 hover:bg-slate-50 hover:text-navy-900'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span>Laporanku</span>
          </button>

          <button
            @click="activeMenu = 'pesan'"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all text-left"
            :class="[
              activeMenu === 'pesan'
                ? 'bg-mint-wash text-brand-teal'
                : 'text-slate-600 hover:bg-slate-50 hover:text-navy-900'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span>Pesan Klaim</span>
          </button>
        </nav>
      </div>

      <!-- Bagian Bawah: Tombol Peringatan 'Report User' Berwarna Teks Merah -->
      <div class="pt-6 border-t border-slate-100 space-y-2">
        <button
          @click="openReportModal"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors border border-rose-100"
        >
          <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <span>Report User</span>
        </button>

        <button
          @click="handleLogout"
          class="w-full text-center py-1.5 text-[11px] text-slate-400 hover:text-slate-600 transition-colors"
        >
          Keluar (Logout)
        </button>
      </div>
    </aside>

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
              placeholder="Jelaskan indikasi penipuan, klaim palsu, atau perilaku tidak pantas..."
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
              :disabled="reportSubmitting"
              class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 disabled:opacity-50 rounded-xl shadow-sm"
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
import { useRouter } from 'vue-router';
import { useAppStore } from '@/store/appStore';
import MapWidget from '@/components/MapWidget.vue';
import ItemCard from '@/components/ItemCard.vue';
import SecureClaimTicketPanel from '@/components/SecureClaimTicketPanel.vue';

const store = useAppStore();
const router = useRouter();

const activeMenu = ref('dashboard');
const reportModalOpen = ref(false);
const reportSubmitting = ref(false);
const reportForm = ref({
  reported_user_id: '',
  reason: '',
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
});

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
  // Otomatis isi ID target jika sedang melihat tiket
  if (store.activeTicket) {
    const currentId = store.user?.id;
    const targetUser = store.activeTicket.lost_item?.user_id === currentId
      ? store.activeTicket.found_item?.user_id
      : store.activeTicket.lost_item?.user_id;
    if (targetUser) {
      reportForm.value.reported_user_id = targetUser;
    }
  }
  reportModalOpen.value = true;
}

async function submitReport() {
  if (!reportForm.value.reported_user_id || !reportForm.value.reason) return;
  reportSubmitting.value = true;
  try {
    await store.submitUserReport(
      reportForm.value.reported_user_id,
      reportForm.value.reason
    );
    reportForm.value = { reported_user_id: '', reason: '' };
    reportModalOpen.value = false;
  } catch (err) {
    // error handled in store
  } finally {
    reportSubmitting.value = false;
  }
}

async function handleLogout() {
  await store.logout();
  router.push('/');
}
</script>
