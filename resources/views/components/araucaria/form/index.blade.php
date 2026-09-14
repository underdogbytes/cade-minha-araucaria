@props(['modo' => 'criar'])

@php
$sufixo = $modo === 'criar' ? 'create' : 'edit';
@endphp

<div class="map-flex-container rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">

  <div id="map-{{ $sufixo }}" class="relative min-h-[380px]">
    <div class="absolute top-3 left-3 z-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md px-3 py-1.5 rounded-lg border border-emerald-500/20 shadow-sm text-xs font-semibold text-emerald-800 dark:text-emerald-300">
      📍 Clique no mapa para marcar a localização
    </div>
  </div>

  <div id="form-container" class="bg-gray-50/50 dark:bg-gray-900/50 p-6 overflow-y-auto">

    <form id="araucariaForm-{{ $sufixo }}" method="POST"
      :action="idEdicao ? '/observations/' + idEdicao : '/observations'" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <template x-if="idEdicao">
        <input type="hidden" name="_method" value="PUT">
      </template>

      <x-araucaria.form.photo ::required="!idEdicao" />

      <div class="p-3 bg-emerald-50/80 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800/60 flex items-center space-x-3">
        <input
          type="checkbox"
          id="dataexif-{{ $sufixo }}"
          name="dataexif"
          class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4 cursor-pointer"
          @change="(async () => {
            const sufixo = idEdicao ? 'edit' : 'create';
            const form = document.getElementById(`araucariaForm-${sufixo}`);
            if (!form) return;
            const fileInput = form.querySelector('[name=\'photo_path\']') || form.querySelector('input[type=\'file\']');
            const file = fileInput ? fileInput.files[0] : null;
        
            window.dispatchEvent(new CustomEvent('process-image-exif', {
              detail: { isChecked: $event.target.checked, file, formElement: form, mapId: `map-${sufixo}` }
            }));
            
            setTimeout(() => {
              const latEl = form.querySelector('[name=\'latitude\']');
              const lngEl = form.querySelector('[name=\'longitude\']');
              const obsEl = form.querySelector('[name=\'observed_at\']');
              if (latEl && latEl.value) editLat = latEl.value;
              if (lngEl && lngEl.value) editLng = lngEl.value;
              if (obsEl && obsEl.value) editObservedAt = obsEl.value;
            }, 50);
          })()">
        <label for="dataexif-{{ $sufixo }}" class="text-xs font-semibold text-emerald-900 dark:text-emerald-200 cursor-pointer">
          Usar dados EXIF (Data/GPS) da foto enviada
        </label>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div class="form-group">
          <label for="latitude-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Latitude</label>
          <input type="text" id="latitude-{{ $sufixo }}" name="latitude" required x-model="editLat" class="w-full text-xs font-mono rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 p-2.5">
        </div>
        
        <div class="form-group">
          <label for="longitude-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Longitude</label>
          <input type="text" id="longitude-{{ $sufixo }}" name="longitude" required x-model="editLng" class="w-full text-xs font-mono rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 p-2.5">
        </div>
      </div>

      <div class="form-group">
        <label for="stage-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Estágio de Desenvolvimento</label>
        <select id="stage-{{ $sufixo }}" name="stage" required x-model="editStage" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
          <option value="seedling">Muda (Plântula)</option>
          <option value="sapling">Jovem (Desenvolvimento)</option>
          <option value="adult">Adulta (Copa Formada)</option>
          <option value="dead">Morta / Cortada</option>
        </select>
      </div>

      <div class="form-group">
        <label for="gender-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Gênero</label>
        <select id="gender-{{ $sufixo }}" name="gender" required x-model="editGender" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
          <option value="unknown">❓ Desconhecido / Não Identificado</option>
          <option value="male">♂️ Macho (Produz Estimais de Pólen)</option>
          <option value="female">♀️ Fêmea (Produz Pinhas/Pinhões)</option>
        </select>
      </div>

      <div class="form-group" x-data="{
        displayDate: '',
        fpInstance: null,
        isoToBr(iso) {
          if (!iso) return '';
          const clean = String(iso).replace(' ', 'T');
          const m = clean.match(/^(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2}))?/);
          if (!m) return '';
          return `${m[3]}/${m[2]}/${m[1]}${m[4] && m[5] ? ' ' + m[4] + ':' + m[5] : ''}`;
        },
        brToIso(br) {
          if (!br) return '';
          const m = String(br).trim().match(/^(\d{2})\/(\d{2})\/(\d{4})(?:\s+(\d{2}):(\d{2}))?$/);
          if (!m) return '';
          const d = parseInt(m[1], 10), mo = parseInt(m[2], 10), y = parseInt(m[3], 10);
          if (d < 1 || d > 31 || mo < 1 || mo > 12 || y < 1900 || y > 2100) return '';
          const h = m[4] !== undefined ? m[4] : '12';
          const min = m[5] !== undefined ? m[5] : '00';
          return `${m[3]}-${m[2]}-${m[1]}T${h}:${min}`;
        },
        applyMask(val) {
          const digits = String(val).replace(/\D/g, '').slice(0, 12);
          let res = '';
          if (digits.length > 0) res = digits.slice(0, 2);
          if (digits.length > 2) res += '/' + digits.slice(2, 4);
          if (digits.length > 4) res += '/' + digits.slice(4, 8);
          if (digits.length > 8) res += ' ' + digits.slice(8, 10);
          if (digits.length > 10) res += ':' + digits.slice(10, 12);
          return res;
        },
        handleInput(e) {
          const masked = this.applyMask(e.target.value);
          this.displayDate = masked;
          e.target.value = masked;
          if (masked.length === 16) {
            const iso = this.brToIso(masked);
            if (iso) {
              editObservedAt = iso;
              this.syncHidden(iso);
              if (this.fpInstance) this.fpInstance.setDate(iso, false);
            }
          } else if (!masked) {
            editObservedAt = '';
            this.syncHidden('');
            if (this.fpInstance) this.fpInstance.clear();
          }
        },
        handleBlur() {
          if (this.displayDate && this.displayDate.length === 10) {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const min = String(now.getMinutes()).padStart(2, '0');
            this.displayDate += ` ${h}:${min}`;
            const iso = this.brToIso(this.displayDate);
            if (iso) {
              editObservedAt = iso;
              this.syncHidden(iso);
              if (this.fpInstance) this.fpInstance.setDate(iso, false);
            }
          }
        },
        setAgora() {
          const now = new Date();
          const y = now.getFullYear();
          const m = String(now.getMonth() + 1).padStart(2, '0');
          const d = String(now.getDate()).padStart(2, '0');
          const h = String(now.getHours()).padStart(2, '0');
          const min = String(now.getMinutes()).padStart(2, '0');
          const iso = `${y}-${m}-${d}T${h}:${min}`;
          const br = `${d}/${m}/${y} ${h}:${min}`;
          this.displayDate = br;
          editObservedAt = iso;
          this.syncHidden(iso);
          if (this.fpInstance) this.fpInstance.setDate(now, false);
        },
        openPicker() {
          if (this.fpInstance) {
            this.fpInstance.open();
          } else if (this.$refs.displayInput) {
            this.$refs.displayInput.focus();
          }
        },
        syncHidden(iso) {
          if (this.$refs.hiddenInput) {
            this.$refs.hiddenInput.value = iso;
            this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
          }
        },
        init() {
          if (editObservedAt) {
            this.displayDate = this.isoToBr(editObservedAt);
            this.syncHidden(editObservedAt);
          } else if ('{{ $modo }}' === 'criar') {
            this.setAgora();
          }

          this.$watch('editObservedAt', (val) => {
            const expected = this.isoToBr(val);
            if (expected !== this.displayDate) {
              this.displayDate = expected;
              this.syncHidden(val || '');
              if (this.fpInstance) {
                if (val) {
                  this.fpInstance.setDate(val, false);
                } else {
                  this.fpInstance.clear();
                }
              }
            }
          });

          this.$nextTick(() => {
            if (window.flatpickr && this.$refs.displayInput) {
              const ptLocale = (window.flatpickr.l10ns && window.flatpickr.l10ns.pt) ? window.flatpickr.l10ns.pt : {};
              this.fpInstance = window.flatpickr(this.$refs.displayInput, {
                locale: ptLocale,
                dateFormat: 'd/m/Y H:i',
                enableTime: true,
                time_24hr: true,
                allowInput: true,
                defaultDate: editObservedAt ? new Date(editObservedAt) : null,
                onChange: (selectedDates, dateStr) => {
                  if (selectedDates.length > 0) {
                    const d = selectedDates[0];
                    const y = d.getFullYear();
                    const m = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    const h = String(d.getHours()).padStart(2, '0');
                    const min = String(d.getMinutes()).padStart(2, '0');
                    const iso = `${y}-${m}-${day}T${h}:${min}`;
                    this.displayDate = dateStr;
                    editObservedAt = iso;
                    this.syncHidden(iso);
                  } else {
                    this.displayDate = '';
                    editObservedAt = '';
                    this.syncHidden('');
                  }
                }
              });
            }
          });
        }
      }">
        <div class="flex items-center justify-between mb-1">
          <label for="observed_at_display-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            Data & Hora da Observação
          </label>
          <button type="button" @click="setAgora()" class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition flex items-center space-x-1 cursor-pointer">
            <span>🕒 Preencher com Agora</span>
          </button>
        </div>

        <div class="relative">
          <input
            type="text"
            x-ref="displayInput"
            id="observed_at_display-{{ $sufixo }}"
            placeholder="dd/mm/aaaa hh:mm"
            maxlength="16"
            :value="displayDate"
            @input="handleInput($event)"
            @blur="handleBlur()"
            required
            class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 pr-10 focus:ring-emerald-500 focus:border-emerald-500"
          />

          <button
            type="button"
            @click="openPicker()"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition cursor-pointer"
            title="Abrir calendário"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </button>
        </div>

        <!-- Campo oculto enviado na requisição mantendo o padrão ISO exigido pelo back-end -->
        <input
          type="hidden"
          x-ref="hiddenInput"
          id="observed_at-{{ $sufixo }}"
          name="observed_at"
          :value="editObservedAt"
          @input="if ($event.target.value !== editObservedAt) { editObservedAt = $event.target.value; displayDate = isoToBr($event.target.value); }"
          @change="if ($event.target.value !== editObservedAt) { editObservedAt = $event.target.value; displayDate = isoToBr($event.target.value); }"
        />

        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">
          Padrão brasileiro: dd/mm/aaaa hh:mm (Ex: 14/09/2026 15:30)
        </p>
      </div>

      <div class="p-3 bg-gray-100 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 flex items-center space-x-3">
        <input
          type="checkbox"
          id="is_shared-{{ $sufixo }}"
          name="is_shared"
          value="1"
          checked
          class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4 cursor-pointer">
        <label for="is_shared-{{ $sufixo }}" class="text-xs font-semibold text-gray-800 dark:text-gray-200 cursor-pointer">
          Permitir que a comunidade anexe fotos de acompanhamento desta árvore
        </label>
      </div>

      <div class="flex gap-2 pt-2">
        <button type="submit"
          class="flex-1 bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-sm py-2.5 px-4 rounded-xl shadow-md shadow-emerald-700/20 transition duration-200 disabled:opacity-50 flex items-center justify-center space-x-2">
          <span x-text="idEdicao ? '💾 Salvar Alterações' : '🌱 Registrar Observação'"></span>
        </button>

        <template x-if="idEdicao">
          <button type="button" @click="subAba = 'tabela'; idEdicao = null;"
            class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold text-xs py-2.5 px-4 rounded-xl transition whitespace-nowrap">
            Cancelar
          </button>
        </template>
      </div>
    </form>
  </div>
</div>