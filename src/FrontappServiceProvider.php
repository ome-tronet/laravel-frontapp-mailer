<?php

namespace tronet\FrontappMailer;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use tronet\FrontappService\Mail\Transports\FrontappTransport;

class FrontappServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/frontapp-mailer.php',
            'frontapp-mailer'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/frontapp-mailer.php' => config_path('frontapp-mailer.php'),
        ]);

        $frontappConfig = config('frontapp-mailer');

        if (!empty($frontappConfig)) {
            config(['mail.mailers.front' => $frontappConfig]);
        }

        Mail::extend('front', function (array $config = []) {
            return new FrontappTransport(new FrontappService($config));
        });
    }
}
