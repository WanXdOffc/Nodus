<template>
  <div class="relative w-full h-64 sm:h-72 rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm bg-slate-100 z-0">
    <!-- Map Container -->
    <div ref="mapContainer" class="w-full h-full z-0"></div>

    <!-- Soft Inner Shadow Overlay for Premium Depth -->
    <div class="absolute inset-0 rounded-3xl pointer-events-none shadow-[inset_0_2px_8px_rgba(0,0,0,0.08)] z-[390]"></div>

    <!-- Floating Map Control Info -->
    <div class="absolute top-3 left-4 z-[400] bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-slate-200/80 shadow-sm flex items-center gap-2 pointer-events-auto">
      <span class="w-2 h-2 rounded-full bg-brand-teal animate-pulse"></span>
      <span class="text-xs font-semibold text-navy-900">Singaraja, Buleleng</span>
      <span class="text-[10px] text-text-secondary">Radius 3.5 km</span>
    </div>

    <!-- Map Legend -->
    <div class="absolute bottom-3 right-4 z-[400] bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 text-[11px] pointer-events-auto">
      <div class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-brand-orange ring-2 ring-orange-200"></span>
        <span class="text-slate-700 font-semibold">Zona Hilang (~300m)</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-brand-teal ring-2 ring-teal-200"></span>
        <span class="text-slate-700 font-semibold">Zona Temuan (~300m)</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useAppStore } from '@/store/appStore';

const store = useAppStore();
const mapContainer = ref(null);
let mapInstance = null;
let markersLayer = null;
let radiusCircle = null;

// Koordinat Singaraja, Buleleng, Bali
const SINGARAJA_COORDS = [-8.112, 115.088];

onMounted(() => {
  if (!mapContainer.value) return;

  // Inisialisasi Peta Leaflet
  mapInstance = L.map(mapContainer.value, {
    center: SINGARAJA_COORDS,
    zoom: 13,
    zoomControl: false,
  });

  // Tile layer dari OpenStreetMap (Aman, stabil, dan tanpa batasan API key)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(mapInstance);

  // Zoom control diposisikan di kanan atas
  L.control.zoom({ position: 'topright' }).addTo(mapInstance);

  // Radius terpusat di Singaraja (Radar Modern Teal Dashed)
  radiusCircle = L.circle(SINGARAJA_COORDS, {
    color: '#14B8A6',
    fillColor: '#14B8A6',
    fillOpacity: 0.05,
    weight: 2,
    dashArray: '6, 8',
    radius: 3500, // 3.5 km radius
  }).addTo(mapInstance);

  markersLayer = L.layerGroup().addTo(mapInstance);
  renderMarkers();

  // Pastikan Leaflet menghitung dimensi kontainer secara akurat
  setTimeout(() => {
    mapInstance?.invalidateSize();
  }, 250);

  window.addEventListener('resize', handleResize);
});

function handleResize() {
  if (mapInstance) {
    mapInstance.invalidateSize();
  }
}

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
  if (mapInstance) {
    mapInstance.remove();
    mapInstance = null;
  }
});

// Perbarui marker saat data barang di store berubah
watch(
  () => store.filteredItems,
  () => {
    renderMarkers();
  },
  { deep: true }
);

function renderMarkers() {
  if (!mapInstance || !markersLayer) return;

  markersLayer.clearLayers();

  const items = store.filteredItems;

  items.forEach((item) => {
    const lat = item.latitude && !isNaN(item.latitude) ? Number(item.latitude) : SINGARAJA_COORDS[0];
    const lng = item.longitude && !isNaN(item.longitude) ? Number(item.longitude) : SINGARAJA_COORDS[1];

    const isLost = item.type === 'lost';
    const markerColor = isLost ? '#FF6B35' : '#14B8A6';

    // Gunakan L.circle dengan radius 300 meter untuk mengaburkan lokasi publik demi privasi
    const circle = L.circle([lat, lng], {
      radius: 300,
      color: '#14B8A6',
      fillColor: markerColor,
      fillOpacity: 0.1,
      weight: 1,
      dashArray: '5, 5',
    });

    circle.bindPopup(`
      <div style="font-family: inherit; font-size: 12px; min-width: 160px; padding: 4px;">
        <span style="display:inline-block; font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 2px 8px; border-radius: 9999px; margin-bottom: 6px; background: ${isLost ? '#FFF1ED' : '#E6F7F5'}; color: ${markerColor};">
          Zona Perkiraan (~300m)
        </span>
        <div style="font-weight: 700; color: #1B2A33; font-size: 13px; margin-bottom: 3px; line-height: 1.3;">${item.title}</div>
        <div style="color: #64748b; font-size: 11px;">📍 Area ${item.location_name || 'Singaraja'}</div>
        <div style="color: #94a3b8; font-size: 10px; margin-top: 2px;">🛡️ Lokasi dikaburkan demi privasi</div>
        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px solid #f1f5f9; color: #14B8A6; font-weight: 600; font-size: 11px; cursor: pointer;">
          Klik untuk melihat detail &rarr;
        </div>
      </div>
    `);

    circle.on('click', () => {
      store.selectItem(item);
    });

    markersLayer.addLayer(circle);
  });
}
</script>

<style>
/* Leaflet clean popup reset */
.leaflet-popup-content-wrapper {
  border-radius: 18px !important;
  box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.12), 0 4px 8px -2px rgba(0, 0, 0, 0.05) !important;
  border: 1px solid #f1f5f9;
}
.leaflet-popup-tip {
  background: white;
}
</style>
