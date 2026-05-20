> # 📦 This package has moved
>
> `stechstudio/laravel-email-events` has been renamed to **[`stechstudio/laravel-postmaster`](https://github.com/stechstudio/laravel-postmaster)**.
>
> Version `0.14` (documented below) is the final release under this name. It will keep working, but all new development — including persistence, multitenancy, and additional providers — happens in the new package.
>
> To upgrade:
>
> ```bash
> composer remove stechstudio/laravel-email-events
> composer require stechstudio/laravel-postmaster
> ```
>
> See the [upgrade guide](https://github.com/stechstudio/laravel-postmaster/blob/master/UPGRADE.md) for the namespace and config changes.

---

# Email event webhook processing

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stechstudio/laravel-email-events.svg?style=flat-square)](https://packagist.org/packages/stechstudio/laravel-email-events)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

So you have your Laravel app sending email out through SendGrid or Mailgun or Postmark, super. Now you want to capture webhooks and react to email events, such as deliveries or bounces. This is where it can get a bit messy, authorizing the webhook submission, parsing the payload, handling the details... and then of course you've tied yourself to one particular email provider.

This package greatly simplifies the process. It will accept webhook submissions from any supported email provider, authorize the submission, generalize it to a standard data format, and fire off a Laravel event. Now all you have to do is listen for an event and go!

## Installation

Via Composer

``` bash
$ composer require stechstudio/laravel-email-events
```

## Quick start

### 1. Add the routes

In your `routes/web.php` file add:

```php
EmailEvents::routes();
```

This will wire up a route at `.hooks/email-events/{provider}`. 

### 2. Configure your auth method

There are multiple authorization options available. The default option (and works with any provider) is just a URL token.

In your app .env file set a token secret:

```
MAIL_EVENTS_AUTH_TOKEN=mysecrettoken
```

### 3. Configure your mail provider with your webhook endpoint

Log in to your email provider account, find the webhook section, and add your URL endpoint. Make sure to specify the provider name, and include the auth token.

For example, if you are using SendGrid you would go to the [Mail Settings](https://app.sendgrid.com/settings/mail_settings) page, turn on the "Event Notification" setting, check all the actions that you care about, and provide your POST URL:

```
https://<yourdomain>/.hooks/email-events/sendgrid?auth=mysecrettoken
```

> Note while in local/development mode you can use [`valet share`](https://laravel.com/docs/master/valet#sharing-sites) to get a publicly accessible domain for your app.

### 4. Listen for the event

Lastly, you need to listen for an email event in your app. Setup an [event listener](https://laravel.com/docs/master/events#defining-listeners) and listen for `STS\EmailEvents\EmailEvent`.

Something like this:

```php
namespace App\Listeners;

use STS\EmailEvents\EmailEvent;

class NotifyBouncedEmail {

    public function handle(EmailEvent $event)
    {
        // I only care about bounces
        if($event->getAction() != EmailEvent::EVENT_BOUNCED) {
            return;
        } 
        
        // Ok so we have an email bounce! Need to go handle that. Maybe notify us on Slack?
        // ...
    }
    
}
```
