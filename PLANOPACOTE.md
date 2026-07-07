# Plano de Ação

## Previsto no Pacote v1.1.5
- Página de perfil usuário;

## Próximos passos

### UI/UX, Design
- **Geolocalização Nativa:** Inicializar o mapa automaticamente na região do usuário.
- **Landing Page:** Melhorias gerais de UI/UX.
- **Identidade Visual:** Definição de paleta de cores, criação de favicon e desenvolvimento de pixel art de pinhões.

### Painel Interno
- Criação de perfil customizado de usuário.

### Gamificação
- Joguinho de pinhões;
- Joguinho de cultivo de Rocaria;

### Qualidade de Código
- Adicionar script de formatação;
- Testes do CRUD;

### Features core
- Adicionar as colunas na tabela Araucária:
  - Altura estimada;
  - Diâmetro estimado;
    - Isso habilitar cálculo de biomassa e emissão de carbono (acho preciso pesquisar mais);
  - Estágio (a discutir);
- Várias fotos de uma vez (permitindo casca, folhas, pinhas, árvore completa);
- Idade estimada
- Tags de risco/ameaçada por:
  - Incêndio, pressão do corte ilegal de madeira, doenças, avanço urbano;
- Marcação de "Verificada por especialistas" (para ter o rótulo de “Confiável para pesquisa” após validação de especialistas);
- Permitir que os usuários "adotem" ou acompanhem as árvores que mapearam;
- Enviar lembretes sazonais para fotografar novamente a mesma árvore (fenologia, alterações na saúde, crescimento);
- Exibir comparações de "antes e depois" na página de perfil da árvore;
- Monitorar taxas de sobrevivência em todo o conjunto de dados (pra validar o impacto do projeto);

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

## Questões em aberto
- Como lidar com a duplicata de árvores?