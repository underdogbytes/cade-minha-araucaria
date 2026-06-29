import js from '@eslint/js';

export default [
  // 1. Arquivos e pastas ignorados pelo Lint:
  {
    ignores: [
      'node_modules/**',
      'dist/**',
      'build/**',
      'vendor/**',
      'public/**',
      'storage/**',
      'bootstrap/cache/**',
    ],
  },
  // 2. Definições de ambiente e variáveis globais:
  {
    files: ['**/*.{js,jsx,mjs,cjs}'],
    languageOptions: {
      ecmaVersion: 2021,
      sourceType: 'module',
      globals: {
        console: 'readonly',
        process: 'readonly',
        window: 'readonly',
        document: 'readonly',
        navigator: 'readonly',
        Alpine: 'readonly',
        ExifReader: 'readonly',
      },
    },
  },
  // 3. Regras recomendadas do JavaScript clássico:
  js.configs.recommended,
  // 4. Customizações de regras:
  {
    rules: {
      // Transforma variáveis não usadas em avisos (ignora se começar com _)
      'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],

      // Permite console.log em desenvolvimento, mas avisa/barra se for para produção
      'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',

      // Boas práticas extras de segurança e qualidade:
      'no-undef': 'error',       // Proíbe o uso de variáveis não declaradas
      'no-const-assign': 'error',// Impede reatribuir uma constante
      'no-duplicate-imports': 'warn', // Evita fazer múltiplos imports do mesmo arquivo
    },
  },
];