#!/bin/bash

git add .

echo "Abrindo aicommits para gerar a mensagem de commit..."
aicommits

if [ $? -eq 0 ]; then
  echo "Realizando push para o repositório remoto..."
  git push
  echo "Commit e push realizados com sucesso!"
else
  echo "Commit cancelado pelo usuário. Push não realizado."
  exit 1
fi
