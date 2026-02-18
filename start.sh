#!/bin/bash

GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}==================================${NC}"
echo -e "${BLUE}  Starting Stride Lab Application${NC}"
echo -e "${BLUE}==================================${NC}\n"

if [ ! -d "api" ]; then
    echo -e "${RED}Erro: Diretório 'api' não encontrado${NC}"
    exit 1
fi

if [ ! -d "client" ]; then
    echo -e "${RED}Erro: Diretório 'client' não encontrado${NC}"
    exit 1
fi

echo -e "${GREEN}[1/2] Iniciando API (Laravel)...${NC}"
cd api
php artisan serve &
API_PID=$!
cd ..

sleep 2

echo -e "${GREEN}[2/2] Iniciando Client (Frontend)...${NC}"
cd client
npm run dev &
CLIENT_PID=$!
cd ..

echo -e "\n${BLUE}==================================${NC}"
echo -e "${GREEN}✓ Aplicação iniciada com sucesso!${NC}"
echo -e "${BLUE}==================================${NC}"
echo -e "API PID: $API_PID"
echo -e "Client PID: $CLIENT_PID"
echo -e "\nPressione Ctrl+C para encerrar ambos os servidores\n"

cleanup() {
    echo -e "\n${BLUE}Encerrando servidores...${NC}"
    kill $API_PID 2>/dev/null
    kill $CLIENT_PID 2>/dev/null
    echo -e "${GREEN}Servidores encerrados com sucesso!${NC}"
    exit 0
}

trap cleanup INT TERM

wait