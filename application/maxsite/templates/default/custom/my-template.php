<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Смена языка шаблона
// $MSO->language = 'en'; 

// Дополнительный сайдбар
// mso_register_sidebar('2', tf('Второй сайдбар')); 

// Инициализация опций шаблона 
if ($fn = mso_fe('custom/set-options.php')) require_once $fn;

// Ранняя инициализация компонентов
my_init_components();

// Шотркод лайтслайдер [lightslider 1]
if ($fn = mso_fe('components/lightslider/lightslider-shortcode.php')) require_once $fn;

// Корзина
if ($fn = mso_fe('custom/cart/cart.php')) require_once $fn;

// шорткод [module]pages/page1[/module] — вывод модулей юнитов
if ($fn = mso_fe('custom/shortcode/unit-module.php')) require_once $fn;

# end of file
