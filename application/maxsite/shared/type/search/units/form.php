<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

echo '
<p><form name="f_search" method="get" onsubmit="location.href=\'' . getinfo('siteurl') . 'search/\' + encodeURIComponent(this.s.value).replace(/%20/g, \'+\'); return false;"><input type="text" class="text" name="s" size="20" onfocus="if (this.value == \''. tf('что искать?'). '\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''. tf('что искать?'). '\';}" value="'. tf('что искать?'). '">&nbsp;<input type="submit" class="submit" name="Submit" value="  '. tf('Поиск'). '  "></form></p>';

		
# end file