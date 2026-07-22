#!/bin/bash

# ============================================================
# START.SH — Starter tunggal untuk SLA_MONITOR2 + SLA_MONITORING
# Jalankan: bash start.sh  (atau: chmod +x start.sh && ./start.sh)
# ============================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SLA_MONITOR_DIR="$SCRIPT_DIR"
SLA_BACKEND_DIR="$SCRIPT_DIR/SLA_MONITORING"
PHP_INI="/home/root/Documents/php.ini"

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo ""
echo -e "${CYAN}╔══════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║        SLA Monitor - Unified Server Starter       ║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════════╝${NC}"
echo ""

# -----------------------------------------------------------
# Cek apakah port sudah dipakai
# -----------------------------------------------------------
check_port() {
    local port=$1
    if ss -tlnp 2>/dev/null | grep -q ":$port "; then
        return 0  # port sudah dipakai
    fi
    return 1  # port kosong
}

# -----------------------------------------------------------
# Hentikan proses lama jika ada
# -----------------------------------------------------------
stop_old_servers() {
    local pids
    pids=$(pgrep -f "php -S localhost:8000" 2>/dev/null)
    if [ -n "$pids" ]; then
        echo -e "${YELLOW}⚠  Menutup server lama di port 8000 (PID: $pids)...${NC}"
        kill $pids 2>/dev/null
        sleep 1
    fi

    pids=$(pgrep -f "php -S localhost:8001" 2>/dev/null)
    if [ -n "$pids" ]; then
        echo -e "${YELLOW}⚠  Menutup server lama di port 8001 (PID: $pids)...${NC}"
        kill $pids 2>/dev/null
        sleep 1
    fi
}

stop_old_servers

# -----------------------------------------------------------
# Cek apakah php.ini ada
# -----------------------------------------------------------
PHP_INI_ARGS=""
if [ -f "$PHP_INI" ]; then
    PHP_INI_ARGS="-c $PHP_INI"
fi

# -----------------------------------------------------------
# Start Server 1: SLA_MONITOR2 (DSI Log Book) — port 8000
# -----------------------------------------------------------
echo -e "${GREEN}▶  Memulai SLA_MONITOR2 (DSI Log Book) di port 8000...${NC}"
cd "$SLA_MONITOR_DIR" || exit 1
PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:8000 $PHP_INI_ARGS > /tmp/sla_monitor2.log 2>&1 &
SERVER1_PID=$!
sleep 0.5

if kill -0 "$SERVER1_PID" 2>/dev/null; then
    echo -e "${GREEN}   ✔ Server 1 berjalan (PID: $SERVER1_PID)${NC}"
    echo -e "   📌 URL: ${CYAN}http://localhost:8000${NC}"
else
    echo -e "${RED}   ✘ Server 1 GAGAL dijalankan! Cek /tmp/sla_monitor2.log${NC}"
fi

# -----------------------------------------------------------
# Start Server 2: SLA_MONITORING (Backend API) — port 8001
# -----------------------------------------------------------
echo ""
echo -e "${GREEN}▶  Memulai SLA_MONITORING (Backend API) di port 8001...${NC}"
cd "$SLA_BACKEND_DIR" || { echo -e "${RED}Folder SLA_MONITORING tidak ditemukan!${NC}"; exit 1; }
PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:8001 -t public public/index.php > /tmp/sla_monitoring.log 2>&1 &
SERVER2_PID=$!
sleep 0.5

if kill -0 "$SERVER2_PID" 2>/dev/null; then
    echo -e "${GREEN}   ✔ Server 2 berjalan (PID: $SERVER2_PID)${NC}"
    echo -e "   📌 URL: ${CYAN}http://localhost:8001${NC}"
else
    echo -e "${RED}   ✘ Server 2 GAGAL dijalankan! Cek /tmp/sla_monitoring.log${NC}"
fi

# -----------------------------------------------------------
# Simpan PID ke file untuk stop.sh
# -----------------------------------------------------------
echo "$SERVER1_PID" > /tmp/sla_server_pids.txt
echo "$SERVER2_PID" >> /tmp/sla_server_pids.txt

# -----------------------------------------------------------
# Info akhir
# -----------------------------------------------------------
echo ""
echo -e "${CYAN}══════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Kedua server berjalan! Akses aplikasi di:${NC}"
echo -e "   🌐 ${CYAN}http://localhost:8000${NC}  ← Buka ini di browser"
echo -e "   🔧 ${CYAN}http://localhost:8001${NC}  ← SLA Monitoring API (embed via iframe)"
echo ""
echo -e "   Menu SLA Monitoring tersedia di tab${NC} ${YELLOW}'SLA Monitoring'${NC}"
echo -e "   di dalam aplikasi utama."
echo ""
echo -e "   Untuk menghentikan: ${YELLOW}bash stop.sh${NC}"
echo -e "${CYAN}══════════════════════════════════════════════════${NC}"
echo ""

# -----------------------------------------------------------
# Tunggu — jangan exit agar proses tidak mati (opsional)
# Tekan Ctrl+C untuk keluar & kedua server tetap jalan di background
# -----------------------------------------------------------
echo -e "${YELLOW}💡 Server berjalan di background. Tekan Ctrl+C untuk kembali ke terminal.${NC}"
echo -e "   (Server tidak akan berhenti meski terminal ditutup)"
echo ""

# Tunggu sampai Ctrl+C
wait
