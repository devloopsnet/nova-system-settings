![Laravel Nova System Settings Tool](https://banners.beyondco.de/Laravel%20Nova%20System%20Settings.png?theme=dark&packageManager=composer+require&packageName=devloops%2Fnova-system-settings&pattern=architect&style=style_2&description=The+missing+Laravel+Nova+System+Settings+Tool&md=1&showWatermark=0&fontSize=100px&images=cog&widths=350)

[![Latest Version on Packagist](https://poser.pugx.org/devloops/nova-system-settings/v/stable?format=flat-square&color=#0E7FC0)](https://packagist.org/packages/devloops/nova-system-settings)
[![License](https://poser.pugx.org/devloops/nova-system-settings/license?format=flat-square)](https://packagist.org/packages/devloops/nova-system-settings)
[![Total Downloads](https://poser.pugx.org/devloops/nova-system-settings/downloads?format=flat-square)](https://packagist.org/packages/devloops/nova-system-settings)

# The Missing Laravel Nova System Settings Tool.

This packages saves the times for you when creating the system settings part of your project, it handles the UI in a very 
intuitive convenient way.
It has a straightforward, Nova-Like implementation, and it was built over Spatie's [laravel-settings](https://github.com/spatie/laravel-settings) package.

* A look at Spatie's package [docs](https://github.com/spatie/laravel-settings?tab=readme-ov-file#usage) is needed to keep track of how things are going.

## Installation

```
composer require devloops/nova-system-settings
```

## Implementation

The usage of this package is very simple as creating a class that extends ``Devloops\NovaSystemSettings\Contracts\SystemSettings`` abstract class, which itself 
extends the ``Spatie\LaravelSettings\Settings`` class of Spatie's laravel-settings packages, then register the tool inside the ``NovaServiceProvider``
giving it an array of the settings you defined for your system.

Below is a full of example

1- Create your settings class as follows.

```php
<?php

namespace App\Nova\Settings\General;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Devloops\NovaSystemSettings\Contracts\SystemSettings;


class SiteSettings extends SystemSettings
{

    public ?string $title;

    public ?string $slogan;

    public ?string $email;

    public ?string $phoneNumber;

    public ?string $address;

    public static function group(): string
    {
        return 'general';
    }

    public static function title(): string
    {
        return __('Site Settings');
    }

    public static function icon(): string
    {
        return 'cog';
    }

    public static function name(): string
    {
        return 'site_settings';
    }

    public static function fields(): array
    {
        return [
            Text::make(__('Site Title'), 'title'),
            Text::make(__('Site Slogan'), 'slogan'),
            Text::make(__('Site Email'), 'email'),
            Text::make(__('Site Phone Number'), 'phoneNumber'),
            Textarea::make(__('Site Address'), 'address'),
        ];
    }
}
```

```php
<?php

namespace App\Nova\Settings\General;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Devloops\NovaSystemSettings\Contracts\SystemSettings;

class MailSettings extends SystemSettings
{

    public ?string $mailer;

    public ?string $host;

    public ?int $port;

    public ?string $username;

    public ?string $password;

    public ?string $encryption;

    public static function group(): string
    {
        return 'general';
    }

    public static function title(): string
    {
        return __('Mail Settings');
    }

    public static function icon(): string
    {
        return 'mail';
    }

    public static function name(): string
    {
        return 'mail_settings';
    }

    public static function fields(): array
    {
        return [
            Select::make(__('Mailer'), 'mail')
                  ->options([
                      'smtp'     => __('SMTP'),
                      'sendmail' => __('Sendmail'),
                      'mailgun'  => __('Mailgun'),
                  ]),
            Text::make(__('Host'), 'host'),
            Number::make(__('Port'), 'port'),
            Text::make(__('Username'), 'username'),
            Password::make(__('Password'), 'password'),
            Select::make(__('Encryption'), 'encryption')
                  ->options([
                      null  => __('None'),
                      'tls' => __('TLS'),
                      'ssl' => __('SSL'),
                  ]),
        ];
    }
}

```

```php
<?php

namespace App\Nova\Settings\Store;

use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Devloops\NovaSystemSettings\Contracts\SystemSettings;

class OrderSettings extends SystemSettings
{
    public ?float $minOrder;

    public ?bool $allowGuestCheckout;

    public ?bool $allowFreeShipping;

    public static function group(): string
    {
        return 'store';
    }

    public static function title(): string
    {
        return __('Order Settings');
    }

    public static function icon(): string
    {
        return 'shopping-cart';
    }

    public static function name(): string
    {
        return 'order_settings';
    }

    public static function fields(): array
    {
        return [
            Number::make(__('Minimum Order'), 'minOrder'),
            Boolean::make(__('Allow Guest Checkout'), 'allowGuestCheckout'),
            Boolean::make(__('Allow Free Shipping'), 'allowFreeShipping'),
        ];
    }
}
```

```php
<?php

namespace App\Nova\Settings\Tenant\Store;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Devloops\NovaSystemSettings\Contracts\SystemSettings;

class CustomerSettings extends SystemSettings
{

    public ?string $loginVia;

    public ?bool $requiresEmailVerification;

    public ?int $canRegister;

    public static function group(): string
    {
        return 'store';
    }

    public static function title(): string
    {
        return __('Customer Settings');
    }

    public static function icon(): string
    {
        return 'user';
    }

    public static function name(): string
    {
        return 'customer_settings';
    }

    public static function fields(): array
    {
        return [
            Select::make(__('Login Via'))
                  ->options([
                      'email'        => __('Email'),
                      'phone_number' => __('Phone Number'),
                  ]),
            Boolean::make(__('Requires Email Verification'), 'requiresEmailVerification'),
            Boolean::make(__('Can Register'), 'canRegister'),
        ];
    }
}
```

The above classes implements five methods that are abstractly inherited from the ``SystemSettings`` class, the methods are:

```php

    /**
     * Get system settings group.
     *
     * @return string
     */
    abstract public static function group(): string;

    /**
     * Get system settings title.
     *
     * @return string
     */
    abstract public static function title(): string;

    /**
     * Get system settings icon.
     *
     * @return string
     */
    abstract public static function icon(): string;

    /**
     * Get system settings name.
     *
     * @return string
     */
    abstract public static function name(): string;

    /**
     * Return system settings fields.
     *
     * @return array
     */
    abstract public static function fields(): array;
```

The comments on the methods tells each methods goal.

2- Register all your settings via the ``tool()`` method in the ``App\Providers\NovaServiceProvider`` like the example below.

```php

use App\Nova\Settings\General\SiteSettings;
use App\Nova\Settings\General\MailSettings;
use App\Nova\Settings\Store\OrderSettings;
use App\Nova\Settings\Store\CustomerSettings;

    public function tools(): array
    {
        return [
            NovaSystemSettings::make([
                //General
                SiteSettings::make(),
                MailSettings::make(),

                //Store
                OrderSettings::make(),
                CustomerSettings::make()
            ]),
        ];
    }
```

4- The system settings groups title are translatable, you need to create a locale file ``resources/lang/en/system-settings.php``

```php
<?php

return [
    'groups' => [        
        'general'          => 'General',
        'store'            => 'Store',
    ],
];
```

5- System settings internal usage is pretty simple, as Spatie's laravel-settings package behaves, you can simply use 
dependency injection to inject the settings class in either your services, controllers, repositories or any other place in your system.

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Nova\Settings\Store\CustomerSettings;
use App\Http\Requests\Api\V1\Auth\SendOtpRequest;

class AuthController extends ApiController
{

    public function __construct(
        public CustomerSettings $customerSettings
    ) {
    }

    public function sendOtp(SendOtpRequest $request)
    {
        dd($this->customerSettings->loginVia);
    }
}
```

# Screenshots

![Screenshot 1](https://i.ibb.co/Wy6CjpM/screenshot-5.png)

![Screenshot 2](https://i.ibb.co/b16YdQG/screenshot-4.png)

![Screenshot 3](https://i.ibb.co/5n9RBLQ/screenshot-3.png)

![Screenshot $](https://i.ibb.co/b6Zrpgh/screenshot-2.png)

## Credits

* [Abdullah Al-Faqeir](https://github.com/abdullahfaqeir)

## License

The MIT License (MIT). Please see [License File](./LICENSE.md) for more information.
