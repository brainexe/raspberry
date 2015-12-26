[![Build Status](https://travis-ci.org/brainexe/homie.png?branch=master)](https://travis-ci.org/brainexe/homie)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brainexe/homie/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brainexe/homie/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/brainexe/homie/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/brainexe/homie/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5669f01243cfea003100019c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5669f01243cfea003100019c)

# Overview
Homie is a software to automate you home using low-budget hardware, like a RaspberryPi

# Requirements
 - PHP5.6 / PHP7
 - nodejs 0.10 - 4.1
 - npm
 - redis-server
 - sass ([gem install sass](http://sass-lang.com/install))

# Installation
  - composer install
  - npm install
  - grunt exec:install
  - ./console user:create user pAsSworD admin # creates in initial user "user" with admin role and the given password
  - ./console server:run # runs the build-in PHP webserver on port 8080

# Tests
## Unit tests:
```
phpunit --testsuite unit 
```

## Integration test:
```
phpunit --testsuite integration
```

## End to end test:
```npm install -g protractor
webdriver-manager start
cd test/Frontend
protractor config.js
```
# Features
tbd

# Screenshots
tbd

