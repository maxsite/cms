<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$component = basename(__DIR__);

// mso_delete_option($component . '-options', getinfo('template'));

$options_default = mso_get_component_option($component, '_default.txt', false);
$options = mso_get_option($component . '-options', getinfo('template'), $options_default);

if (!$options) return; // нет опций

$options = str_replace("\r", '', $options); // для windows
$options = str_replace('[siteurl]', getinfo('siteurl'), $options);

$site = mso_section_to_array($options, 'site', ['name' => '', 'description' => '', 'icon' => '', 'effect' => 1, 'name-class' => 't-robotoslab t190 t-gray800'], true);

if (!$site) return;

$blocks = mso_section_to_array($options, 'block', ['name' => '', 'icon' => '', 'href' => '', 'link' => ''], true);
$socials = mso_section_to_array($options, 'social', ['class' => '', 'href' => '', 'title' => '', 'attr' => ''], true);

if (!$blocks) $blocks = [];
if (!$socials) $socials = [];
$is_link_home = (!is_type('home') or !mso_current_paged() > 1);

?>
<div class="w100 bor3 bor-solid-b bor-gray300 z-index9999 ScrollToFixed" id="myHeader">
    <div class="layout-center-wrap pad25-tb bg-white">
        <div class="layout-wrap flex flex-wrap-tablet flex-vcenter">
        
            <div class="flex-grow2 w60-tablet w100-phone flex-order1">
                <div class="link-no-color t-center-phone">
                    <i class="<?= $site[0]['icon'] ?>"></i>

                    <?php if ($is_link_home) { ?>
                    <a class="hover-no-underline" href="<?= getinfo("siteurl") ?>"><span class="<?= $site[0]['name-class'] ?>"><?= $site[0]['name'] ?></span></a>
                <?php } else {
                    echo '<span class="' . $site[0]['name-class'] . '">' . $site[0]['name'] . '</span>';
                };
                ?>

                </div>

                <?= $site[0]['description'] ?>
            </div>

            <?php foreach ($blocks as $blok) : ?>
                <div class="flex-grow0 pad20-rl flex-order2 flex-order3-tablet">
                    <div class="flex flex-vcenter">
                        <i class="<?= $blok['icon'] ?> t180 pad10"></i>
                        <div>
                            <div class="t-gray400"><?= $blok['name'] ?></div>
                            <a class="t90 t-gray800 hover-t-gray750" href="<?= $blok['href'] ?>"><?= $blok['link'] ?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
            
            <div class="flex-grow0 w40-tablet w100-small pad10-small t-center-phone t-right pad20-l flex-order3 flex-order2-tablet">
                <?php foreach ($socials as $social) : ?>
                    <a class="<?= $social['class'] ?>" href="<?= $social['href'] ?>" title="<?= $social['title'] ?>" <?= $social['attr'] ?>></a>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>

<div class="layout-center-wrap bg-cyan800" id="myMenu1">
    <div class="layout-wrap flex flex-wrap-phone">
        <div class="flex-grow1 w0-tablet"></div>
        <div class="flex-grow0 w100-tablet">
            <?php
            if ($fn = mso_fe('components/_menu/_menu.php')) require $fn;
            ?>
        </div>
        <div class="flex-grow1 w0-tablet"></div>
    </div>
</div>

<?php if ($site[0]['effect'] == '1') require 'effect1.js.php'; 

# end of file
