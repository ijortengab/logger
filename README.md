Logger
==================

Logger adalah PHP Object dengan fungsi menyimpan catatan/log. Mengimplementasi
interface ```psr/log/LoggerInterface```. Seluruh log disimpan didalam property
bertipe array di dalam object. Penyimpanan dapat dilakukan dengan instance
object atau langsung kedalam Class (static).

## Requirement

 - PHP > 5.4
 - ```composer require psr/log```

## Repository

Tambahkan code berikut pada composer.json jika project anda membutuhkan library
ini. Perhatikan _trailing comma_ agar format json anda tidak rusak.

```json
{
    "require": {
        "ijortengab/logger": "master"
    },
    "minimum-stability": "dev",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ijortengab/logger"
        }
    ]
}
```

## Usage

Basic usage:

```php
// Found error and save that error in logger.
Logger::setError('Found error in line {line}.', ['line' => 17]);

// Found some interest information.
Logger::setInfo('User {user} has been logged in.', ['user' => 'admin']);

// Retrieve all error and send email to admin.
$error = Logger::getError();
empty($error) or $this->sendMail($error);
```

Class ini dapat dibuat instance. Tiap instance tersebut menjadi tempat
penyimpanan log tersendiri.

```php
$log = new Logger;
$log->error('log message');
print_r($log->getError());

$other_log = new Logger;
$other_log->notice('other log message');
print_r($other_log->getNotice());

```

Jika Class tidak dibuat instance, maka Class ini dapat dipanggil langsung
melalui method static, nantinya akan otomatis dibuat satu instance tersembunyi
di dalam class.

```php
// Set log.
Logger::setError('message');
Logger::setNotice('message');
// Get log.
print_r(Logger::getError());
print_r(Logger::getNotice());
// Get all log.
print_r(Logger::get());
// Get the hiding instance.
$instance = Logger::getInstance();
```

Class ini dapat di-extend. Extended Class ini dapat pula dipanggil langsung
melalui method static. Storage-nya akan sama dengan parent, karena memiliki
instance yang sama.

```php
use IjorTengab\Logger;

class ChildLogger extends Logger {}

ChildLogger::setNotice('child_log');
Logger::setNotice('log');

// Hasil output get log dibawah ini adalah sama.
print_r(ChildLogger::get());
print_r(Logger::get());

```

Extended class yang ingin memiliki storage terpisah dengan parent maka perlu
meng-override property $name. Pemisahan storage ini dimungkinkan karena ada
fitur "Late Static Bindings" dari PHP.

```php

use IjorTengab\Logger;

class MyLogger extends Logger
{
     protected static $name = __CLASS__;
}

MyLogger::setNotice('my_log');

Logger::setNotice('log');

// Hasil output get log dibawah ini berbeda karena lain storage.
print_r(MyLogger::get());
print_r(Logger::get());

```

Object instance yang tersembunyi di dalam Class dapat diganti dengan instance
lain.

```php
$mylog = new Logger;
Logger::setInstance($mylog);

$mylog->notice('i love you');
Logger::setNotice('you love me');

// Hasil output get log dibawah ini adalah sama karena storage-nya sama.
print_r($mylog->get());
print_r(Logger::get());
```

Method static available:

| No | Level     | Method for create    |  Method for retrieve    |
|----|-----------|----------------------|-------------------------|
| 1  | emergency | Logger::setEmergency | Logger::getEmergency    |
| 2  | alert     | Logger::setAlert     | Logger::getAlert        |
| 3  | critical  | Logger::setCritical  | Logger::getCritical     |
| 4  | error     | Logger::setError     | Logger::getError        |
| 5  | warning   | Logger::setWarning   | Logger::getWarning      |
| 6  | notice    | Logger::setNotice    | Logger::getNotice       |
| 7  | info      | Logger::setInfo      | Logger::getInfo         |
| 8  | debug     | Logger::setDebug     | Logger::getDebug        |

Method for override entire log: ```Logger::set```.

Method for retrieve all log: ```Logger::get```.

Method for retrieve instance: ```Logger::getInstance```.

## Referensi

http://php.net/manual/en/language.oop5.late-static-bindings.php
