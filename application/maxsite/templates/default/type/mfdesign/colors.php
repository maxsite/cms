<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

h2(h3) Палитра Material Design

<div class="b-flex t-center t90">
    <div class="pad15 w150px">primary</div>
    <div class="pad15 bg-primary50">50</div>
    <div class="pad15 bg-primary100">100</div>
    <div class="pad15 bg-primary200">200</div>
    <div class="pad15 bg-primary300">300</div>
    <div class="pad15 bg-primary400">400</div>
    <div class="pad15 bg-primary500">500</div>
    <div class="pad15 bg-primary600">600</div>
    <div class="pad15 bg-primary700 t-white">700</div>
    <div class="pad15 bg-primary800 t-white">800</div>
    <div class="pad15 bg-primary900 t-white">900</div>
    <div class="pad15 bg-primary950 t-white">950</div>
</div>

<div class="b-flex t-center t90">
    <div class="pad15 w150px">secondary</div>
    <div class="pad15 bg-secondary50">50</div>
    <div class="pad15 bg-secondary100">100</div>
    <div class="pad15 bg-secondary200">200</div>
    <div class="pad15 bg-secondary300">300</div>
    <div class="pad15 bg-secondary400">400</div>
    <div class="pad15 bg-secondary500">500</div>
    <div class="pad15 bg-secondary600">600</div>
    <div class="pad15 bg-secondary700 t-white">700</div>
    <div class="pad15 bg-secondary800 t-white">800</div>
    <div class="pad15 bg-secondary900 t-white">900</div>
    <div class="pad15 bg-secondary950 t-white">950</div>
</div>

<div class="b-flex t-center t90">
    <div class="pad15 w150px">tertiary</div>
    <div class="pad15 bg-tertiary50">50</div>
    <div class="pad15 bg-tertiary100">100</div>
    <div class="pad15 bg-tertiary200">200</div>
    <div class="pad15 bg-tertiary300">300</div>
    <div class="pad15 bg-tertiary400">400</div>
    <div class="pad15 bg-tertiary500">500</div>
    <div class="pad15 bg-tertiary600">600</div>
    <div class="pad15 bg-tertiary700 t-white">700</div>
    <div class="pad15 bg-tertiary800 t-white">800</div>
    <div class="pad15 bg-tertiary900 t-white">900</div>
    <div class="pad15 bg-tertiary950 t-white">950</div>
</div>


h2(h3) Цветовые палитры

_ Если цвет не отображается, то он не определён в шаблоне. Используется <a href="https://maxsite.org/berry">Berry CSS</a>.
<?php

	$states = [50, 100, 150, 200, 250, 300, 350, 400, 450, 500, 550, 600, 650, 700, 750, 800, 850, 900, 950];

	$colors = ['gray', 'red', 'brown', 'orange', 'yellow', 'lime', 'green', 'teal', 'cyan', 'blue', 'indigo', 'violet', 'purple', 'pink', ];

    foreach($states as $state)
	{
		echo '<div class="flex flex-ai-center t80 t-center t-black">';
        
        echo '<div class="flex-basis100px pad10-tb pad5-rl t-black">' . $state . '</div>';
        
		foreach($colors as $color)
		{
            echo '<div class="flex-basis100px pad10-tb pad5-rl bg-' . $color . $state . '">' 
                . $color 
                . '<div class="t-white">' . $color . '</div>'
                . '</div>';
		}
		
		echo '</div>';
	}

    echo '<br>';
    
	foreach($colors as $color)
	{
		echo '<div class="b-flex t-black t80 flex-ai-center">';
        echo '<div class="w60px t-' . $color . '">' . $color . '</div>';
        
		foreach($states as $state)
		{
            echo '<div class="flex-grow2 pad10-tb pad5-rl bg-' . $color . $state . '">' 
                . $state 
                . '<div class="t-white">' . $state . '</div>'
            . '</div>';
		}
		
		echo '</div>';
	}
?>

div(mar40-t b-flex)
    __(w23 mar20-r t-black bg-white pad10 bordered) bg-white / t-black
    __(w23 t-white bg-black pad10) bg-black / t-white
    
/div

div(flex mar20-t t90 flex-wrap)
    __(w20 t-color1 pad5) t-color1
    __(w20 t-color2 pad5) t-color2
    __(w20 t-color3 pad5) t-color3
    __(w20 t-color4 pad5) t-color4
    __(w20 t-color5 pad5) t-color5
    __(w20 t-color6 pad5) t-color6
    __(w20 t-color7 pad5) t-color7
    __(w20 t-primary pad5) t-primary
    __(w20 t-secondary pad5) t-secondary
    __(w20 t-secondary pad5) t-tertiary
/div

div(flex mar10-tb t90 flex-wrap)
    __(w20 t-color1 pad5 bg-black) t-color1
    __(w20 t-color2 pad5 bg-black) t-color2
    __(w20 t-color3 pad5 bg-black) t-color3
    __(w20 t-color4 pad5 bg-black) t-color4
    __(w20 t-color5 pad5 bg-black) t-color5
    __(w20 t-color6 pad5 bg-black) t-color6
    __(w20 t-color7 pad5 bg-black) t-color7
    __(w20 t-primary pad5 bg-black) t-primary
    __(w20 t-secondary pad5 bg-black) t-secondary
    __(w20 t-secondary pad5 bg-black) t-tertiary
/div

div(mar30-t flex flex-wrap t-black t90)
    __(w23 pad10 mar5-b bg-color1) bg-color1 <br><span class="t-white">bg-color1</span>
    __(w23 pad10 mar5-b bg-color2) bg-color2 <br><span class="t-white">bg-color2</span>
    __(w23 pad10 mar5-b bg-color3) bg-color3 <br><span class="t-white">bg-color3</span>
    __(w23 pad10 mar5-b bg-color4) bg-color4 <br><span class="t-white">bg-color4</span>
    __(w23 pad10 mar5-b bg-color5) bg-color5 <br><span class="t-white">bg-color5</span>
    __(w23 pad10 mar5-b bg-color6) bg-color6 <br><span class="t-white">bg-color6</span>
    __(w23 pad10 mar5-b bg-color7) bg-color7 <br><span class="t-white">bg-color7</span>
    __(w23 pad10 mar5-b bg-primary) bg-primary <br><span class="t-white">bg-primary</span>
    __(w23 pad10 mar5-b bg-secondary pad5) bg-secondary <br><span class="t-white">bg-secondary</span>
    __(w23 pad10 mar5-b bg-tertiary pad5) bg-tertiary <br><span class="t-white">bg-tertiary</span>
/div


h4 Бордюры

div(mar20-tb b-flex flex-wrap flex-ai-center)
    __(w23 bor1 bor-solid bor-black pad10 mar10-tb mar20-r) bor-black
    __(w23 mar10-tb bg-black pad5) <div class="bor1 bor-solid bor-white pad5 t-white">bor-white</div>
/div


div(mar20-tb flex flex-wrap flex-ai-center)
    __(w23 bor2 bor-solid bor-gray100 pad10 mar10-tb) bor-gray100
    __(w23 bor2 bor-solid bor-gray200 pad10 mar10-tb) bor-gray200
    __(w23 bor2 bor-solid bor-gray300 pad10 mar10-tb) bor-gray300
    __(w23 bor2 bor-solid bor-gray400 pad10 mar10-tb) bor-gray400
    __(w23 bor2 bor-solid bor-gray500 pad10 mar10-tb) bor-gray(500)
    __(w23 bor2 bor-solid bor-gray600 pad10 mar10-tb) bor-gray600
    __(w23 bor2 bor-solid bor-gray700 pad10 mar10-tb) bor-gray700
    __(w23 bor2 bor-solid bor-gray800 pad10 mar10-tb) bor-gray800
    __(w23 bor2 bor-solid bor-gray900 pad10 mar10-tb) bor-gray900
/div

div(mar20-tb flex flex-wrap flex-ai-center)
    __(w23 bor2 bor-solid bor-color1 pad10 mar10-tb) bor-color1
    __(w23 bor2 bor-solid bor-color2 pad10 mar10-tb) bor-color2
    __(w23 bor2 bor-solid bor-color3 pad10 mar10-tb) bor-color3
    __(w23 bor2 bor-solid bor-color4 pad10 mar10-tb) bor-color4
    __(w23 bor2 bor-solid bor-color5 pad10 mar10-tb) bor-color5
    __(w23 bor2 bor-solid bor-color6 pad10 mar10-tb) bor-color6
    __(w23 bor2 bor-solid bor-color7 pad10 mar10-tb) bor-color7
    __(w23 bor2 bor-solid bor-primary pad10 mar10-tb) bor-primary
    __(w23 bor2 bor-solid bor-secondary pad10 mar10-tb) bor-secondary
    __(w23 bor2 bor-solid bor-tertiary pad10 mar10-tb) bor-tertiary
/div

div(mar20-tb flex flex-wrap flex-ai-center)
    __(w23 bor2 bor-solid bor-red pad10 mar10-tb) bor-red
    __(w23 bor2 bor-solid bor-brown pad10 mar10-tb) bor-brown
    __(w23 bor2 bor-solid bor-orange pad10 mar10-tb) bor-orange
    __(w23 bor2 bor-solid bor-yellow pad10 mar10-tb) bor-yellow
    __(w23 bor2 bor-solid bor-lime pad10 mar10-tb) bor-lime
    __(w23 bor2 bor-solid bor-green pad10 mar10-tb) bor-green
    __(w23 bor2 bor-solid bor-teal pad10 mar10-tb) bor-teal
    __(w23 bor2 bor-solid bor-cyan pad10 mar10-tb) bor-cyan
    __(w23 bor2 bor-solid bor-blue pad10 mar10-tb) bor-blue
    __(w23 bor2 bor-solid bor-indigo pad10 mar10-tb) bor-indigo
    __(w23 bor2 bor-solid bor-violet pad10 mar10-tb) bor-violet
    __(w23 bor2 bor-solid bor-purple pad10 mar10-tb) bor-purple
    __(w23 bor2 bor-solid bor-pink pad10 mar10-tb) bor-pink

/div
