<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *  * MaxSite CMS
 * (c) https://max-3000.com/
 */

echo '<div class="mar30-t mar20-b"><a class="im-rss" href="' . getinfo('siteurl') . 'comments/feed">Подписаться по RSS</a> <a class="b-inline b-right im-user" href="' . getinfo('siteurl') . 'users">' . tf('Список комментаторов') . '</a></div>';

echo '<h3 class="mar20-b">' . tf('Последние комментарии') . '</h3>';

# end of file
