<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$IMAGES = getinfo('template_url') . '/type/mfdesign/images/';

?>

h1 Дизайн шаблона

_ Если цвет не отображается, то он не определён в шаблоне. Используется <a href="https://maxsite.org/berry">Berry CSS</a> (демо для <a href="https://maxsite.github.io/berry/berry-colors.html" rel="nofollow" target="_blank">всех цветов</a>).

div(flex flex-wrap t-white)
	__(flex-grow1 pad10 bordered bg-white t-black) white
	__(flex-grow1 pad10 bg-black) black
	__(flex-grow1 pad10 bg-red) red
	__(flex-grow1 pad10 bg-blue) blue
	__(flex-grow1 pad10 bg-green) green
	__(flex-grow1 pad10 bg-yellow) yellow
/div

div(flex flex-wrap)
	__(flex-grow1 pad10 t-white) white
	__(flex-grow1 pad10 t-black) black
	__(flex-grow1 pad10 t-red) red
	__(flex-grow1 pad10 t-blue) blue
	__(flex-grow1 pad10 t-green) green
	__(flex-grow1 pad10 t-yellow) yellow
/div

div(flex flex-wrap t-white)	
	__(flex-grow1 pad10 bg-orange) orange
	__(flex-grow1 pad10 bg-olive) olive
	__(flex-grow1 pad10 bg-teal) teal
	__(flex-grow1 pad10 bg-violet) violet
	__(flex-grow1 pad10 bg-purple) purple
	__(flex-grow1 pad10 bg-pink) pink
	__(flex-grow1 pad10 bg-brown) brown
/div


div(flex flex-wrap t-white)	
	__(flex-grow1 pad10 t-orange) orange
	__(flex-grow1 pad10 t-olive) olive
	__(flex-grow1 pad10 t-teal) teal
	__(flex-grow1 pad10 t-violet) violet
	__(flex-grow1 pad10 t-purple) purple
	__(flex-grow1 pad10 t-pink) pink
	__(flex-grow1 pad10 t-brown) brown
/div

div(flex flex-wrap t-white)	
	__(flex-grow1 pad10 bg-color1) color1
	__(flex-grow1 pad10 bg-color2) color2
	__(flex-grow1 pad10 bg-color3) color3
	__(flex-grow1 pad10 bg-color4) color4
	__(flex-grow1 pad10 bg-color5) color5
/div

<?php

	$states = array(50, 100, 200, 300, 400, 500, 600, 700, 800, 900);
	$colors = array(
		'red',
		'pink',
		'purple', 
		'violet',
		'indigo', 
		'blue', 
		'cyan', 
		'teal', 
		'green', 
		'lime', 
		'yellow', 
		'orange', 
		'brown',
		'olive', 
		);

	foreach($colors as $color)
	{
		echo '<div class="flex flex-wrap t-white">';
		
		echo '<div class="w60px pad5 t-gray300"><span class="t-' . $color .'">' . $color . '</span></div>';
		
		foreach($states as $state)
		{
			echo '<div class="flex-grow1 pad5 bg-' . $color . $state . '">' . $state . '</div>';
		}
		
		echo '</div>';
	}


?>


div(flex flex-wrap bg-black mar30-t)
	__(flex-grow1 pad10 t-gray50) 50
	__(flex-grow1 pad10 t-gray100) 100
	__(flex-grow1 pad10 t-gray200) 200
	__(flex-grow1 pad10 t-gray300) 300
	__(flex-grow1 pad10 t-gray400) 400
	__(flex-grow1 pad10 t-gray500) 500
	__(flex-grow1 pad10 t-gray600) 600
	__(flex-grow1 pad10 t-gray700) 700
	__(flex-grow1 pad10 t-gray800) 800
	__(flex-grow1 pad10 t-gray900) 900
/div


div(flex flex-wrap bg-white)
	__(flex-grow1 pad10 t-gray50) 50
	__(flex-grow1 pad10 t-gray100) 100
	__(flex-grow1 pad10 t-gray200) 200
	__(flex-grow1 pad10 t-gray300) 300
	__(flex-grow1 pad10 t-gray400) 400
	__(flex-grow1 pad10 t-gray500) 500
	__(flex-grow1 pad10 t-gray600) 600
	__(flex-grow1 pad10 t-gray700) 700
	__(flex-grow1 pad10 t-gray800) 800
	__(flex-grow1 pad10 t-gray900) 900
/div

div(flex flex t-white mar30-t)
	__(w10 pad5 bg-gray50) 50
	__(w10 pad5 bg-gray100) 100
	__(w10 pad5 bg-gray200) 200
	__(w10 pad5 bg-gray300) 300
	__(w10 pad5 bg-gray400) 400
	__(w10 pad5 bg-gray500) 500
	__(w10 pad5 bg-gray600) 600
	__(w10 pad5 bg-gray700) 700
	__(w10 pad5 bg-gray800) 800
	__(w10 pad5 bg-gray900) 900
	__(w10 pad5 bg-black) black
/div

div(flex flex t-black)
	__(w10 pad5 bg-gray50) 50
	__(w10 pad5 bg-gray100) 100
	__(w10 pad5 bg-gray200) 200
	__(w10 pad5 bg-gray300) 300
	__(w10 pad5 bg-gray400) 400
	__(w10 pad5 bg-gray500) 500
	__(w10 pad5 bg-gray600) 600
	__(w10 pad5 bg-gray700) 700
	__(w10 pad5 bg-gray800) 800
	__(w10 pad5 bg-gray900) 900
	__(w10 pad5 bg-black) black
/div


div(flex flex t-white mar30-t t80 t-center)
	__(w5 pad10-tb bg-gray50) 50
	__(w5 pad10-tb bg-gray100) 100
	__(w5 pad10-tb bg-gray150) 150
	__(w5 pad10-tb bg-gray200) 200
	__(w5 pad10-tb bg-gray250) 250
	__(w5 pad10-tb bg-gray300) 300
	__(w5 pad10-tb bg-gray350) 350
	__(w5 pad10-tb bg-gray400) 400
	__(w5 pad10-tb bg-gray450) 450
	__(w5 pad10-tb bg-gray500) 500
	__(w5 pad10-tb bg-gray550) 550
	__(w5 pad10-tb bg-gray600) 600
	__(w5 pad10-tb bg-gray650) 650
	__(w5 pad10-tb bg-gray700) 700
	__(w5 pad10-tb bg-gray750) 750
	__(w5 pad10-tb bg-gray800) 800
	__(w5 pad10-tb bg-gray850) 850
	__(w5 pad10-tb bg-gray900) 900
	__(w5 pad10-tb bg-gray950) 950
	__(w5 pad10-tb bg-black) black
/div
div(flex flex t-black t80 t-center)
	__(w5 pad10-tb bg-gray50) 50
	__(w5 pad10-tb bg-gray100) 100
	__(w5 pad10-tb bg-gray150) 150
	__(w5 pad10-tb bg-gray200) 200
	__(w5 pad10-tb bg-gray250) 250
	__(w5 pad10-tb bg-gray300) 300
	__(w5 pad10-tb bg-gray350) 350
	__(w5 pad10-tb bg-gray400) 400
	__(w5 pad10-tb bg-gray450) 450
	__(w5 pad10-tb bg-gray500) 500
	__(w5 pad10-tb bg-gray550) 550
	__(w5 pad10-tb bg-gray600) 600
	__(w5 pad10-tb bg-gray650) 650
	__(w5 pad10-tb bg-gray700) 700
	__(w5 pad10-tb bg-gray750) 750
	__(w5 pad10-tb bg-gray800) 800
	__(w5 pad10-tb bg-gray850) 850
	__(w5 pad10-tb bg-gray900) 900
	__(w5 pad10-tb bg-gray950) 950
	__(w5 pad10-tb bg-black) black
/div

<div class="flex flex-wrap-tablet t-gray700 mar30-t t-center t90">
	div(w23 w48-tablet w100-phone mar20-b bg-gray100 pad10 rounded)
		__(t150 mar10-t) gray100
		__(mar10-tb) 19$/month
		__(mar10-tb) 10 database
		__(mar10-tb) 5 users
		__(mar10-tb) 1 sites
	/div
	
	div(w23 w48-tablet w100-phone mar20-b bg-gray200 pad10 rounded)
		__(t150 mar10-t) gray200
		__(mar10-tb) 19$/month
		__(mar10-tb) 10 database
		__(mar10-tb) 5 users
		__(mar10-tb) 1 sites
	/div
	
	div(w23 w48-tablet w100-phone mar20-b bg-gray300 pad10 rounded)
		__(t150 mar10-t) gray300
		__(mar10-tb) 19$/month
		__(mar10-tb) 10 database
		__(mar10-tb) 5 users
		__(mar10-tb) 1 sites
	/div
	
	div(w23 w48-tablet w100-phone mar20-b bg-gray400 pad10 rounded)
		__(t150 mar10-t) gray400
		__(mar10-tb) 19$/month
		__(mar10-tb) 10 database
		__(mar10-tb) 5 users
		__(mar10-tb) 1 sites
	/div	
</div>

__(mar50-t)

h1 Заголовок H1
h2 Заголовок H2
h3 Заголовок H3
h4 Заголовок H4
h5 Заголовок H5
h6 Заголовок H6

hr

__ <a href="#">Обычная ссылка</a>

__(mar20-tb) <button class="button" type="button">Обычная кнопка</button> <a class="button" href="#">Ссылка-кнопка</a>


h3(mar50-t) Форма
	
<form class="mso-form">
	<div class="mar20-t"><label>
		<b>Ваше имя *</b> 
		<br><input class="w100" type="text" name="myform[form][name]" required>
	</label></div> 
	
	<div class="mar20-t"><label>
		<b>Ваш email *</b> <span class="t-gray mar10-l">(на него мы отправим ответ)</span>
		<br><input class="w100" type="email" name="myform[form][email]" required>
	</label></div>
	
	<div class="mar20-t"><label>
			<b>Тема сообщения</b> 
			<br><input class="w100" type="text" name="myform[form][subj]">
	</label></div>
	
	<div class="mar20-t"><label>
		<b>Сообщение *</b> 
		<br><textarea class="w100" name="myform[form][text]" required></textarea>
	</label></div>

	<div class="mar20-t"><label class="t-gray600">
		<input type="checkbox" disabled checked> Нажимая кнопку «Отправить», вы соглашаетесь на обработку персональных данных
	</label></div>
	
	<button class="mar30-t" type="button">Отправить</button>
</form>


h3(mar50-t) Списки

div(flex)

	ul(w48 mar0-t)
	* Список 
	* Список 
	* Список 
	* Список 
	/ul
	
	ol(w48 mar0-t)
	* Список 
	* Список 
	* Список 
	* Список 
	/ol	
	
/div

<blockquote>Quisque vehicula; neque id condimentum varius, metus nisl vehicula orci, sit amet malesuada massa ipsum ut orci. Ut tincidunt congue eleifend. Proin et eros nisi, eget sollicitudin lacus. Maecenas sit amet nibh et felis volutpat vulputate ut sed nisi. Praesent vestibulum, magna in accumsan lacinia, odio dolor laoreet nibh, non cursus nisl dui non augue?

<br><cite>- Duis justo quam <strong>CITE</strong></cite>

</blockquote>

h2(mar30-tb) <a href="https://getbootstrap.com/docs" rel="nofollow" target="_blank">Bootstrap example</a>

<div class="alert alert-success" role="alert">
  <h4 class="alert-heading mar0-t">Well done!</h4>
  <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
  <hr>
  <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
</div>

hr

<div class="alert alert-warning alert-dismissible fade show" role="alert">
  <strong>Holy guacamole!</strong> You should check in on some of those fields below.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="box-shadow: none;">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

hr

<button type="button" class="btn btn-primary">
  Notifications <span class="badge badge-light">4</span>
</button>

<span class="badge badge-primary">Primary</span>
<span class="badge badge-secondary">Secondary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-danger">Danger</span>
<span class="badge badge-warning">Warning</span>
<span class="badge badge-info">Info</span>
<span class="badge badge-light">Light</span>
<span class="badge badge-dark">Dark</span>

hr

<a href="#" class="badge badge-primary">Primary</a>
<a href="#" class="badge badge-secondary">Secondary</a>
<a href="#" class="badge badge-success">Success</a>
<a href="#" class="badge badge-danger">Danger</a>
<a href="#" class="badge badge-warning">Warning</a>
<a href="#" class="badge badge-info">Info</a>
<a href="#" class="badge badge-light">Light</a>
<a href="#" class="badge badge-dark">Dark</a>

hr

<button type="button" class="btn btn-primary">Primary</button>
<button type="button" class="btn btn-secondary">Secondary</button>
<button type="button" class="btn btn-success">Success</button>
<button type="button" class="btn btn-danger">Danger</button>
<button type="button" class="btn btn-warning">Warning</button>
<button type="button" class="btn btn-info">Info</button>
<button type="button" class="btn btn-dark">Dark</button>

hr

<button type="button" class="btn btn-outline-primary">Primary</button>
<button type="button" class="btn btn-outline-secondary">Secondary</button>
<button type="button" class="btn btn-outline-success">Success</button>
<button type="button" class="btn btn-outline-danger">Danger</button>
<button type="button" class="btn btn-outline-warning">Warning</button>
<button type="button" class="btn btn-outline-info">Info</button>
<button type="button" class="btn btn-outline-dark">Dark</button>

hr

<div class="btn-toolbar mb-3" role="toolbar" aria-label="Toolbar with button groups">
  <div class="btn-group mr-2" role="group" aria-label="First group">
    <button type="button" class="btn btn-secondary">1</button>
    <button type="button" class="btn btn-secondary">2</button>
    <button type="button" class="btn btn-secondary">3</button>
    <button type="button" class="btn btn-secondary">4</button>
  </div>
  <div class="input-group">
    <div class="input-group-prepend">
      <div class="input-group-text" id="btnGroupAddon">@</div>
    </div>
    <input type="text" class="form-control" placeholder="Input group example" aria-label="Input group example" aria-describedby="btnGroupAddon">
  </div>
</div>

hr
<div class="btn-group" role="group" aria-label="Basic example">
  <button type="button" class="btn btn-secondary">Left</button>
  <button type="button" class="btn btn-secondary">Middle</button>
  <button type="button" class="btn btn-secondary">Right</button>
</div>

hr

<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
  <div class="btn-group mr-2" role="group" aria-label="First group">
    <button type="button" class="btn btn-secondary">1</button>
    <button type="button" class="btn btn-secondary">2</button>
    <button type="button" class="btn btn-secondary">3</button>
    <button type="button" class="btn btn-secondary">4</button>
  </div>
  <div class="btn-group mr-2" role="group" aria-label="Second group">
    <button type="button" class="btn btn-secondary">5</button>
    <button type="button" class="btn btn-secondary">6</button>
    <button type="button" class="btn btn-secondary">7</button>
  </div>
  <div class="btn-group" role="group" aria-label="Third group">
    <button type="button" class="btn btn-secondary">8</button>
  </div>
</div>

hr

<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
  <button type="button" class="btn btn-secondary">1</button>
  <button type="button" class="btn btn-secondary">2</button>

  <div class="btn-group" role="group">
    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Dropdown
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
      <a class="dropdown-item" href="#">Dropdown link</a>
      <a class="dropdown-item" href="#">Dropdown link</a>
    </div>
  </div>
</div>

hr

<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title mar0-t">Card title</h5>
    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
    <a href="#" class="card-link">Card link</a>
    <a href="#" class="card-link">Another link</a>
  </div>
</div>

hr

<div class="card">
  <div class="card-header">
    Featured
  </div>
  <div class="card-body">
    <h5 class="card-title mar0-t">Special title treatment</h5>
    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
    <a href="#" class="btn btn-primary">Go somewhere</a>
  </div>
</div>

hr

<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="<?= $IMAGES ?>nature1.jpg" class="d-block w-100" alt="">
    </div>
    <div class="carousel-item">
      <img src="<?= $IMAGES ?>nature2.jpg" class="d-block w-100" alt="">
    </div>
    <div class="carousel-item">
      <img src="<?= $IMAGES ?>nature3.jpg" class="d-block w-100" alt="">
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

hr

<div class="accordion" id="accordionExample">
  <div class="card">
    <div class="card-header" id="headingOne">
      <h4 class="mb-0 mar0 cursor-pointer">
        <span data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Collapsible Group Item #1
        </span>
      </h4>
    </div>

    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="card-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingTwo">
      <h4 class="mb-0 mar0 cursor-pointer">
        <span data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Collapsible Group Item #2
        </span>
      </h4>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="card-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingThree">
      <h4 class="mb-0 mar0 cursor-pointer">
        <span data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          Collapsible Group Item #3
        </span>
      </h4>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
      <div class="card-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
</div>

hr

<div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Dropdown button
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="#">Action</a>
    <a class="dropdown-item" href="#">Another action</a>
    <a class="dropdown-item" href="#">Something else here</a>
  </div>
</div>

hr

<div class="btn-group">
  <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Action
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" href="#">Action</a>
    <a class="dropdown-item" href="#">Another action</a>
    <a class="dropdown-item" href="#">Something else here</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="#">Separated link</a>
  </div>
</div>

hr

<form class="form-inline">
  <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Preference</label>
  <select class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref">
    <option selected>Choose...</option>
    <option value="1">One</option>
    <option value="2">Two</option>
    <option value="3">Three</option>
  </select>

  <div class="custom-control custom-checkbox my-1 mr-sm-2">
    <input type="checkbox" class="custom-control-input" id="customControlInline">
    <label class="custom-control-label" for="customControlInline">Remember my preference</label>
  </div>

  <button type="submit" class="btn btn-primary my-1">Submit</button>
</form>

hr

<div class="input-group mb-3">
  <div class="input-group-prepend">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="#">Action</a>
      <a class="dropdown-item" href="#">Another action</a>
      <a class="dropdown-item" href="#">Something else here</a>
      <div role="separator" class="dropdown-divider"></div>
      <a class="dropdown-item" href="#">Separated link</a>
    </div>
  </div>
  <input type="text" class="form-control" aria-label="Text input with dropdown button">
</div>

<div class="input-group">
  <input type="text" class="form-control" aria-label="Text input with dropdown button">
  <div class="input-group-append">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="#">Action</a>
      <a class="dropdown-item" href="#">Another action</a>
      <a class="dropdown-item" href="#">Something else here</a>
      <div role="separator" class="dropdown-divider"></div>
      <a class="dropdown-item" href="#">Separated link</a>
    </div>
  </div>
</div>

hr

<ul class="list-group">
  <li class="list-group-item active">Cras justo odio</li>
  <li class="list-group-item">Dapibus ac facilisis in</li>
  <li class="list-group-item">Morbi leo risus</li>
  <li class="list-group-item">Porta ac consectetur ac</li>
  <li class="list-group-item">Vestibulum at eros</li>
</ul>

hr

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mar0-t" id="exampleModalLabel">Modal title</h5>
        <div class="close cursor-pointer" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </div>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

hr

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Navbar</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto mar0-tb">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Dropdown
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>

hr

<div class="progress mar10-b">
  <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="progress mar10-b">
  <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="progress mar10-b">
  <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="progress mar10-b">
  <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="progress mar10-b">
  <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
</div>

hr

<div class="spinner-border text-primary" role="status">
  <span class="sr-only">Loading...</span>
</div>
<div class="spinner-border text-secondary" role="status">
  <span class="sr-only">Loading...</span>
</div>
<div class="spinner-border text-success" role="status">
  <span class="sr-only">Loading...</span>
</div>
<div class="spinner-border text-danger" role="status">
  <span class="sr-only">Loading...</span>
</div>
<div class="spinner-border text-warning" role="status">
  <span class="sr-only">Loading...</span>
</div>
<div class="spinner-border text-info" role="status">
  <span class="sr-only">Loading...</span>
</div>
<div class="spinner-border text-light" role="status">
  <span class="sr-only">Loading...</span>
</div>
<div class="spinner-border text-dark" role="status">
  <span class="sr-only">Loading...</span>
</div>

hr

<button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Tooltip on top">
  Tooltip on top
</button>
<button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="right" title="Tooltip on right">
  Tooltip on right
</button>
<button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="bottom" title="Tooltip on bottom">
  Tooltip on bottom
</button>
<button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="left" title="Tooltip on left">
  Tooltip on left
</button>

<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip();
})
</script>

<br><br>
