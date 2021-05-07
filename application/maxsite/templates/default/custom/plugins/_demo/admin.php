<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if (!mso_check_allow('demo_edit')) {
    echo t('Доступ запрещен');
    return;
}

// опции плагина
// ключ, тип, ключи массива
mso_admin_plugin_options(
    'plugin_demo',
    'plugins',
    [
        'option1' => [
            'type' => 'text',
            'name' => t('Название'),
            'description' => t('Описание'),
            'default' => ''
        ],
    ],
    t('Настройки плагина demo'), // титул
    t('Укажите необходимые опции.')   // инфо
);


# enf of file