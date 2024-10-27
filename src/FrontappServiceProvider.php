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
            __DIR__.'/../config/frontapp-mailer.php', 'mail.mailers'
        );
    }

    public function boot(): void
    {
        Mail::extend('front', function (array $config = []) {
            return new FrontappTransport(new FrontappService($config));
        });
    }
}
