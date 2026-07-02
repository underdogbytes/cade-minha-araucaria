# Plano de Ação do Pacote v1.1.4

Será implementado:

### Rate limiting
Eu sei, eu sei, tô abusando da sorte sem colocar limite nas requests de rotas :P

### Fluxo de Denúncias
- Flag em card de imagem e na view da imagem para denunciar;
  - Ao clicar na flag, terá 3 opções:
    - 1. Imagem imprópria (não é uma araucária);
    - 2. Autoria (a foto foi tirada por outra pessoa);
    - 3. Outros (textarea máximo 144 caracteres);
  - Após a denúncia aparecerá no painel da moderação;
- Painel de moderação simples com lista de imagens denunciadas;
  - Opções do painel:
    - Deletar imagem;
    - Atribuir imagem a outro usuário;

### Favicon
Tomar vergonha na cara e desenhar um favicon :p


## A ser feito entre hoje e a volta de Cristo

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
- Restringir acessos por roles;

### Gerenciamento
- Pipeline CI/CD (build, test e deploy);
- Padronizar ambientes;
- Documentar procedimento rollback e incident response;


## Tarefas relativamente pequenas para próximos pacotes
- **Melhoria na UI/UX do Mapa:**
  - Geolocalização nativa para inicializar mapa na região onde user mora;
- **Painel Administrativo**
  - Adicionar flag para denunciar foto;
  - Modelar fluxo de denúncias (UML);
  - Implementar fluxo de denúncias;
  - Painel administrativo para moderação;
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