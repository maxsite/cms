<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Смена языка шаблона
// $MSO->language = 'en';

// Дополнительный сайдбар
if (mso_get_option('enable_sidebar2', getinfo('template'), 0))
    mso_register_sidebar('2', tf('Второй сайдбар'));

// загружаем alpine — она используется в шаблоне
// mso_add_file('assets/js/alpine.min.js', true);
// mso_add_preload('assets/js/alpine.min.js');

// либо из дистрибутива
// <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

mso_add_lazy('<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>');
mso_add_preload_html('<link rel="preload" href="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" as="script">');

// Инициализация опций шаблона. 
if ($fn = mso_fe('custom/set-options.php')) require_once $fn;

// Ранняя инициализация компонентов
my_init_components();

// Корзина
if (mso_get_option('cart_enabled', getinfo('template'), true))
    if ($fn = mso_fe('custom/cart/cart.php')) require_once $fn;

// Функции, расширяющие шаблон
if ($my = glob(getinfo('template_dir') . 'custom/my/[!_]*.php')) {
    foreach ($my as $fn) require_once $fn;
}

// Шорткоды
if ($my = glob(getinfo('template_dir') . 'custom/shortcode/[!_]*.php')) {
    foreach ($my as $fn) require_once $fn;
}

// Плагины
if ($my = glob(getinfo('template_dir') . 'custom/plugins/[!_]*/index.php')) {
    foreach ($my as $fn) require_once $fn;
}

# end of file
