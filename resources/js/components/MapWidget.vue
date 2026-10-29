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
        <span class="text-slate-700 font-semibold">Kehilangan (Lost)</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-brand-teal ring-2 ring-teal-200"></span>
        <span class="text-slate-700 font-semibold">Ditemukan (Found)</span>
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

  // Modern Tileset: CartoDB Positron (Clean, sleek, and high contrast)
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
    maxZoom: 19,
    subdomains: 'abcd',
    attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
  }).addTo(mapInstance);

  // Zoom control diposisikan di kanan atas
  L.control.zoom({ position: 'topright' }).addTo(mapInstance);

  // Radius terpusat di Singaraja (Area Radar Nodus)
  radiusCircle = L.circle(SINGARAJA_COORDS, {
    color: '#00C2A8',
    fillColor: '#00C2A8',
    fillOpacity: 0.08,
    weight: 2,
    dashArray: '4, 6',
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
    const lat = item.latitude && !isNaN(item.latitude) ? Number(item.latitude) : SINGARAJA_COORDS[0];
    const lng = item.longitude && !isNaN(item.longitude) ? Number(item.longitude) : SINGARAJA_COORDS[1];

    const isLost = item.type === 'lost';
    const markerColor = isLost ? '#FF6B35' : '#00C2A8';
    const ringColor = isLost ? 'rgba(255, 107, 53, 0.25)' : 'rgba(0, 194, 168, 0.25)';

    // Modern Pin Marker Icon
    const customIcon = L.divIcon({
      className: 'custom-leaflet-marker',
      html: `
        <div style="
          position: relative;
          width: 32px;
          height: 32px;
          display: flex;
          align-items: center;
          justify-content: center;
        ">
          <div style="
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: ${ringColor};
            animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
          "></div>
          <div style="
            position: relative;
            width: 26px;
            height: 26px;
            background: ${markerColor};
            border: 2.5px solid white;
            border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
          ">
            <div style="width: 7px; height: 7px; background: white; border-radius: 9999px;"></div>
          </div>
        </div>
      `,
      iconSize: [32, 32],
      iconAnchor: [16, 16],
    });

    const marker = L.marker([lat, lng], { icon: customIcon });

    marker.bindPopup(`
      <div style="font-family: inherit; font-size: 12px; min-width: 150px; padding: 4px;">
        <span style="display:inline-block; font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 2px 8px; border-radius: 9999px; margin-bottom: 6px; background: ${isLost ? '#FFF1ED' : '#E6F7F5'}; color: ${markerColor};">
          ${isLost ? 'Kehilangan' : 'Ditemukan'}
        </span>
        <div style="font-weight: 700; color: #1B2A33; font-size: 13px; margin-bottom: 3px; line-height: 1.3;">${item.title}</div>
        <div style="color: #64748b; font-size: 11px;">📍 ${item.location_name}</div>
        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px solid #f1f5f9; color: #00C2A8; font-weight: 600; font-size: 11px;">
          Klik untuk melihat detail &rarr;
        </div>
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
  border-radius: 18px !important;
  box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.12), 0 4px 8px -2px rgba(0, 0, 0, 0.05) !important;
  border: 1px solid #f1f5f9;
}
.leaflet-popup-tip {
  background: white;
}
</style>
