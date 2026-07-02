# Changelog

## [1.1.5] - 2026-07-02

### Adicionado
- Página de perfil de usuário;
- Exibição de pinhões de usuário;

### Modificado
- Tradução texto default de configurações de perfil para PT-BR;

## [1.1.4] - 2026-07-02

### Adicionado
- Restringido acesso por roles;
- Fluxo de Denúncias (botão no card e páginas de moderação);

### Modificado
- Rate Limiting e respectivo teste;
- Alterações menores de estilo (posicionamento e consistência de alertas);

## [1.1.3] - 2026-07-01

### Adicionado
- Teste PHPUnit;
- Mapa na parte interna;

### Modificado
- Remoção links de navegação/tabs e título no cabeçalho;
- Correção cor de fundo de tab "Registrar Araucária";
- Rotas web e api;

## [1.1.2] - 2026-06-30

### Adicionado
- Configuração ESLint;

### Modificado
- Refatoração formulário;
- Paginação API de 15 itens;


## [1.1.1] - 2026-06-29

### Adicionado
- Captura latitude e longitude automaticamente se o usuário permitir;
- Isolamento de lógica de alertas


## [1.1.0] - 2026-06-09

### Adicionado
- Integração do mapa com Marker Cluster (agrupação de marcadores);
- Alertas visuais em caso de erro ao carregar observações;

### Modificado
- Refatoração da requisição de observações para utilizar `async/await`;
- Otimização do carregamento das imagens das araucárias;

### Corrigido
- Adicionado proteção em caso de ausência de coordenadas;