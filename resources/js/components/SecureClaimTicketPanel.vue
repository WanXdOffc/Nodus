<template>
  <div class="h-full flex flex-col bg-white border-l border-slate-200/80 shadow-[-4px_0_20px_rgba(0,0,0,0.03)] z-10">
    <!-- Header Gelap -->
    <div class="bg-navy-900 text-white px-6 py-4 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-full bg-brand-teal/20 text-brand-teal flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-sm font-bold tracking-wide">Secure Claim Ticket</h3>
          <p class="text-[11px] text-slate-400">Verifikasi &amp; Komunikasi Terenkripsi</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <span
          class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase"
          :class="[
            ticket?.status === 'verified' ? 'bg-emerald-500/20 text-emerald-400' :
            ticket?.status === 'rejected' ? 'bg-rose-500/20 text-rose-400' :
            'bg-brand-teal/20 text-brand-teal'
          ]"
        >
          {{ ticket?.status || 'OPEN' }}
        </span>
        <button
          @click="$emit('close')"
          class="p-1 rounded-full text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
          title="Tutup Panel"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto p-5 space-y-5">
      <!-- Side-by-Side Comparison -->
      <div class="space-y-2">
        <div class="flex items-center justify-between text-xs font-bold text-navy-900">
          <span>Perbandingan Barang</span>
          <span class="text-brand-teal font-extrabold flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            {{ ticket?.ai_match_score || 95 }}% Match Score
          </span>
        </div>

        <div class="grid grid-cols-2 gap-3 relative">
          <!-- Lost Item Card -->
          <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200/70 space-y-2">
            <div class="aspect-square rounded-xl bg-slate-200 overflow-hidden flex items-center justify-center">
              <img
                v-if="ticket?.lost_item?.image_path"
                :src="'/storage/' + ticket.lost_item.image_path"
                class="w-full h-full object-cover"
              />
              <span v-else class="text-[10px] text-slate-400 font-medium">No Image</span>
            </div>
            <div>
              <span class="text-[9px] font-bold text-brand-orange bg-orange-100/70 px-2 py-0.5 rounded-full uppercase">
                Hilang
              </span>
              <p class="text-xs font-bold text-navy-900 truncate mt-1">
                {{ ticket?.lost_item?.title || 'Dompet Pria' }}
              </p>
              <p class="text-[10px] text-slate-500 truncate">
                📍 {{ ticket?.lost_item?.location_name || 'Singaraja' }}
              </p>
            </div>
          </div>

          <!-- Found Item Card -->
          <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200/70 space-y-2">
            <div class="aspect-square rounded-xl bg-slate-200 overflow-hidden flex items-center justify-center">
              <img
                v-if="ticket?.found_item?.image_path"
                :src="'/storage/' + ticket.found_item.image_path"
                class="w-full h-full object-cover"
              />
              <span v-else class="text-[10px] text-slate-400 font-medium">No Image</span>
            </div>
            <div>
              <span class="text-[9px] font-bold text-brand-teal bg-mint-wash px-2 py-0.5 rounded-full uppercase">
                Ditemukan
              </span>
              <p class="text-xs font-bold text-navy-900 truncate mt-1">
                {{ ticket?.found_item?.title || 'Dompet Hitam' }}
              </p>
              <p class="text-[10px] text-slate-500 truncate">
                📍 {{ ticket?.found_item?.location_name || 'Singaraja' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat Thread Sederhana -->
      <div class="space-y-3 pt-2 border-t border-slate-100">
        <div class="flex items-center justify-between text-xs font-bold text-navy-900">
          <div class="flex items-center gap-1.5">
            <span>Pesan Klaim &amp; Bukti</span>
            <span class="text-[10px] font-normal text-slate-400">Terenkripsi</span>
          </div>
          <!-- Tombol Report Fraud (Anti-ID-Hafal) -->
          <button
            type="button"
            @click="reportModalOpen = true"
            class="text-[11px] font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100/80 px-2.5 py-1 rounded-lg transition-colors flex items-center gap-1"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Report Fraud</span>
          </button>
        </div>

        <!-- Chat Bubble List -->
        <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200/60 min-h-[160px] max-h-[220px] overflow-y-auto space-y-2.5">
          <div v-if="messagesLoading" class="text-center py-6 text-xs text-slate-400">
            Memuat percakapan...
          </div>
          <div v-else-if="!messages.length" class="text-center py-6 text-xs text-slate-400">
            Belum ada pesan. Mulai ajukan pertanyaan verifikasi kepemilikan di bawah.
          </div>
          <div
            v-for="msg in messages"
            :key="msg.id"
            class="flex flex-col"
            :class="msg.user_id === currentUserId ? 'items-end' : 'items-start'"
          >
            <div class="text-[10px] text-slate-400 mb-0.5 px-1">
              {{ msg.user?.name || (msg.user_id === currentUserId ? 'Anda' : 'Penemu') }}
            </div>
            <div
              class="max-w-[85%] p-2.5 text-xs rounded-2xl shadow-sm leading-relaxed"
              :class="[
                msg.user_id === currentUserId
                  ? 'bg-brand-teal text-white rounded-tr-sm'
                  : 'bg-white text-slate-800 border border-slate-200/80 rounded-tl-sm'
              ]"
            >
              {{ msg.message }}
            </div>
          </div>
        </div>

        <!-- Input Chat -->
        <form @submit.prevent="handleSendMessage" class="flex gap-2">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Tulis pesan verifikasi..."
            class="flex-1 px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal bg-white"
          />
          <button
            type="submit"
            :disabled="!newMessage.trim() || sending"
            class="px-4 py-2 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-50 text-white rounded-xl text-xs font-semibold shadow-sm transition-all flex items-center justify-center"
          >
            Kirim
          </button>
        </form>
      </div>
    </div>

    <!-- Action Area (Bawah) -->
    <div class="p-5 border-t border-slate-200/80 bg-slate-50/50 space-y-2.5 flex-shrink-0">
      <button
        @click="handleVerifyOwnership"
        :disabled="actionLoading || ticket?.status === 'verified'"
        class="w-full py-3 px-4 bg-brand-orange hover:bg-[#e85b27] disabled:opacity-50 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ ticket?.status === 'verified' ? 'Sudah Terverifikasi' : 'Verify Ownership & Claim' }}</span>
      </button>

      <button
        @click="handleAction('rejected')"
        :disabled="actionLoading || ticket?.status === 'rejected'"
        class="w-full py-2.5 px-4 bg-white hover:bg-slate-100 disabled:opacity-50 text-slate-700 border border-slate-200 font-semibold text-xs rounded-xl shadow-sm transition-all"
      >
        <span>Reject Match</span>
      </button>
    </div>

    <!-- Modal Report User / Fraud (Anti-ID-Hafal) -->
    <ReportUserModal
      v-model="reportModalOpen"
      :target-user="counterpartUser"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useAppStore } from '@/store/appStore';
import ReportUserModal from '@/components/ReportUserModal.vue';

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close']);

const store = useAppStore();
const newMessage = ref('');
const sending = ref(false);
const actionLoading = ref(false);
const reportModalOpen = ref(false);

const currentUserId = computed(() => store.user?.id);
const messages = computed(() => store.ticketMessages);
const messagesLoading = computed(() => store.messagesLoading);

// Otomatis tentukan pihak lawan bicara (counterpart)
const counterpartUser = computed(() => {
  if (!props.ticket) return null;
  if (props.ticket.counterpart_user) return props.ticket.counterpart_user;
  const currentId = store.user?.id;
  if (props.ticket.lost_item?.user_id === currentId) {
    return props.ticket.found_item?.user;
  }
  return props.ticket.lost_item?.user;
});

async function handleSendMessage() {
  if (!newMessage.value.trim() || sending.value) return;
  sending.value = true;
  try {
    await store.sendTicketMessage(newMessage.value);
    newMessage.value = '';
  } catch (err) {
    // error handled in store
  } finally {
    sending.value = false;
  }
}

async function handleVerifyOwnership() {
  if (actionLoading.value || !props.ticket) return;
  actionLoading.value = true;
  try {
    await store.resolveClaimTicket(props.ticket.id);
  } catch (err) {
    // handled in store
  } finally {
    actionLoading.value = false;
  }
}

async function handleAction(status) {
  if (actionLoading.value) return;
  actionLoading.value = true;
  try {
    await store.updateTicketStatus(status);
  } catch (err) {
    // error handled in store
  } finally {
    actionLoading.value = false;
  }
}
</script>
