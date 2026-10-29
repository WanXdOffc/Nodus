<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 bg-navy-900/50 backdrop-blur-xs flex items-center justify-center p-4"
  >
    <div
      class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95 duration-200"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <div class="w-10 h-10 rounded-2xl bg-orange-50 text-brand-orange flex items-center justify-center">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-base font-extrabold text-navy-900">Beri Ulasan Nodus</h3>
            <p class="text-xs text-text-secondary">Bantu kami meningkatkan kualitas layanan</p>
          </div>
        </div>
        <button
          type="button"
          @click="closeModal"
          class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form @submit.prevent="submitReview" class="space-y-4">
        <!-- Interactive Star Rating -->
        <div class="text-center py-2 bg-slate-50 rounded-2xl border border-slate-100">
          <p class="text-xs font-semibold text-slate-600 mb-2">Seberapa puas Anda dengan Nodus?</p>
          <div class="flex items-center justify-center gap-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              @click="rating = star"
              @mouseenter="hoverRating = star"
              @mouseleave="hoverRating = 0"
              class="p-1 text-2xl transition-transform hover:scale-125 focus:outline-none"
            >
              <svg
                class="w-7 h-7 transition-colors"
                :class="[
                  (hoverRating || rating) >= star
                    ? 'text-brand-orange fill-brand-orange'
                    : 'text-slate-200 fill-slate-200'
                ]"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
            </button>
          </div>
          <span class="inline-block text-[11px] font-bold text-brand-orange mt-1">
            {{ ratingLabels[rating] || 'Pilih Bintang' }}
          </span>
        </div>

        <!-- Komentar Ulasan -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Ceritakan Pengalaman Anda <span class="text-rose-500">*</span>
          </label>
          <textarea
            v-model="comment"
            rows="3"
            required
            maxlength="1000"
            placeholder="Bagikan cerita bagaimana barang Anda kembali atau kemudahan fitur radar AI Nodus..."
            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal transition-colors"
          ></textarea>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="submitting || !comment.trim()"
            class="px-5 py-2.5 bg-brand-teal hover:bg-[#00ab94] disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5"
          >
            <span v-if="submitting">Mengirim...</span>
            <span v-else>Kirim Ulasan</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useAppStore } from '@/store/appStore';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue', 'submitted']);

const store = useAppStore();
const rating = ref(5);
const hoverRating = ref(0);
const comment = ref('');
const submitting = ref(false);

const ratingLabels = {
  1: '1/5 - Sangat Kurang Puas',
  2: '2/5 - Kurang Puas',
  3: '3/5 - Cukup',
  4: '4/5 - Puas',
  5: '5/5 - Sangat Puas & Terbantu!',
};

function closeModal() {
  emit('update:modelValue', false);
}

async function submitReview() {
  if (!comment.value.trim() || submitting.value) return;

  submitting.value = true;
  try {
    const res = await axios.post('/api/reviews', {
      rating: rating.value,
      comment: comment.value,
    });

    store.notify(res.data.message || 'Ulasan Anda berhasil dikirim!', 'success');
    comment.value = '';
    rating.value = 5;
    closeModal();
    emit('submitted');
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal mengirim ulasan.';
    store.notify(msg, 'error');
  } finally {
    submitting.value = false;
  }
}
</script>
