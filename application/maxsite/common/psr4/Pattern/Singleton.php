<?php
/**
 * (c) Albireo Framework, https://maxsite.org/albireo, 2021
 * https://maxsite.org/page/php-singleton
 */

/**
 * Design pattern "Singleton" (Creational)
 * 
 * class MyClass
 * {
 *      use Pattern\Singleton;
 * 
 *      ...
 * }
 * 
 * $m = MyClass::getInstance();
 * $m->...
 */

namespace Pattern;

trait Singleton
{
	private static $instance;

	public static function getInstance()
	{
		if (empty(self::$instance)) self::$instance = new static();

		return self::$instance;
	}
    
    // на самом деле здесь должен быть private, но мы вынуждены использовать public для совместимости с CodeIgniter
	public function __construct()
	{
	}

	private function __clone()
	{
	}

	public function __wakeup()
	{
	}
}

# end of file