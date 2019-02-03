# Doctrine Log Bridge

_PSR-3 log bridge for Doctrine's SQLLogger_

## Getting Started

### Prerequisites

You need a PSR-3 logger and Doctrine's DBAL for this to make sense.

### Installing

Download using composer

```
composer require marcuspi/doctrine-log-bridge
```

Then pass your favorite logger and the log level to the constructor

```php
<?php

use Marcuspi\DoctrineLogBridge\LogBridge;
use \Psr\Log\LogLevel;

// set up your logger, or get it from you dependency container or whatever
/** @var \Psr\Log\LoggerInterface $logger */

$logBridge = new LogBridge($logger, LogLevel::INFO);

// ...
// Then, when setting up the DBAL:
/** @var \Doctrine\DBAL\Configuration $config */

$config->setSQLLogger($logBridge);
```

## Versioning

[SemVer](http://semver.org/) is used for versioning. For the versions available, see the [tags on this repository](https://github.com/marcusirgens/doctrine-log-bridge). 

## Authors

* **Marcus Pettersen Irgens** - *Initial work* - [marcusirgens](https://github.com/PurpleBooth)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details