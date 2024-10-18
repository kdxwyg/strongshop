<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use OpenStrong\StrongAdmin\StrongAdminApplicationServiceProvider;
use Illuminate\Support\Facades\View;

class StrongAdminServiceProvider extends StrongAdminApplicationServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //后台数据录入使用多语言录入形式
        View::share('_multiLanguageBackend', config('strongshop.multiLanguageBackend', false));
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewStrongAdmin', function ($user) {
            return in_array($user->email, [
            //
            ]);
        });
    }

}
