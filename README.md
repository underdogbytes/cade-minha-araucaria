# 🌲 Cadê minha araucária?? 🧐

Plataforma web colaborativa para mapear, monitorar e proteger nossas araucárias (*Araucaria angustifolia*).

---
## Tecnologias

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Tailwind CSS, Alpine.js & Livewire (via Laravel Jetstream)
- **Mapas & Geolocalização:** Leaflet.js
- **Manipulação de Imagens:** Intervention Image (v3)
- **Banco de Dados:** MySQL

---

## Como rodar o projeto

### Pré-requisitos
Certifique-se de ter instalado em sua máquina o **PHP 8.2+**, **Composer**, **Node.js** e um servidor **MySQL**.

### Clonar o Projeto
```bash
  git clone [https://github.com/underdogbytes/cade-minha-araucaria.git](https://github.com/underdogbytes/cade-minha-araucaria)
```

### Instalação de Dependêcias
```bash
  composer install
  npm install
```

### Configuração de Ambiente
Copie o arquivo de ambiente e configure suas credenciais de banco de dados.
```bash
  # Caso esteja no Linux ou com o terminal do VSCode aberto e queira um atalho para copiar o arquivo de exemplo e já criar um novo:
  cp .env.example .env

  # Para gerar chaves:
  php artisan key:generate
```

### Migrações (Banco de Dados)
```bash
  php artisan migrate
```

### Compilação Assets e Rodar Servidor
Em terminais separados, execute:
```bash
  # Para compilar o Vite:
  npm run dev

  # Rodar o servidor PHP:
  php artisan serve
```
Se deu boa até aqui, vai tá tudo abrindo em http://127.0.0.1:8000 , amém


## Como contribuir
Faça PRs curtos e explique o quê está implementando ou arrumando :D

## Vulnerabilidades de segurança
Por favor, reporte vulnerabilidades de segurança abrindo uma issue :)

## Planos Futuros
### UI/UX
- Logo pro site;
- Perfil de Usuário;

### Mapa
- Novo ícone;

### Araucárias
- Adicionar propriedade Descrição/Notas;
- Denúncias;