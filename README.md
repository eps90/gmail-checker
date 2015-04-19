# GMail email presence Behat tests
Simple project checking whether there are any mails in your mailbox.

## Installation

You need to install dependencies, by running:

```sh
composer install
```

You will also need a Selenium server. You download it from Selenium page and run it in second terminal (or in background):

```sh
java -jar selenium-server-standalone-X.YY.Z.jar 
```

where *X.YY.Z* is your selenium version.

## Running the tests
To be able to log in, you must provide your Google account details. You can do this in two ways:

* Provide **username** and **password** in behat config and running Behat by `bin/behat`

```yaml
# behat.yml.dist
# ...
  suites:
    default:
      contexts:
        - Context\FeatureContext:
            username: john.smith
            password: qwerty12345
```

* or by passing them via environment variables to the script:

```sh
GMAIL_USERNAME="john.smith" GMAIL_PASSWD=qwe123 bin/behat
```

## Limitations
These tests doesn't support accounts with two-step authentication. 

## What should be done to improve the project?
* Add support for multiple email providers (Windows Live, etc). 
* Ask automatically for Google account's data during `composer install`
* Separate steps implementation by creating PageObjects
