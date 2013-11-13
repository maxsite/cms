<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Описание: Форма поиска. Подкомпонет.
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
