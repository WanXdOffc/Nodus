<template>
  <header class="fixed top-4 left-0 right-0 z-50 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto">
      <nav class="bg-white/95 backdrop-blur-md border border-slate-100 rounded-full px-5 sm:px-8 py-3 shadow-[0_4px_20px_rgba(0,0,0,0.06)] flex items-center justify-between transition-all duration-300">
        <!-- Logo Kiri -->
        <a href="#" class="flex items-center gap-2 group">
          <div class="w-8 h-8 rounded-full bg-brand-teal/10 flex items-center justify-center">
            <span class="w-3 h-3 rounded-full bg-brand-teal"></span>
          </div>
          <span class="text-xl sm:text-2xl font-bold tracking-wider text-navy-900 group-hover:text-brand-teal transition-colors">
            NODUS
          </span>
        </a>

        <!-- Link Navigasi Tengah (Desktop) -->
        <div class="hidden md:flex items-center gap-8">
          <a
            href="#hero"
            class="text-sm font-medium text-slate-600 hover:text-brand-teal transition-colors"
          >
            Home
          </a>
          <a
            href="#cara-kerja"
            class="text-sm font-medium text-slate-600 hover:text-brand-teal transition-colors"
          >
            Cara Kerja
          </a>
          <a
            href="#keamanan"
            class="text-sm font-medium text-slate-600 hover:text-brand-teal transition-colors"
          >
            Keamanan
          </a>
        </div>

        <!-- Sisi Kanan: Aksi (Login & Tombol Portal Teal) -->
        <div class="hidden sm:flex items-center gap-3">
          <a
            href="#login"
            class="text-sm font-semibold text-slate-700 hover:text-brand-teal px-3 py-2 transition-colors"
          >
            Login
          </a>
          <a
            href="/admin"
            class="inline-flex items-center gap-1.5 bg-brand-teal hover:bg-[#00ab94] text-white text-sm font-medium px-5 py-2.5 rounded-full shadow-sm hover:shadow transition-all duration-200"
          >
            <span>Portal</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </a>
        </div>

        <!-- Tombol Menu Mobile Hamburger -->
        <button
          type="button"
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden p-2 rounded-full text-slate-600 hover:text-navy-900 hover:bg-slate-50 transition-colors"
          aria-label="Toggle menu"
        >
          <svg v-if="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </nav>

      <!-- Dropdown Mobile Menu -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div
          v-if="mobileMenuOpen"
          class="md:hidden mt-2 bg-white rounded-2xl p-4 shadow-lg border border-slate-100 space-y-3"
        >
          <a
            href="#hero"
            @click="mobileMenuOpen = false"
            class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-xl"
          >
            Home
          </a>
          <a
            href="#cara-kerja"
            @click="mobileMenuOpen = false"
            class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-xl"
          >
            Cara Kerja
          </a>
          <a
            href="#keamanan"
            @click="mobileMenuOpen = false"
            class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-xl"
          >
            Keamanan
          </a>
          <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
            <a
              href="#login"
              class="text-sm font-semibold text-slate-700 px-3 py-2"
            >
              Login
            </a>
            <a
              href="/admin"
              class="inline-flex items-center gap-1.5 bg-brand-teal text-white text-sm font-medium px-5 py-2 rounded-full"
            >
              <span>Portal</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </a>
          </div>
        </div>
      </transition>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';

const mobileMenuOpen = ref(false);
</script>
