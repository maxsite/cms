<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$component = basename(dirname(__FILE__));

// mso_delete_option_mask($component . '-', getinfo('template'));

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option($component . '-rules_output', getinfo('template'), ''))) {
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');

	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

$options_default = mso_get_component_option($component, '_default.txt', false);
$options = mso_get_option($component . '-options', getinfo('template'), $options_default);

if (!$options) return; // нет опций

$options = str_replace("\r", '', $options); // для windows
$options = str_replace('[siteurl]', getinfo('siteurl'), $options);
$options = str_replace('[template_url]', getinfo('template_url'), $options);
$options = str_replace('[templateurl]', getinfo('template_url'), $options);
$options = str_replace('[name_site]', getinfo('name_site'), $options);
$options = str_replace('[description_site]', getinfo('description_site'), $options);

$site = mso_section_to_array($options, 'site', ['name' => '', 'description' => '', 'icon' => '', 'effect' => 1, 'class' => 'bor3 bor-solid-b bor-gray200', 'menu' => 'bg-primary700'], true);

if (!$site) return;

$class = $site[0]['class'];
$menu = $site[0]['menu'];

$blocks = mso_section_to_array($options, 'block', ['class' => '', 'html' => '', 'design' => 1, 'block1' => ''], true);

$socials = mso_section_to_array($options, 'icon', ['class' => '', 'href' => '', 'title' => '', 'attr' => ''], true);

if (!$blocks) $blocks = [];
if (!$socials) $socials = [];
$is_link_home = (!is_type('home') or !mso_current_paged() > 1);

// pr($blocks);
?>
<div class="w100 z-index9999 pos0-l -w-max-layout <?= $class ?>" id="myHeader">
    <div class="layout-center-wrap pad20-tb">
        <div class="layout-wrap flex flex-wrap-tablet flex-vcenter">
            <div class="flex-grow2 w100-phone mar10-tb flex">

                <div class="flex-grow5 b-hide show-phone"></div>

                <?php if ($site[0]['icon']) : ?>
                    <i class="flex-basis50px <?= $site[0]['icon'] ?>"></i>
                <?php endif ?>

                <div class="flex-grow1">
                    <?= $site[0]['name'] ?>
                    <?= $site[0]['description'] ?>
                </div>

                <div class="flex-grow5 b-hide show-phone"></div>
            </div>

            <?php
            foreach ($blocks as $block) {
                if ($block['design'] == 2)
                    require __DIR__ . '/design-block2.php';
                else
                    require __DIR__ . '/design-block1.php';
            }
            ?>

            <div class="flex-grow0 w100-phone t-center-phone t-right">
                <?php foreach ($socials as $social) : ?><a class="<?= $social['class'] ?>" href="<?= $social['href'] ?>" title="<?= $social['title'] ?>" <?= $social['attr'] ?>></a><?php endforeach ?>
            </div>
        </div>
    </div>
</div>

<?php if ($site[0]['effect'] == '2') : ?>
    <div id="myHeaderOffset"></div>
    <?php require 'effect2.js.php'; ?>
<?php endif; ?>

<div class="layout-center-wrap <?= $menu ?>" id="myMenu1">
    <div class="layout-wrap">
        <?php
		mso_set_val('menu-control-class', 't-left');
        if ($fn = mso_fe('components/_menu/_menu.php')) require $fn;
        ?>
    </div>
</div>

<?php if ($site[0]['effect'] == '1') require 'effect1.js.php'; 

# end of file
