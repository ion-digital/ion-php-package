# Packaging

[![License: LGPL v3](https://img.shields.io/badge/License-LGPL%20v3-blue.svg)](https://www.gnu.org/licenses/lgpl-3.0)

A small library that functions as the base to all ION packages, intended to handle basic package versioning functionality.

## Getting Started

###As an included library, with Composer:

Make sure Composer is installed - if not, you can get it from [here](https://getcomposer.org/ "getcomposer.org").

First, you need to add _ion/versioning_ as a dependency in your _composer.json_ file.

To use the current stable version, add the following to download it straight from [here](https://packagist.org/ "packagist.org"):

```
"require": {
    "php": ">=7.4",
    "ion/packaging": "^1.0.0",
}
```

To use the bleeding edge (development) version, add the following:

```
"require": {
    "php": ">=7.4",
    "ion/packaging": "dev-default",	
},
"repositories": {
    {
      "type": "vcs",
      "url": "https://github.com/ion-digital/ion-php-packaging.git"
    }
}
```

Then run the following in the root directory of your project:

> php composer.phar install

### Prerequisites

* Composer (_optional_)


## Built With

* [Composer](https://getcomposer.org/) - Dependency Management

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/ion-digital/ion-php-packaging/tags "GitHub"). 

## Authors

* **Justus Meyer** - *Initial work* - [GitHub](https://justusmeyer.com/github), [Upwork](https://justusmeyer.com/upwork)

## License

This project is licensed under the LGPL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details.

