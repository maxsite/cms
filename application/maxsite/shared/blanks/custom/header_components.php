<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// компоненты шапки

if ($fn = get_component_fn('default_header_component1', 'logo-links.php')) require($fn);
if ($fn = get_component_fn('default_header_component2', 'menu.php')) require($fn);
if ($fn = get_component_fn('default_header_component3', 'image-slider.php')) require($fn);

/*
// либо явное подключение, без возможности выбора в админ-панели
// нужно в my_options.ini скрыть опции БЛОКИ
// [Первый блок шапки]
// [Второй блок шапки]
// [Третий блок шапки]
// [Четвёртый блок шапки]
// [Пятый блок шапки]
// [Первый блок подвала]
// [Второй блок подвала]
// [Третий блок подвала]
// [Четвёртый блок подвала]
// [Пятый блок подвала]

require(getinfo('template_dir') . 'components/logo-links.php');
require(getinfo('template_dir') . 'components/menu.php');
require(getinfo('template_dir') . 'components/image-slider.php');

*/