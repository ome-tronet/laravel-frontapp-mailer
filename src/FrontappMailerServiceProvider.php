<?php

namespace tronet\FrontappMailer;

use Illuminate\Support\Facades\Mail;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use tronet\FrontappService\Mail\Transports\FrontappTransport;

class FrontappMailerServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/frontapp_mail.php', 'mail.mailers'
        );

        Mail::extend('front', function (array $config = []) {
            return new FrontappTransport(new FrontappService($config));
        });
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-frontapp-mailer')
            ->hasConfigFile();
    }
}
