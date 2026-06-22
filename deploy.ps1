#!/usr/bin/env pwsh
# ============================================================
# SIGEDOC - Script de Despliegue a Produccion
# Servidor: mesa.hector@10.70.69.15
# Ruta:     /home/mesa.hector/SIGEDOC
# ============================================================

param (
    [Parameter(Mandatory=$true)]
    [string]$Password
)

$SERVER   = "10.70.69.15"
$USER     = "mesa.hector"
$RUTA     = "/home/mesa.hector/SIGEDOC"
$LOCAL    = "C:\Temp\SIGEDOC"
$VERSION  = "v2.2.7"

Write-Host ""
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host "  SIGEDOC - Despliegue a Produccion $VERSION" -ForegroundColor Cyan
Write-Host "  Servidor: $USER@$SERVER" -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host ""

$passPlain = $Password


Write-Host ""
Write-Host "[1/4] Comprimiendo y subiendo código fuente..." -ForegroundColor Yellow

# Comprimir todo el proyecto local (excluyendo la carpeta docker/db si fuera necesario, pero por ahora todo)
& tar.exe -czf "$LOCAL\sigedoc_deploy.tar.gz" -C "$LOCAL" app public views config.php docker docker-compose.yml Dockerfile

if ($LASTEXITCODE -ne 0) {
    Write-Host "[!] Error al comprimir el proyecto." -ForegroundColor Red
    exit 1
}

Write-Host "  -> Subiendo sigedoc_deploy.tar.gz..." -NoNewline
$result = & "C:\Program Files\PuTTY\pscp.exe" -pw $passPlain -batch "$LOCAL\sigedoc_deploy.tar.gz" "${USER}@${SERVER}:$RUTA/sigedoc_deploy.tar.gz" 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host " OK" -ForegroundColor Green
} else {
    Write-Host " ERROR" -ForegroundColor Red
    Write-Host "    $result" -ForegroundColor Red
    exit 1
}

Write-Host "  -> Extrayendo archivos en el servidor..." -NoNewline
$extractCmd = "cd $RUTA && tar -xzf sigedoc_deploy.tar.gz && rm sigedoc_deploy.tar.gz"
& "C:\Program Files\PuTTY\plink.exe" -pw $passPlain -batch "${USER}@${SERVER}" $extractCmd

if ($LASTEXITCODE -eq 0) {
    Write-Host " OK" -ForegroundColor Green
} else {
    Write-Host " ERROR" -ForegroundColor Red
    exit 1
}

# Limpiar archivo local
Remove-Item "$LOCAL\sigedoc_deploy.tar.gz" -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "[2/4] Reconstruyendo imagen Docker en el servidor..." -ForegroundColor Yellow

$buildCmd = @"
cd $RUTA && \
echo '--- Build ---' && \
echo '$Password' | sudo -S docker build -t sigedoc_app:$VERSION . && \
echo '--- Build OK ---'
"@

& "C:\Program Files\PuTTY\plink.exe" -pw $passPlain -batch `
    "${USER}@${SERVER}" $buildCmd

if ($LASTEXITCODE -ne 0) {
    Write-Host "[!] Error al construir la imagen Docker." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "[3/4] Reemplazando contenedor en ejecucion..." -ForegroundColor Yellow

$deployCmd = @"
cd $RUTA && \
echo '$Password' | sudo -S docker compose down --remove-orphans && \
echo '$Password' | sudo -S APP_VERSION=$VERSION docker compose up -d && \
echo '--- Contenedor levantado ---'
"@

& "C:\Program Files\PuTTY\plink.exe" -pw $passPlain -batch `
    "${USER}@${SERVER}" $deployCmd

if ($LASTEXITCODE -ne 0) {
    Write-Host "[!] Error al redesplegar el contenedor." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "[4/4] Verificando estado del contenedor..." -ForegroundColor Yellow

Start-Sleep -Seconds 5

$statusCmd = "echo '$Password' | sudo -S docker ps --filter name=sigedoc_app --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}'"

& "C:\Program Files\PuTTY\plink.exe" -pw $passPlain -batch `
    "${USER}@${SERVER}" $statusCmd

Write-Host ""
Write-Host "======================================================" -ForegroundColor Green
Write-Host "  Despliegue completado exitosamente!" -ForegroundColor Green
Write-Host "  URL: http://${SERVER}:8080" -ForegroundColor Green
Write-Host "======================================================" -ForegroundColor Green
Write-Host ""

# Limpiar variable de contrasena de la memoria
$passPlain = $null
$secPass   = $null
[System.GC]::Collect()
