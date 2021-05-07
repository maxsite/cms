<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$p->format('edit', '<i class="im-edit t-gray600 hover-t-gray950" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');
$p->format('title', '<h1 class="t-gray700 mar10-t t220">', '</h1>', false);
$p->format('cat', ', ', '<span class="im-bookmark" title="' . tf('Рубрика записи') . '">', '</span>');

$p->html('<header class="mar30-t mar20-b">');
    $p->line('[edit][title]');
    $p->div_start('t-gray600 t90');
        $p->line('[cat]');
    $p->div_end('');
$p->html('</header>');

# end of file
