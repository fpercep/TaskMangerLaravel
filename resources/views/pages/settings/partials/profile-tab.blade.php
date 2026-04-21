<div class="space-y-6">
    <!-- Componente de Identidad / Avatar -->
    <div class="bg-white p-6 md:p-8 shadow-sm rounded-xl border border-gray-100 flex items-center gap-6">
        <div class="w-20 h-20 rounded-full bg-gray-100 border border-gray-200 shrink-0 flex items-center justify-center overflow-hidden">
             <span class="text-2xl font-bold text-gray-500 uppercase">{{ $user->initials }}</span>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
            <div class="mt-3 flex items-center gap-2 text-xs text-gray-400">
                <x-lucide-calendar class="w-4 h-4" />
                <span>Miembro desde {{ $user->created_at->translatedFormat('M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Actualización de Información de Perfil -->
    <div class="bg-white p-6 md:p-8 shadow-sm rounded-xl border border-gray-100">
        <header class="mb-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Profile Information') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __("Update your account's profile information and email address.") }}
            </p>
        </header>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-6 max-w-xl">
            @csrf
            @method('patch')

            <div>
                <x-ui.input-label for="name" :value="__('Name')" />
                <x-ui.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-ui.input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-ui.input-label for="email" :value="__('Email')" />
                <x-ui.text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-ui.input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 space-y-2">
                        <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-md border border-amber-100 flex items-start gap-2">
                            <x-lucide-alert-triangle class="w-5 h-5 shrink-0" />
                            <span>
                                {{ __('Your email address is unverified.') }}
                                <button form="send-verification" class="underline text-sm hover:text-amber-800 transition-colors focus:outline-none">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </span>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="text-sm text-green-600 bg-green-50 p-3 rounded-md border border-green-100">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4 pt-2">
                <x-ui.primary-button>{{ __('Save') }}</x-ui.primary-button>

                <x-ui.success-message :on="session('status') === 'profile-updated'">
                    {{ __('Saved.') }}
                </x-ui.success-message>
            </div>
        </form>
    </div>

    <!-- Actualización de Contraseña -->
    <div class="bg-white p-6 md:p-8 shadow-sm rounded-xl border border-gray-100">
        <header class="mb-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Update Password') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </p>
        </header>

        <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
            @csrf
            @method('put')

            <div>
                <x-ui.input-label for="update_password_current_password" :value="__('Current Password')" />
                <x-ui.text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-ui.input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-ui.input-label for="update_password_password" :value="__('New Password')" />
                <x-ui.text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-ui.input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-ui.input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                <x-ui.text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-ui.input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <x-ui.primary-button>{{ __('Save') }}</x-ui.primary-button>

                <x-ui.success-message :on="session('status') === 'password-updated'">
                    {{ __('Saved.') }}
                </x-ui.success-message>
            </div>
        </form>
    </div>

    <!-- Zona de Peligro: Eliminar Cuenta -->
    <div class="bg-red-50/50 p-6 md:p-8 shadow-sm rounded-xl border border-red-100">
        <header class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                 <x-lucide-alert-triangle class="w-5 h-5 text-red-600" />
                 <h2 class="text-lg font-medium text-red-900">
                     {{ __('Delete Account') }}
                 </h2>
            </div>
            <p class="mt-1 text-sm text-red-700 max-w-3xl">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
            </p>
        </header>

        <x-ui.danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Delete Account') }}</x-ui.danger-button>

        <x-ui.modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="mt-6">
                    <x-ui.input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                    <x-ui.text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-ui.input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-ui.secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-ui.secondary-button>

                    <x-ui.danger-button>
                        {{ __('Delete Account') }}
                    </x-ui.danger-button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</div>
