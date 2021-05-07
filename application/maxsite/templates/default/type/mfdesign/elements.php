<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$IMAGES = getinfo('template_url') . '/type/mfdesign/images/';

?>

h1(mar40-t) Дизайн шаблона

<?php 
	require 'colors.php';
?>

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

<hr>

h1 Заголовок H1
_ Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi nisi optio id, quas maxime repudiandae fugiat modi necessitatibus consectetur dicta adipisci impedit quasi, earum soluta vitae enim sed laboriosam, ipsa voluptates odit porro totam nostrum fugit. Optio, saepe? Incidunt itaque rem maiores cumque nulla quis cum quod totam excepturi quos!


h2 Заголовок H2
_ Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi nisi optio id, quas maxime repudiandae fugiat modi necessitatibus consectetur dicta adipisci impedit quasi, earum soluta vitae enim sed laboriosam, ipsa voluptates odit porro totam nostrum fugit. Optio, saepe? Incidunt itaque rem maiores cumque nulla quis cum quod totam excepturi quos!

h3 Заголовок H3
_ Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi nisi optio id, quas maxime repudiandae fugiat modi necessitatibus consectetur dicta adipisci impedit quasi, earum soluta vitae enim sed laboriosam, ipsa voluptates odit porro totam nostrum fugit. Optio, saepe? Incidunt itaque rem maiores cumque nulla quis cum quod totam excepturi quos!

h4 Заголовок H4
_ Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi nisi optio id, quas maxime repudiandae fugiat modi necessitatibus consectetur dicta adipisci impedit quasi, earum soluta vitae enim sed laboriosam, ipsa voluptates odit porro totam nostrum fugit. Optio, saepe? Incidunt itaque rem maiores cumque nulla quis cum quod totam excepturi quos!

h5 Заголовок H5
_ Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi nisi optio id, quas maxime repudiandae fugiat modi necessitatibus consectetur dicta adipisci impedit quasi, earum soluta vitae enim sed laboriosam, ipsa voluptates odit porro totam nostrum fugit. Optio, saepe? Incidunt itaque rem maiores cumque nulla quis cum quod totam excepturi quos!

h6 Заголовок H6
_ Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi nisi optio id, quas maxime repudiandae fugiat modi necessitatibus consectetur dicta adipisci impedit quasi, earum soluta vitae enim sed laboriosam, ipsa voluptates odit porro totam nostrum fugit. Optio, saepe? Incidunt itaque rem maiores cumque nulla quis cum quod totam excepturi quos!


_(mar20-t) <a href="#">Обычная ссылка</a>

hr

<button class="button button1" type="button">.button1</button>
<button class="button button2" type="button">.button2</button>
<button class="button button3" type="button">.button3</button>

<button class="button button-outline1" type="button">.button-outline1</button>
<button class="button button-outline2" type="button">.button-outline2</button>

__(mar20-tb)

<button class="mso-button" type="button">Button (.mso-button)</button>

hr

<button class="button button1 im-check" type="im-check">button</button>
<button class="button button1 fas fa-check" type="button">fas fa-check</button>
<br><br>
<button class="button button1 im-phone" type="button">im-phone</button>
<button class="button button1 fas fa-phone" type="button">fas fa-phone</button>
<br><br>
<button class="button button1 im-download" type="button">im-download</button>
<button class="button button1 fas fa-download" type="button">fas fa-download</button>

h3(mar50-t) Форма FORM

<form>
	<div class="mar20-t">
        <label class="flex flex-vcenter flex-wrap">
            <div class="w20 w100-phone">Name *</div> 
            <input class="flex-grow3 form-input" type="text" name="myform[name]" placeholder="name...">
        </label>
    </div>
	
	<div class="mar20-t">
        <label class="flex flex-vcenter flex-wrap">
            <div class="w20 w100-phone">Email</div> 
            <input class="flex-grow3 form-input" type="email" name="myform[email]" placeholder="email...">
        </label>
    </div>    
    
    <div class="mar20-t">
        <label class="flex flex-wrap">
            <div class="w20 w100-phone">Message</div> 
            <textarea class="flex-grow3 h100px form-input" name="myform[text]" placeholder="message..."></textarea>
        </label>
    </div>
    
    <div class="mar20-t">
        <label class="flex flex-wrap">
            <div class="w20 w100-phone">Choose an option</div>
            <div class="flex-grow3">
                <select class="w100 form-input">
                    <option>Choose an option</option>
                    <option>Slack</option>
                    <option>Skype</option>
                    <option>Hipchat</option>
                </select>
            </div>
        </label>
    </div>
    
     <div class="mar20-t flex">
        <div class="w20 w0-phone"></div>
        
        <div class="flex-grow3 w100-phone">
            <label class="form-checkbox">
                <input type="checkbox">
                <span class="form-checkbox-icon bg-gray200"></span> gray
            </label>
            
            <label class="form-checkbox mar20-l">
                <input type="checkbox">
                <span class="form-checkbox-icon bg-blue200"></span> blue
            </label>
            
            <label class="form-checkbox mar20-l">
                <input type="checkbox">
                <span class="form-checkbox-icon"></span> default
            </label>
            
            <label class="mar20-l">
                <input type="checkbox"> Standart checkbox
            </label>
            
        </div>   
    </div>   
    
     <div class="mar20-t flex">
        <div class="w20 w0-phone"></div>
        
        <div class="flex-grow3 w100-phone">
            <label class="form-radio">
                <input type="radio" name="myform[radio]" value="1">
                <span class="form-radio-icon bg-green300"></span> green
            </label>
            
            <label class="form-radio mar20-l">
                <input type="radio" name="myform[radio]" value="2">
                <span class="form-radio-icon bg-yellow300"></span> yellow
            </label>
            
            <label class="form-radio mar20-l">
                <input type="radio" name="myform[radio]" value="3">
                <span class="form-radio-icon"></span> default 
            </label>
            
            <label class="mar20-l">
                <input type="radio" name="myform[radio]" value="4"> Standart radio
            </label>
            
        </div>
    </div>

    <div class="mar20-t flex">
        __(w20 w0-phone) 
        __(flex-grow3 w100-phone) <button class="button bg-blue500 hover-bg-blue600 t-blue100 hover-t-white" type="submit">Отправить</button>
    </div>
</form>


h3(mar50-t) Форма .mso-form
	
<form class="mso-form">
	<div class="mar20-t"><label>
		<b>Ваше имя *</b> 
		<br><input class="w100 " type="text" name="myform[form][name]" required>
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

	ul(w23 mar0-t)
	* Список 
	* Список 
	* Список 
	* Список 
	/ul
	
	ol(w23 mar0-t)
	* Список 
	* Список 
	* Список 
	* Список 
	/ol	
    
 	ul(square w23 mar0-t)
	* Список 
	* Список 
	* Список 
	* Список 
	/ul
    
    ul(circle w23 mar0-t)
	* Список 
	* Список 
	* Список 
	* Список 
	/ul
	
/div

<blockquote>Quisque vehicula; neque id condimentum varius, metus nisl vehicula orci, sit amet malesuada massa ipsum ut orci. Ut tincidunt congue eleifend. Proin et eros nisi, eget sollicitudin lacus. Maecenas sit amet nibh et felis volutpat vulputate ut sed nisi. Praesent vestibulum, magna in accumsan lacinia, odio dolor laoreet nibh, non cursus nisl dui non augue?

<br><cite>- Duis justo quam <strong>CITE</strong></cite>

</blockquote>


<?php 
	require 'icons-im.php';
?>

<br>
<br>
<br>