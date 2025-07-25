<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'replies' => 'App\Models\Reply',
            'tickets' => 'App\Models\Ticket',
            'user' => 'App\Models\User',
        ]);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response(__('limiter.to_many_attempts'), 429, $headers);
                });
        });
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response(__('limiter.to_many_attempts'), 429, $headers);
                });
        });

        RateLimiter::for('login_with_same_email', function (Request $request) {
            return Limit::perMinute(3)->by($request->get('email'))
                ->response(function (Request $request, array $headers) {
                    return response(__('limiter.to_many_attempts'), 429, $headers);
                });
        });
        RateLimiter::for('user_send_ticket', function (Request $request) {
            return Limit::perMinute(3)->by($request->user()?->id)
                ->response(function (Request $request, array $headers) {
                    return response(__('limiter.to_many_tickets_submitted'), 429, $headers);
                });
        });

    }

}
