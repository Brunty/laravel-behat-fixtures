# Behat Fixtures in Laravel

## Installation

This package requires that you have the [Laracasts: Behat Laravel Extension](https://github.com/laracasts/Behat-Laravel-Extension) setup and working. Due to relying on that to load and setup Laravel for us.

`composer require brunty/laravel-behat-fixtures --dev`

## Configuration

Include the fixture in your suite, and pass it the path to your fixtures folder:
```yaml
default:
    suites:
        default:
            contexts:
                - Brunty\LaravelBehatFixtures\FixtureContext:
                    - '%paths.base%/features/bootstrap/fixtures/'
```

## Usage

### Resetting & Refreshing the Database

This package gives access to a tag that uses the `BeforeScenario` hook. It refreshes your database migrations before each scenario, use `@db-refresh` either before a feature, or a scenario:

```gherkin
# Will be called before each scenario in the feature
@db-refresh
Feature: My amazing feature
```

```gherkin
  # Will be called before only scenarios tagged with this
  @db-refresh
  Scenario: Things
    Given nothing is happening
    When nothing happens
    Then nothing should have happened
```

### Fixtures

Creating files within the fixture folder enables them to be loaded into features and scenarios. These are just plain ol' PHP files, so you can create things using Laravel's `factory()` method, or using Eloquent methods on your Models:

`%paths.base%/features/bootstrap/fixtures/users.base.php`

```php
<?php

// Create users via factories
$users = factory(App\User::class, 5)->create();

// Alternatively, you can just do whatever you'd like with eloquent
$user = new \App\User(['name' => 'Matt', 'email' => 'thing@thing.com', 'password' => bcrypt('apassword')]);
$user->save();
```

You can then load this file with the following:

```gherkin
@db-refresh
Feature:

  @fixture(users.base)
  Scenario: Things
    Given nothing is happening
    When nothing happens
    Then nothing should have happened

```

If you wanted to load lots of fixtures for a scenario, you can do so:

```gherkin
@db-refresh
Feature:

  @fixture(users.base) @fixture(users.active)
  Scenario: Things
    Given nothing is happening
    When nothing happens
    Then nothing should have happened

```
These would then be loaded in the order: `users.base.php` followed by `users.active.php`

## Contributing

This started as a project of boredom one Saturday evening, if you find yourself using this, and want more features, please feel free to suggest them, or submit a PR!

Although this project is small, openness and inclusivity are taken seriously. To that end the following code of conduct has been adopted.

[Contributor Code of Conduct](CONTRIBUTING.md)
