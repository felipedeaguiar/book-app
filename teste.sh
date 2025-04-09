#!/bin/bash

# URL da API
URL="http://3.142.140.244:8989"

# Número de requisições que você quer fazer
NUM_REQUESTS=1000

# Loop para fazer múltiplas requisições
for i in $(seq 1 $NUM_REQUESTS)
do
  echo "Requisição $i"
  curl -s -o /dev/null $URL  # Faz a requisição GET
done
