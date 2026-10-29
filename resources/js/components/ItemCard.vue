<template>
  <div
    @click="$emit('select', item)"
    class="cursor-pointer p-4 rounded-2xl bg-white border transition-all duration-200 group flex items-start gap-4 hover:shadow-card"
    :class="[
      isSelected
        ? 'ring-2 ring-brand-teal border-brand-teal bg-mint-wash/20 shadow-sm'
        : 'border-slate-200/70 hover:border-slate-300'
    ]"
  >
    <!-- Thumbnail Kotak -->
    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-100 flex-shrink-0 overflow-hidden relative border border-slate-100 flex items-center justify-center">
      <img
        v-if="item.image_path"
        :src="'/storage/' + item.image_path"
        :alt="item.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div v-else class="text-slate-400 flex flex-col items-center justify-center">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <!-- Type Indicator Tag on Thumbnail -->
      <span
        class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold text-white uppercase"
        :class="item.type === 'lost' ? 'bg-brand-orange' : 'bg-brand-teal'"
      >
        {{ item.type }}
      </span>
    </div>

    <!-- Informasi Barang -->
    <div class="flex-1 min-w-0 space-y-1.5">
      <div class="flex items-center justify-between gap-2">
        <span class="text-[11px] font-semibold text-text-secondary tracking-wide uppercase">
          {{ item.category || 'Umum' }} &bull; {{ formatDate(item.item_date) }}
        </span>

        <!-- AI Match Badge (Jika ada kecocokan) -->
        <span
          v-if="item.has_ai_match || item.status === 'matched'"
          class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-mint-wash text-brand-teal border border-brand-teal/20"
        >
          <span class="w-1.5 h-1.5 rounded-full bg-brand-teal animate-pulse"></span>
          AI MATCH {{ item.match_score || 95 }}%
        </span>
      </div>

      <!-- Judul -->
      <h4 class="text-sm sm:text-base font-bold text-navy-900 group-hover:text-brand-teal transition-colors truncate">
        {{ item.title }}
      </h4>

      <!-- Lokasi -->
      <p class="flex items-center gap-1 text-xs text-text-secondary truncate">
        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>{{ item.location_name }}</span>
      </p>

      <!-- AI Tags Mini Pills -->
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
  </div>
</template>

<script setup>
defineProps({
  item: {
    type: Object,
    required: true,
  },
  isSelected: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['select']);

function formatDate(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
  });
}
</script>
