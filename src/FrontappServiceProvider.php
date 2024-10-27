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
        $this->mergeConfigFrom(
            __DIR__.'/../config/frontapp_mail.php', 'mail.mailers'
        );

        Mail::extend('front', function (array $config = []) {
            return new FrontappTransport(new FrontappService($config));
        });
    }
}
