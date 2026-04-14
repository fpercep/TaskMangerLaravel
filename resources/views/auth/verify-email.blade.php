<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    <x-ui.auth-session-status class="mb-4" :status="session('status') == 'verification-link-sent' ? __('A new verification link has been sent to the email address you provided during registration.') : null" />

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-ui.primary-button>
                    {{ __('Resend Verification Email') }}
                </x-ui.primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm text-gray-600 hover:text-black hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
