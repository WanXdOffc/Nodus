<template>
  <div class="relative w-full h-64 sm:h-72 rounded-3xl overflow-hidden border border-slate-200/70 shadow-sm bg-slate-100 z-0">
    <!-- Map Container -->
    <div ref="mapContainer" class="w-full h-full z-0"></div>

    <!-- Floating Map Control Info -->
    <div class="absolute top-3 left-4 z-[400] bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-slate-200/80 shadow-sm flex items-center gap-2 pointer-events-auto">
      <span class="w-2 h-2 rounded-full bg-brand-teal animate-pulse"></span>
      <span class="text-xs font-semibold text-navy-900">Singaraja, Buleleng</span>
      <span class="text-[10px] text-text-secondary">Radius 3.5 km</span>
    </div>

    <!-- Map Legend -->
    <div class="absolute bottom-3 right-4 z-[400] bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3 text-[11px] pointer-events-auto">
      <div class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-brand-orange"></span>
        <span class="text-slate-700 font-medium">Hilang</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-brand-teal"></span>
        <span class="text-slate-700 font-medium">Ditemukan</span>
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

  // Tile layer dari OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(mapInstance);

  // Zoom control diposisikan di kanan atas
  L.control.zoom({ position: 'topright' }).addTo(mapInstance);

  // Radius terpusat di Singaraja
  radiusCircle = L.circle(SINGARAJA_COORDS, {
    color: '#00C2A8',
    fillColor: '#00C2A8',
    fillOpacity: 0.12,
    weight: 2,
    radius: 3500, // 3.5 km radius
  }).addTo(mapInstance);

  markersLayer = L.layerGroup().addTo(mapInstance);
  renderMarkers();
});

onUnmounted(() => {
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
    // Gunakan koordinat item atau sebaran sekitar Singaraja jika di luar rentang
    const lat = item.latitude && !isNaN(item.latitude) ? Number(item.latitude) : SINGARAJA_COORDS[0];
    const lng = item.longitude && !isNaN(item.longitude) ? Number(item.longitude) : SINGARAJA_COORDS[1];

    const isLost = item.type === 'lost';
    const markerColor = isLost ? '#FF6B35' : '#00C2A8';

    // Custom HTML Marker Icon
    const customIcon = L.divIcon({
      className: 'custom-leaflet-marker',
      html: `
        <div style="
          width: 28px;
          height: 28px;
          background: ${markerColor};
          border: 3px solid white;
          border-radius: 9999px;
          box-shadow: 0 4px 12px rgba(0,0,0,0.25);
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
        ">
          <div style="width: 8px; height: 8px; background: white; border-radius: 9999px;"></div>
        </div>
      `,
      iconSize: [28, 28],
      iconAnchor: [14, 14],
    });

    const marker = L.marker([lat, lng], { icon: customIcon });

    marker.bindPopup(`
      <div style="font-family: inherit; font-size: 12px; min-width: 140px; padding: 4px;">
        <span style="display:inline-block; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 9999px; margin-bottom: 4px; background: ${isLost ? '#FFF1ED' : '#E6F7F5'}; color: ${markerColor};">
          ${isLost ? 'KEHILANGAN' : 'DITEMUKAN'}
        </span>
        <div style="font-weight: bold; color: #1B2A33; font-size: 13px; margin-bottom: 2px;">${item.title}</div>
        <div style="color: #666; font-size: 11px;">📍 ${item.location_name}</div>
      </div>
    `);

    marker.on('click', () => {
      store.selectItem(item);
    });

    markersLayer.addLayer(marker);
  });
}
</script>

<style>
/* Leaflet clean popup reset */
.leaflet-popup-content-wrapper {
  border-radius: 16px !important;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
  border: 1px solid #f1f5f9;
}
.leaflet-popup-tip {
  background: white;
}
</style>
