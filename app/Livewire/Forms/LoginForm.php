<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->email;

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([
            $field => $login,
            'password' => $this->password,
        ], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => 'Email/username atau password salah.',
            ]);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => 'Akun Anda sedang nonaktif. Silakan hubungi admin.',
            ]);
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        RateLimiter::clear($this->throttleKey());
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}