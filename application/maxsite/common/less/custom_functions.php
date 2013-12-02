<?php
# 	Libs for LessPHP 0.4.0 to less.js 1.5.1
#	http://leafo.net/lessphp/docs/#custom_functions
#
#	Added:
#
#	multiply(color1, color2)
#	screen(color1, color2)
#	overlay(color1, color2)
#	softlight(color1, color2)
#	hardlight(color1, color2)
#	difference(color1, color2)
#	exclusion(color1, color2)
#	average(color1, color2)
#	negation(color1, color2)
#
#	MAX — http://maxsite.org/ | http://max-3000.com/ | http://wpjournal.com/

// $compiler = new lessc;

/*
# multiply(color1, color2)

lessjs 1.5.1:
multiply: function(color1, color2) {
        var r = color1.rgb[0] * color2.rgb[0] / 255;
        var g = color1.rgb[1] * color2.rgb[1] / 255;
        var b = color1.rgb[2] * color2.rgb[2] / 255;
        return this.rgb(r, g, b);
    }
*/
function less_multiply($arg)
{
	list($color1, $color2) = $arg[2];
	
	$r = $color1[1] * $color2[1] / 255;
	$g = $color1[2] * $color2[2] / 255;
	$b = $color1[3] * $color2[3] / 255;

	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("multiply", "less_multiply");


/*
# screen(color1, color2)

lessjs 1.5.1:
screen: function(color1, color2) {
        var r = 255 - (255 - color1.rgb[0]) * (255 - color2.rgb[0]) / 255;
        var g = 255 - (255 - color1.rgb[1]) * (255 - color2.rgb[1]) / 255;
        var b = 255 - (255 - color1.rgb[2]) * (255 - color2.rgb[2]) / 255;
        return this.rgb(r, g, b);
    }
*/
function less_screen($arg)
{
	list($color1, $color2) = $arg[2];
	
	$r = 255 - (255 - $color1[1]) * (255 - $color2[1]) / 255;
	$g = 255 - (255 - $color1[2]) * (255 - $color2[2]) / 255;
	$b = 255 - (255 - $color1[3]) * (255 - $color2[3]) / 255;
	
	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("screen", "less_screen");


/*
# overlay(color1, color2)

lessjs 1.5.1:
overlay: function(color1, color2) {
        var r = color1.rgb[0] < 128 ? 2 * color1.rgb[0] * color2.rgb[0] / 255 : 255 - 2 * (255 - color1.rgb[0]) * (255 - color2.rgb[0]) / 255;
        var g = color1.rgb[1] < 128 ? 2 * color1.rgb[1] * color2.rgb[1] / 255 : 255 - 2 * (255 - color1.rgb[1]) * (255 - color2.rgb[1]) / 255;
        var b = color1.rgb[2] < 128 ? 2 * color1.rgb[2] * color2.rgb[2] / 255 : 255 - 2 * (255 - color1.rgb[2]) * (255 - color2.rgb[2]) / 255;
        return this.rgb(r, g, b);
    }
*/
function less_overlay($arg)
{
	list($color1, $color2) = $arg[2];
	
	$r = $color1[1] < 128 ? 2 * $color1[1] * $color2[1] / 255 : 255 - 2 * (255 - $color1[1]) * (255 - $color2[1]) / 255;
	
	$g = $color1[2] < 128 ? 2 * $color1[2] * $color2[2] / 255 : 255 - 2 * (255 - $color1[2]) * (255 - $color2[2]) / 255;
	
	$b = $color1[3] < 128 ? 2 * $color1[3] * $color2[3] / 255 : 255 - 2 * (255 - $color1[3]) * (255 - $color2[3]) / 255;
	
	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("overlay", "less_overlay");

/*
# softlight(color1, color2)

lessjs 1.5.1:
softlight: function(color1, color2) {
	var t = color2.rgb[0] * color1.rgb[0] / 255;
	var r = t + color1.rgb[0] * (255 - (255 - color1.rgb[0]) * (255 - color2.rgb[0]) / 255 - t) / 255;
	
	t = color2.rgb[1] * color1.rgb[1] / 255;
	var g = t + color1.rgb[1] * (255 - (255 - color1.rgb[1]) * (255 - color2.rgb[1]) / 255 - t) / 255;
	
	t = color2.rgb[2] * color1.rgb[2] / 255;
	var b = t + color1.rgb[2] * (255 - (255 - color1.rgb[2]) * (255 - color2.rgb[2]) / 255 - t) / 255;
	
	return this.rgb(r, g, b);
}
*/
function less_softlight($arg)
{
	list($color1, $color2) = $arg[2];
	
	$t = $color2[1] * $color1[1] / 255;
	$r =  $t + $color1[1] * (255 - (255 - $color1[1]) * (255 - $color2[1]) / 255 - $t) / 255;
	
	$t = $color2[2] * $color1[2] / 255;
	$g = $t + $color1[2] * (255 - (255 - $color1[2]) * (255 - $color2[2]) / 255 - $t) / 255;
	
	$t = $color2[3] * $color1[3] / 255;
	$b = $t + $color1[3] * (255 - (255 - $color1[3]) * (255 - $color2[3]) / 255 - $t) / 255;
	
	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("softlight", "less_softlight");


/*
# hardlight(color1, color2)

lessjs 1.5.1:
hardlight: function(color1, color2) {
	var r = color2.rgb[0] < 128 ? 2 * color2.rgb[0] * color1.rgb[0] / 255 : 255 - 2 * (255 - color2.rgb[0]) * (255 - color1.rgb[0]) / 255;
	var g = color2.rgb[1] < 128 ? 2 * color2.rgb[1] * color1.rgb[1] / 255 : 255 - 2 * (255 - color2.rgb[1]) * (255 - color1.rgb[1]) / 255;
	var b = color2.rgb[2] < 128 ? 2 * color2.rgb[2] * color1.rgb[2] / 255 : 255 - 2 * (255 - color2.rgb[2]) * (255 - color1.rgb[2]) / 255;
	return this.rgb(r, g, b);
}
*/
function less_hardlight($arg)
{
	list($color1, $color2) = $arg[2];
	
	$r =  $color2[1] < 128 ? 2 * $color2[1] * $color1[1] / 255 : 255 - 2 * (255 - $color2[1]) * (255 - $color1[1]) / 255;
	
	$g = $color2[2] < 128 ? 2 * $color2[2] * $color1[2] / 255 : 255 - 2 * (255 - $color2[2]) * (255 - $color1[2]) / 255;
	
	$b = $color2[3] < 128 ? 2 * $color2[3] * $color1[3] / 255 : 255 - 2 * (255 - $color2[3]) * (255 - $color1[3]) / 255;
	
	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("hardlight", "less_hardlight");


/*
# difference(color1, color2)

lessjs 1.5.1:
difference: function(color1, color2) {
	var r = Math.abs(color1.rgb[0] - color2.rgb[0]);
	var g = Math.abs(color1.rgb[1] - color2.rgb[1]);
	var b = Math.abs(color1.rgb[2] - color2.rgb[2]);
	return this.rgb(r, g, b);
}
*/
function less_difference($arg)
{
	list($color1, $color2) = $arg[2];
	
	$r = abs($color1[1] - $color2[1]);
	$g = abs($color1[2] - $color2[2]);
	$b = abs($color1[3] - $color2[3]);
	
	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("difference", "less_difference");

/*
# exclusion(color1, color2)

lessjs 1.5.1:
exclusion: function(color1, color2) {
	var r = color1.rgb[0] + color2.rgb[0] * (255 - color1.rgb[0] - color1.rgb[0]) / 255;
	var g = color1.rgb[1] + color2.rgb[1] * (255 - color1.rgb[1] - color1.rgb[1]) / 255;
	var b = color1.rgb[2] + color2.rgb[2] * (255 - color1.rgb[2] - color1.rgb[2]) / 255;
	return this.rgb(r, g, b);
}
*/
function less_exclusion($arg)
{
	list($color1, $color2) = $arg[2];
	
	$r = $color1[1] + $color2[1] * (255 - $color1[1] - $color1[1]) / 255;
	$g = $color1[2] + $color2[2] * (255 - $color1[2] - $color1[3]) / 255;
	$b = $color1[3] + $color2[3] * (255 - $color1[3] - $color1[3]) / 255;
	
	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("exclusion", "less_exclusion");


/*
# average(color1, color2)

lessjs 1.5.1:
average: function(color1, color2) {
	var r = (color1.rgb[0] + color2.rgb[0]) / 2;
	var g = (color1.rgb[1] + color2.rgb[1]) / 2;
	var b = (color1.rgb[2] + color2.rgb[2]) / 2;
	return this.rgb(r, g, b);
}
*/
function less_average($arg)
{
	list($color1, $color2) = $arg[2];
	
	$r = ($color1[1] + $color2[1]) / 2;
	$g = ($color1[2] + $color2[2]) / 2;
	$b = ($color1[3] + $color2[3]) / 2;
	
	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("average", "less_average");

/*
# negation(color1, color2)

lessjs 1.5.1:
negation: function(color1, color2) {
	var r = 255 - Math.abs(255 - color2.rgb[0] - color1.rgb[0]);
	var g = 255 - Math.abs(255 - color2.rgb[1] - color1.rgb[1]);
	var b = 255 - Math.abs(255 - color2.rgb[2] - color1.rgb[2]);
	return this.rgb(r, g, b);
}
*/
function less_negation($arg)
{
	list($color1, $color2) = $arg[2];
	
	$r = 255 - abs(255 - $color2[1] - $color1[1]);
	$g = 255 - abs(255 - $color2[2] - $color1[2]);
	$b = 255 - abs(255 - $color2[3] - $color1[3]);
	
	return array('color', round($r), round($g), round($b));
}

$compiler->registerFunction("negation", "less_negation");


# end of file