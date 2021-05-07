<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<form x-data="{text: ''}" @submit.prevent="if (text > '') window.location.href = '<?= getinfo('siteurl') ?>search/' + encodeURIComponent(text.replace(/%20/g, '+'));" method="get">
    <div class="pos-relative">
        <input x-model="text" class="w100 t90 form-input pad40-r" type="search" name="s" placeholder="<?= tf('поиск по сайту...') ?>">
        <button class="pos-absolute pos0-t pos0-r im-search button button1 icon0 pad0-tb pad10-rl h100 lh100" style="border-radius: 0 5px 5px 0;" type="submit"></button>
    </div>
</form>
