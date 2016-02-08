# QuickStrap
Quickly configure your project with test suites, continuous integrations and more.

## Installation
The recommended usage is to install QuickStrap through composer with a global installation:

```
composer global require quickstrap/quickstrap
```

Now, you can use QuickStrap in any of your new projects!

## Usage

### QuickStrap-ing PHPUnit
This will install PHPUnit, and setup your test directory and phpunit.xml configuration for you.

```
quickstrap testsuites:phpunit
```

### QuickStrap-ing Behat [TODO]
This will install Behat, and setup your feature directory and behat.yml configuration for you.
```
quickstrap testsuites:behat
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
