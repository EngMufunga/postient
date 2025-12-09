<?php
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->namespace('User\Auth')->name('user.')->group(function () {

    Route::controller('LoginController')->group(function(){
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function(){
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function(){
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });
    Route::controller('ResetPasswordController')->group(function(){
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->prefix('social')->group(function () {
        Route::get('login/{provider}', 'socialLogin')->name('social.login');
        Route::get('login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->name('user.')->group(function () {
    //authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function(){
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend/verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify/email', 'emailVerification')->name('verify.email');
        Route::post('verify/mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify/g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['check.status'])->group(function () {

        Route::get('user/data', 'User\UserController@userData')->name('data');
        Route::post('user/data/submit', 'User\UserController@userDataSubmit')->name('data.submit');

        Route::middleware('registration.complete')->namespace('User')->group(function () {

            Route::controller('UserController')->group(function(){
                Route::get('dashboard', 'home')->name('home');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions','transactions')->name('transactions');

                // Plan & Subscription
                Route::get('plan', 'plans')->name('plans');
                Route::get('subscription', 'subscriptions')->name('subscriptions');

                Route::get('free-plan/subscription', 'freePlanSubscription')->name('free.plan.subscription');


                //Notification
                Route::get('notifications','notifications')->name('notifications');
                Route::get('notification/read/{id}','notificationRead')->name('notification.read');

                Route::post('notifications/read-all','readAll')->name('notifications.readAll');
                Route::post('notifications/delete','deleteAll')->name('all.notification.delete');
                Route::get('notification/delete/{id}','singleNotificationDelete')->name('single.notification.delete');

                Route::get('credit/refill', 'creditRefill')->name('credit.refill');
                Route::post('credit/refill/confirm', 'creditRefillConfirm')->name('credit.refill.confirm');

                Route::get('attachment-download/{fil_hash}','attachmentDownload')->name('attachment.download');
            });

            // Social Media Post
            Route::controller('PostController')->name('posts.')->prefix('posts')->group(function(){
                Route::get('/', 'index')->name('index');
                Route::get('scheduled', 'scheduled')->name('scheduled');
                Route::get('drafted', 'drafted')->name('drafted');
                Route::get('published', 'published')->name('published');

                Route::get('select-type', 'selectType')->name('select.type');

                Route::get('create/{id?}', 'create')->name('create');

                Route::post('store', 'store')->name('store');
                Route::post('delete/{id}', 'delete')->name('delete');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::post('delete-image/{id}', 'deleteImage')->name('image.delete');
                Route::get('hashtag-generate', 'hashtagGenerate')->name('hashtag.generate');
                Route::get('content-generate', 'contentGenerate')->name('content.generate');

                Route::get('generate-image', 'generateImage')->name('generate.image');
                Route::post('remove-ai-image', 'removeAiImage')->name('remove.ai.image');
            });


            // Social Account Post
            Route::controller('SocialAccountController')->name('social.account.')->prefix('social-account')->group(function(){
                Route::get('/', 'index')->name('index');
                   Route::middleware('plan.check')->group(function () {
                    Route::get('connect/{provider}', 'connect')->name('connect');
                    Route::get('callback/{provider}', 'callback')->name('connect.callback');
                });
            });

            //Profile setting
            Route::controller('ProfileController')->group(function(){
                Route::get('profile/setting', 'profile')->name('profile.setting');
                Route::post('profile/setting', 'submitProfile')->name('profile.update');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword')->name('password.update');
                Route::post('profile-image', 'profileUpdate')->name('profile.image.update');
            });

        });

        // Payment
        Route::middleware('registration.complete')->controller('Gateway\PaymentController')->group(function(){
            Route::get('plan/payment/{id}', 'planPayment')->name('plan.payment');
            Route::post('subscription/payment/{id}', 'subscriptionPayment')->name('subscription.payment');


            Route::any('/deposit', 'deposit')->name('deposit');
            Route::post('deposit/insert', 'depositInsert')->name('deposit.insert');
            Route::get('deposit/confirm', 'depositConfirm')->name('deposit.confirm');
            Route::get('deposit/manual', 'manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('deposit/manual', 'manualDepositUpdate')->name('deposit.manual.update');
        });
    });
});
