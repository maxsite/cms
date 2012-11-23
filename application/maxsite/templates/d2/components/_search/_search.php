<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*

	Файл: search.php

	Описание: Форма поиска. Подкомпонет.
		
	PHP-связи:
		if ($fn = mso_fe('components/_search/_search.php')) require($fn);

*/

?>
<div class="search">
	<form class="fform" name="f_search" method="get" onsubmit="location.href='<?= getinfo('siteurl') ?>search/' + encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;">
		<p>
			<span><input type="search" name="s" placeholder="Поиск"></span>
			<span class="fbutton"><button type="submit">Поиск</button></span>
		</p>
	</form>
</div>
