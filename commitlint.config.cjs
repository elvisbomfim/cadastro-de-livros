module.exports = {
  extends: ['@commitlint/config-conventional'],
  rules: {
    'type-enum': [
      2,
      'always',
      [
        'feat',     // Nova funcionalidade
        'fix',      // Correção de bug
        'docs',     // Documentação
        'style',    // Formatação, ponto e vírgula faltando, etc
        'refactor', // Refatoração de código
        'perf',     // Melhoria de performance
        'test',     // Adicionando testes
        'build',    // Mudanças no sistema de build
        'ci',       // Mudanças na CI
        'chore',    // Mudanças no processo de build ou ferramentas auxiliares
        'revert',   // Reverter um commit
      ],
    ],
    'type-case': [2, 'always', 'lower-case'],
    'type-empty': [2, 'never'],
    'scope-case': [2, 'always', 'lower-case'],
    'subject-empty': [2, 'never'],
    'subject-full-stop': [2, 'never', '.'],
    'header-max-length': [2, 'always', 100],
  },
};

