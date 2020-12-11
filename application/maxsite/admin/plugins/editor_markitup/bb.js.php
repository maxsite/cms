<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------

myBbcodeSettings = {
	nameSpace:	"bbcode", // Useful to prevent multi-instances CSS conflict
	
	previewParserPath: "<?= getinfo('ajax') . base64_encode('admin/plugins/editor_markitup/preview-ajax.php') ?>",
	// previewInWindow: 'width=960, height=800, resizable=yes, scrollbars=yes',
	
	<?= $editor_config['preview'] ?>
	<?= $editor_config['previewautorefresh'] ?>
	<?= $editor_config['previewPosition'] ?>
	
	markupSet:	[

		{name:'<?= t('Шрифт') ?>', openWith:'[b]', closeWith:'[/b]', className:"fonts", multiline:false, dropMenu: [
			{name:'<?= t('Полужирный (важный)') ?>', openWith:'[b]', closeWith:'[/b]', className:"bold", key:"B" },
			{name:'<?= t('Курсив (важный)') ?>', openWith:'[i]', closeWith:'[/i]', className:"italic", key:"I" },
			{separator:'---------------' },
			{name:'<?= t('Полужирный (простой)') ?>', openWith:'[bold]', closeWith:'[/bold]', className:"bold" },
			{name:'<?= t('Курсив (простой)') ?>', openWith:'[italic]', closeWith:'[/italic]', className:"italic" },
			{separator:'---------------' },
			{name:'<?= t('Подчеркнутый') ?>', openWith:'[u]', closeWith:'[/u]', className:"underline" },
			{name:'<?= t('Зачеркнутый') ?>', openWith:'[s]', closeWith:'[/s]', className:"stroke" },
			{separator:'---------------' },
			{name:'<?= t('Верхний индекс') ?>', openWith:'[sup]', closeWith:'[/sup]', className:"sup" },
			{name:'<?= t('Нижний индекс') ?>', openWith:'[sub]', closeWith:'[/sub]', className:"sub" },
			{separator:'---------------' },
			{name:'<?= t('Уменьшенный шрифт') ?>', openWith:'[small]', closeWith:'[/small]', className:"small" },
			{separator:'---------------' },
			{name:'<?= t('Размер текста') ?>', openWith:'[size=[![<?= t('Размер текста') ?>]!]%]', closeWith:'[/size]', className:"text-smallcaps"},
		]},
		
		{name:'<?= t('Ссылка') ?>', key:'L', openBlockWith:'[url=[![<?= t('Адрес с http://') ?>]!]]', closeBlockWith:'[/url]', className:"link", dropMenu: [
			{name:'<?= t('Ссылка (адрес и текст)') ?>', openBlockWith:'[url=[![<?= t('Адрес с http://') ?>]!]][![<?= t('Текст ссылки') ?>]!][/url]', closeBlockWith:'', className:"link"}, 
		]},
		      
 		{name:'<?= t('Цитата') ?>', openBlockWith:'[quote]\n', closeBlockWith:'\n[/quote]', className:"quote", dropMenu: [
			{name:'<?= t('Цитата (блок)') ?>', openBlockWith:'\n[quote]\n', closeBlockWith:'\n[/quote]', className:"quote"}, 
			{name:'<?= t('Цитирование в строке') ?>', openBlockWith:'[q]', closeBlockWith:'[/q]', className:"quote"}, 
			{name:'<?= t('Абревиатура') ?>', openBlockWith:'[abbr [![<?= t('Определение') ?>]!]]', closeBlockWith:'[/abbr]', className:"abbr"}, 
			{name:'<?= t('Сноска') ?>', openBlockWith:'[cite]', closeBlockWith:'[/cite]', className:"cite"}, 
			{name:'<?= t('Адрес') ?>', openBlockWith:'[address]', closeBlockWith:'[/address]', className:"address"}, 
			{name:'<?= t('Новый термин') ?>', openBlockWith:'[dfn]', closeBlockWith:'[/dfn]', className:"dfn"}, 
			{name:'<?= t('Код (строка)') ?>', openBlockWith:'[code]', closeBlockWith:'[/code]', className:"code1"}, 
			{name:'<?= t('Принудительный перенос') ?>', replaceWith:'[br]\n', className:"page-red"},
			{name:'<?= t('Линия') ?>', openBlockWith:'\n[hr]\n', className:"hr"}, 
			
		]},
	
		{name:'<?= t('Изображение') ?>', openBlockWith:'[img [![<?= t('Описание') ?>]!]][![<?= t('Адрес') ?>]!][/img]', className:"picture", dropMenu: [
			{name:'<?= t('Изображение') ?>', replaceWith:'[img][![<?= t('Адрес') ?>]!][/img]', className:"picture"}, 
			{separator:'---------------' },
			{name:'[img]', openBlockWith:'[img [![<?= t('Описание') ?>]!]][![<?= t('Адрес') ?>]!][/img]', className:"image_add"},
			{name:'[img(left)]', openBlockWith:'[img(left) [![<?= t('Описание') ?>]!]][![<?= t('Адрес') ?>]!][/img]', className:"image_add"},
			{name:'[img(right)]', openBlockWith:'[img(right) [![<?= t('Описание') ?>]!]][![<?= t('Адрес') ?>]!][/img]', className:"image_add"},
			{name:'[img(center)]', openBlockWith:'[img(center) [![<?= t('Описание') ?>]!]][![<?= t('Адрес') ?>]!][/img]', className:"image_add"},
		]},
	
		{name:'<?= t('Цвет') ?>', openWith:'[color=[![Color]!]]', closeWith:'[/color]', className:"colors", dropMenu: [
			{name:'<?= t('Желтый') ?>', openWith:'[color=yellow]', closeWith:'[/color]', className:"col-yellow" },
			{name:'<?= t('Оранжевый') ?>', openWith:'[color=orange]', closeWith:'[/color]', className:"col-orange" },
			{name:'<?= t('Красный') ?>', openWith:'[color=red]', closeWith:'[/color]', className:"col-red" },
			{name:'<?= t('Синий') ?>', openWith:'[color=blue]', closeWith:'[/color]', className:"col-blue" },
			{name:'<?= t('Фиолетовый') ?>', openWith:'[color=purple]', closeWith:'[/color]', className:"col-purple" },
			{name:'<?= t('Зеленый') ?>', openWith:'[color=green]', closeWith:'[/color]', className:"col-green" },
			{name:'<?= t('Белый') ?>', openWith:'[color=white]', closeWith:'[/color]', className:"col-white" },
			{name:'<?= t('Серый') ?>', openWith:'[color=gray]', closeWith:'[/color]', className:"col-gray" },
			{name:'<?= t('Черный') ?>', openWith:'[color=black]', closeWith:'[/color]', className:"col-black" },
			{name:'<?= t('Ярко-голубой') ?>', openWith:'[color=cyan]', closeWith:'[/color]', className:"col-cyan" },
			{name:'<?= t('Ярко-зеленый') ?>', openWith:'[color=lime]', closeWith:'[/color]', className:"col-lime" },
			
			{name:'<?= t('Таблица цветов') ?>', className:'help', beforeInsert:function(){miu.select_colors();}, className:"col-select"},
			
		]},
		
		<?php if ($smiles) echo $smiles ?>
		
		
		{separator:'---------------' },
		
		{name:'<?= t('Заголовок') ?>', openWith:'[h1]', closeWith:'[/h1]', className:"h1", dropMenu: [
			{name:'<?= t('Заголовок 1') ?>', openWith:'[h1]', closeWith:'[/h1]', className:"h1"}, 
			{name:'<?= t('Заголовок 2') ?>', openWith:'[h2]', closeWith:'[/h2]', className:"h2"}, 
			{name:'<?= t('Заголовок 3') ?>', openWith:'[h3]', closeWith:'[/h3]', className:"h3"}, 
			{name:'<?= t('Заголовок 4') ?>', openWith:'[h4]', closeWith:'[/h4]', className:"h4"}, 
			{name:'<?= t('Заголовок 5') ?>', openWith:'[h5]', closeWith:'[/h5]', className:"h5"}, 
			{name:'<?= t('Заголовок 6') ?>', openWith:'[h6]', closeWith:'[/h6]', className:"h6"}, 
		]},
		
		{name:'<?= t('Выравнивание') ?>', openWith:'[p(t(left)]', closeWith:'[/p]', className:"left", dropMenu :[  
			{name:'<?= t('Абзац влево') ?>', openWith:'[p(t-left)]', closeWith:'[/p]', className:"left" },
			{name:'<?= t('Абзац по центру') ?>', openWith:'[p(t-center)]', closeWith:'[/p]', className:"center" },
			{name:'<?= t('Абзац вправо') ?>', openWith:'[p(t-right)]', closeWith:'[/p]', className:"right" },
			{name:'<?= t('Абзац по формату') ?>', openWith:'[p(t-justify)]', closeWith:'[/p]', className:"justify" },
			
			/*
			{separator:'---------------' },
			
			{name:'<?= t('Блок влево') ?>', openWith:'[div(b-left)]', closeWith:'[/div]', className:"text-padding-left"}, 
			{name:'<?= t('Блок по центру') ?>', openWith:'[div(b-center)]', closeWith:'[/div]', className:"text-padding-center"},       
			{name:'<?= t('Блок вправо') ?>', openWith:'[div(b-right)]', closeWith:'[/div]', className:"text-padding-right"}, 
			*/
			
			{separator:'---------------' },
			
			{name:'<?= t('p - абзац') ?>', openWith:'[p]', closeWith:'[/p]', className:"add"}, 
			
			{separator:'---------------' },
			
			{name:'div.class', openBlockWith:'[div([![Css class]!])]', closeBlockWith:'[/div]', className:"add"}, 
			{name:'span.class', openBlockWith:'[span([![Css class]!])]', closeBlockWith:'[/span]', className:"add"}, 
			{name:'[div <?= t('свойства') ?>]', openBlockWith:'[div [![<?= t('Свойства') ?>]!]]', closeBlockWith:'[/div]', className:"add"}, 
			{name:'[span <?= t('свойства') ?>]', openBlockWith:'[span [![<?= t('Свойства') ?>]!]]', closeBlockWith:'[/span]', className:"add"}, 
		]},

		
		{name: '<?= t('Список') ?>', className:"list-bullet", openBlockWith:'[ul]\n', openWith:'[*]', closeWith:'', closeBlockWith:'\n[/ul]', multiline:true, dropMenu: [ 
			{name:'<?= t('Номера') ?>', className:'list-numeric', openBlockWith:'[ol]\n', openWith:'[*]', closeWith:'', closeBlockWith:'\n[/ol]', multiline:true}, 
			{name:'<?= t('Элемент списка') ?>', openWith:'[*]', className:"list-item"},
		
			{separator:'---------------' },
			
			{name:'<?= t('Список определений') ?>', openBlockWith:'\n[dl]\n', closeBlockWith:'\n[/dl]', className:"dl"}, 
			{name:'<?= t('Определение') ?>', openBlockWith:'[dt]', closeBlockWith:'[/dt]', className:"dl"}, 
			{name:'<?= t('Описание') ?>', openBlockWith:'[dd]', closeBlockWith:'[/dd]', className:"dl"}, 
			{name:'<?= t('Заготовка') ?>', openBlockWith:'\n[dl]\n[dt]<?= t('Определение') ?>[/dt]\n[dd]<?= t('Описание') ?>[/dd]\n\n[dt]<?= t('Определение') ?>[/dt]\n[dd]<?= t('Описание') ?>[/dd]\n[/dl]', closeBlockWith:'', className:"dl"}, 
		]},
		
		{name:'<?= t('Таблица') ?>', openBlockWith:'\n[table]\n', closeBlockWith:'\n[/table]', className:"table", dropMenu: [
			{name:'<?= t('Таблица') ?>', openBlockWith:'\n[table]\n', closeBlockWith:'\n[/table]\n', className:"table-add"}, 
			
			{name:'<?= t('Строка') ?>', openBlockWith:'[tr]\n', closeBlockWith:'\n[/tr]',  className:"table-row-insert"}, 
			
			{name:'<?= t('Строка ячеек') ?>', openBlockWith:'[tr]\n', closeBlockWith:'\n[/tr]', openWith:'[td]', closeWith:'[/td]', className:"table-row-insert"},
			
			{name:'<?= t('Ячейки') ?>', openWith:'[td]', closeWith:'[/td]', className:"table-select"}, 
			
			{name:'<?= t('Заготовка1') ?>', openBlockWith:'[table]\n[tr]\n[td] [/td]\n[td] [/td]\n[td] [/td]\n[/tr]\n[/table]', className:"table-go"}, 
			{name:'<?= t('Заготовка2') ?>', openBlockWith:'\n[tr]\n[td] [/td]\n[td] [/td]\n[td] [/td]\n[/tr]', className:"table-go"}, 
		]},

		{separator:'---------------'},

		{name:'<?= t('Подсветка синтаксиса') ?>', openBlockWith:'[pre]\n', closeBlockWith:'\n[/pre]', className:"code", dropMenu: [
			{name:'<?= t('Обычный текст') ?>', openBlockWith:'[pre]\n', closeBlockWith:'\n[/pre]', className:"text" },
			{name:'<?= t('PHP-код') ?>', openBlockWith:'[pre lang=php]\n', closeBlockWith:'\n[/pre]', className:"php" },
			{name:'<?= t('HTML-код') ?>', openBlockWith:'[pre lang=html]\n', closeBlockWith:'\n[/pre]', className:"html-pre" },
			{name:'<?= t('LESS/CSS-код') ?>', openBlockWith:'[pre lang=less]\n', closeBlockWith:'\n[/pre]', className:"css" },
			{name:'<?= t('CSS-код') ?>', openBlockWith:'[pre lang=css]\n', closeBlockWith:'\n[/pre]', className:"css" },
			{name:'<?= t('JavaScript-код') ?>', openBlockWith:'[pre lang=js]\n', closeBlockWith:'\n[/pre]', className:"js" },
			{name:'<?= t('Delphi/Pascal-код') ?>', openBlockWith:'[pre lang=pascal]\n', closeBlockWith:'\n[/pre]', className:"pascal" },
			{name:'<?= t('SQL-код') ?>', openBlockWith:'[pre lang=sql]\n', closeBlockWith:'\n[/pre]', className:"sql" },
			{name:'<?= t('C#-код') ?>', openBlockWith:'[pre lang=csharp]\n', closeBlockWith:'\n[/pre]', className:"csharp" },
			{name:'<?= t('XML-код') ?>', openBlockWith:'[pre lang=xml]\n', closeBlockWith:'\n[/pre]', className:"xml" },
			{name:'<?= t('Java-код') ?>', openBlockWith:'[pre lang=java]\n', closeBlockWith:'\n[/pre]', className:"java" }
		]},

		{name:'<?= t('Очистить текст от BB-кодов') ?>', className:"clean", replaceWith:function(h) { return h.selection.replace(/\[(.*?)\]/g, "") }, dropMenu: [
	
			{name:'<?= t('Очистить текст от BB-кодов') ?>', className:"clean", replaceWith:function(h) { return h.selection.replace(/\[(.*?)\]/g, "") }},
			
			{name:'<?= t('Очистить текст от HTML') ?>', className:"clean", replaceWith:function(h) { return h.selection.replace(/\<(.*?)\>/g, "") }},

			{name:'<?= t('Заменить адреса на [page_images]') ?>', className:'pi', beforeInsert:function(markItUp) { miu.repl_pi(markItUp) }},
			
			{name:'<?= t('Заменить http-адреса сайта на https') ?>', className:'pi', beforeInsert:function(markItUp) { miu.repl_http(markItUp) }},
			
			{name:'<?= t('Замена в тексте') ?>', className:'qrepl', beforeInsert:function(markItUp) { miu.repl(markItUp) }},	
			
		]},
		
		
		{separator:'---------------'},

		{name:'<?= t('Прочие') ?>', openBlockWith:'', closeBlockWith:'', className:"script-edit", dropMenu: [
		
			{name:'<?= t('Задать HTML/JS-код') ?>', openBlockWith:'[html]', closeBlockWith:'[/html]', className:"html-code"}, 
			
			<?php if (function_exists('run_php_custom')) { ?>
			{name:'<?= t('Исполняемый PHP-код') ?>', openBlockWith:'[php]', closeBlockWith:'[/php]', className:"php"},
			<?php } ?>
			
			{separator:'---------------' },
			
			{name:'<?= t('Текст (demo)') ?>', openBlockWith:'[text-demo]', closeBlockWith:'', className:"add"}, 
			{name:'<?= t('Текст (normalize)') ?>', openBlockWith:'[text-normalize]', closeBlockWith:'', className:"add"}, 
			
			{separator:'---------------' },
			
			<?php if (function_exists('ushka')) { ?>
			{name:'<?= t('Ушка') ?>', openBlockWith:'[ushka=[![<?= t('Имя ушки') ?>]!]]', closeBlockWith:'', className:"add"}, 
			<?php } ?>
			
			{name:'<?= t('Показать/спрятать (show)') ?>', openBlockWith:'[show [![<?= t('Заголовок блока') ?>]!]]', closeBlockWith:'[/show]', className:"add"}, 
			
			<?php if (function_exists('down_count_content')) { ?>
			{name:'<?= t('Счетчик переходов') ?>', openBlockWith:'[dc]', closeBlockWith:'[/dc]', className:"add"}, 
			<?php } ?>

			<?php if (function_exists('audioplayer_content')) { ?>
			{name:'<?= t('Аудиоплеер MP3') ?>', replaceWith:'[audio=[![Адрес]!]]', className:"audio"}, 
			<?php } ?>

			<?php if (function_exists('faq_custom')) { ?>
			{name:'<?= t('FAQ (заготовка)') ?>', openBlockWith:'[faqs]\n[faq=<?= t('вопрос') ?>]<?= t('ответ') ?>[/faq]\n[faq=<?= t('вопрос2') ?>]<?= t('ответ2') ?>[/faq]\n[/faqs]', closeBlockWith:'', className:"add"}, 
			<?php } ?>

			<?php if (function_exists('spoiler_custom')) { ?>
			{name:'<?= t('Показать/спрятать (spoiler)') ?>', openBlockWith:'[spoiler=[![<?= t('Заголовок блока') ?>]!]]', closeBlockWith:'[/spoiler]', className:"add"}, 
			<?php } ?>

			<?php if (function_exists('auth_content_parse')) { ?>
			{name:'<?= t('Спрятать от незалогиненных') ?>', openBlockWith:'[auth]', closeBlockWith:'[/auth]', className:"add"}, 
			<?php } ?>
			
			
			<?php mso_hook('editor_markitup_bbcode_other') ?>
			
		]},
		
		
		<?php mso_hook('editor_markitup_bbcode_add') ?>

		{separator:'---------------' },

		{name:'<?= t('Отметить конец анонса') ?>', replaceWith:'\n[cut]\n', className:"separator", dropMenu: [
			{name:'<?= t('Отрезать анонс') ?>', replaceWith:'\n[xcut]\n', className:"separator2"},
		]}, 
		
			
		{separator:'---------------' },

		{name:'<?= t('Быстрое сохранение текста') ?>', className:'qsave', key:"S", beforeInsert:function(markItUp) { miu.save(markItUp) }},
		{name:'<?= t('Предпросмотр (с ALT скрыть)') ?>', className:'preview', call:'preview', key:"E"},
		{name:'<?= t('Полноэкранный режим редактора (F2)') ?>', className:'fullscreen', beforeInsert:function(){shsh();} },

		{separator:'---------------' },

		{name:'<?= t('Помощь по BB-кодам') ?>', className:'help', beforeInsert:function(){miu.help_bb();} },

		<?php mso_hook('editor_markitup_bbcode') ?>

	]
}

miu = {
	save: function(markItUp) 
	{
		data = markItUp.textarea.value;
		$.post(autosaveurl, {"text": data, "id": autosaveid}, 
			function(response) 
			{
				var dd = new Date();
				$('.autosave-editor').html('<a target="_blank" href="' + response + '"><?= t('Сохранено в') ?> ' + dd.toLocaleTimeString() + '</a>'); //.fadeOut(15000);
				// alert("<?= t('Сохранено!') ?>");
				
			});
	},

	repl: function(markItUp) 
	{
		str = markItUp.textarea.value;
		
		var s_search = prompt('<?= t('Что ищем?') ?>');
		var s_replace = prompt('<?= t('На что меняем?') ?>');
		
		markItUp.textarea.value = str.replace(new RegExp(s_search,'g'), s_replace);
		
		alert("<?= t('Выполнено!') ?>");
	},
	
	help_bb: function()
	{
		window.open('<?= getinfo('plugins_url') ?>bbcode/bbcode-help.html');
	},

	select_colors: function()
	{
		window.open('<?= getinfo('admin_url') ?>plugins/editor_markitup/color-table.html');
	},
	
	repl_pi: function(markItUp) 
	{
		str = markItUp.textarea.value;
				
		var s_search = "<?= getinfo('uploads_url') . '_pages/' ?>" + autosaveid + "/";
		
		markItUp.textarea.value = str.replace(new RegExp(s_search,'g'), "[page_images]");
		
		alert("<?= t('Выполнено!') ?>");
	},
	
	repl_http: function(markItUp) 
	{
		str = markItUp.textarea.value;
		
		var s_search = 'http://<?= $_SERVER['HTTP_HOST'] ?>';
		var s_replace = 'https://<?= $_SERVER['HTTP_HOST'] ?>';
				
		markItUp.textarea.value = str.replace(new RegExp(s_search,'g'), s_replace);
				
		alert("<?= t('Выполнено!') ?>");
	},
	
}

