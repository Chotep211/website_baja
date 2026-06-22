FROM php:8.2-cli

# Install dependency sistem + ekstensi PHP yang dibutuhkan: pdo_pgsql (Supabase/PostgreSQL), curl, mbstring
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && docker-php-ext-install pdo pdo_pgsql pgsql curl mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy semua file project ke dalam container
COPY . .

# Railway akan inject variable PORT secara otomatis saat runtime
EXPOSE 8080

# Jalankan PHP built-in server, bind ke 0.0.0.0 dan port dari Railway
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t ."]