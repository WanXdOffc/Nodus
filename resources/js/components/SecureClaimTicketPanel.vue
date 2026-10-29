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

      <!-- Accurate Location Mini-Map (Hanya jika tiket sudah di-approve admin) -->
      <div
        v-if="ticket?.admin_status === 'approved'"
        class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3"
      >
        <div class="flex items-center justify-between text-xs font-bold text-navy-900 border-b border-slate-100 pb-2">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Titik Koordinat Asli Terverifikasi</span>
          </div>
          <span class="text-[10px] text-emerald-600 bg-emerald-50 font-bold px-2 py-0.5 rounded-full border border-emerald-100/80 flex items-center gap-1">
            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Akses Titik Presisi
          </span>
        </div>

        <p class="text-[11px] text-text-secondary leading-relaxed">
          Privasi publik di peta luar dikaburkan dalam zona radius 300m. Di dalam tiket resmi yang disetujui ini, pin lokasi menunjukkan titik koordinat aslinya.
        </p>

        <!-- Container Leaflet Mini-Map -->
        <div class="relative w-full h-44 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 z-0">
          <div ref="miniMapContainer" class="w-full h-full z-0"></div>

          <!-- Mini Legend -->
          <div class="absolute bottom-2 left-2 z-[400] bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-xl text-[10px] font-semibold text-slate-700 shadow-sm border border-slate-200/80 flex items-center gap-3 pointer-events-auto">
            <span class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-brand-orange"></span>
              <span>Barang Hilang</span>
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-brand-teal"></span>
              <span>Barang Temuan</span>
            </span>
          </div>
        </div>
      </div>

      <!-- QR Handshake Information Banner -->
      <!-- Jika User adalah Pelapor: Info QR Handshake -->
      <div
        v-if="ticket?.status === 'open' && isReporter"
        class="bg-gradient-to-r from-teal-500/10 via-brand-teal/5 to-transparent rounded-2xl p-4 border border-teal-200/60 flex items-center justify-between gap-3 shadow-xs"
      >
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-brand-teal text-white flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-xs font-bold text-navy-900 truncate">QR Handshake Pelapor Siap</p>
            <p class="text-[11px] text-text-secondary">Tunjukkan QR ke penemu saat bertemu langsung untuk menyelesaikan klaim.</p>
          </div>
        </div>
        <button
          type="button"
          @click="showQrModal = true"
          class="px-3.5 py-1.5 bg-brand-teal hover:bg-[#00ad95] text-white text-[11px] font-bold rounded-xl transition-all shadow-xs flex-shrink-0 cursor-pointer"
        >
          Lihat QR
        </button>
      </div>

      <!-- Jika User adalah Penemu: Info Scanner Kamera -->
      <div
        v-else-if="ticket?.status === 'open' && isFinder"
        class="bg-gradient-to-r from-teal-500/10 via-brand-teal/5 to-transparent rounded-2xl p-4 border border-teal-200/60 flex items-center justify-between gap-3 shadow-xs"
      >
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-brand-teal text-white flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-xs font-bold text-navy-900 truncate">Verifikasi Scanner Penemu</p>
            <p class="text-[11px] text-text-secondary">Pindai kode QR pelapor untuk memvalidasi penyerahan barang.</p>
          </div>
        </div>
        <button
          type="button"
          @click="scannerModalOpen = true"
          class="px-3.5 py-1.5 bg-brand-teal hover:bg-[#00ad95] text-white text-[11px] font-bold rounded-xl transition-all shadow-xs flex-shrink-0 cursor-pointer"
        >
          Pindai
        </button>
      </div>

      <!-- Jika Tiket Berhasil Diverifikasi: Tampilkan Banner Sukses Handshake -->
      <div
        v-if="ticket?.status === 'verified'"
        class="bg-emerald-50 border border-emerald-200/70 rounded-2xl p-4 text-center space-y-2 shadow-sm"
      >
        <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-600 flex items-center justify-center mx-auto">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <p class="text-xs font-bold text-emerald-900">
          Serah Terima Berhasil Diverifikasi!
        </p>
        <p class="text-[11px] text-emerald-700 leading-relaxed max-w-xs mx-auto">
          Barang telah resmi diselesaikan. Poin Trust Score +0.5 telah ditambahkan ke profil Anda.
        </p>
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
      <!-- STATE 1: Tiket Sudah Selesai (Verified / Resolved) -->
      <div
        v-if="ticket?.status === 'verified' || ticket?.status === 'resolved'"
        class="w-full py-3 px-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-center gap-2 text-emerald-800 font-bold text-xs shadow-xs"
      >
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>Resolved &bull; Serah Terima Telah Selesai</span>
      </div>

      <!-- STATE 2: Tiket Masih Open -->
      <template v-else-if="ticket?.status === 'open'">
        <!-- JIKA User adalah Pelapor/Pemilik (user.id == ticket.lostItem.user_id): Tampilkan Tombol Tampilkan QR Klaim -->
        <button
          v-if="isReporter || isAdmin"
          type="button"
          @click="showQrModal = true"
          :disabled="actionLoading"
          class="w-full py-3 px-4 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-50 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
          </svg>
          <span>Tampilkan QR Klaim</span>
        </button>

        <!-- JIKA User adalah Penemu (user.id == ticket.foundItem.user_id): Tampilkan Tombol Buka Kamera Scanner -->
        <button
          v-if="isFinder || isAdmin"
          type="button"
          @click="scannerModalOpen = true"
          :disabled="actionLoading"
          class="w-full py-3 px-4 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-50 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span>Buka Kamera Scanner</span>
        </button>

        <!-- Tombol Tolak Kecocokan -->
        <button
          type="button"
          @click="handleAction('rejected')"
          :disabled="actionLoading"
          class="w-full py-2.5 px-4 bg-white hover:bg-slate-100 disabled:opacity-50 text-slate-600 border border-slate-200 font-semibold text-xs rounded-xl shadow-xs transition-all cursor-pointer"
        >
          <span>Tolak Kecocokan (Reject Match)</span>
        </button>
      </template>
    </div>

    <!-- Modal Tampilkan QR Klaim (Khusus Pelapor/Pemilik) -->
    <div
      v-if="showQrModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"
    >
      <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl border border-slate-100 overflow-hidden p-6 text-center space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
          <div class="flex items-center gap-2 text-brand-teal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            <h3 class="text-sm font-bold text-navy-900">QR Code Klaim Pelapor</h3>
          </div>
          <button @click="showQrModal = false" class="p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 inline-block shadow-inner mx-auto">
          <qrcode-vue
            :value="ticket?.qr_token || 'nodus-pending-token'"
            :size="190"
            level="H"
            render-as="svg"
            class="mx-auto"
          />
        </div>

        <div class="space-y-1">
          <p class="text-xs font-bold text-navy-900">Tunjukkan QR ini kepada Penemu</p>
          <p class="text-[11px] text-text-secondary leading-relaxed">
            Penemu akan memindai kode QR ini menggunakan kamera scanner pada aplikasinya untuk memverifikasi serah terima secara aman dan menaikkan reputasi Trust Score.
          </p>
        </div>

        <button
          type="button"
          @click="showQrModal = false"
          class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all cursor-pointer"
        >
          Tutup
        </button>
      </div>
    </div>

    <!-- Modal Pindai QR Code (HTML5 Scanner) -->
    <QrScannerModal
      v-model="scannerModalOpen"
      @scanned="handleQrScanned"
    />

    <!-- Modal Report User / Fraud (Anti-ID-Hafal) -->
    <ReportUserModal
      v-model="reportModalOpen"
      :target-user="counterpartUser"
    />

    <!-- Modal Ulasan Aplikasi (Pemicu Acak setelah Klaim Sukses) -->
    <ReviewModal
      v-model="showReviewModal"
    />
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useAppStore } from '@/store/appStore';
import QrcodeVue from 'qrcode.vue';
import QrScannerModal from '@/components/QrScannerModal.vue';
import ReportUserModal from '@/components/ReportUserModal.vue';
import ReviewModal from '@/components/ReviewModal.vue';

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
const showReviewModal = ref(false);
const scannerModalOpen = ref(false);
const showQrModal = ref(false);
const miniMapContainer = ref(null);
let miniMapInstance = null;

const currentUserId = computed(() => store.user?.id);
const isAdmin = computed(() => store.user?.role === 'admin');
const messages = computed(() => store.ticketMessages);
const messagesLoading = computed(() => store.messagesLoading);

// Identifikasi peran pengguna pada tiket ini:
// Pelapor (Loser): pemilik barang yang hilang
const isReporter = computed(() => {
  if (!props.ticket || !currentUserId.value) return false;
  const lostUser = props.ticket.lost_item?.user_id ?? props.ticket.lostItem?.user_id;
  return Number(lostUser) === Number(currentUserId.value);
});

// Penemu (Finder): pemilik barang temuan
const isFinder = computed(() => {
  if (!props.ticket || !currentUserId.value) return false;
  const foundUser = props.ticket.found_item?.user_id ?? props.ticket.foundItem?.user_id;
  return Number(foundUser) === Number(currentUserId.value);
});

// Otomatis tentukan pihak lawan bicara (counterpart)
const counterpartUser = computed(() => {
  if (!props.ticket) return null;
  if (props.ticket.counterpart_user) return props.ticket.counterpart_user;
  const currentId = store.user?.id;
  const lostUser = props.ticket.lost_item?.user_id ?? props.ticket.lostItem?.user_id;
  if (lostUser === currentId) {
    return props.ticket.found_item?.user ?? props.ticket.foundItem?.user;
  }
  return props.ticket.lost_item?.user ?? props.ticket.lostItem?.user;
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

async function handleQrScanned(token) {
  if (!token || actionLoading.value || !props.ticket) return;
  actionLoading.value = true;
  try {
    const res = await axios.post(`/api/claim-tickets/${props.ticket.id}/verify-qr`, { token });

    // Ubah status lokal tiket menjadi verified agar UI langsung berganti ke Selesai / Resolved
    if (props.ticket) {
      props.ticket.status = 'verified';
    }
    if (store.activeTicket && store.activeTicket.id === props.ticket.id) {
      store.activeTicket.status = 'verified';
    }
    const foundTicket = store.tickets.find((t) => t.id === props.ticket.id);
    if (foundTicket) {
      foundTicket.status = 'verified';
    }

    store.notify(res.data.message || 'Verifikasi serah terima berhasil! Trust Score Anda meningkat.', 'success');
    await store.fetchCurrentUser();
    await store.fetchItems();

    // Pemicu acak setelah sukses untuk memunculkan modal ulasan
    if (Math.random() > 0.5) {
      setTimeout(() => {
        showReviewModal.value = true;
      }, 700);
    }
  } catch (err) {
    const errorMsg = err.response?.data?.message || 'Kode QR tidak cocok atau gagal diverifikasi.';
    store.notify(errorMsg, 'error');
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

function initMiniMap() {
  if (!miniMapContainer.value || !props.ticket || props.ticket.admin_status !== 'approved') return;

  if (miniMapInstance) {
    miniMapInstance.remove();
    miniMapInstance = null;
  }

  const lostLat = Number(props.ticket.lost_item?.latitude ?? props.ticket.lostItem?.latitude);
  const lostLng = Number(props.ticket.lost_item?.longitude ?? props.ticket.lostItem?.longitude);
  const foundLat = Number(props.ticket.found_item?.latitude ?? props.ticket.foundItem?.latitude);
  const foundLng = Number(props.ticket.found_item?.longitude ?? props.ticket.foundItem?.longitude);

  const validLost = !isNaN(lostLat) && !isNaN(lostLng) && lostLat !== 0;
  const validFound = !isNaN(foundLat) && !isNaN(foundLng) && foundLat !== 0;

  const defaultCenter = [-8.112, 115.088];
  const center = validLost ? [lostLat, lostLng] : (validFound ? [foundLat, foundLng] : defaultCenter);

  miniMapInstance = L.map(miniMapContainer.value, {
    center: center,
    zoom: 14,
    zoomControl: false,
    attributionControl: false,
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
  }).addTo(miniMapInstance);

  const bounds = [];

  // Marker titik koordinat asli barang hilang (Orange)
  if (validLost) {
    const lostIcon = L.divIcon({
      className: 'accurate-lost-marker',
      html: `
        <div style="position:relative; width:28px; height:28px; display:flex; align-items:center; justify-content:center;">
          <div style="position:absolute; inset:0; border-radius:9999px; background:rgba(255,107,53,0.3); animation:ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
          <div style="width:22px; height:22px; background:#FF6B35; border:2px solid white; border-radius:9999px; box-shadow:0 3px 8px rgba(0,0,0,0.25); display:flex; align-items:center; justify-content:center; color:white; font-size:10px; font-weight:800;">
            H
          </div>
        </div>
      `,
      iconSize: [28, 28],
      iconAnchor: [14, 14],
    });

    const lostMarker = L.marker([lostLat, lostLng], { icon: lostIcon }).addTo(miniMapInstance);
    lostMarker.bindPopup(`
      <div style="font-family: inherit; font-size: 11px; padding: 2px;">
        <span style="color:#FF6B35; font-weight:800; text-transform:uppercase;">Titik Hilang Asli</span>
        <div style="font-weight:700; color:#1e293b; margin-top:2px;">${props.ticket.lost_item?.title || props.ticket.lostItem?.title || 'Barang Hilang'}</div>
        <div style="color:#64748b; font-size:10px;">📍 ${props.ticket.lost_item?.location_name || props.ticket.lostItem?.location_name || ''}</div>
      </div>
    `);
    bounds.push([lostLat, lostLng]);
  }

  // Marker titik koordinat asli barang temuan (Teal)
  if (validFound) {
    const foundIcon = L.divIcon({
      className: 'accurate-found-marker',
      html: `
        <div style="position:relative; width:28px; height:28px; display:flex; align-items:center; justify-content:center;">
          <div style="position:absolute; inset:0; border-radius:9999px; background:rgba(20,184,166,0.3); animation:ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
          <div style="width:22px; height:22px; background:#14B8A6; border:2px solid white; border-radius:9999px; box-shadow:0 3px 8px rgba(0,0,0,0.25); display:flex; align-items:center; justify-content:center; color:white; font-size:10px; font-weight:800;">
            T
          </div>
        </div>
      `,
      iconSize: [28, 28],
      iconAnchor: [14, 14],
    });

    const foundMarker = L.marker([foundLat, foundLng], { icon: foundIcon }).addTo(miniMapInstance);
    foundMarker.bindPopup(`
      <div style="font-family: inherit; font-size: 11px; padding: 2px;">
        <span style="color:#14B8A6; font-weight:800; text-transform:uppercase;">Titik Temu Asli</span>
        <div style="font-weight:700; color:#1e293b; margin-top:2px;">${props.ticket.found_item?.title || props.ticket.foundItem?.title || 'Barang Temuan'}</div>
        <div style="color:#64748b; font-size:10px;">📍 ${props.ticket.found_item?.location_name || props.ticket.foundItem?.location_name || ''}</div>
      </div>
    `);
    bounds.push([foundLat, foundLng]);
  }

  if (bounds.length > 1) {
    miniMapInstance.fitBounds(bounds, { padding: [30, 30], maxZoom: 16 });
  } else if (bounds.length === 1) {
    miniMapInstance.setView(bounds[0], 15);
  }

  setTimeout(() => {
    miniMapInstance?.invalidateSize();
  }, 250);
}

onMounted(() => {
  nextTick(() => {
    initMiniMap();
  });
});

watch(
  () => [props.ticket?.id, props.ticket?.admin_status],
  () => {
    nextTick(() => {
      initMiniMap();
    });
  }
);

onUnmounted(() => {
  if (miniMapInstance) {
    miniMapInstance.remove();
    miniMapInstance = null;
  }
});
</script>
