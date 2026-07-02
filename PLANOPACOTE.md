# Cronograma

## Previsto no próximo pacote (v1.1.5):
- Página de perfil usuário;
- Exibição de pinhões de usuário;
- Favicon;
- Pinhão no mapa;
- Website:
  - Footer;
  - Seção "Nossos Resultados":
    - Quantos usuários;
    - Quantas araucárias registradas;
  - Página Quem Somos;
  - Página Contato;


## Tarefas relativamente pequenas para próximos pacotes
- **Melhoria na UI/UX do Mapa:**
  - Geolocalização nativa para inicializar mapa na região onde user mora;
- **Painel Administrativo**
  - Perfil customizado de usuário;
- **Design**
  - Paleta de cores;
  - Criar identidade visual;
  - Pixel artes de pinhões;
  - Desenhar favicon;
  - Melhorar UI/UX landing page;
- **Gamificação**
  - Joguinho de pinhões;
  - Joguinho de cultivo de Rocaria;
- **Qualidade de Código**
  - Adicionar script de formatação;
  - Testes do CRUD;


## Tarefas mais complexas a serem feitas entre hoje e a volta de Cristo

### Disponibilidade
- Load balancer/CDN na frente da aplicação;
- Habilitar múltiplas instâncias e health checks;
- Verificar possibilidade de backups automáticos;

### Escalabilidade
- Mover sessões e cache p/ Redis;
- Migrar arquivo storage p/ blob ou s3 (ver custos);
- Ativar autoscalling e workers;

### Confiabilidade
- Configurar filas e alertas de falhas;
- Adicionar monitoramento de disponibilidade, latência e erros;

### Segurança
- HTTPS forçado;

### Gerenciamento
- Pipeline CI/CD (build, test e deploy);
- Padronizar ambientes;
- Documentar procedimento rollback e incident response;