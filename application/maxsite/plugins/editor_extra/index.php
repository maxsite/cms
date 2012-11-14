<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function editor_extra_autoload($args = array())
{
	mso_hook_add( 'editor_controls_extra_css', 'editor_controls_extra_css');
	mso_hook_add( 'editor_controls_extra', 'editor_controls_extra');
	return $args;
}

# подключаем css-стили своих кнопок
function editor_controls_extra_css($args = array())
{
	echo '
	<style>
		div.wysiwyg ul.panel li a.extra {background-image: none; width: 30px; color: black; text-align: center;}
		div.wysiwyg ul.panel li a.extra:hover {text-decoration: none;}

		div.wysiwyg ul.panel li a.e_cut:before {content: "cut";}
		
		div.wysiwyg ul.panel li a.e_xcut:before {content: "xcut";}
		
		div.wysiwyg ul.panel li a.e_bb_bold {width: 20px;}
		div.wysiwyg ul.panel li a.e_bb_bold:before {content: "[b]";}
		
		div.wysiwyg ul.panel li a.e_bb_italic {width: 20px;}
		div.wysiwyg ul.panel li a.e_bb_italic:before {content: "[i]";}
		
		div.wysiwyg ul.panel li a.e_url:before {content: "[url]";}
		
		div.wysiwyg ul.panel li a.e_pre:before {content: "[pre]";}
		
		div.wysiwyg ul.panel li a.e_html {content: "[html]"; width: 40px;}
		div.wysiwyg ul.panel li a.e_html:before {content: "[html]";}
	</style>' . NR;
	
	return $args;
}

# сама js-функция кнопок
function editor_controls_extra($args = array())
{
	// запятая в начале обязательно!
	echo <<<EOF
	, 

	e_cut : 
	{
		visible : true,
		title : 'Разделить на анонс [cut]',
		className : 'extra e_cut',
		exec    : function()
		{
			this.editorDoc.execCommand('inserthtml', false, '<br>[cut]<br>');
		}
	},
	
	e_xcut : 
	{
		visible : true,
		title : 'Разделить на анонс [xcut]',
		className : 'extra e_xcut',
		exec    : function()
		{
			this.editorDoc.execCommand('inserthtml', false, '<br>[xcut]<br>');
		}
	},
	
	separator2 : { separator : true },
	
	e_bb_bold : 
	{
		visible : true,
		title : 'Полужирный [b]',
		className : 'extra e_bb_bold',
		exec    : function()
		{
			var selection = $(this.editor).documentSelection();
			this.editorDoc.execCommand('inserthtml', false, '[b]' + selection + '[/b]');
		}
	},
	
	e_bb_italic : 
	{
		visible : true,
		title : 'Курсив [i]',
		className : 'extra e_bb_italic',
		exec    : function()
		{
			var selection = $(this.editor).documentSelection();
			this.editorDoc.execCommand('inserthtml', false, '[i]' + selection + '[/i]');
		}
	},
	
	e_url : 
	{
		visible : true,
		title : 'Ссылка [url]',
		className : 'extra e_url',
		exec    : function()
		{
			var selection = $(this.editor).documentSelection();
			
			if ( selection.search('http:') > -1 ) // есть http
			{
				var nameURL = prompt('Name URL', ''); // просим ввести название ссылки
				this.editorDoc.execCommand('inserthtml', false, '[url=' + selection + ']' + nameURL + '[/url]');
			}
			else
			{
				var URL = prompt('URL', ''); // происм ввести адрес ссылки
				this.editorDoc.execCommand('inserthtml', false, '[url=' + URL + ']' + selection + '[/url]');
			}
		}
	},
	
	e_pre : 
	{
		visible : true,
		title : 'Код [pre]',
		className : 'extra e_pre',
		exec    : function()
		{
			var selection = $(this.editor).documentSelection();
			this.editorDoc.execCommand('inserthtml', false, '[pre]<br>' + selection + '<br>[/pre]');
		}
	},
	
	e_html : 
	{
		visible : true,
		title : 'HTML-код [html]',
		className : 'extra e_html',
		exec    : function()
		{
			var selection = $(this.editor).documentSelection();
			this.editorDoc.execCommand('inserthtml', false, '[html]<br>' + selection + '<br>[/html]');
		}
	}
	
EOF;

	// в конце запятой не должно быть!
	
	return $args;
}


?>