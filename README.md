[![Stories in Ready](https://badge.waffle.io/quickstrap/quickstrap.png?label=ready&title=Ready)](https://waffle.io/quickstrap/quickstrap)
[![Code Climate](https://codeclimate.com/github/quickstrap/quickstrap/badges/gpa.svg)](https://codeclimate.com/github/quickstrap/quickstrap)
[![Test Coverage](https://codeclimate.com/github/quickstrap/quickstrap/badges/coverage.svg)](https://codeclimate.com/github/quickstrap/quickstrap/coverage)
[![Coverage Status](https://coveralls.io/repos/github/quickstrap/quickstrap/badge.svg?branch=master)](https://coveralls.io/github/quickstrap/quickstrap?branch=master)


## Install Quickstrap
At a Terminal prompt paste the following:

```
php -r "copy('https://raw.githubusercontent.com/quickstrap/installer/master/src/setup.php', 'quickstrap-setup.php');"
php quickstrap-setup.php
php -r "unlink('quickstrap-setup.php');"
```

The script explains what it will do and then pauses before it does it.

## What does Quickstrap do?
Quickstrap remembers how to configure things so you don't have to.
  
```
cd path/to/my/project
quickstrap testsuites:phpunit
```
------

It guides the user through configuration via prompts.

```
What version of PHPUnit ?
    [1] 4.8
    [2] 5.7
> 2
Verbose output? [Y|n]: y
Stop on failure? [Y|n]: y
... other prompts
```

------
And generates the configuration files.

```
Generated ./phpunit.xml.dist
```

## There are many helpers
This will install Behat, and setup your feature directory and behat.yml configuration for you.
```
quickstrap testsuites:behat
```
------
This will install Code Sniffer.
```
quickstrap analyzers:codesniffer
```
------
This will generate a .travis-ci.yml configuration for you. If you've got phpunit or behat installed it will automatically
add them to the configuration. [TODO]
```
quickstrap ci:travis-ci
```
------
This will generate a .gitlab-ci.yml configuration for you. If you've got phpunit or behat installed it will automatically
add them to the configuration. [TODO]
```
quickstrap ci:gitlab-ci
```
------
This will setup a zend framework apigility skeleton project for you. [TODO]
```
quickstrap frameworks:apigility
```
------
This will setup a slimframework skeleton project for you. [TODO]
```
quickstrap frameworks:slim
```
------
This will setup a silex skeleton project for you. [TODO]
```
quickstrap frameworks:silex
```


## Making more helpers
Easily create your own configuration helpers

```
quickstrap create my-php-helper
Created barebone helper project at ./my-php-helper
```
And publish your package as a git repository somewhere (github/bitbucket/gitlab etc).

------
Install new helpers 
```
quickstrap install https://github.com/vendor/package-name.git
```

