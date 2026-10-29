<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
  >
    <div
      class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-100 my-8 relative overflow-hidden"
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
      <div class="mb-6 space-y-1">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-mint-wash text-brand-teal text-[11px] font-bold mb-1">
          <span class="w-2 h-2 rounded-full bg-brand-teal animate-pulse"></span>
          AI Powered Matching
        </div>
        <h3 class="text-xl sm:text-2xl font-extrabold text-navy-900">
          Buat Laporan Barang
        </h3>
        <p class="text-xs text-text-secondary">
          AI Gemini akan mengekstrak kata kunci dan otomatis mencocokkan laporan Anda secara geografis.
        </p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Pilihan Tipe: Kehilangan vs Ditemukan -->
        <div class="grid grid-cols-2 gap-3 p-1 bg-slate-100 rounded-2xl">
          <button
            type="button"
            @click="form.type = 'lost'"
            class="py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
            :class="form.type === 'lost' ? 'bg-brand-orange text-white shadow-xs' : 'text-slate-600 hover:text-navy-900'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>Barang Kehilangan (Lost)</span>
          </button>

          <button
            type="button"
            @click="form.type = 'found'"
            class="py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
            :class="form.type === 'found' ? 'bg-brand-teal text-white shadow-xs' : 'text-slate-600 hover:text-navy-900'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Barang Ditemukan (Found)</span>
          </button>
        </div>

        <!-- Judul & Kategori -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              Nama / Judul Barang <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.title"
              type="text"
              required
              placeholder="Contoh: Dompet Kulit Braun Buffel Hitam"
              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              Kategori <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.category"
              required
              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal bg-white"
            >
              <option value="Dompet & Tas">Dompet & Tas</option>
              <option value="Elektronik & Gadget">Elektronik & Gadget</option>
              <option value="Dokumen & Kartu">Dokumen & Kartu</option>
              <option value="Kunci & Kendaraan">Kunci & Kendaraan</option>
              <option value="Perhiasan & Jam">Perhiasan & Jam</option>
              <option value="Barang Pribadi Lainnya">Barang Pribadi Lainnya</option>
            </select>
          </div>
        </div>

        <!-- Deskripsi Barang -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Deskripsi &amp; Ciri Spesifik <span class="text-rose-500">*</span>
          </label>
          <textarea
            v-model="form.description"
            rows="2"
            required
            placeholder="Jelaskan warna, merk, isi, atau tanda pengenal khusus..."
            class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
          ></textarea>
        </div>

        <!-- Tanggal & Nama Lokasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              Nama Lokasi Spesifik <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.location_name"
              type="text"
              required
              placeholder="Contoh: Taman Kota Singaraja / Pantai Penimbangan"
              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              Tanggal Kejadian <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.item_date"
              type="date"
              required
              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal bg-white"
            />
          </div>
        </div>

        <!-- Pemilih Titik Koordinat Peta Mini Leaflet -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-semibold text-slate-700">
              Pilih Titik di Peta (Klik pada Peta) <span class="text-rose-500">*</span>
            </label>
            <span class="text-[10px] text-text-secondary font-mono">
              Lat: {{ form.latitude.toFixed(4) }}, Lng: {{ form.longitude.toFixed(4) }}
            </span>
          </div>

          <div class="relative w-full h-44 rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 z-0">
            <div ref="miniMapContainer" class="w-full h-full z-0"></div>
            <div class="absolute bottom-2 left-3 z-[400] bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-full text-[10px] font-semibold text-navy-900 border border-slate-200/80 pointer-events-none">
              📍 Klik peta untuk memindahkan pin lokasi
            </div>
          </div>
        </div>

        <!-- Unggah Foto Barang -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Foto Barang (Opsional, JPG/PNG maks 5MB)
          </label>
          <div class="flex items-center gap-3">
            <input
              type="file"
              accept="image/*"
              @change="handleImageChange"
              class="text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-mint-wash file:text-brand-teal hover:file:bg-[#00ab94]/20 cursor-pointer"
            />
            <div v-if="imagePreview" class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0">
              <img :src="imagePreview" class="w-full h-full object-cover" />
            </div>
          </div>
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
            :disabled="submitting"
            class="px-6 py-2.5 bg-brand-teal hover:bg-[#00ab94] disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-2"
          >
            <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>{{ submitting ? 'Memproses AI & Menyimpan...' : 'Kirim Laporan' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useAppStore } from '@/store/appStore';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  initialType: {
    type: String,
    default: 'lost',
  },
});

const emit = defineEmits(['update:modelValue', 'created']);
const store = useAppStore();

const submitting = ref(false);
const miniMapContainer = ref(null);
let miniMap = null;
let currentMarker = null;

const imageFile = ref(null);
const imagePreview = ref('');

const SINGARAJA_DEFAULT = [-8.112, 115.088];

const form = ref({
  type: props.initialType || 'lost',
  title: '',
  description: '',
  category: 'Dompet & Tas',
  location_name: '',
  item_date: new Date().toISOString().split('T')[0],
  latitude: SINGARAJA_DEFAULT[0],
  longitude: SINGARAJA_DEFAULT[1],
});

watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      nextTick(() => {
        initMiniMap();
      });
    } else {
      destroyMiniMap();
    }
  }
);

function initMiniMap() {
  if (!miniMapContainer.value || miniMap) return;

  miniMap = L.map(miniMapContainer.value, {
    center: [form.value.latitude, form.value.longitude],
    zoom: 14,
    zoomControl: false,
  });

  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
    maxZoom: 19,
    subdomains: 'abcd',
    attribution: '&copy; OpenStreetMap &copy; CARTO',
  }).addTo(miniMap);

  L.control.zoom({ position: 'topright' }).addTo(miniMap);

  updateMarker(form.value.latitude, form.value.longitude);

  miniMap.on('click', (e) => {
    form.value.latitude = e.latlng.lat;
    form.value.longitude = e.latlng.lng;
    updateMarker(e.latlng.lat, e.latlng.lng);
  });
}

function updateMarker(lat, lng) {
  if (!miniMap) return;
  const isLost = form.value.type === 'lost';
  const markerColor = isLost ? '#FF6B35' : '#00C2A8';

  if (currentMarker) {
    miniMap.removeLayer(currentMarker);
  }

  const icon = L.divIcon({
    className: 'picker-marker',
    html: `
      <div style="
        width: 24px;
        height: 24px;
        background: ${markerColor};
        border: 3px solid white;
        border-radius: 9999px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      "></div>
    `,
    iconSize: [24, 24],
    iconAnchor: [12, 12],
  });

  currentMarker = L.marker([lat, lng], { icon }).addTo(miniMap);
}

watch(
  () => form.value.type,
  () => {
    updateMarker(form.value.latitude, form.value.longitude);
  }
);

function destroyMiniMap() {
  if (miniMap) {
    miniMap.remove();
    miniMap = null;
    currentMarker = null;
  }
}

onUnmounted(() => {
  destroyMiniMap();
});

function handleImageChange(e) {
  const file = e.target.files[0];
  if (file) {
    imageFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  } else {
    imageFile.value = null;
    imagePreview.value = '';
  }
}

async function handleSubmit() {
  submitting.value = true;
  try {
    const formData = new FormData();
    formData.append('type', form.value.type);
    formData.append('title', form.value.title);
    formData.append('description', form.value.description);
    formData.append('category', form.value.category);
    formData.append('location_name', form.value.location_name);
    formData.append('item_date', form.value.item_date);
    formData.append('latitude', form.value.latitude);
    formData.append('longitude', form.value.longitude);

    if (imageFile.value) {
      formData.append('image', imageFile.value);
    }

    const created = await store.createItem(formData);
    emit('created', created);
    emit('update:modelValue', false);

    // Reset form
    form.value.title = '';
    form.value.description = '';
    form.value.location_name = '';
    imageFile.value = null;
    imagePreview.value = '';
  } catch (err) {
    // Handled in store
  } finally {
    submitting.value = false;
  }
}
</script>
