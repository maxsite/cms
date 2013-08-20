Шрифт .f()

.f > .verdana; // Verdana, Arial, Helvetica, sans-serif
.f > .arial; // Arial, Helvetica, Verdana, sans-serif
.f > .tahoma; // Tahoma, Arial, Helvetica, Verdana, sans-serif
.f > .georgia; // Georgia, "Times New Roman", serif
.f > .times; // "Times New Roman", Georgia, serif
.f > .segoe; // "Segoe UI", Verdana, Arial, sans-serif
.f > .helvetica; // "Helvetica Neue", Helvetica, Arial, sans-serif

.f > .serif(Georgia); // любой произвольный serif, "Times New Roman", Times, serif
.f > .sans(Arial); // любой произвольный sans-serif, "Helvetica Neue", Helvetica, Arial, sans-serif
.f > .cursive(Comic Sans); // любой произвольный cursive
.f > .mono(Courier New); // любой произвольный mono, Monaco, Menlo, Consolas, "Courier New", monospace

.f > .f(@name, serif); // font-family: @name, serif;

.f > .size(9pt);
.f > .normal; // убрать жирность и курсив font-weight: normal;
.f > .bold(bold); // можно указать жирность: bold | bolder | lighter | normal
.f > .color(цвет);
.f > .i(italic); // italic
.f > .italic(italic); // italic
.f > .style(normal); // font-style: normal | italic | oblique
.f > .upper; // верхний регистр
.f > .lower; // нижний
.f > .underline(underline); // подчеркивание: underline | blink | line-through | overline | underline | none 
.f > .small_caps(); // капитель


.bold; // полужирный
.bold(normal); // нормальный bold | bolder | lighter | normal

.italic; // курсив
.italic(normal); // обычный normal | italic | oblique

.color(цвет); // цвет текста 


.block_center(@top: 0, @bottom: 0); // блок по центру + отступы сверху-снизу

.left() // float: left;
.right() // float: right;

.lheight(30px); // height = line-height

.align(left) // выравнивание текста text-align: left;

.border_radius(радиус); // скругление углов

.border(#C0C0C0, 1px, solid); 
.border_top(#C0C0C0, 1px, solid); 
.border_right(#C0C0C0, 1px, solid); 
.border_bottom(#C0C0C0, 1px, solid); 
.border_left(#C0C0C0, 1px, solid); 
.border(none);

.box_shadow(@x, @y, 3px, #888); // тень блока
.box_shadow(none); // отключить тень
.box_shadow(param, аттрибуты); // произвольные параметры

.box_shadow_inset(0, 0, 0, #000000); // внутреняя тень

.text_shadow(@x, @y, 3px, #888); // тень текста

.bg(@color: white); // background-color: @color;
.bgr(@color: red); 
.bgy(@color: yellow); 
.bgg(@color: green); 

.bg(@color, @file); // включая url-картинку background: @color url("../images/backgrounds/@{file}");
.bgu(@file, @attr: no-repeat) // background: url(@file) @attr;

// градиенты
.gradient > .horizontal(@start-color: #555, @start-percent: 0%, @end-color: #333, @end-percent: 100%);
.gradient > .vertical(@start-color: #555, @start-percent: 0%, @end-color: #333, @end-percent: 100%);
.gradient > .directional(@start-color: #555, @end-color: #333, @deg: 45deg);
.gradient > .horizontal_three_colors(@start-color: #00b3ee, @mid-color: #7a43b6, @color-stop: 50%, @end-color: #c3325f);
.gradient > .vertical_three_colors(@start-color: #00b3ee, @mid-color: #7a43b6, @color-stop: 50%, @end-color: #c3325f);
.gradient > .radial(@inner-color: #555, @outer-color: #333);
.gradient > .striped(@color: #555, @angle: 45deg);

.background_gradient(#555, #333); // линейный градиент вертикальный
.radial_gradient(#555, #333, center, center); // радиальный градиент

.opacity(100); // прозрачность
.op(100); // прозрачность

.op_color(@color: #abc, @opacity: .5) // аналог rgba(R,G,B, op) только с #RRGGBB
.bg_op(@color: #abc, @opacity: .5) // цвет фона и его прозрачность

.transition(background-color 0.3s 0s ease); // трансформация

.box_sizing(border-box); // боксовая модель
.box(); // аналог .box_sizing

.transform(); // трансформация CSS3

.placeholder(@color: #777); // цвет подсказки для input

.text_overflow(); // обрезка текста, который не помещается в блок
