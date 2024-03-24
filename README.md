![Kirby Logger](./.github/kirby-logger.png)

# Kirby Logger

Gain deeper insights into your Kirby website's behavior with the powerful Logger. The new Logger panel area provides a
clear view of what's happening behind the scenes, while the included Logger utility lets you easily log events within
your custom code. This combination empowers you to both understand your website's activity and streamline debugging for
a smoother development experience.

## Key Features

* 🕵️ **Effortless Log Exploration:** The intuitive panel view makes exploring your website's logs a breeze. Quickly find
  the information you need to understand what's happening.
* 💾 **Reliable Log Storage:** Enjoy peace of mind knowing your logs are saved securely and efficiently using SQLite, a
  robust database technology.
* 🐞 **Comprehensive Logging:** Gain a complete picture of your website's behavior. The extension automatically captures
  all Kirby after hooks and logs exceptions, providing valuable insights.
* ⚡️ **Customizable Event Logging:** Take control of your debugging process! The custom Logger utility empowers you to
  effortlessly log specific events within your custom code, allowing you to pinpoint issues with greater speed and
  accuracy.

## Installation

### Composer

```
composer require michnhokn/kirby-logger
```

### Download

Download and copy this repository to `/site/plugins/kirby-logger`

## Usage

Bolster your website's security with the comprehensive Audit Logs feature. This new panel section offers a centralized
view of all activity, complete with timestamps for context. Leverage granular filtering by channel and log level, or
utilize the powerful search bar to pinpoint specific events with ease. Gain actionable insights and stay on top of
everything happening within your website.

### Configuration

```php
<?php
// site/config/config.php

return [
    'michnhokn.logger' => [
        // optional - add your custom channels for better filtering in the panel
        'channels' => ['custom', 'my-plugin-a'],

        // optional - add specific hooks to an ignore list to prevent spamming in the panel
        'ignoreHooks' => [
            'page.render:after',
            'kirbytags:after'
        ]
    ]
];
```

### Logger Utility

The `Michnhokn\Logger` class lets you easily add new log entries. Available levels
are `DEBUG`, `INFO`, `WARNING`, `ERROR`, `CRITICAL`.

```php
// Write a custom log entry
\Michnhokn\Logger::write(
    message: 'Whoops! Something bad happened.',
    level: Logger::LEVEL_ERROR,
    context: ['userId' => App::instance()->user()?->id()]
);

// you can also use all available levels as a method name
\Michnhokn\Logger::info('A user logged in.',context: ['userId' => App::instance()->user()?->id()]);
```

## Upcoming features

* More details in the panel view (e.g. event arguments)
* More configuration for the panel view (e.g. date filter, event context)
* Better translations for the panel

## Support the project

> [!NOTE]
> This plugin is provided free of charge & published under the permissive MIT License. If you use it in a commercial
> project, please consider to [buy me a beer 🍺](https://buymeacoff.ee/michnhokn)

## License

[MIT](./LICENSE.md) License © 2021-PRESENT [Michael Engel](https://github.com/michnhokn)
