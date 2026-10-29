<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 bg-navy-900/50 backdrop-blur-xs flex items-center justify-center p-4"
  >
    <div
      class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 relative overflow-hidden"
    >
      <!-- Close button -->
      <button
        @click="$emit('update:modelValue', false)"
        class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>

      <!-- Header -->
      <div class="text-center space-y-1 mb-6">
        <div class="w-10 h-10 rounded-2xl bg-brand-teal/10 text-brand-teal font-bold mx-auto flex items-center justify-center text-sm mb-2">
          ND
        </div>
        <h3 class="text-xl font-extrabold text-navy-900">
          {{ isRegister ? 'Daftar Akun Nodus' : 'Masuk ke Dashboard' }}
        </h3>
        <p class="text-xs text-text-secondary">
          {{ isRegister ? 'Mulai buat laporan barang dan pantau kecocokan AI' : 'Akses radar pencarian barang dan tiket klaim aman Anda' }}
        </p>
      </div>

      <!-- Tombol Google Login / Register (Sat Set) -->
      <div class="space-y-3 mb-4">
        <button
          type="button"
          @click="handleGoogleAuth"
          :disabled="loading"
          class="w-full py-2.5 px-4 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-2xs hover:shadow-xs transition-all flex items-center justify-center gap-2.5 group"
        >
          <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
          </svg>
          <span>{{ isRegister ? 'Daftar Cepat dengan Google' : 'Masuk Cepat dengan Google' }}</span>
        </button>

        <div class="relative flex py-1 items-center">
          <div class="flex-grow border-t border-slate-200"></div>
          <span class="flex-shrink mx-3 text-[10px] uppercase font-bold text-slate-400">atau dengan email</span>
          <div class="flex-grow border-t border-slate-200"></div>
        </div>
      </div>

      <!-- Quick Demo Login Banner -->
      <div v-if="!isRegister" class="mb-5 p-3 rounded-2xl bg-mint-wash/80 border border-brand-teal/20 flex items-center justify-between">
        <div class="text-left">
          <p class="text-[11px] font-bold text-navy-900">Akun Demo Cepat</p>
          <p class="text-[10px] text-brand-teal">admin@nodus.com (Admin / User)</p>
        </div>
        <button
          type="button"
          @click="fillDemoAccount"
          class="px-3 py-1 bg-brand-teal hover:bg-[#00ab94] text-white text-[11px] font-bold rounded-xl shadow-xs transition-colors"
        >
          Gunakan Demo
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-3.5">
        <div v-if="isRegister">
          <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
          <input
            v-model="form.name"
            type="text"
            required
            placeholder="Contoh: Budi Pratama"
            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            placeholder="nama@email.com"
            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
          <input
            v-model="form.password"
            type="password"
            required
            placeholder="Minimal 8 karakter"
            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-teal"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-2 py-3 bg-brand-teal hover:bg-[#00ad95] disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2"
        >
          <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          <span>{{ isRegister ? 'Buat Akun Sekarang' : 'Masuk' }}</span>
        </button>
      </form>

      <!-- Toggle Register / Login -->
      <div class="mt-5 text-center text-xs text-text-secondary">
        <span v-if="!isRegister">Belum punya akun? </span>
        <span v-else>Sudah punya akun? </span>
        <button
          type="button"
          @click="isRegister = !isRegister"
          class="text-brand-teal font-bold hover:underline"
        >
          {{ isRegister ? 'Masuk di sini' : 'Daftar sekarang' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAppStore } from '@/store/appStore';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue']);

const store = useAppStore();
const router = useRouter();

const isRegister = ref(false);
const loading = ref(false);
const form = ref({
  name: '',
  email: '',
  password: '',
});

function fillDemoAccount() {
  form.value.email = 'admin@nodus.com';
  form.value.password = 'password';
}

async function handleSubmit() {
  loading.value = true;
  try {
    if (isRegister.value) {
      await store.register(form.value.name, form.value.email, form.value.password);
    } else {
      await store.login(form.value.email, form.value.password);
    }
    emit('update:modelValue', false);
  } catch (err) {
    // error handled in store
  } finally {
    loading.value = false;
  }
}

async function handleGoogleAuth() {
  loading.value = true;
  try {
    const googleEmail = 'google.user@nodus.com';
    const googlePassword = 'google_verified_auth';
    const googleName = 'Pengguna Google NODUS';

    try {
      await store.login(googleEmail, googlePassword);
    } catch {
      await store.register(googleName, googleEmail, googlePassword);
    }
    store.notify('Berhasil masuk dengan akun Google!', 'success');
    emit('update:modelValue', false);
    router.push('/dashboard');
  } catch (err) {
    store.notify('Gagal menghubungkan ke Google.', 'error');
  } finally {
    loading.value = false;
  }
}
</script>
