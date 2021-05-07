<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Домашняя страница: https://github.com/ganlanyuan/tiny-slider
// Примеры:  http://ganlanyuan.github.io/tiny-slider/demo/

// ! добавил js-my-add.txt в tiny-slider.min.js в конец файла

// файлы грузим сразу, чтобы ускорить отображение слайдеров
mso_add_file('assets/tiny-slider.min.js', false, __DIR__);
mso_add_file('assets/tiny-slider.css', false, __DIR__);

// хак со стилями нужен, чтобы слайдер не «моргал»
echo '<style>.tns-liveregion{display: none}</style>';

/**
 * функция для формирования и вывода js-кода слайдера
 */
if (!function_exists('tinyslider')) {
    function tinyslider(array $slider)
    {
        $config = $slider['config'] ?? '';

        // преобразования для js
        $config = trim($config);
        $config = trim($config, ',');
        if ($config) $config .= ',';
		
		// уникальный js-id
        $sliderJS = 'slider' . $slider['id']; // $slider['id'] — обязательный параметр

        // принудительная загрузка основной библиотеки
        // используется в слайдерах, которые загружаются как компоненты Module1/2
        $tnsLoad = $slider['tns-load'] ?? false;

        if ($tnsLoad) echo '<script src="' . getinfo('template_url') . 'distr/tiny-slider/assets/tiny-slider.min.js"></script>';

        // буферизация для сжатия js-кода
        ob_start();

?>
        <script>
            const <?= $sliderJS ?> = tns({
                container: '#<?= $slider['id'] ?>',
                controls: false,
                autoplayButtonOutput: false,
                <?= $config ?>

                onInit: function(info) {
                    document.addEventListener('DOMContentLoaded', function() {
                        info.slideItems[1].classList.remove('visibility-hidden');
                        tnsChange(info);
                    });
                },
            });

            document.querySelector('#<?= $slider['id'] ?>next').onclick = function() {
                <?= $sliderJS ?>.goTo('next');
            };

            document.querySelector('#<?= $slider['id'] ?>prev').onclick = function() {
                <?= $sliderJS ?>.goTo('prev');
            };

            <?= $sliderJS ?>.events.on('indexChanged', tnsCustomizedFunction);
        </script>
<?php
        $out = trim(ob_get_contents());
        ob_end_clean();

        // если в js-коде есть «//»-комментарий, то используем мягкое сжатие без удаления \n
        if (strpos($out, '//') !== false)
            echo str_replace(["\t", '  ', '   ', '    '], '', $out);
        else
            echo str_replace(["\r", "\n", "\t", '  ', '   ', '    '], '', $out);
    }
}

# end of file
