<div class="space-y-6">
    <!-- Generate API Token -->
    <x-panel :header="__('Create API Token')"
             :sub-header="__('API tokens allow third-party services to authenticate with our application on your behalf.')">
        <form wire:submit="createApiToken" class="space-y-6">
            <!-- Token Name -->
            <div class="max-w-xl">
                <x-text-input name="name" :label="__('Token Name')" model="createApiTokenForm.name" autofocus/>
            </div>

            <!-- Token Permissions -->
            <fieldset>
                <legend class="text-sm font-medium text-on-surface-600">{{ __('Permissions') }}</legend>

                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($permissions as $permission)
                        <label wire:key="create-permission-{{ $permission }}" class="flex cursor-pointer items-center gap-2 rounded-lg border border-surface-300 px-3 py-3 text-sm text-on-surface-600 hover:bg-surface-50 focus-within:ring-2 focus-within:ring-primary-600">
                            <x-checkbox wire:model="createApiTokenForm.permissions" :value="$permission"/>
                            <span>{{ ucfirst($permission) }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="flex flex-wrap items-center justify-end gap-4">
                <x-action-message on="created">
                    {{ __('Created.') }}
                </x-action-message>

                <x-button type="submit">
                    {{ __('Create API Token') }}
                </x-button>
            </div>
        </form>
    </x-panel>

    @if (auth()->user()->tokens->isNotEmpty())
        <!-- Manage API Tokens -->
        <div>
            <x-panel :header="__('Manage API Tokens')"
                     :sub-header="__('You may delete any of your existing tokens if they are no longer needed.')">
                <!-- API Token List -->
                <div class="divide-y divide-surface-200">
                    @foreach (auth()->user()->tokens->sortBy('name') as $token)
                        <div wire:key="api-token-{{ $token->id }}" class="flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0 break-all font-medium text-on-surface-600">
                                {{ $token->name }}
                            </div>

                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 sm:justify-end">
                                @if ($token->last_used_at)
                                    <div class="text-sm text-on-surface-500">
                                        {{ __('Last used') }} {{ $token->last_used_at->diffForHumans() }}
                                    </div>
                                @endif

                                <button type="button" class="cursor-pointer text-sm font-medium text-primary-700 underline underline-offset-2 hover:text-primary-600"
                                        wire:click="manageApiTokenPermissions({{ $token->id }})">
                                    {{ __('Permissions') }}
                                </button>

                                <button type="button" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-700"
                                        wire:click="confirmApiTokenDeletion({{ $token->id }})">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-panel>
        </div>
    @endif

    <!-- Token Value Modal -->
    <x-dialog-modal name="displaying-token" focusable>
        <x-slot name="title">
            {{ __('API Token') }}
        </x-slot>

        <x-slot name="content">
            <div>
                {{ __('Please copy your new API token. For your security, it won\'t be shown again.') }}
            </div>
            <x-text-input
                x-ref="plaintextToken" type="text" readonly :value="$plainTextToken"
                class="mt-4 w-full break-all"
                autofocus autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                @showing-token-modal.window="setTimeout(() => $refs.plaintextToken.select(), 250)"
            />
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button @click="$dispatch('close')" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <!-- API Token Permissions Modal -->
    <x-dialog-modal name="managing-api-token-permissions" focusable>
        <x-slot name="title">
            {{ __('API Token Permissions') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($permissions as $permission)
                    <label wire:key="manage-permission-{{ $permission }}" class="flex cursor-pointer items-center gap-2 rounded-lg border border-surface-300 px-3 py-3 text-sm text-on-surface-600 hover:bg-surface-50 focus-within:ring-2 focus-within:ring-primary-600">
                        <x-checkbox wire:model="updateApiTokenForm.permissions" :value="$permission"/>
                        <span>{{ ucfirst($permission) }}</span>
                    </label>
                @endforeach
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button @click="$dispatch('close')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="updateApiToken" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Delete Token Confirmation Modal -->
    <x-confirmation-modal name="confirming-api-token-deletion">
        <x-slot name="title">
            {{ __('Delete API Token') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to delete this API token?') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button @click="$dispatch('close')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="deleteApiToken" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
