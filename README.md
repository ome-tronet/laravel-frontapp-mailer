# Laravel Frontapp mailer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ome-tronet/laravel-frontapp-mailer.svg?style=flat-square)](https://packagist.org/packages/ome-tronet/laravel-frontapp-mailer)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ome-tronet/laravel-frontapp-mailer/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ome-tronet/laravel-frontapp-mailer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ome-tronet/laravel-frontapp-mailer.svg?style=flat-square)](https://packagist.org/packages/ome-tronet/laravel-frontapp-mailer)

This package registers a laravel mailer so you can send your application's mails via Front API (https://front.com/). This can be useful if you are expecting a conversation with the recipient and want to follow it up in Front. You also have the option of tagging emails when they are sent in order to classify them in Front.

## Requires

- PHP ^8.2
- Laravel ^10.0||^11.0

## Installation

You can install the package via composer:

```bash
composer require ome-tronet/laravel-frontapp-mailer
```
Add your Frontapp API token to your .env file like this:

```dotenv
FRONTAPP_API_TOKEN="your_token"
```

You must publish the config file `frontapp-mailer.php` in order to specify all allowed senders.

```bash
 php artisan vendor:publish --provider="tronet\FrontappMailer\FrontappServiceProvider"
```
Just FYI: This config will automatically be added as a new config key of `mail.mailers` by the package when booting.

To add your senders you need the Front `channel_id` of the shared inbox. If you want to send mails from a specific user you also need to provide the `author_id` of the teammate.

```php
return [
    'transport' => 'front',
    'api_base_url' => 'https://api2.frontapp.com',
    'api_token' => env('FRONTAPP_API_TOKEN'),
    'senders' => [

        /*
         * You must specify all desired senders.
         *
         * Specify shared inboxes as senders
         * with their channel_id.
         *
         */

         'info@example.com' => [
            'channel_id' => 'cha_XXXXX'
         ]

        /*
         * Specify teammates as senders
         * with their author_id.
         * You can use non-existing addresses.
         *
         * 'teammate.info@example.com' => [
         *      'channel_id' => 'cha_XXXXX',
         *      'author_id' => 'tea_XXXXX'
         * ]
         *
         */

    ]
];
```

## Usage

Create your mailable as usual with `php artisan make:mail MyMail` and use one of the senders you added to the `frontapp-mailer.php` config file as address in the envelope. You may also add front tags by their `tag_id` to the conversation upfront.

```php
class MyMail extends Mailable
{
    // [...]
    
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@example.com'), // must be configured in frontapp-mailer.php
            subject: 'your_subject',
            tags: ['tag_XXXXX'],
        );
    }
    
    // [...]
}
```

Now you can use the Front mailer everywhere you like to send this mailable to your recipients.

```php
use Illuminate\Support\Facades\Mail;

        Mail::mailer('front')
            ->to(['your_recipient']) 
            ->cc(['your_cc_recipient'])
            ->bcc(['your_bcc_recipient'])
            ->send(new MyMail());
```

## Where to get the API token

In the front app, go to Settings > Developers and click on the API tokens tab. There you can get an existing API token or create a new one.

## Where do I find the IDs of channels, authors, and tags

Go to Front's API reference page to list the channels, teammates and tags. If you provide your API token, the page will generate the API call ready to use as e.g. curl shell command:

- https://dev.frontapp.com/reference/list-channels
- https://dev.frontapp.com/reference/list-teammates
- https://dev.frontapp.com/reference/list-tags

Extract the IDs of the channels ('cha_XXXXX'), authors ('tea_XXXXX') and tags ('tag_XXXXX') that you want to use in your application from the API responses.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
