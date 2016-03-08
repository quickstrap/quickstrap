[![Stories in Ready](https://badge.waffle.io/quickstrap/quickstrap.png?label=ready&title=Ready)](https://waffle.io/quickstrap/quickstrap)
[![Code Climate](https://codeclimate.com/github/quickstrap/quickstrap/badges/gpa.svg)](https://codeclimate.com/github/quickstrap/quickstrap)
[![Test Coverage](https://codeclimate.com/github/quickstrap/quickstrap/badges/coverage.svg)](https://codeclimate.com/github/quickstrap/quickstrap/coverage)

# QuickStrap
Quickly configure your project with test suites, continuous integrations and more.

## Installation
The recommended usage is to install QuickStrap through composer with a global installation:

```
composer global require quickstrap/quickstrap
```

Now, you can use QuickStrap in any of your new projects!
_Note that your home composer bin folder should be added to your environment path_

## Usage

### QuickStrap-ing PHPUnit
This will install PHPUnit, and setup your test directory and phpunit.xml configuration for you.

```
quickstrap testsuites:phpunit
```

### QuickStrap-ing Behat
This will install Behat, and setup your feature directory and behat.yml configuration for you.
```
quickstrap testsuites:behat
```

### QuickStrap-ing Code Sniffer
This will install Code Sniffer.
```
quickstrap analyzers:codesniffer
```


### QuickStrap-ing TravisCI [TODO]
This will generate a .travis-ci.yml configuration for you. If you've got phpunit or behat installed it will automatically
add them to the configuration.
```
quickstrap ci:travis-ci
```

### QuickStrap-ing GitlabCI [TODO]
This will generate a .gitlab-ci.yml configuration for you. If you've got phpunit or behat installed it will automatically
add them to the configuration.
```
quickstrap ci:gitlab-ci
```

### QuickStrap-ing Apigility [TODO]
This will setup a zend framework apigility skeleton project for you.
```
quickstrap frameworks:apigility
```

### QuickStrap-ing Slim [TODO]
This will setup a slimframework skeleton project for you.
```
quickstrap frameworks:slim
```

### QuickStrap-ing Silex [TODO]
This will setup a silex skeleton project for you.
```
quickstrap frameworks:silex
```

## extensions [TODO]
But wait, there's more! QuickStrap supports extensions. You can create a custom quickstraps of your own and make them available
with the quickstrap command.

### extension installation
Follow our extension implementation guidelines and developers will be able to do the following:

```
composer global require your/quickstrap-extension
quickstrap register your/quickstrap-ci-extension
```
Now when you run quickstrap list you will see your extension available
```
quickstrap list
> ...
> ci:travis-ci
> ci:your-custom-extension
```
