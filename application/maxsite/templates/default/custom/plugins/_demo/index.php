<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// DEMO плагин шаблона

// demo - замените на имя плагина

// запуск плагина только для админки
if (mso_segment(1) == 'admin') demo_autoload(); 

# mso_delete_option('plugin_demo', 'plugins'); // удалим созданные опции
# mso_remove_allow('demo_edit'); // удалим созданные разрешения

//  функция автоподключения плагина
function demo_autoload()
{
    mso_create_allow('demo_edit', t('Админ-доступ к настройкам demo'));
    mso_hook_add('admin_init', 'demo_admin_init'); # хук на админку
}

// функция выполняется при указаном хуке admin_init
function demo_admin_init($args = [])
{	
    if (mso_check_allow('plugin_demo')) {
        $this_plugin_url = 'plugin_demo'; // url и hook
        mso_admin_menu_add('plugins', $this_plugin_url, t('demo'));
        mso_admin_url_hook($this_plugin_url, 'demo_admin_page');
    }

    return $args;
}

// функция вызываемая при хуке, указанном в mso_admin_url_hook
function demo_admin_page($args = [])
{
    # выносим админские функции отдельно в файл
    if (!mso_check_allow('plugin_demo')) {
        echo t('Доступ запрещен');

        return $args;
    }

    mso_hook_add_dinamic('mso_admin_header', ' return $args . t("demo", "plugins"); ');
    mso_hook_add_dinamic('admin_title', ' return t("demo", "plugins") . " - " . $args; ');

    require(__DIR__ . '/admin.php');
}

# end of file
