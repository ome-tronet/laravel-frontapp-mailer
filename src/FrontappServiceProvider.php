<?php

namespace tronet\FrontappMailer;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use tronet\FrontappService\Mail\Transports\FrontappTransport;

class FrontappServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/frontapp-mailer.php' => config_path('frontapp-mailer.php'),
        ]);

        $this->app['config']['mail.mailers'] = array_merge(
            $this->app['config']['mail.mailers'],
            $this->app['config']['frontapp-mailer'] ?? []
        );

        Mail::extend('front', function (array $config = []) {
            return new FrontappTransport(new FrontappService($config));
        });
    }
}
