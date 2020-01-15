<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="mso-page-only">
	<h1 class="mso-type-page_404"><?= tf('404 - несуществующая страница') ?></h1>
	<div class="mso-page-content">
		<p><?= tf('Извините по вашему запросу ничего не найдено!') ?></p>
		<?= mso_hook('page_404') ?>
	</div>
</div>
