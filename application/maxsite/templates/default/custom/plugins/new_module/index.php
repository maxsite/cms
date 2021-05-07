<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// Плагин new_module используется для создания нового модуля шаблона

// запуск плагина только для админки
if (mso_segment(1) == 'admin') new_module_autoload(); 

// если нужно будет удалить опции и разрешения, убрать комментарии
# mso_delete_option('plugin_new_module', 'plugins'); // удалим созданные опции
# mso_remove_allow('new_module_edit'); // удалим созданные разрешения

//  функция автоподключения плагина
function new_module_autoload()
{
    mso_create_allow('new_module_edit', t('Админ-доступ к new_module'));
    mso_hook_add('admin_init', 'new_module_admin_init'); # хук на админку
}

// функция выполняется при указаном хуке admin_init
function new_module_admin_init($args = [])
{	
    if (mso_check_allow('plugin_new_module')) {
        $this_plugin_url = 'plugin_new_module'; // url и hook
        mso_admin_menu_add('page', $this_plugin_url, t('Создать модуль'), 100);
        mso_admin_url_hook($this_plugin_url, 'new_module_admin_page');
    }

    return $args;
}

// функция вызываемая при хуке, указанном в mso_admin_url_hook
function new_module_admin_page($args = [])
{
    # выносим админские функции отдельно в файл
    if (!mso_check_allow('plugin_new_module')) {
        echo t('Доступ запрещен');

        return $args;
    }

    mso_hook_add_dinamic('mso_admin_header', ' return $args . t("Создать модуль юнитов", "plugins"); ');
    mso_hook_add_dinamic('admin_title', ' return t("Создать модуль юнитов", "plugins") . " - " . $args; ');

    require(__DIR__ . '/admin.php');
}

# end of file
