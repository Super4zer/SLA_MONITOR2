#!/bin/bash

# ============================================================
# STOP.SH — Menghentikan semua server SLA Monitor
# ============================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

echo ""
echo -e "${YELLOW}🛑 Menghentikan SLA Monitor servers...${NC}"

stopped=0

# Hentikan dari file PID jika ada
if [ -f /tmp/sla_server_pids.txt ]; then
    while IFS= read -r pid; do
        if [ -n "$pid" ] && kill -0 "$pid" 2>/dev/null; then
            kill "$pid" 2>/dev/null
            echo -e "${GREEN}   ✔ Proses PID $pid dihentikan${NC}"
            stopped=$((stopped+1))
        fi
    done < /tmp/sla_server_pids.txt
    rm -f /tmp/sla_server_pids.txt
fi

# Hentikan berdasarkan port (fallback)
for port in 8000 8001; do
    pids=$(pgrep -f "php -S localhost:$port" 2>/dev/null)
    if [ -n "$pids" ]; then
        kill $pids 2>/dev/null
        echo -e "${GREEN}   ✔ Server port $port (PID: $pids) dihentikan${NC}"
        stopped=$((stopped+1))
    fi
done

if [ "$stopped" -eq 0 ]; then
    echo -e "${YELLOW}   ℹ Tidak ada server yang sedang berjalan.${NC}"
else
    echo ""
    echo -e "${GREEN}✅ Semua server dihentikan.${NC}"
fi
echo ""
