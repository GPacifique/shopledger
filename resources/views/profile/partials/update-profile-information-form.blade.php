<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your photo, contact details and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- ===== Profile photo ===== --}}
        <div
            x-data="{
                preview: @js($user->profile_photo_url),
                remove: false,
                pick(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    this.remove = false;
                    this.preview = URL.createObjectURL(file);
                },
                clear() {
                    this.$refs.photo.value = '';
                    this.preview = null;
                    this.remove = true;
                }
            }"
        >
            <x-input-label for="photo" :value="__('Profile photo')" />

            <div class="mt-2 flex items-center gap-4">
                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-full bg-indigo-100 ring-2 ring-white shadow">
                    <template x-if="preview">
                        <img :src="preview" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!preview">
                        <div class="flex h-full w-full items-center justify-center text-2xl font-semibold text-indigo-600">
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                    </template>
                </div>

                <div class="space-y-2">
                    <input
                        id="photo" name="photo" type="file" class="hidden" x-ref="photo"
                        accept="image/png,image/jpeg" @change="pick($event)"
                    >
                    <input type="hidden" name="remove_photo" :value="remove ? 1 : 0">

                    <div class="flex flex-wrap gap-2">
                        <x-secondary-button type="button" @click="$refs.photo.click()">
                            {{ __('Change photo') }}
                        </x-secondary-button>

                        <button
                            type="button" x-show="preview" @click="clear()"
                            class="text-sm text-red-600 underline hover:text-red-800 focus:outline-none"
                        >
                            {{ __('Remove') }}
                        </button>
                    </div>

                    <p class="text-xs text-gray-500">{{ __('JPG or PNG, up to 2 MB.') }}</p>
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        {{-- ===== Basic details ===== --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="name" :value="__('Full name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" placeholder="07XXXXXXXX" maxlength="10" inputmode="numeric" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- ===== Location ===== --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="country" :value="__('Country')" />
                <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $user->country)" autocomplete="country-name" />
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
            </div>

            <div>
                <x-input-label for="city" :value="__('City')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city)" autocomplete="address-level2" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>
        </div>

        <div>
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" autocomplete="street-address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

        </div>
    </form>

    {{-- ===== Success alert ===== --}}
    @if (session('status') === 'profile-updated')
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4000)"
            x-show="show"
            x-transition.opacity.duration.300ms
            role="status"
            aria-live="polite"
            class="fixed right-4 top-4 z-50 flex w-full max-w-sm items-start gap-3 rounded-xl border border-green-200 bg-white p-4 shadow-lg"
            style="top: max(1rem, env(safe-area-inset-top))"
        >
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 111.4-1.4l3.8 3.8 6.8-6.8a1 1 0 011.4 0z" clip-rule="evenodd"/>
                </svg>
            </span>

            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">{{ __('Profile updated') }}</p>
                <p class="mt-0.5 text-sm text-gray-600">{{ __('Your changes have been saved successfully.') }}</p>
            </div>

            <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="{{ __('Close') }}">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M6.3 5.3a1 1 0 00-1 1.4L8.6 10l-3.3 3.3a1 1 0 101.4 1.4L10 11.4l3.3 3.3a1 1 0 001.4-1.4L11.4 10l3.3-3.3a1 1 0 10-1.4-1.4L10 8.6 6.7 5.3z"/>
                </svg>
            </button>
        </div>
    @endif
</section>