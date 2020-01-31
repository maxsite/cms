<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$component = basename(__DIR__);

// mso_delete_option($component . '-options', getinfo('template'));

$options_default = mso_get_component_option($component, '_default.txt', false);
$options = mso_get_option($component . '-options', getinfo('template'), $options_default);

if (!$options) return; // нет опций

$options = str_replace("\r", '', $options); // для windows
$options = str_replace('[siteurl]', getinfo('siteurl'), $options);

$site = mso_section_to_array($options, 'site', ['name' => '', 'description' => '', 'icon' => '', 'effect' => 1, 'name-class' => 't-robotoslab t180 t-gray800'], true);

if (!$site) return;

$blocks = mso_section_to_array($options, 'block', ['class' => '', 'html' => ''], true);
$socials = mso_section_to_array($options, 'social', ['class' => '', 'href' => '', 'title' => '', 'attr' => ''], true);

if (!$blocks) $blocks = [];
if (!$socials) $socials = [];
$is_link_home = (!is_type('home') or !mso_current_paged() > 1);

// pr($blocks);
?>
<div class="w100 z-index9999 -w-max-layout bor3 bor-solid-b bor-gray200" id="myHeader">
    <div class="layout-center-wrap pad20-tb bg-white">
        <div class="layout-wrap flex flex-wrap-tablet flex-vcenter">
            <div class="flex-grow2 w100-phone mar10-tb t-center-phone">
                <?php if ($site[0]['icon']) : ?>
                    <i class="<?= $site[0]['icon'] ?>"></i>
                <?php endif ?>

                <?php if ($is_link_home) { ?>
                    <a class="hover-no-underline" href="<?= getinfo("siteurl") ?>"><span class="<?= $site[0]['name-class'] ?>"><?= $site[0]['name'] ?></span></a>
                <?php } else {
                    echo '<span class="' . $site[0]['name-class'] . '">' . $site[0]['name'] . '</span>';
                };
                ?>

                <?= $site[0]['description'] ?>
            </div>

            <?php foreach ($blocks as $block) : ?>
                <div class="flex-grow1 mar10-t-phone mar10">
                    <div class="<?= $block['class'] ?>"><?= $block['html'] ?></div>
                </div>
            <?php endforeach ?>

            <div class="flex-grow0 w100-phone t-center-phone t-right">
                <?php foreach ($socials as $social) : ?>
                    <a class="<?= $social['class'] ?>" href="<?= $social['href'] ?>" title="<?= $social['title'] ?>" <?= $social['attr'] ?>></a>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>

<div class="layout-center-wrap bg-gray50" id="myMenu1">
    <div class="layout-wrap flex flex-wrap-phone">
        <div class="flex-grow1 w100-phone wow">
            <?php
            if ($fn = mso_fe('components/_menu/_menu.php')) require $fn;
            ?>
        </div>
    </div>
</div>

<?php if ($site[0]['effect'] == '1') require 'effect1.js.php'; 

# end of file
