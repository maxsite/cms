<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$component = basename(dirname(__FILE__));

//mso_delete_option_mask($component . '-', getinfo('template'));

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option($component . '-rules_output', getinfo('template'), ''))) {
    $rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');

    if ($rules_result === false) $rules_result = 1;
    if ($rules_result !== 1) return;
}

$optionsINI = mso_get_defoptions_from_ini(__DIR__ . '/options.ini');

// это лого
$logo = mso_get_option($component . '-logo', '', '', $optionsINI);
$logo = str_replace('[siteurl]', getinfo('siteurl'), $logo);
$logo = str_replace('[templateurl]', getinfo('template_url'), $logo);
$logo = str_replace('[template_url]', getinfo('template_url'), $logo);

// $is_link_home = (!is_type('home') or !mso_current_paged() > 1);

?>
<div class="layout-center-wrap">
    <div class="layout-wrap bor2 bor-solid-b bor-gray200">
        <div class="flex flex-wrap-tablet flex-vcenter">
            <div class="flex-grow5 flex-order2-tablet">
                <?= $logo ?>
            </div>

            <div class="flex-shrink5 w100-tablet flex-order1-tablet pad10-tb">
                <?php
                // дополнительные данные для меню
                mso_set_val('menu-add-class', 'animation-zoom animation-fast mar15-rl');
                mso_set_val('menu-control', '<span class="button button1 rounded0 im-bars w100">Меню</span>');
                mso_set_val('menu-control-class', 't-center');

                if ($fn = mso_fe('components/_menu/_menu.php')) require $fn;
                ?>
            </div>

            <div class="flex-shrink5 flex-order3-tablet">
                <?php
                require 'modal.php';
                ?>
            </div>
        </div>
    </div>
</div>