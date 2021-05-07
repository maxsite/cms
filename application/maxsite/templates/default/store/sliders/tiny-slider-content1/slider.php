<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// tiny-slider - слайдер контента

// Домашняя страница: https://github.com/ganlanyuan/tiny-slider
// Примеры:  http://ganlanyuan.github.io/tiny-slider/demo/

// опции слайдера
$slider = [
    'id' => 'tns' . abs(crc32(__FILE__)), // id слайдера - обязательный параметр!
    // 'tns-load' => true, // принудительное подключение js-библиотеки — использовать когда слайдер в компонентах Module1/2

    // конфигурация слайдера в js-формате
    'config' => '
		items: 1,
		// responsive: {480: {items: 2}, 660: {items: 3}, 1024: {items: 4}},
		// gutter: 10,
		mouseDrag: true,
		nav: true,
		navPosition: "bottom",
    '
];

?>
<div class="pos-relative mar30-b" style="overflow: hidden;">
    <div id="<?= $slider['id'] ?>">
    <?php
        if (file_exists(__DIR__ . '/content.php')) {
            require __DIR__ . '/content.php';
        }
        ?>
    </div>

    <span id="<?= $slider['id'] ?>prev" class="pos-absolute hover-bg-primary300 cursor-pointer opacity50 flex flex-vcenter pad10-rl pos0-t pos0-l" style="height: calc(100% - 25px);"><i class="im-angle-left icon0"></i></span>

    <span id="<?= $slider['id'] ?>next" class="pos-absolute hover-bg-primary300 cursor-pointer opacity50 flex flex-vcenter pad10-rl pos0-t pos0-r" style="height: calc(100% - 25px);"><i class="im-angle-right icon0"></i></span>

</div>

<?php

// основная библиотека
if ($fn = mso_fe('distr/tiny-slider/tiny-slider.php')) {
    require_once $fn;
    tinyslider($slider);
}

/*

-- навигация next/prev по желанию — если не нужно, то достаточно просто удалить html-код

-- управлять nav можно через css-переменные основного контейнера
    <div class="pos-relative mar30-b" style="--nav-bg-active: #B8BEC3;"> ...

-- .tns-change и data-change="" меняет классы при появлении слайда (когда он становится первым)
 лучше использовать для одиночных слайдов items=1
    <h1 class="tns-change" data-change="animation-right animation-slow">Call to Action</h1>

-- Для слайда (если items=1) можно указать класс visibility-hidden для плавного отображения при загрузке
    <div class="visibility-hidden bg-primary100 pad20 h200px-min">

-- вариант навигации

    <span id="<?= $slider['id'] ?>prev" class="pos-absolute bg-primary200 hover-bg-primary300 icon-circle cursor-pointer opacity60 im-arrow-left" style="top: 40%; left: 5px;"></span>

    <span id="<?= $slider['id'] ?>next" class="pos-absolute bg-primary200 hover-bg-primary300 icon-circle cursor-pointer opacity60 im-arrow-right" style="top: 40%; right: 5px;"></span>

*/

# end of file
