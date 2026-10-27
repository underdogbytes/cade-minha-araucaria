@props(['observation'])

@php
  $user = auth()->user();
  $userVotes = $user
    ? $observation->statusVotes->where('user_id', $user->id)->pluck('status')->values()->all()
    : [];

  $initialCounts = [
    'normal' => (int) ($observation->votes_normal_count ?? 0),
    'protegida' => (int) ($observation->votes_protegida_count ?? 0),
    'risco' => (int) ($observation->votes_risco_count ?? 0),
    'doente' => (int) ($observation->votes_doente_count ?? 0),
    'morta' => (int) ($observation->votes_morta_count ?? 0),
  ];

  $computedStatus = $observation->computed_status ?: ($observation->stage === 'dead' ? 'morta' : 'normal');
@endphp

<div
  x-data="{
    counts: {{ json_encode($initialCounts) }},
    userVotes: {{ json_encode($userVotes) }},
    computedStatus: '{{ $computedStatus }}',
    isSubmitting: false,
    errorMessage: '',
    statusLabels: {
      'normal': 'Normal / Saudável',
      'protegida': 'Protegida',
      'risco': 'Em Risco',
      'doente': 'Doente',
      'morta': 'Morta',
      'doente_risco': 'Doente & Em Risco',
      'normal_protegida': 'Normal & Protegida',
      'protegida_risco': 'Protegida & Em Risco',
      'protegida_doente': 'Protegida & Doente'
    },
    statusColors: {
      'normal': '#1b4332',
      'protegida': '#059669',
      'risco': '#d97706',
      'doente': '#dc2626',
      'morta': '#18181b'
    },
    hasVoted(status) {
      return this.userVotes.includes(status);
    },
    getStatusLabel() {
      if (this.statusLabels[this.computedStatus]) {
        return this.statusLabels[this.computedStatus];
      }
      return this.computedStatus.replace('_', ' & ');
    },
    getStatusBadgeStyle() {
      const parts = this.computedStatus.split(/[_-]/);
      if (parts.length > 1) {
        const c1 = this.statusColors[parts[0]] || '#1b4332';
        const c2 = this.statusColors[parts[1]] || '#d97706';
        return `background: linear-gradient(90deg, ${c1} 50%, ${c2} 50%);`;
      }
      const c = this.statusColors[this.computedStatus] || '#1b4332';
      return `background-color: ${c};`;
    },
    async toggleVote(status) {
      @guest
        this.errorMessage = 'Faça login para registrar seu voto nesta observação.';
        return;
      @endguest

      if (this.isSubmitting) return;
      this.isSubmitting = true;
      this.errorMessage = '';

      // Otimista com backup para rollback seguro
      const previouslyVoted = this.hasVoted(status);
      const previousUserVotes = [...this.userVotes];
      const previousCounts = { ...this.counts };

      if (previouslyVoted) {
        this.userVotes = this.userVotes.filter(s => s !== status);
        this.counts[status] = Math.max(0, (this.counts[status] || 0) - 1);
      } else {
        this.userVotes.push(status);
        this.counts[status] = (this.counts[status] || 0) + 1;

        // Regra: se votou em 'morta' ou 'doente', o voto em 'normal' é removido automaticamente
        if (status === 'morta' || status === 'doente') {
          if (this.userVotes.includes('normal')) {
            this.userVotes = this.userVotes.filter(s => s !== 'normal');
            this.counts['normal'] = Math.max(0, (this.counts['normal'] || 0) - 1);
          }
        } else if (status === 'normal') {
          // Regra: se votou em 'normal', votos prévios em 'morta' e 'doente' são removidos
          if (this.userVotes.includes('morta')) {
            this.userVotes = this.userVotes.filter(s => s !== 'morta');
            this.counts['morta'] = Math.max(0, (this.counts['morta'] || 0) - 1);
          }
          if (this.userVotes.includes('doente')) {
            this.userVotes = this.userVotes.filter(s => s !== 'doente');
            this.counts['doente'] = Math.max(0, (this.counts['doente'] || 0) - 1);
          }
        }
      }

      try {
        const response = await fetch('{{ route('observations.vote', $observation->id) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ status: status })
        });

        const data = await response.json();
        if (!response.ok) {
          throw new Error(data.message || 'Erro ao processar voto.');
        }

        if (data.counts) this.counts = data.counts;
        if (data.user_votes) this.userVotes = data.user_votes;
        if (data.computed_status) this.computedStatus = data.computed_status;
      } catch (err) {
        this.errorMessage = err.message || 'Falha ao registrar voto.';
        this.userVotes = previousUserVotes;
        this.counts = previousCounts;
      } finally {
        this.isSubmitting = false;
      }
    }
  }"
  class="rounded-2xl border border-gray-200/90 dark:border-gray-700/80 bg-gradient-to-b from-gray-50/70 to-white dark:from-gray-900/40 dark:to-gray-800/60 p-5 shadow-sm space-y-4"
>
  <!-- Cabeçalho do Bloco de Votação -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-gray-100 dark:border-gray-700/70">
    <div>
      <div class="flex items-center gap-2">
        <span class="text-base font-bold text-gray-900 dark:text-white">
          Avaliação Comunitária
        </span>
        <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">
          (Reports Rápidos)
        </span>
      </div>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
        Vote no estado real desta árvore. Em caso de empate, o mapa exibe o ícone dividido.
      </p>
    </div>

    <!-- Badge do Status Computado -->
    <div class="flex items-center gap-2 shrink-0">
      <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">Predominante:</span>
      <span
        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm transition-all duration-300"
        :style="getStatusBadgeStyle()"
        x-text="getStatusLabel()"
      ></span>
    </div>
  </div>

  <!-- Mensagem de Alerta/Erro -->
  <div
    x-show="errorMessage"
    x-transition
    class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 text-xs text-amber-800 dark:text-amber-300 flex items-center justify-between"
  >
    <div class="flex items-center gap-2">
      <span>⚠️</span>
      <span x-text="errorMessage"></span>
    </div>
    @guest
      <a href="{{ route('login') }}" class="font-bold underline ml-2">Fazer Login</a>
    @endguest
  </div>

  <!-- Grid de Botões Rápidos (5 Opções) -->
  <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">

    <!-- 1. Normal / Saudável -->
    <button
      type="button"
      @click="toggleVote('normal')"
      :class="hasVoted('normal')
        ? 'bg-[#1b4332] text-white border-[#1b4332] shadow-md shadow-emerald-950/20 ring-2 ring-emerald-500/30'
        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-emerald-600 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20'"
      class="group relative flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none"
      title="Marcar como Normal / Saudável"
    >
      <div class="h-6 w-6 mb-1.5 flex items-center justify-center transition-transform group-hover:scale-110">
        <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
          <path d="M12 2C11.4 2 11 2.4 11 3V5.5C9.2 5.6 7.6 6.4 6.4 7.7C6.1 8.1 6.1 8.7 6.5 9C6.9 9.3 7.5 9.3 7.8 8.9C8.8 7.8 10.3 7.1 11.8 7.1V10C9.8 10.2 8 11.2 6.8 12.8C6.5 13.2 6.6 13.8 7 14.1C7.4 14.4 8 14.3 8.3 13.9C9.2 12.7 10.6 12 12 12C13.4 12 14.8 12.7 15.7 13.9C16 14.3 16.6 14.4 17 14.1C17.4 13.8 17.5 13.2 17.2 12.8C16 11.2 14.2 10.2 12.2 10V7.1C13.7 7.1 15.2 7.8 16.2 8.9C16.5 9.3 17.1 9.3 17.5 9C17.9 8.7 17.9 8.1 17.6 7.7C16.4 6.4 14.8 5.6 13 5.5V3C13 2.4 12.6 2 12 2ZM11 14.5V22H13V14.5H11Z"/>
        </svg>
      </div>
      <span class="text-xs font-bold leading-tight">Normal</span>
      <span
        class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold"
        :class="hasVoted('normal') ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
        x-text="counts.normal || 0"
      ></span>
    </button>

    <!-- 2. Protegida -->
    <button
      type="button"
      @click="toggleVote('protegida')"
      :class="hasVoted('protegida')
        ? 'bg-[#059669] text-white border-[#059669] shadow-md shadow-emerald-700/20 ring-2 ring-emerald-500/30'
        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20'"
      class="group relative flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none"
      title="Marcar como Protegida"
    >
      <div class="h-6 w-6 mb-1.5 flex items-center justify-center transition-transform group-hover:scale-110">
        <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
          <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 16l-4-4 1.41-1.41L11 14.17l6.59-6.59L19 9l-8 8z"/>
        </svg>
      </div>
      <span class="text-xs font-bold leading-tight">Protegida</span>
      <span
        class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold"
        :class="hasVoted('protegida') ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
        x-text="counts.protegida || 0"
      ></span>
    </button>

    <!-- 3. Em Risco -->
    <button
      type="button"
      @click="toggleVote('risco')"
      :class="hasVoted('risco')
        ? 'bg-[#d97706] text-white border-[#d97706] shadow-md shadow-amber-700/20 ring-2 ring-amber-500/30'
        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-amber-500 hover:bg-amber-50/50 dark:hover:bg-amber-950/20'"
      class="group relative flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none"
      title="Marcar como Em Risco"
    >
      <div class="h-6 w-6 mb-1.5 flex items-center justify-center transition-transform group-hover:scale-110">
        <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
          <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
        </svg>
      </div>
      <span class="text-xs font-bold leading-tight">Em Risco</span>
      <span
        class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold"
        :class="hasVoted('risco') ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
        x-text="counts.risco || 0"
      ></span>
    </button>

    <!-- 4. Doente -->
    <button
      type="button"
      @click="toggleVote('doente')"
      :class="hasVoted('doente')
        ? 'bg-[#dc2626] text-white border-[#dc2626] shadow-md shadow-rose-700/20 ring-2 ring-rose-500/30'
        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-rose-500 hover:bg-rose-50/50 dark:hover:bg-rose-950/20'"
      class="group relative flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none"
      title="Marcar como Doente"
    >
      <div class="h-6 w-6 mb-1.5 flex items-center justify-center transition-transform group-hover:scale-110">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
          <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
          <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
        </svg>
      </div>
      <span class="text-xs font-bold leading-tight">Doente</span>
      <span
        class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold"
        :class="hasVoted('doente') ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
        x-text="counts.doente || 0"
      ></span>
    </button>

    <!-- 5. Morta -->
    <button
      type="button"
      @click="toggleVote('morta')"
      :class="hasVoted('morta')
        ? 'bg-[#18181b] text-white border-zinc-600 shadow-md shadow-zinc-950/40 ring-2 ring-zinc-500/30'
        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-700/40'"
      class="group relative col-span-2 sm:col-span-1 flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all duration-200 cursor-pointer focus:outline-none"
      title="Marcar como Morta"
    >
      <div class="h-6 w-6 mb-1.5 flex items-center justify-center transition-transform group-hover:scale-110">
        <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
          <path d="M12 2C6.48 2 2 6.48 2 12c0 2.85 1.2 5.41 3.12 7.24.23.22.48.42.74.6V21a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1h4v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1.16c.26-.18.51-.38.74-.6C20.8 17.41 22 14.85 22 12c0-5.52-4.48-10-10-10zm-3 11c-.83 0-1.5-.67-1.5-1.5S8.17 10 9 10s1.5.67 1.5 1.5S9.83 13 9 13zm3 4c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm3-4c-.83 0-1.5-.67-1.5-1.5S14.17 10 15 10s1.5.67 1.5 1.5S15.83 13 15 13z"/>
        </svg>
      </div>
      <span class="text-xs font-bold leading-tight">Morta</span>
      <span
        class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold"
        :class="hasVoted('morta') ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
        x-text="counts.morta || 0"
      ></span>
    </button>

  </div>
</div>
