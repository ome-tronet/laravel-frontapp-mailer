# Laravel Package for using Front as a mailer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ome-tronet/laravel-frontapp-mailer.svg?style=flat-square)](https://packagist.org/packages/ome-tronet/laravel-frontapp-mailer)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ome-tronet/laravel-frontapp-mailer/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ome-tronet/laravel-frontapp-mailer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ome-tronet/laravel-frontapp-mailer/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ome-tronet/laravel-frontapp-mailer/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ome-tronet/laravel-frontapp-mailer.svg?style=flat-square)](https://packagist.org/packages/ome-tronet/laravel-frontapp-mailer)

This package registers a laravel mailer with which you can send your application's mails via Front API (https://front.com/). his can be useful if you are expecting a conversation with the recipient and want to follow it up in Front. You also have the option of tagging emails when they are sent in order to classify them in Front.

## Installation

You can install the package via composer:

```bash
composer require ome-tronet/laravel-frontapp-mailer
```
Add your Frontapp API token to your .env file.

```dotenv
FRONTAPP_API_TOKEN="yourtoken"
```

You must publish the config file in order to specify all allowed senders.

```bash
 php artisan vendor:publish --provider="tronet\FrontappMailer\FrontappServiceProvider"
```

To specify your senders you need the Front channel_id of the inbox. If it's a personal inbox you also need to provide the author_id of the teammate.

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
         * with their channel_id
         *
         */

         'info@example.com' => [
            'channel_id' => 'cha_XXXXX'
         ]

         /*
         * Specify personal inboxes as senders
         * additionally with their author_id
         *
         * 'name@example.com' => [
         *      'channel_id' => 'cha_XXXXX',
         *      'author_id' => 'tea_XXXXX'
         * ]
         *
         */

    ]
];
```

## Usage

```php
$frontappMailer = new tronet\FrontappMailer();
echo $frontappMailer->echoPhrase('Hello, tronet!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Oliver Merklinghaus](https://github.com/ome-tronet)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
