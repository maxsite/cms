<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// файл для <option> в admin/editor_files

$select = '<option value="" selected>-</option>';

if (mso_fe('type/home/units.php'))
	$select .= '<option value="' . base64_encode('type/home/units.php') . '">type/home/units.php</option>';

$filesModules = glob(getinfo('template_dir') . 'modules/*/*/*.php');

$select .= '<optgroup class="bg-gray100" label="Modules"></optgroup>';

// цикл для modules
foreach ($filesModules as $file) {

	$file = str_replace(getinfo('template_dir') . 'modules/', '', $file);

	if (strpos($file, '_') === 0) continue;

	if (strpos($file, 'optgroup') === false)
		$select .= '<option value="' . base64_encode('modules/' . $file) . '">' . $file . '</option>';
	else
		$select .= $file;
}

echo $select;

# end of file
