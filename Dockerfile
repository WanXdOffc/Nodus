# ==============================================================================
# Stage 1: Build Frontend Assets (Vue 3 + Vite + Tailwind CSS)
# ==============================================================================
FROM node:20-alpine AS frontend

WORKDIR /app

# Salin definisi dependensi paket
COPY package*.json ./

# Pasang dependensi JavaScript
RUN npm install

# Salin source code frontend dan konfigurasi build
COPY . .

# Kompilasi aset Vue untuk lingkungan produksi
RUN npm run build

# ==============================================================================
# Stage 2: PHP 8.3 + Nginx (Base Image: webdevops/php-nginx:8.3-alpine)
# ==============================================================================
FROM webdevops/php-nginx:8.3-alpine

WORKDIR /app

# Set environment variable Document Root untuk Nginx vhost Laravel
ENV WEB_DOCUMENT_ROOT=/app/public
ENV APP_ENV=production

# Copy seluruh source code Laravel ke /app
COPY . /app

# Copy hasil build aset frontend dari Stage 1 ke public/build
COPY --from=frontend /app/public/build /app/public/build

# Pasang dependensi PHP untuk lingkungan produksi
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ubah kepemilikan dan permission direktori storage dan bootstrap/cache ke user application
RUN chown -R application:application /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Buat symbolic link storage jika belum ada
RUN php artisan storage:link || true

EXPOSE 80

