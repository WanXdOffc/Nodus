import { defineStore } from 'pinia';
import axios from 'axios';

// Konfigurasi dasar axios
axios.defaults.baseURL = '/';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const savedToken = localStorage.getItem('nodus_token') || '';
if (savedToken) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
}

export const useAppStore = defineStore('app', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('nodus_user') || 'null'),
    token: savedToken,
    items: [],
    itemsLoading: false,
    activeItem: null,
    activeTicket: null,
    tickets: [],
    ticketMessages: [],
    messagesLoading: false,
    selectedTypeFilter: '', // '', 'lost', 'found'
    searchQuery: '',
    stats: {
      totalLost: 0,
      totalFound: 0,
      waitingAi: 0,
    },
    toast: {
      show: false,
      message: '',
      type: 'success', // 'success', 'error', 'info'
    },
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    filteredItems: (state) => {
      let list = state.items;
      if (state.selectedTypeFilter) {
        list = list.filter((i) => i.type === state.selectedTypeFilter);
      }
      if (state.searchQuery) {
        const q = state.searchQuery.toLowerCase();
        list = list.filter(
          (i) =>
            i.title?.toLowerCase().includes(q) ||
            i.location_name?.toLowerCase().includes(q) ||
            i.description?.toLowerCase().includes(q)
        );
      }
      return list;
    },
  },

  actions: {
    notify(message, type = 'success') {
      this.toast = { show: true, message, type };
      setTimeout(() => {
        this.toast.show = false;
      }, 4000);
    },

    setAuth(user, token) {
      this.user = user;
      this.token = token;
      localStorage.setItem('nodus_token', token);
      localStorage.setItem('nodus_user', JSON.stringify(user));
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },

    clearAuth() {
      this.user = null;
      this.token = '';
      localStorage.removeItem('nodus_token');
      localStorage.removeItem('nodus_user');
      delete axios.defaults.headers.common['Authorization'];
    },

    async fetchCurrentUser() {
      if (!this.token) return null;
      try {
        const response = await axios.get('/api/user');
        this.user = response.data.user || response.data;
        localStorage.setItem('nodus_user', JSON.stringify(this.user));
        return this.user;
      } catch (err) {
        if (err.response?.status === 401) {
          this.clearAuth();
        }
        return null;
      }
    },

    async login(email, password) {
      try {
        const res = await axios.post('/api/login', { email, password });
        this.setAuth(res.data.user, res.data.access_token);
        this.notify(res.data.message || 'Login berhasil!', 'success');
        return true;
      } catch (err) {
        const msg = err.response?.data?.message || 'Login gagal, periksa email & kata sandi.';
        this.notify(msg, 'error');
        throw err;
      }
    },

    async register(name, email, password) {
      try {
        const res = await axios.post('/api/register', { name, email, password });
        this.setAuth(res.data.user, res.data.access_token);
        this.notify('Pendaftaran berhasil!', 'success');
        return true;
      } catch (err) {
        const msg = err.response?.data?.message || 'Registrasi gagal.';
        this.notify(msg, 'error');
        throw err;
      }
    },

    async logout() {
      try {
        if (this.token) {
          await axios.post('/api/logout');
        }
      } catch (e) {
        // ignore
      } finally {
        this.clearAuth();
        this.notify('Anda telah keluar.', 'info');
      }
    },

    async fetchItems(params = {}) {
      this.itemsLoading = true;
      try {
        const res = await axios.get('/api/items', { params });
        const data = res.data.data || res.data || [];
        this.items = Array.isArray(data) ? data : [];

        // Hitung statistik
        this.calculateStats();
        return this.items;
      } catch (err) {
        console.error('Failed to fetch items:', err);
        return [];
      } finally {
        this.itemsLoading = false;
      }
    },

    calculateStats() {
      let lost = 0;
      let found = 0;
      let waiting = 0;

      this.items.forEach((item) => {
        if (item.type === 'lost') lost++;
        if (item.type === 'found') found++;
        if (item.status === 'searching') waiting++;
      });

      this.stats = {
        totalLost: lost,
        totalFound: found,
        waitingAi: waiting,
      };
    },

    async fetchClaimTickets() {
      if (!this.token) return [];
      try {
        const res = await axios.get('/api/claim-tickets');
        const list = res.data.data || res.data || [];
        this.tickets = Array.isArray(list) ? list : [];
        return this.tickets;
      } catch (err) {
        console.error('Failed to fetch tickets:', err);
        return [];
      }
    },

    async selectItem(item) {
      this.activeItem = item;

      // Cari apakah item ini memiliki ClaimTicket yang terhubung
      await this.fetchClaimTickets();
      const matchTicket = this.tickets.find(
        (t) => t.lost_item_id === item.id || t.found_item_id === item.id
      );

      if (matchTicket) {
        await this.loadActiveTicket(matchTicket.id);
      } else {
        // Jika tidak ada tiket resmi tapi item ini memiliki kecocokan simulasi/AI
        this.activeTicket = null;
        this.ticketMessages = [];
      }
    },

    async loadActiveTicket(ticketId) {
      try {
        const res = await axios.get(`/api/claim-tickets/${ticketId}`);
        this.activeTicket = res.data.ticket || res.data;
        await this.fetchTicketMessages(ticketId);
      } catch (err) {
        console.error('Failed to load active ticket:', err);
      }
    },

    async fetchTicketMessages(ticketId) {
      this.messagesLoading = true;
      try {
        const res = await axios.get(`/api/claim-tickets/${ticketId}/messages`);
        this.ticketMessages = res.data.messages || [];
      } catch (err) {
        console.error('Failed to load messages:', err);
      } finally {
        this.messagesLoading = false;
      }
    },

    async sendTicketMessage(message) {
      if (!this.activeTicket || !message.trim()) return;
      try {
        const res = await axios.post(`/api/claim-tickets/${this.activeTicket.id}/messages`, {
          message,
        });
        const newMsg = res.data.data || res.data;
        this.ticketMessages.push(newMsg);
        return newMsg;
      } catch (err) {
        this.notify('Gagal mengirim pesan chat.', 'error');
        throw err;
      }
    },

    async updateTicketStatus(status) {
      if (!this.activeTicket) return;
      try {
        const res = await axios.patch(`/api/claim-tickets/${this.activeTicket.id}`, {
          status,
        });
        this.activeTicket.status = status;
        const actionLabel = status === 'verified' ? 'Klaim berhasil diverifikasi!' : 'Klaim ditolak.';
        this.notify(actionLabel, status === 'verified' ? 'success' : 'info');
        await this.fetchItems();
      } catch (err) {
        this.notify('Gagal memperbarui status tiket.', 'error');
        throw err;
      }
    },

    async resolveClaimTicket(ticketId) {
      const id = ticketId || this.activeTicket?.id;
      if (!id) return;
      try {
        const res = await axios.post(`/api/claim-tickets/${id}/resolve`);
        if (this.activeTicket && this.activeTicket.id === id) {
          this.activeTicket.status = 'verified';
        }
        const foundTicket = this.tickets.find((t) => t.id === id);
        if (foundTicket) {
          foundTicket.status = 'verified';
        }

        this.notify(res.data.message || 'Klaim Berhasil! Trust Score Anda meningkat.', 'success');
        await this.fetchCurrentUser();
        await this.fetchItems();
        return res.data;
      } catch (err) {
        const errorMsg = err.response?.data?.message || 'Gagal memverifikasi dan menyelesaikan klaim.';
        this.notify(errorMsg, 'error');
        throw err;
      }
    },

    async createItem(formData) {
      try {
        const res = await axios.post('/api/items', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        this.notify('Laporan barang berhasil dibuat & diproses oleh AI Nodus!', 'success');
        await this.fetchItems();
        return res.data;
      } catch (err) {
        const msg = err.response?.data?.message || 'Gagal membuat laporan barang.';
        this.notify(msg, 'error');
        throw err;
      }
    },

    async submitUserReport(payload) {
      try {
        let body = payload;
        let config = {};

        if (payload instanceof FormData) {
          body = payload;
          config = { headers: { 'Content-Type': 'multipart/form-data' } };
        } else if (payload && typeof payload === 'object') {
          const fd = new FormData();
          fd.append('reported_user_id', payload.reported_user_id);
          fd.append('reason', payload.reason || '');
          if (payload.reason_category) {
            fd.append('reason_category', payload.reason_category);
          }
          if (payload.evidence_image instanceof File) {
            fd.append('evidence_image', payload.evidence_image);
          }
          body = fd;
          config = { headers: { 'Content-Type': 'multipart/form-data' } };
        }

        const res = await axios.post('/api/user-reports', body, config);
        this.notify('Laporan beserta bukti berhasil dikirim ke tim moderator.', 'success');
        return res.data;
      } catch (err) {
        const msg = err.response?.data?.message || 'Gagal mengirim laporan pengguna.';
        this.notify(msg, 'error');
        throw err;
      }
    },
  },
});
