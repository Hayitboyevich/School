<?php

namespace App\Http\Middleware;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthenticateFilament extends Middleware
{
    public function __construct(Auth $auth, protected Panel $panel)
    {
        parent::__construct($auth);
    }

    protected function authenticate($request, array $guards): void
    {
        $guardName = config('filament.auth.guard');
        $guard = $this->auth->guard($guardName);

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse($guardName);

        $user = $guard->user();

        if ($user instanceof FilamentUser && !$user->canAccessPanel($this->panel)) {
            throw new AuthenticationException('AccessDenied', $guards, route('home'));
        }
    }

    protected function redirectTo($request): string
    {
        return route('login');
    }
}
