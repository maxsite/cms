<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div x-data="{modalShow: false}">
    <button @click="modalShow = true" class="button button1 im-search icon0 cursor-pointer" title="<?= tf('Поиск') ?>"></button>

    <div x-show.transition="modalShow" class="pos-fixed pos0-l pos0-t h100 w100 z-index99 bg-op60" x-cloak>
        <div @click.away="modalShow = false" class="pos-absolute w100 bg-gray50 w600px-max bor3 bor-primary700 bor-solid" style="left: 50%; top: 30%; transform: translate(-50%, -50%);">
            <span @click="modalShow = false" class="pos-absolute pos10-r pos0-t t150 t-primary100 hover-t-primary300 cursor-pointer t-arial">&times;</span>

            <div class="bg-primary700 t-primary100 pad10"><?= tf('Поиск по сайту') ?></div>

            <div class="pad40-tb pad20-rl">
                <?php
                require 'search-form.php';
                ?>
            </div>
        </div>
    </div>
</div>