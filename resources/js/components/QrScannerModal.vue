<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"
  >
    <div
      class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all flex flex-col max-h-[90vh]"
    >
      <!-- Header Modal -->
      <div class="px-6 py-5 bg-navy-900 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-brand-teal/20 text-brand-teal flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-white tracking-tight">Pindai QR Pelapor</h3>
            <p class="text-xs text-slate-300">Verifikasi serah terima barang langsung</p>
          </div>
        </div>

        <button
          @click="closeModal"
          class="p-1.5 rounded-full text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
          title="Tutup Modal"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Scanner Body -->
      <div class="p-6 overflow-y-auto space-y-4">
        <!-- Instruksi -->
        <div class="bg-teal-50/80 border border-teal-100 rounded-2xl p-3.5 flex items-start gap-3">
          <svg class="w-5 h-5 text-brand-teal flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p class="text-xs text-teal-900 leading-relaxed">
            Arahkan kamera ke kode QR yang ada di layar perangkat <strong>Pelapor</strong> saat bertemu. Status kepemilikan akan otomatis terselesaikan.
          </p>
        </div>

        <!-- Camera Scanner Viewport -->
        <div class="relative bg-slate-900 rounded-2xl overflow-hidden min-h-[260px] flex items-center justify-center border border-slate-200">
          <!-- Element target untuk html5-qrcode -->
          <div id="qr-reader-container" class="w-full h-full min-h-[260px] flex items-center justify-center"></div>

          <!-- Loading or Camera Prompt State -->
          <div
            v-if="isStarting"
            class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center text-white space-y-3 p-4"
          >
            <div class="w-8 h-8 border-3 border-brand-teal border-t-transparent rounded-full animate-spin"></div>
            <p class="text-xs font-medium text-slate-300">Menyiapkan kamera...</p>
          </div>

          <!-- Error / Camera Not Allowed State -->
          <div
            v-if="scannerError"
            class="absolute inset-0 bg-slate-900/95 flex flex-col items-center justify-center text-white p-5 text-center space-y-3"
          >
            <div class="w-12 h-12 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs font-semibold text-rose-300 mb-1">Akses Kamera Terkendala</p>
              <p class="text-[11px] text-slate-400 max-w-xs leading-relaxed">
                {{ scannerError }}
              </p>
            </div>
            <button
              @click="initCamera"
              class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded-xl text-white transition-colors"
            >
              Coba Lagi
            </button>
          </div>
        </div>

        <!-- Fallback Manual Input (Sangat berguna jika kamera terkendala atau pengujian manual) -->
        <div class="pt-2 border-t border-slate-100 space-y-2">
          <button
            type="button"
            @click="showManualInput = !showManualInput"
            class="text-xs font-medium text-brand-teal hover:underline flex items-center gap-1.5 mx-auto"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>{{ showManualInput ? 'Sembunyikan Input Manual' : 'Masukkan Token QR Secara Manual' }}</span>
          </button>

          <div v-if="showManualInput" class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2.5">
            <label class="block text-[11px] font-bold text-slate-700">Token QR (UUID):</label>
            <div class="flex gap-2">
              <input
                v-model="manualToken"
                type="text"
                placeholder="Contoh: 8f2b7a91-..."
                class="flex-1 px-3 py-2 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal bg-white"
              />
              <button
                type="button"
                @click="submitManualToken"
                :disabled="!manualToken.trim() || submitting"
                class="px-3.5 py-2 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all shadow-sm"
              >
                Verifikasi
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
        <span class="text-[11px] text-slate-500 flex items-center gap-1.5">
          <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          Enkripsi Handshake Aman
        </span>

        <button
          @click="closeModal"
          class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-sm transition-all"
        >
          Tutup
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { Html5Qrcode } from 'html5-qrcode';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue', 'scanned']);

let html5QrCode = null;
const isStarting = ref(false);
const scannerError = ref('');
const showManualInput = ref(false);
const manualToken = ref('');
const submitting = ref(false);

watch(
  () => props.modelValue,
  async (isOpen) => {
    if (isOpen) {
      scannerError.value = '';
      manualToken.value = '';
      await nextTick();
      initCamera();
    } else {
      stopCamera();
    }
  }
);

async function initCamera() {
  stopCamera();
  scannerError.value = '';
  isStarting.value = true;

  try {
    const element = document.getElementById('qr-reader-container');
    if (!element) return;

    html5QrCode = new Html5Qrcode('qr-reader-container');

    const config = {
      fps: 10,
      qrbox: { width: 200, height: 200 },
      aspectRatio: 1.0,
    };

    await html5QrCode.start(
      { facingMode: 'environment' },
      config,
      (decodedText) => {
        handleScanSuccess(decodedText);
      },
      (errorMessage) => {
        // Scanning frame without QR is normal, don't trigger error
      }
    );
  } catch (err) {
    console.warn('Camera start issue:', err);
    scannerError.value =
      err?.message || 'Tidak dapat mengakses kamera. Pastikan izin kamera aktif atau gunakan input manual.';
  } finally {
    isStarting.value = false;
  }
}

async function stopCamera() {
  if (html5QrCode) {
    try {
      if (html5QrCode.isScanning) {
        await html5QrCode.stop();
      }
      html5QrCode.clear();
    } catch (e) {
      console.warn('Error stopping scanner:', e);
    }
    html5QrCode = null;
  }
}

function handleScanSuccess(decodedText) {
  if (!decodedText) return;
  stopCamera();
  emit('scanned', decodedText.trim());
  emit('update:modelValue', false);
}

function submitManualToken() {
  if (!manualToken.value.trim()) return;
  submitting.value = true;
  stopCamera();
  emit('scanned', manualToken.value.trim());
  emit('update:modelValue', false);
  submitting.value = false;
}

function closeModal() {
  stopCamera();
  emit('update:modelValue', false);
}

onBeforeUnmount(() => {
  stopCamera();
});
</script>

<style scoped>
#qr-reader-container :deep(video) {
  border-radius: 1rem;
  object-fit: cover;
  width: 100% !important;
  height: 100% !important;
}
</style>
