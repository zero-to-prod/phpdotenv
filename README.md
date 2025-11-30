# Zerotoprod\Phpdotenv

![](art/logo.png)

[![Repo](https://img.shields.io/badge/github-gray?logo=github)](https://github.com/zero-to-prod/phpdotenv)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/zero-to-prod/phpdotenv/test.yml?label=test)](https://github.com/zero-to-prod/phpdotenv/actions)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/zero-to-prod/phpdotenv/backwards_compatibility.yml?label=backwards_compatibility)](https://github.com/zero-to-prod/phpdotenv/actions)
[![Packagist Downloads](https://img.shields.io/packagist/dt/zero-to-prod/phpdotenv?color=blue)](https://packagist.org/packages/zero-to-prod/phpdotenv/stats)
[![php](https://img.shields.io/packagist/php-v/zero-to-prod/phpdotenv.svg?color=purple)](https://packagist.org/packages/zero-to-prod/phpdotenv/stats)
[![Packagist Version](https://img.shields.io/packagist/v/zero-to-prod/phpdotenv?color=f28d1a)](https://packagist.org/packages/zero-to-prod/phpdotenv)
[![License](https://img.shields.io/packagist/l/zero-to-prod/phpdotenv?color=pink)](https://github.com/zero-to-prod/phpdotenv/blob/main/LICENSE.md)
[![wakatime](https://wakatime.com/badge/github/zero-to-prod/phpdotenv.svg)](https://wakatime.com/badge/github/zero-to-prod/phpdotenv)
[![Hits-of-Code](https://hitsofcode.com/github/zero-to-prod/phpdotenv?branch=main)](https://hitsofcode.com/github/zero-to-prod/phpdotenv/view?branch=main)

## Contents

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Documentation Publishing](#documentation-publishing)
    - [Automatic Documentation Publishing](#automatic-documentation-publishing)
- [Usage](#usage)
- [Local Development](./LOCAL_DEVELOPMENT.md)
- [Contributing](#contributing)

## Introduction

Loads environment variables from `.env` to `getenv()`, `$_ENV`

## Requirements

- PHP 7.1 or higher.

## Installation

Install `Zerotoprod\Phpdotenv` via [Composer](https://getcomposer.org/):

```bash
composer require zero-to-prod/phpdotenv
```

This will add the package to your project’s dependencies and create an autoloader entry for it.

## Documentation Publishing

You can publish this README to your local documentation directory.

This can be useful for providing documentation for AI agents.

This can be done using the included script:

```bash
# Publish to default location (./docs/zero-to-prod/phpdotenv)
vendor/bin/zero-to-prod-phpdotenv

# Publish to custom directory
vendor/bin/zero-to-prod-phpdotenv /path/to/your/docs
```

#### Automatic Documentation Publishing

You can automatically publish documentation by adding the following to your `composer.json`:

```json
{
  "scripts": {
    "post-install-cmd": [
      "zero-to-prod-phpdotenv"
    ],
    "post-update-cmd": [
      "zero-to-prod-phpdotenv"
    ]
  }
}
```


## Usage

### Basic Usage

The `Phpdotenv` class provides two static methods for parsing environment variables:

#### 1. Parse from String

Parse environment variables directly from a string:

```php
use Zerotoprod\Phpdotenv\Phpdotenv;

$env_content = "APP_NAME=MyApp\nAPP_ENV=production\nAPP_DEBUG=false";
$variables = Phpdotenv::parseFromString($env_content);

// Result:
// [
//     'APP_NAME' => 'MyApp',
//     'APP_ENV' => 'production',
//     'APP_DEBUG' => 'false'
// ]
```

#### 2. Parse from Array

Parse environment variables from an array of lines:

```php
use Zerotoprod\Phpdotenv\Phpdotenv;

$lines = [
    'APP_NAME=MyApp',
    'APP_ENV=production',
    'APP_DEBUG=false'
];
$variables = Phpdotenv::parse($lines);

// Result:
// [
//     'APP_NAME' => 'MyApp',
//     'APP_ENV' => 'production',
//     'APP_DEBUG' => 'false'
// ]
```

### Loading from .env File

```php
use Zerotoprod\Phpdotenv\Phpdotenv;

// Read .env file and parse it
$env_file = file_get_contents(__DIR__ . '/.env');
$variables = Phpdotenv::parseFromString($env_file);

// Set variables in environment
foreach ($variables as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

// Now you can access them
echo getenv('APP_NAME');  // MyApp
echo $_ENV['APP_ENV'];    // production
```

### Supported .env File Format

The parser supports standard `.env` file syntax:

```env
# Comments (lines starting with #)
# Application Settings
APP_NAME=MyApplication
APP_ENV=production
APP_DEBUG=false

# Database Configuration
DATABASE_URL="mysql://user:pass@localhost:3306/mydb"
DATABASE_POOL_SIZE=10

# Empty values
OPTIONAL_SETTING=

# Values with equals signs
CONNECTION_STRING=server=localhost;database=mydb;uid=user;pwd=pass

# Quoted values
API_KEY='secret-key-123'
API_SECRET="another-secret"

# Mixed quotes in values
DESCRIPTION="App with 'single' quotes inside"
```

### Features

#### Comments
Lines starting with `#` are treated as comments and ignored:

```php
$result = Phpdotenv::parseFromString("# This is a comment\nAPP_NAME=MyApp");
// ['APP_NAME' => 'MyApp']
```

#### Quote Handling
The parser automatically strips surrounding quotes from values:

```php
$result = Phpdotenv::parseFromString('API_KEY="secret-123"');
// ['API_KEY' => 'secret-123']

$result = Phpdotenv::parseFromString("API_KEY='secret-123'");
// ['API_KEY' => 'secret-123']
```

#### Whitespace Trimming
Leading and trailing whitespace is automatically trimmed from keys and values:

```php
$result = Phpdotenv::parseFromString('  APP_NAME  =  MyApp  ');
// ['APP_NAME' => 'MyApp']
```

#### Empty Values
Keys can have empty values:

```php
$result = Phpdotenv::parseFromString('OPTIONAL_KEY=');
// ['OPTIONAL_KEY' => '']
```

#### Values with Equals Signs
Values can contain equals signs:

```php
$result = Phpdotenv::parseFromString('CONNECTION=server=localhost;db=mydb');
// ['CONNECTION' => 'server=localhost;db=mydb']
```

#### Newline Format Support
The `parseFromString` method handles all newline formats:

```php
// Unix newlines (\n)
$result = Phpdotenv::parseFromString("KEY1=value1\nKEY2=value2");

// Windows newlines (\r\n)
$result = Phpdotenv::parseFromString("KEY1=value1\r\nKEY2=value2");

// Mac newlines (\r)
$result = Phpdotenv::parseFromString("KEY1=value1\rKEY2=value2");

// Mixed newlines
$result = Phpdotenv::parseFromString("KEY1=value1\r\nKEY2=value2\nKEY3=value3");
```

#### Empty Lines
Empty lines are automatically filtered out:

```php
$result = Phpdotenv::parseFromString("KEY1=value1\n\n\nKEY2=value2");
// ['KEY1' => 'value1', 'KEY2' => 'value2']
```

#### Duplicate Keys
If a key appears multiple times, the last value wins:

```php
$result = Phpdotenv::parseFromString("API_KEY=first\nAPI_KEY=second");
// ['API_KEY' => 'second']
```

#### Invalid Lines
Lines without an equals sign are ignored:

```php
$result = Phpdotenv::parseFromString("VALID=value\nINVALID_LINE\nANOTHER=value");
// ['VALID' => 'value', 'ANOTHER' => 'value']
```

### API Reference

#### `Phpdotenv::parseFromString(string $subject): array`

Parses a string containing environment variables into an associative array.

**Parameters:**
- `$subject` (string): The string containing environment variables (with newlines)

**Returns:**
- `array`: Associative array of key-value pairs

**Example:**
```php
$result = Phpdotenv::parseFromString("APP_NAME=MyApp\nAPP_ENV=production");
// ['APP_NAME' => 'MyApp', 'APP_ENV' => 'production']
```

#### `Phpdotenv::parse(array $lines): array`

Parses an array of lines into an associative array of key-value pairs.

**Parameters:**
- `$lines` (array): Array of strings, each representing a line from an env file

**Returns:**
- `array`: Associative array of key-value pairs

**Example:**
```php
$result = Phpdotenv::parse(['APP_NAME=MyApp', 'APP_ENV=production']);
// ['APP_NAME' => 'MyApp', 'APP_ENV' => 'production']
```

### Pure Functions

Both methods are pure functions with no side effects. They:
- Do not modify the global environment (`$_ENV`, `$_SERVER`, `putenv()`)
- Do not read from the filesystem
- Simply parse and return an associative array

This design gives you full control over what happens with the parsed variables.

## Contributing

Contributions, issues, and feature requests are welcome!
Feel free to check the [issues](https://github.com/zero-to-prod/phpdotenv/issues) page if you want to contribute.

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Commit changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Create a new Pull Request.
