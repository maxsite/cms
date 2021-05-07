<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (file_exists(__DIR__ . '/icons-im-config.php'))
	$iconsIM = require __DIR__ . '/icons-im-config.php';
else
	$iconsIM = [];

?>

h2 Иконки IM <sup class="badge t90 t-gray600"><?= count($iconsIM) ?> шт.</sup>

<?php
$count = 1;
$max = 33;
foreach ($iconsIM as $im) {

	if ($count == 1) echo '<div class="mar20-tb flex flex-wrap">';

	echo '<div class="' . $im . ' pad5-b w30 w50-tablet va-text-top:before"> <span class="t90 t-gray600">' . $im . '</span></div>';

	if ($count >= $max) echo '</div><hr>';

	$count++;

	if ($count > $max) $count = 1;
}

if ($count < $max) echo '</div>';

# end of file
