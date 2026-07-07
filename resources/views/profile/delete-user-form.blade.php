<x-action-section>
    <x-slot name="title">
        Deletar conta
    </x-slot>

    <x-slot name="description">
        Delete sua conta permanentemente.
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
            Uma vez deletada, todos os recursos e dados da sua conta serão permanentemente deletados.
            Antes de deletar sua conta, por favor baixe quaisquer dados ou informações que você deseja manter.
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                Deletar conta
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                Deletar conta
            </x-slot>

            <x-slot name="content">
                Você tem certeza que deseja deletar sua conta?
                Uma vez deletada, todos os recursos e dados da sua conta serão permanentemente deletados.
                Por favor, insira sua senha para confirmar que você deseja deletar permanentemente sua conta.

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                                autocomplete="current-password"
                                placeholder="Senha"
                                x-ref="password"
                                wire:model="password"
                                wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    Deletar conta
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
