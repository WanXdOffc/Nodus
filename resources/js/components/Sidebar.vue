<template>
  <aside class="w-full lg:w-[20%] lg:min-w-[220px] bg-white shadow-[4px_0_24px_rgba(0,0,0,0.03)] z-20 flex flex-col justify-between p-6 border-r border-slate-100 flex-shrink-0">
    <div class="space-y-8">
      <!-- Logo NODUS -->
      <div class="flex items-center justify-between">
        <router-link to="/" class="flex items-center gap-2.5 group">
          <div class="w-9 h-9 rounded-2xl bg-brand-teal/10 flex items-center justify-center">
            <span class="w-3.5 h-3.5 rounded-full bg-brand-teal"></span>
          </div>
          <span class="text-2xl font-bold tracking-wider text-navy-900 group-hover:text-brand-teal transition-colors">
            NODUS
          </span>
        </router-link>
      </div>

      <!-- Profil Singkat Pengguna -->
      <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-brand-teal text-white flex items-center justify-center font-bold text-sm">
          {{ userInitial }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-bold text-navy-900 truncate">{{ store.user?.name || 'Pengguna Nodus' }}</p>
          <div class="flex items-center gap-1 text-[11px] text-brand-teal font-semibold">
            <span>⭐ {{ store.user?.trust_score || '5.0' }}</span>
            <span class="text-slate-400 font-normal">Score</span>
          </div>
        </div>
      </div>

      <!-- Navigasi Menu Utama -->
      <nav class="space-y-1.5">
        <router-link
          to="/dashboard"
          active-class="bg-mint-wash text-brand-teal font-bold shadow-xs"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-navy-900 transition-all text-left"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
          </svg>
          <span>Dashboard</span>
        </router-link>

        <router-link
          to="/my-reports"
          active-class="bg-mint-wash text-brand-teal font-bold shadow-xs"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-navy-900 transition-all text-left"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
          </svg>
          <span>Laporanku</span>
        </router-link>

        <router-link
          to="/messages"
          active-class="bg-mint-wash text-brand-teal font-bold shadow-xs"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-navy-900 transition-all text-left"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <span>Pesan</span>
        </router-link>
      </nav>
    </div>

    <!-- Bagian Bawah: Tombol Peringatan 'Report User' Berwarna Teks Merah -->
    <div class="pt-6 border-t border-slate-100 space-y-2">
      <button
        type="button"
        @click="$emit('open-report')"
        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors border border-rose-100"
      >
        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>Report User</span>
      </button>

      <button
        type="button"
        @click="handleLogout"
        class="w-full text-center py-1.5 text-[11px] text-slate-400 hover:text-slate-600 transition-colors"
      >
        Keluar (Logout)
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAppStore } from '@/store/appStore';

defineEmits(['open-report']);

const store = useAppStore();
const router = useRouter();

const userInitial = computed(() => {
  return store.user?.name ? store.user.name.charAt(0).toUpperCase() : 'U';
});

async function handleLogout() {
  await store.logout();
  router.push('/');
}
</script>
