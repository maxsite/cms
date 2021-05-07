<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if (!mso_check_allow('new_module_edit')) {
    echo t('Доступ запрещен');
    return;
}

require 'functions.php';
require 'post.php';

?>

<h1><?= t('Создать модуль юнитов') ?></h1>
<p class="mso-info"><?= t('Для создания нового модуля, выберите подходящий в каталоге <code>store</code>, который будет использоваться в качестве каркаса. Введите имя, под которым будет доступен новый модуль. Если не указывать имя нового модуля, то он будет скопирован как есть, при условии, что его ещё нет в каталоге <code>modules</code> (отмечены символом «+»).') ?></p>

<p class="mso-info"><?= t('Имя модуля задаётся в формате <code>группа/каталог</code> и содержит только английские символы, цифры и символы <code>/ - _</code>') ?></p>

<?php

$files = glob(getinfo('template_dir') . 'store/*/*/index.php');
$arModules = getAllModules($files);
$sel = '';

if ($arModules) {
    foreach ($arModules as $nameBlock => $arBlocks) {
        $sel .= '<optgroup label="' . $nameBlock . '">';

        foreach ($arBlocks as $elem) {
            $elem = htmlspecialchars($elem);
            $check = file_exists(getinfo('template_dir') . 'modules/' . $elem . '/index.php') ? ' +' : '';
            $sel .= '<option value="' . $elem . '">' . $elem . $check . '</option>';
        }

        $sel .=  '</optgroup>';
    }
}
?>

<form class="mar30-t" method="post">
    <?= mso_form_session('f_session_id') ?>
    <div><?= t('Выберите модуль (в <code>store</code>) в качестве каркаса') ?></div>
    <select class="mar5-t" name="f_module"><?= $sel ?></select>
    <div class="mar20-t"><?= t('Новый модуль (в <code>modules</code>)') ?></div>
    <input class="mar5-t" type="text" name="f_newmodule" -pattern="[0-9A-Za-z_\-/]{3,}">
    <div><button type="submit" class="mar20-t i-plus button"><?= t('Создать') ?></button></div>
</form>
