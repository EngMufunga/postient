<?php

use Illuminate\Support\Facades\Route;


Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    $notify[] = ['success', 'Cache cleared successfully.'];
    return redirect()->back()->withNotify($notify);
})->name('clear.cache');

Route::middleware('admin.guest')->namespace('Auth')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout')->withoutMiddleware('admin.guest');
    });

    // Admin Password Reset
    Route::controller('ForgotPasswordController')->group(function(){
        Route::get('password/reset', 'showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'sendResetCodeEmail');
        Route::get('password/code-verify', 'codeVerify')->name('password.code.verify');
        Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
    });

    Route::controller('ResetPasswordController')->group(function(){
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });
});

Route::middleware(['admin'])->group(function () {

    Route::controller('AdminController')->group(function(){
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications','notifications')->name('notifications');
        Route::get('notification/read/{id}','notificationRead')->name('notification.read');
        Route::get('notifications/read-all','readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request/report','requestReport')->name('request.report');
        Route::post('request/report','reportSubmit');

        Route::get('download/attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });


    Route::middleware('admin.permission:platform-management')->controller('PlatformController')->prefix('platform')->name('platform.')->group(function(){
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
        Route::get('analytics/{id}', 'analytics')->name('analytics');
        Route::get('/{status?}', 'index')->name('index');
    });

    Route::middleware('admin.permission:post-management')->controller('PostController')->prefix('posts')->name('posts.')->group(function(){
        Route::get('/', 'index')->name('index');
    });


        // Plan Packages
    Route::middleware('admin.permission:plan-management')->controller('PlanController')->prefix('plan')->name('plan.')->group(function (){
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
        Route::get('subscriptions', 'subscriptions')->name('subscriptions');
        Route::get('/{status?}', 'index')->name('index');
    });


    // Users Manager
    Route::middleware('admin.permission:user-management')->controller('ManageUsersController')->name('users.')->prefix('manage/users')->group(function(){
        Route::get('log/{status?}', 'allUsers')->name('all');
        Route::post('bulk-action', 'bulkActionForm')->name('bulk.action');
        Route::get('create', 'create')->name('create');
        Route::post('create', 'store')->name('store');
        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add/sub/balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send/notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send/notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('notify-users', 'showNotificationAllForm')->name('notification.all');
        Route::post('notify-users', 'sendNotificationAll')->name('notification.all.send');
        Route::get('get', 'get')->name('get');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');

    });

    Route::middleware('admin.permission:website-menu-management')->name('menu.')->prefix('menu')->controller('MenuController')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('update/{id?}', 'storeOrUpdate')->name('storeorupdate');
        Route::post('delete/{id}', 'remove')->name('delete');
        Route::get('assign-item/{id}', 'assignMenuItem')->name('assign.item');
        Route::post('assign-item/{id}', 'assignMenuItemSubmit')->name('assign.item.submit');

        Route::post('menu-item/store/{id}', 'menuItemStore')->name('item.store');
        Route::post('menu-item/update/{id}', 'menuItemUpdate')->name('item.update');
        Route::post('menu-item/delete/{id}', 'menuItemDelete')->name('item.delete');
    });

    Route::middleware('admin.permission:section-management')->name('custom.section.')->prefix('custom-section')->controller('CustomSectionController')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('delete/{key}', 'delete')->name('delete');
    });


    // Subscriber
    Route::middleware('admin.permission:subscriber-management')->name('subscriber.')->prefix('subscriber')->controller('SubscriberController')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::get('send/email', 'sendEmailForm')->name('send.email');
        Route::post('remove/{id}', 'remove')->name('remove');
        Route::post('send/email', 'sendEmail')->name('send.email');
    });

    Route::middleware('admin.permission:role')->name('role.')->prefix('role')->controller('RoleController')->group(function(){
        Route::get('/{status?}', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::get('permission/seeder', 'seeder')->name('seeder');
    });

    Route::middleware('admin.permission:staff')->name('staff.')->prefix('staff')->controller('StaffController')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('create', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'remove')->name('delete');
        Route::get('permission-setup/{id}', 'setup')->name('setup');
        Route::post('permission-update/{id}', 'setupUpdate')->name('setup.update');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('permission/seeder', 'seeder')->name('seeder');
    });


    // Deposit Gateway
    Route::middleware('admin.permission:payment-method')->name('gateway.')->prefix('payment/gateways')->group(function(){
        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->name('automatic.')->prefix('automatic')->group(function(){
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{code}', 'status')->name('status');
            Route::get('{status?}', 'index')->name('index');
        });


        // Manual Methods
        Route::controller('ManualGatewayController')->name('manual.')->prefix('manual')->group(function(){
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('{status?}', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{code}', 'status')->name('status');
        });
    });


    // DEPOSIT SYSTEM
    Route::middleware('admin.permission:deposit-management')->name('deposit.')->controller('DepositController')->prefix('manage/deposits')->group(function(){
        Route::get('log/{status?}', 'deposit')->name('log');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
    });

    // Report
    Route::middleware('admin.permission:reports')->name('report.')->prefix('report')->controller('ReportController')->group(function(){
        Route::get('transaction', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });


    // Admin Support
    Route::middleware('admin.permission:support-ticket')->controller('SupportTicketController')->name('ticket.')->prefix('support/ticket')->group(function(){
        Route::get('{status?}', 'tickets')->name('index');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::get('download/{ticket}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
    });


    // Language Manager
    Route::middleware('admin.permission:language-management')->controller('LanguageController')->prefix('manage')->group(function(){
        Route::get('languages', 'langManage')->name('language.manage');
        Route::post('language', 'langStore')->name('language.manage.store');
        Route::post('language/delete/{id}', 'langDelete')->name('language.manage.delete');
        Route::post('language/update/{id}', 'langUpdate')->name('language.manage.update');
        Route::get('language/edit/{id}', 'langEdit')->name('language.key');
        Route::post('language/import', 'langImport')->name('language.import.lang');
        Route::post('language/store/key/{id}', 'storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'updateLanguageJson')->name('language.update.key');
        Route::get('language/search/', 'langSearch')->name('language.manage.search');
        Route::get('language/search/replace/', 'langSearchReplace')->name('language.manage.search.replace');
    });


    Route::middleware('admin.permission:settings')->controller('GeneralSettingController')->group(function(){
        // General Setting
        Route::get('global/settings', 'index')->name('setting.index');
        Route::post('global/settings', 'update')->name('setting.update');

        // Logo-Icon
        Route::get('setting/logo', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo', 'logoIconUpdate')->name('setting.logo.icon');

        //Cookie
        Route::get('cookie','cookie')->name('setting.cookie');
        Route::post('cookie','cookieSubmit')->name('setting.cookie.update') ;

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit')->name('setting.custom.css.update');

        // Maintenance
        Route::get('maintenance', 'maintenance')->name('setting.maintenance');
        Route::post('maintenance', 'maintenanceSubmit')->name('setting.maintenance.update');

        //socialite credentials
        Route::get('setting/oauth/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/oauth/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/oauth/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');

        // Open AI Setup
        Route::get('setting/openai', 'openai')->name('setting.openai');
        Route::post('setting/openai', 'openaiSubmit')->name('setting.openai.update');
        Route::get('seed/openai/model', 'openaiModel')->name('setting.openai.model');

        //socialite credentials
        Route::get('setting/social-app/credentials', 'metaSocialiteCredentials')->name('setting.social_app.credentials');
        Route::post('setting/social-app/credentials/update/{key}', 'updateMetaSocialiteCredential')->name('setting.social_app.credentials.update');
        Route::post('setting/social-app/credentials/status/{key}', 'updateMetaSocialiteCredentialStatus')->name('setting.social_app.credentials.status.update');

    });


    //Notification Setting
    Route::middleware('admin.permission:settings')->name('setting.notification.')->controller('NotificationController')->prefix('notifications')->group(function(){
        //Template Setting
        Route::get('global','global')->name('global');
        Route::post('global/update','globalUpdate')->name('global.update');
        Route::get('templates','templates')->name('templates');
        Route::get('template/edit/{id}','templateEdit')->name('template.edit');
        Route::post('template/update/{id}','templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting','emailSetting')->name('email');
        Route::post('email/setting','emailSettingUpdate');
        Route::post('email/test','emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting','smsSetting')->name('sms');
        Route::post('sms/setting','smsSettingUpdate');
        Route::post('sms/test','smsTest')->name('sms.test');
    });


    // Plugin
    Route::middleware('admin.permission:plugin-management')->controller('PluginController')->name('plugins.')->prefix('plugin')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });


    // SEO
    Route::middleware('admin.permission:settings')->get('seo', 'FrontendController@seoEdit')->name('seo');


    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {
        Route::middleware('admin.permission:section-management')->controller('FrontendController')->group(function(){
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::middleware('admin.permission:page-management')->controller('PageBuilderController')->name('manage.')->prefix('manage')->group(function(){
            Route::get('pages', 'managePages')->name('pages');
            Route::post('pages', 'managePagesSave')->name('pages.save');
            Route::post('pages/update', 'managePagesUpdate')->name('pages.update');
            Route::post('pages/delete/{id}', 'managePagesDelete')->name('pages.delete');
            Route::get('section/{id}', 'manageSection')->name('section');
            Route::post('section/{id}', 'manageSectionUpdate')->name('section.update');
        });
    });
});

