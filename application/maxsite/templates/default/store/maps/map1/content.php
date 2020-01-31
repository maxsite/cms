<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

div(layout-center-wrap mar50-tb) || div(layout-wrap)

	h4 Карта проезда
	
	_ Quisque congue ultricies neque Suspendisse in nulla Cras pellentesque erat eu urna Suspendisse eu tortor In vulputate Etiam ornare fermentum felis Pellentesque habitant morbi 
	
	<div class="mar20-t">
		<?php
			// в html-коде карты для iframe нужно поставить размеры width="100%" height="100%"
			if (function_exists('ushka')) echo ushka('maps', '', '«Ушка maps»');
		?>
	</div>
		
/div || /div
