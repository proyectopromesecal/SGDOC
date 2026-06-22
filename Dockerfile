# ============================================================
# SIGEDOC - Dockerfile
# PHP 8.1 + Apache + SQL Server (sqlsrv) + LDAP
# ============================================================
FROM php:8.1-apache

LABEL maintainer="PROMESE/CAL <mesadeayuda@promesecal.gob.do>"
LABEL description="SIGEDOC - Sistema Integrado de Gestión Documental"

# ── Variables de entorno del build ──────────────────────────
ENV DEBIAN_FRONTEND=noninteractive
ENV ACCEPT_EULA=Y

# ── 1. Dependencias del sistema ──────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    gnupg2 \
    apt-transport-https \
    ca-certificates \
    libldap2-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── 2. Microsoft ODBC Driver 18 para SQL Server ──────────────
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl -sSL https://packages.microsoft.com/config/debian/12/prod.list \
        -o /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y --no-install-recommends \
        msodbcsql18 \
        unixodbc-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Fix OpenSSL para SQL Server antiguo (Evita Error 0x2746)
RUN sed -i 's/DEFAULT@SECLEVEL=2/DEFAULT@SECLEVEL=0/g' /etc/ssl/openssl.cnf

# ── 3. Extensiones PHP ───────────────────────────────────────
# Extensiones estándar
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu \
    && docker-php-ext-install -j$(nproc) \
        ldap \
        pdo \
        mbstring \
        xml \
        curl \
        intl \
        zip \
        opcache

# Extensiones SQL Server (sqlsrv + pdo_sqlsrv) via PECL
RUN pecl channel-update pecl.php.net \
    && pecl install sqlsrv-5.11.0 pdo_sqlsrv-5.11.0 \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# ── 4. Composer ──────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── 5. Configuración de Apache ───────────────────────────────
# Habilitar mod_rewrite y mod_headers
RUN a2enmod rewrite headers \
    && sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf

# VirtualHost de SIGEDOC
COPY docker/apache/sigedoc.conf /etc/apache2/sites-available/sigedoc.conf
RUN a2dissite 000-default.conf \
    && a2ensite sigedoc.conf

# ── 6. Configuración de PHP ──────────────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/sigedoc.ini

# ── 7. Copiar código fuente ──────────────────────────────────
WORKDIR /var/www/html

COPY . .

# Instalar dependencias PHP (sin dev, optimizado)
# RUN composer install --no-dev --optimize-autoloader --no-interaction

# ── 8. Permisos de directorios ───────────────────────────────
RUN mkdir -p storage/documentos storage/keys storage/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage

# ── 9. Health check ──────────────────────────────────────────
HEALTHCHECK --interval=30s --timeout=10s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1

EXPOSE 8080
