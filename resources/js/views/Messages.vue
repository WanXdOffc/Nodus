<template>
  <div class="h-screen w-screen overflow-hidden flex flex-col lg:flex-row bg-slate-50 text-text-primary font-sans">
    <!-- Sidebar Kiri -->
    <Sidebar />

    <!-- Area Utama Konten Pesan Klaim -->
    <main class="flex-1 flex flex-col h-full overflow-hidden p-6 sm:p-8 space-y-6">
      <!-- Header Halaman -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/60">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-mint-wash text-brand-teal text-[11px] font-bold mb-1">
            <span class="w-2 h-2 rounded-full bg-brand-teal animate-pulse"></span>
            Pusat Komunikasi &amp; Verifikasi
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight">
            Pesan &amp; Tiket Klaim
          </h1>
          <p class="text-xs text-text-secondary mt-0.5">
            Daftar percakapan aman dan terverifikasi untuk proses klaim kepemilikan barang.
          </p>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-navy-900 shadow-xs">
            {{ store.tickets.length }} Tiket Klaim Terdaftar
          </span>
        </div>
      </div>

      <!-- Feed / List Tiket & Pesan -->
      <div class="flex-1 overflow-y-auto space-y-3 pr-1">
        <div v-if="loading" class="text-center py-16 text-xs text-slate-400">
          Mengambil data tiket klaim &amp; percakapan...
        </div>

        <div
          v-else-if="!store.tickets.length"
          class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-200 p-8 space-y-3"
        >
          <div class="w-14 h-14 rounded-2xl bg-mint-wash text-brand-teal mx-auto flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
          </div>
          <h3 class="text-sm font-bold text-navy-900">Belum Ada Tiket Klaim Aktif</h3>
          <p class="text-xs text-text-secondary max-w-sm mx-auto">
            Tiket klaim akan otomatis dibuat saat sistem AI mendeteksi kecocokan antara barang hilang dan temuan.
          </p>
        </div>

        <div v-else class="space-y-3 max-w-4xl">
          <div
            v-for="ticket in store.tickets"
            :key="ticket.id"
            class="bg-white rounded-3xl p-5 border border-slate-200/70 shadow-xs hover:shadow-card transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
          >
            <!-- Sisi Kiri: Detail Tiket & Kecocokan -->
            <div class="flex items-start gap-4 flex-1">
              <div class="w-12 h-12 rounded-2xl bg-brand-teal/10 text-brand-teal flex items-center justify-center flex-shrink-0 font-bold text-sm">
                #{{ ticket.id }}
              </div>
              <div class="space-y-1 min-w-0">
                <div class="flex items-center gap-2">
                  <span
                    class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full"
                    :class="[
                      ticket.status === 'verified' ? 'bg-emerald-100 text-emerald-700' :
                      ticket.status === 'rejected' ? 'bg-rose-100 text-rose-700' :
                      'bg-mint-wash text-brand-teal'
                    ]"
                  >
                    {{ ticket.status }}
                  </span>
                  <span class="text-[11px] font-bold text-brand-teal">
                    ⭐ {{ ticket.ai_match_score || 95 }}% AI Match Score
                  </span>
                </div>

                <h4 class="text-sm font-bold text-navy-900 truncate">
                  {{ ticket.lost_item?.title || 'Barang Hilang' }} &bull; {{ ticket.found_item?.title || 'Barang Temuan' }}
                </h4>

                <p class="text-xs text-text-secondary truncate">
                  Penemu: <span class="font-semibold text-navy-900">{{ ticket.found_item?.user?.name || 'Warga Nodus' }}</span>
                  &bull; Pelapor: <span class="font-semibold text-navy-900">{{ ticket.lost_item?.user?.name || 'Pemilik' }}</span>
                </p>
              </div>
            </div>

            <!-- Sisi Kanan: Aksi Masuk ke Dashboard Claim Ticket -->
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
              <button
                @click="openTicketInDashboard(ticket)"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-teal hover:bg-[#00ab94] text-white text-xs font-bold rounded-xl shadow-xs transition-colors"
              >
                <span>Buka Chat Tiket</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
              </button>
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
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAppStore } from '@/store/appStore';
import Sidebar from '@/components/Sidebar.vue';

const store = useAppStore();
const router = useRouter();
const loading = ref(false);
const reportModalOpen = ref(false);
const reportForm = ref({ reported_user_id: '', reason: '' });

onMounted(async () => {
  loading.value = true;
  if (!store.user) {
    await store.fetchCurrentUser();
  }
  await store.fetchClaimTickets();
  loading.value = false;
});

async function openTicketInDashboard(ticket) {
  await store.loadActiveTicket(ticket.id);
  router.push('/dashboard');
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
