# Logger

Package Logger terdiri dari:

  - Class ```IjorTengab\Logger\Log```

Requirement:
  - PHP > 5.4.0

## IjorTengab\Logger\Log

Class ```IjorTengab\Logger\Log``` adalah class sederhana yang
mengimplementasi ```psr/log/LoggerInterface```. Seluruh log disimpan
didalam property bertipe array di dalam object.

Basic usage:

```php

// Process here ...

// Found error and save that error in log.
Log::setError('Found error in line {line}.', ['line' => 17]);

// Another Process here ...

// Found some interest information.
Log::setInfo('User {user} has been logged in.', ['user' => 'admin']);

// Retrieve all error and send email to admin.
$error = Log::getError();
empty($error) or $this->sendMail($error);

```

Class ini dapat dibuat instance. Tiap instance tersebut menjadi tempat
penyimpanan log tersendiri.

```php

$log = new Log;
$log->error('log message');
print_r($log->getError());

$other_log = new Log;
$other_log->notice('other log message');
print_r($other_log->getNotice());

```

Jika Class tidak dibuat instance, maka Class ini dapat dipanggil langsung
melalui method static, nantinya akan otomatis dibuat satu instance tersembunyi
di dalam class.

```php

Log::setError('message');
Log::setNotice('message');
print_r(Log::getError());
print_r(Log::getNotice());
print_r(Log::get());

$instance = Log::getInstance();

```

Class ini dapat di-extend. Extended Class ini dapat pula dipanggil langsung
melalui method static. Storage-nya akan sama dengan parent, karena memiliki
instance yang sama.

```php

use IjorTengab\Logger\Log;

class ChildLog extends Log {}

ChildLog::setNotice('child_log');
Log::setNotice('log');

// Hasil output get log dibawah ini adalah sama.
print_r(ChildLog::get());
print_r(BaseLog::get());

```

Extended class yang ingin memiliki storage terpisah dengan parent maka perlu
meng-override property $name. Pemisahan storage ini dimungkinkan karena ada
fitur "Late Static Bindings" dari PHP.

```php

use IjorTengab\Logger\Log;

class MyLog extends Log
{
     protected static $name = __CLASS__;
}

MyLog::setNotice('my_log');

Log::setNotice('log');

// Hasil output get log dibawah ini berbeda karena lain storage.
print_r(MyLog::get());
print_r(Log::get());

```

Object instance yang tersembunyi di dalam Class dapat diganti dengan instance
lain.

```php
$mylog = new Log;
Log::setInstance($mylog);

$mylog->notice('i love you');
Log::setNotice('you love me');

// Hasil output get log dibawah ini adalah sama karena storage-nya sama.
print_r($mylog->get());
print_r(Log::get());
```

Method static available:

| No | Level     | Method for create |  Method for retrieve |
|----|-----------|-------------------|----------------------|
| 1  | emergency | Log::setEmergency | Log::getEmergency    |
| 2  | alert     | Log::setAlert     | Log::getAlert        |
| 3  | critical  | Log::setCritical  | Log::getCritical     |
| 4  | error     | Log::setError     | Log::getError        |
| 5  | warning   | Log::setWarning   | Log::getWarning      |
| 6  | notice    | Log::setNotice    | Log::getNotice       |
| 7  | info      | Log::setInfo      | Log::getInfo         |
| 8  | debug     | Log::setDebug     | Log::getDebug        |

Method for override entire log: ```Log::set```.

Method for retrieve all log: ```Log::get```.

Method for retrieve instance: ```Log::getInstance```.

Referensi:
http://php.net/manual/en/language.oop5.late-static-bindings.php
