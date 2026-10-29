<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
  >
    <div
      class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 my-8 relative overflow-hidden space-y-5"
    >
      <!-- Close Button -->
      <button
        @click="$emit('update:modelValue', false)"
        class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>

      <!-- Header -->
      <div class="space-y-1">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-600 text-[11px] font-bold">
          <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
          Moderasi &amp; Keamanan Komunitas
        </div>
        <h3 class="text-xl font-extrabold text-navy-900 flex items-center gap-2">
          Laporkan Pengguna (Report Fraud)
        </h3>
        <p class="text-xs text-text-secondary">
          Bantu kami menjaga komunitas Nodus tetap aman dan terpercaya. Laporan akan ditinjau langsung dengan bukti terlampir.
        </p>
      </div>

      <!-- Target User Info Card (Anti-ID-Hafal) -->
      <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
            {{ targetUser?.name ? targetUser.name.charAt(0).toUpperCase() : 'U' }}
          </div>
          <div>
            <div class="flex items-center gap-2">
              <p class="text-xs font-bold text-navy-900">{{ targetUser?.name || 'Pengguna Tidak Diketahui' }}</p>
              <span class="text-[10px] font-mono bg-white px-2 py-0.5 rounded-md border border-slate-200 text-slate-500">
                ID: #{{ targetUser?.id || '-' }}
              </span>
            </div>
            <p class="text-[11px] text-brand-teal font-semibold">
              ⭐ {{ targetUser?.trust_score || '5.0' }} Trust Score
            </p>
          </div>
        </div>
        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100">
          Terisi Otomatis
        </span>
      </div>

      <!-- Form Laporan -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Dropdown Kategori Alasan (Wajib) -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Kategori Pelanggaran <span class="text-rose-500">*</span>
          </label>
          <select
            v-model="form.reason_category"
            required
            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal bg-white"
          >
            <option value="" disabled>Pilih indikasi pelanggaran...</option>
            <option value="Meminta Imbalan Paksa / Pemerasan">Meminta Imbalan Paksa / Pemerasan</option>
            <option value="Barang Palsu / Modus Penipuan">Barang Palsu / Modus Penipuan</option>
            <option value="Pelecehan / Kata-kata Tidak Pantas">Pelecehan / Kata-kata Tidak Pantas</option>
            <option value="Klaim Palsu / Bukan Pemilik Asli">Klaim Palsu / Bukan Pemilik Asli</option>
            <option value="Menolak Menyerahkan Barang Temuan">Menolak Menyerahkan Barang Temuan</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>

        <!-- Teks Keterangan Rinci (Wajib) -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Keterangan &amp; Kronologi Kejadian <span class="text-rose-500">*</span>
          </label>
          <textarea
            v-model="form.reason"
            rows="3"
            required
            placeholder="Jelaskan secara rinci tindakan mencurigakan atau indikasi penipuan..."
            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
          ></textarea>
        </div>

        <!-- Unggah Bukti Gambar (Wajib: Screenshot chat atau foto) -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Lampiran Bukti (Screenshot Chat / Foto Barang) <span class="text-rose-500">* (Wajib disertakan)</span>
          </label>
          <div class="flex items-center gap-3">
            <input
              type="file"
              accept="image/*"
              required
              @change="handleEvidenceChange"
              class="text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-600 hover:file:bg-rose-100 cursor-pointer"
            />
            <div v-if="evidencePreview" class="w-12 h-12 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0">
              <img :src="evidencePreview" class="w-full h-full object-cover" />
            </div>
          </div>
          <p class="text-[10px] text-text-secondary mt-1">
            Wajib menyertakan bukti screenshot chat atau foto indikasi pelanggaran (Format: JPG, PNG, WebP maks 5MB).
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
          <button
            type="button"
            @click="$emit('update:modelValue', false)"
            class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="submitting || !targetUser?.id"
            class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-2"
          >
            <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>{{ submitting ? 'Mengirim Laporan...' : 'Kirim Laporan Fraud' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useAppStore } from '@/store/appStore';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  targetUser: {
    type: Object,
    default: () => null,
  },
});

const emit = defineEmits(['update:modelValue', 'submitted']);
const store = useAppStore();

const submitting = ref(false);
const evidenceFile = ref(null);
const evidencePreview = ref('');

const form = ref({
  reason_category: 'Meminta Imbalan Paksa / Pemerasan',
  reason: '',
});

watch(
  () => props.modelValue,
  (val) => {
    if (!val) {
      form.value.reason = '';
      evidenceFile.value = null;
      evidencePreview.value = '';
    }
  }
);

function handleEvidenceChange(e) {
  const file = e.target.files[0];
  if (file) {
    evidenceFile.value = file;
    evidencePreview.value = URL.createObjectURL(file);
  } else {
    evidenceFile.value = null;
    evidencePreview.value = '';
  }
}

async function handleSubmit() {
  if (!props.targetUser?.id || !form.value.reason.trim()) return;

  if (!evidenceFile.value) {
    store.notify('Bukti foto/screenshot pelanggaran wajib disertakan!', 'error');
    return;
  }

  submitting.value = true;
  try {
    const payload = {
      reported_user_id: props.targetUser.id,
      reason_category: form.value.reason_category,
      reason: form.value.reason,
      evidence_image: evidenceFile.value,
    };

    await store.submitUserReport(payload);
    emit('submitted');
    emit('update:modelValue', false);
  } catch (err) {
    // Handled in store
  } finally {
    submitting.value = false;
  }
}
</script>
