import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';

export default [
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
    {
        files: ['**/*.{js,jsx,mjs,cjs,vue}'],
        languageOptions: {
            ecmaVersion: 2021,
            sourceType: 'module',
            globals: {
                console: 'readonly',
                process: 'readonly',
                window: 'readonly',
                document: 'readonly',
                navigator: 'readonly',
            },
        },
    },
    js.configs.recommended,
    ...pluginVue.configs['flat/essential'],
    {
        rules: {
            'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
            'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
        },
    },
];
