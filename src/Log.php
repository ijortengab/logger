<?php

namespace IjorTengab\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;

/**
 * Class sederhana yang mengimplementasi psr/log/LoggerInterface.
 */
class Log implements LoggerInterface
{
    /**
     * Load eight method from LoggerTrait.
     */
    use LoggerTrait;

    /**
     * Nama class ini dan akan menjadi indikator untuk membuat instance
     * jika Class ini dipanggil secara static untuk set/get log.
     */
    protected static $name = __CLASS__;

    /**
     * Property untuk menyimpan berbagai instance jika class ini di-extends.
     */
    protected static $instances = [];

    /**
     * Property tempat penyimpanan log.
     */
    protected $storage = [];

    /**
     * Implements of LoggerInterface::log().
     */
    public function log($level, $message, array $context = array())
    {
        if (!empty($context)) {
            $message = $this->interpolate($message, $context);
        }
        $this->storage([$level => $message]);
    }

    /**
     * Translate context from message.
     *
     * @link
     *   https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
     */
    public static function interpolate($message, array $context = array())
    {
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        return strtr($message, $replace);
    }

    /**
     * Magic method to support:
     *  - $this->get{LevelLog}
     *  - $this->get
     *  - $this->set{LevelLog}
     *  - $this->set
     */
    public function __call($name, $arguments)
    {
        try {
            if (strpos($name, 'get') === 0) {
                $name = str_replace('get', '', $name);
                if ($name == '') {
                    // Sesuaikan urutan level.
                    $ref = array_flip($this->checkLevel());
                    $storage = $this->storage;
                    array_walk($ref, function(&$value, $key, $storage) {
                        $value = isset($storage[$key]) ? $storage[$key] : false;
                    }, $storage);
                    $log = array_filter($ref);
                    return $log;
                }
                $name = strtolower($name);
                if (!self::checkLevel($name)) {
                    throw new InvalidArgumentException;
                }
                $storage = $this->storage;
                return array_key_exists($name, $storage) ? $storage[$name] : array();
            }
            if (strpos($name, 'set') === 0) {
                // Setidaknya perlu ada 1 arguments, yakni $message.
                if (empty($arguments)) {
                    throw new InvalidArgumentException;
                }
                $name = str_replace('set', '', $name);
                if ($name == '') {
                    return $this->storage = array_shift($arguments);
                }
                $name = strtolower($name);
                if (!self::checkLevel($name)) {
                    throw new InvalidArgumentException;
                }
                return call_user_func_array(array($this, $name), $arguments);
            }
        }
        catch (InvalidArgumentException $e) {
            die($e);
        }
    }

    /**
     * Magic method to support:
     *  - Log::get{LevelLog}
     *  - Log::get
     *  - Log::set{LevelLog}
     *  - Log::set
     */
    public static function __callStatic($name, $arguments)
    {
        try {
            if (strpos($name, 'get') === 0) {
                $name = str_replace('get', '', $name);
                if ($name == '') {
                    // Sesuaikan urutan level.
                    $ref = array_flip(self::checkLevel());
                    $storage = self::getInstance()->storage;
                    array_walk($ref, function(&$value, $key, $storage) {
                        $value = isset($storage[$key]) ? $storage[$key] : false;
                    }, $storage);
                    $log = array_filter($ref);
                    return $log;
                }
                $name = strtolower($name);
                if (!self::checkLevel($name)) {
                    throw new InvalidArgumentException;
                }
                $storage = self::getInstance()->storage;
                return array_key_exists($name, $storage) ? $storage[$name] : array();
            }
            if (strpos($name, 'set') === 0) {
                // Setidaknya perlu ada 1 arguments, yakni $message.
                if (empty($arguments)) {
                    throw new InvalidArgumentException;
                }
                $name = str_replace('set', '', $name);
                if ($name == '') {
                    return self::getInstance()->storage = array_shift($arguments);
                }
                $name = strtolower($name);
                if (!self::checkLevel($name)) {
                    throw new InvalidArgumentException;
                }
                return call_user_func_array(array(self::getInstance(), $name), $arguments);
            }
        }
        catch (InvalidArgumentException $e) {
            die($e);
        }
    }

    /**
     * Return log level approval from PSR.
     */
    public static function checkLevel($level = null)
    {
        if (null === $level) {
            return [
                LogLevel::EMERGENCY,
                LogLevel::ALERT,
                LogLevel::CRITICAL,
                LogLevel::ERROR,
                LogLevel::WARNING,
                LogLevel::NOTICE,
                LogLevel::INFO,
                LogLevel::DEBUG,
            ];
        }
        switch ($level) {
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
            case LogLevel::WARNING:
            case LogLevel::NOTICE:
            case LogLevel::INFO:
            case LogLevel::DEBUG:
                return true;
        }
    }

    /**
     * Membuat dan mendapatkan instance untuk keperluan call static.
     */
    public static function getInstance()
    {
        $my_name = static::$name;
        if (!isset(self::$instances[$my_name])) {
            self::$instances[$my_name] = new $my_name;
        }
        return self::$instances[$my_name];
    }

    /**
     * Menaruh instance lain ke dalam class.
     */
    public static function setInstance(Log $object)
    {
        $my_name = static::$name;
        self::$instances[$my_name] = $object;
    }

    /**
     * Method untuk set log ke dalam storage.
     */
    protected function storage(array $value)
    {
        $this->storage = array_merge_recursive($this->storage, $value);
    }
}
