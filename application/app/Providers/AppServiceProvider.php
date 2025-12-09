<?php

namespace App\Providers;

use App\Models\User;
use App\Lib\Searchable;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Language;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\UserNotification;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\RateLimiter;

use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Instagram\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('instagram', Provider::class);
        });

        $general = gs();
        $activeTemplate = activeTemplate();
        $viewShare['general'] = $general;
        $viewShare['activeTemplate'] = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['language'] = Language::all();
        $viewShare['emptyMessage'] = 'No data';
        view()->share($viewShare);


        view()->composer('admin.support.tickets', function ($view) {
            $view->with([
                'pendingTicketCount'         => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
            ]);
        });
        view()->composer('admin.components.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                'pendingTicketCount'         => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                'pendingDepositsCount'    => Deposit::pending()->count(),
            ]);
        });

        view()->composer('admin.components.topnav', function ($view) {
            $view->with([
                'adminNotifications'=>AdminNotification::where('read_status', Status::NO)->with('user')->orderBy('id','desc')->take(10)->get(),
                'adminNotificationCount'=>AdminNotification::where('read_status', Status::NO)->count(),
            ]);
        });

        view()->composer('includes.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        if($general->force_ssl){
            URL::forceScheme('https');
        }

        View::addNamespace('Template',resource_path('views/presets/'.activeTemplateName()));
        View::addNamespace('Admin',resource_path('views/admin'));
        View::addNamespace('UserTemplate',resource_path('views/presets/'.activeTemplateName().'/user'));

        Paginator::useBootstrapFive();

        // Admin Directive
        Blade::directive('adminHas', function ($expression) {
            return "<?php if(auth()->guard('admin')->check() && auth()->guard('admin')->user()->hasPermission($expression)): ?>";
        });

        Blade::directive('endadminHas', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('adminHasAny', function ($expression) {
            return "<?php if(auth()->guard('admin')->check() && collect($expression)->some(fn(\$perm) => auth()->guard('admin')->user()->hasPermission(\$perm))): ?>";
        });

        Blade::directive('endadminHasAny', function () {
            return "<?php endif; ?>";
        });


        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });


    }
}
