<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

div(layout-center-wrap mar50-tb) || div(layout-wrap) 

<div x-data="{tab: 1}">
    <button :class="{'bg-primary600 t-white': tab == 1}" @click="tab = 1" class="button">Первая</button>
    <button :class="{'bg-primary600 t-white': tab == 2}" @click="tab = 2" class="button">Вторая</button>
    <button :class="{'bg-primary600 t-white': tab == 3}" @click="tab = 3" class="button">Третья</button>
 
    <div class="pad20 bordered">
        <div x-show="tab == 1" class="animation-fade">
            Вкладка 1
        </div>
        
        <div x-show="tab == 2" class="animation-fade" x-cloak>
            Вкладка 2
        </div>
        
        <div x-show="tab == 3" class="animation-fade" x-cloak>
            Вкладка 3
        </div>
    </div>
</div>

/div || /div
