/**
 * WYSIWYG - jQuery plugin 0.4
 *
 * Copyright (c) 2008 Juan M Martinez
 * http://plugins.jquery.com/project/jWYSIWYG
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * $Id: $
 */
(function( $ )
{
    $.fn.document = function()
    {
        var element = this[0];

        if ( element.nodeName.toLowerCase() == 'iframe' )
            return element.contentWindow.document;
            /*
            return ( $.browser.msie )
                ? document.frames[element.id].document
                : element.contentWindow.document // contentDocument;
             */
        else
            return $(this);
    };

    $.fn.documentSelection = function()
    {
        var element = this[0];

        if ( element.contentWindow.document.selection )
            return element.contentWindow.document.selection.createRange().text;
        else
            return element.contentWindow.getSelection().toString();
    };
	
    $.fn.wysiwyg = function( options )
    {
        if ( arguments.length > 0 && arguments[0].constructor == String )
        {
            var action = arguments[0].toString();
            var params = [];

            for ( var i = 1; i < arguments.length; i++ )
                params[i - 1] = arguments[i];

            if ( action in Wysiwyg )
            {
                return this.each(function()
                {
                    $.data(this, 'wysiwyg')
                     .designMode();

                    Wysiwyg[action].apply(this, params);
                });
            }
            else return this;
        }

        var controls = {};

        /**
         * If the user set custom controls, we catch it, and merge with the
         * defaults controls later.
         */
        if ( options && options.controls )
        {
            var controls = options.controls;
            delete options.controls;
        }

        var options = $.extend({
            // html : '<'+'?xml version="1.0" encoding="UTF-8"?'+'><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">STYLE_SHEET</head><body>INITIAL_CONTENT</body></html>',
            
            html : '<'+'?xml version="1.0" encoding="UTF-8"?'+'><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><link rel="stylesheet" type="text/css" href="STYLE_SHEET" /></head><body>INITIAL_CONTENT</body></html>', // max
            
            css  : {},

            debug        : false,

            autoSave     : true,  // http://code.google.com/p/jwysiwyg/issues/detail?id=11
            rmUnwantedBr : true,  // http://code.google.com/p/jwysiwyg/issues/detail?id=15
            brIE         : true,

            controls : {},
            controls_extra : {},
            messages : {}
        }, options);

        $.extend(options.messages, Wysiwyg.MSGS_EN);
		
       // $.extend(options.controls, Wysiwyg.TOOLBAR);
       $.extend(options.controls_extra, Wysiwyg.TOOLBAREXTRA);
       $.extend(options.controls, Wysiwyg.TOOLBAR, options.controls_extra);

        for ( var control in controls )
        {
            if ( control in options.controls )
                $.extend(options.controls[control], controls[control]);
            else
                options.controls[control] = controls[control];
        }

        // not break the chain
        return this.each(function()
        {
            Wysiwyg(this, options);
        });
    };

    function Wysiwyg( element, options )
    {
        return this instanceof Wysiwyg
            ? this.init(element, options)
            : new Wysiwyg(element, options);
    }

    $.extend(Wysiwyg, {
        insertImage : function( szURL, attributes )
        {
            var self = $.data(this, 'wysiwyg');

            if ( self.constructor == Wysiwyg && szURL && szURL.length > 0 )
            {
                if ( attributes )
                {
                    self.editorDoc.execCommand('insertImage', false, '#jwysiwyg#');
                    var img = self.getElementByAttributeValue('img', 'src', '#jwysiwyg#');

                    if ( img )
                    {
                        img.src = szURL;

                        for ( var attribute in attributes )
                        {
                            img.setAttribute(attribute, attributes[attribute]);
                        }
                    }
                }
                else
                {
                    self.editorDoc.execCommand('insertImage', false, szURL);
                }
            }
        },

        createLink : function( szURL )
        {
            var self = $.data(this, 'wysiwyg');

            if ( self.constructor == Wysiwyg && szURL && szURL.length > 0 )
            {
                var selection = $(self.editor).documentSelection();

                if ( selection.length > 0 )
                {
                    self.editorDoc.execCommand('unlink', false, []);
                    self.editorDoc.execCommand('createLink', false, szURL);
                }
                else if ( self.options.messages.nonSelection )
                    alert(self.options.messages.nonSelection);
            }
        },

        clear : function()
        {
            var self = $.data(this, 'wysiwyg');
                self.setContent('');
                self.saveContent();
        },

        MSGS_EN : {
            nonSelection : 'select the text you wish to link'
        },

        TOOLBAR : {
            bold          : { visible : true, tags : ['b', 'strong'], css : { fontWeight : 'bold' } },
            italic        : { visible : true, tags : ['i', 'em'], css : { fontStyle : 'italic' } },
            strikeThrough : { visible : true, tags : ['s', 'strike'], css : { textDecoration : 'line-through' } },
            underline     : { visible : true, tags : ['u'], css : { textDecoration : 'underline' } },

            separator00 : { visible : true, separator : true },

            justifyLeft   : { visible : true, css : { textAlign : 'left' } },
            justifyCenter : { visible : true, tags : ['center'], css : { textAlign : 'center' } },
            justifyRight  : { visible : true, css : { textAlign : 'right' } },
            justifyFull   : { visible : true, css : { textAlign : 'justify' } },

            separator01 : { visible : true, separator : true },

            indent  : { visible : true },
            outdent : { visible : true },

            separator02 : { visible : true, separator : true },

            subscript   : { visible : true, tags : ['sub'] },
            superscript : { visible : true, tags : ['sup'] },

            separator03 : { visible : true, separator : true },

            undo : { visible : true },
            redo : { visible : true },

            separator04 : { visible : true, separator : true },

            insertOrderedList    : { visible : true, tags : ['ol'] },
            insertUnorderedList  : { visible : true, tags : ['ul'] },
            insertHorizontalRule : { visible : true, tags : ['hr'] },

            separator05 : { separator : true },

            createLink : {
                visible : true,
                exec    : function()
                {
                    var selection = $(this.editor).documentSelection();

                    if ( selection.length > 0 )
                    {
                        if ( $.browser.msie )
                            this.editorDoc.execCommand('createLink', true, null);
                        else
                        {
                            var szURL = prompt('URL', 'http://');

                            if ( szURL && szURL.length > 0 )
                            {
                                this.editorDoc.execCommand('unlink', false, []);
                                this.editorDoc.execCommand('createLink', false, szURL);
                            }
                        }
                    }
                    else if ( this.options.messages.nonSelection )
                        alert(this.options.messages.nonSelection);
                },

                tags : ['a']
            },

            insertImage : {
                visible : true,
                exec    : function()
                {
                    if ( $.browser.msie )
                        this.editorDoc.execCommand('insertImage', true, null);
                    else
                    {
                        var szURL = prompt('URL', 'http://');

                        if ( szURL && szURL.length > 0 )
                            this.editorDoc.execCommand('insertImage', false, szURL);
                    }
                },

                tags : ['img']
            },

            separator06 : { separator : true },
            
            forecolor1 : { visible : true, className : 'red', command : 'forecolor', arguments : ['red'], title : 'Красный' },
            forecolor2 : { visible : true, className : 'green', command : 'forecolor', arguments : ['green'], title : 'Зеленый' },
            forecolor3 : { visible : true, className : 'blue', command : 'forecolor', arguments : ['blue'], title : 'Синий' },
            forecolor4 : {
                visible : true,
                exec    : function()
                {

                        var szColor = prompt('COLOR', '#');

                        if ( szColor && szColor.length > 0 )
                            this.editorDoc.execCommand('forecolor', false, szColor);
                },

                className : 'selectcolor',
                title : 'Выберите цвет'
            },
            

            
            separator06 : { separator : true },

            h1mozilla : { visible : true && $.browser.mozilla, className : 'h1', command : 'heading', arguments : ['h1'], tags : ['h1'] },
            h2mozilla : { visible : true && $.browser.mozilla, className : 'h2', command : 'heading', arguments : ['h2'], tags : ['h2'] },
            h3mozilla : { visible : true && $.browser.mozilla, className : 'h3', command : 'heading', arguments : ['h3'], tags : ['h3'] },

            h1 : { visible : true && !( $.browser.mozilla ), className : 'h1', command : 'formatBlock', arguments : ['Heading 1'], tags : ['h1'] },
            h2 : { visible : true && !( $.browser.mozilla ), className : 'h2', command : 'formatBlock', arguments : ['Heading 2'], tags : ['h2'] },
            h3 : { visible : true && !( $.browser.mozilla ), className : 'h3', command : 'formatBlock', arguments : ['Heading 3'], tags : ['h3'] },

            separator07 : { visible : false, separator : true },

            cut   : { visible : true },
            copy  : { visible : true },
            paste : { visible : true },

            separator08 : { separator : true && !( $.browser.msie ) },

            increaseFontSize : { visible : true && !( $.browser.msie ), tags : ['big'] },
            decreaseFontSize : { visible : true && !( $.browser.msie ), tags : ['small'] },

            separator09 : { separator : true },
            
            removeFormat : {
                visible : true,
                exec    : function()
                {
                    this.editorDoc.execCommand('removeFormat', false, []);
                    this.editorDoc.execCommand('unlink', false, []);
                }
            },
            
            html : {
                visible : true,
                exec    : function()
                {
                    if ( this.viewHTML )
                    {
                        this.setContent( $(this.original).val() );
                        $(this.original).hide();
                        
						$(this.editor).css({'height': '400px'}); // max
                        $(this.editor).show(); // max
                        
                    }
                    else
                    {
                        this.saveContent();
                        $(this.original).show();
						$(this.editor).hide(); // max
                        
                        // max
						$(this.original).css({'width': '100%'});
						
						if ( $.browser.msie ) { ww = $(this.original).width() - 32;}
							else {ww = $(this.original).width() - 16;}
					
						$(this.original).css({'width': ww + 'px'});
                        // /max
						
                    }

                    this.viewHTML = !( this.viewHTML );
                }
            }
        },
		
		TOOLBAREXTRA : {}
		
    });

    $.extend(Wysiwyg.prototype,
    {
        original : null,
        options  : {},

        element  : null,
        editor   : null,

        init : function( element, options )
        {
            var self = this;

            this.editor = element;
            this.options = options || {};

            $.data(element, 'wysiwyg', this);

            var newX = element.width || element.clientWidth;
            var newY = element.height || element.clientHeight;
            
            if ( element.nodeName.toLowerCase() == 'textarea' )
            {
                this.original = element;
                
                if ( newX == 0 && element.cols )
                    newX = ( element.cols * 8 ) + 21;

                if ( newY == 0 && element.rows )
                    newY = ( element.rows * 16 ) + 16;

                var editor = this.editor = $('<iframe></iframe>').css({
                    minHeight : ( newY - 0 ).toString() + 'px'
                    //width     : ( newX + 0 ).toString() + 'px' // max
                }).attr('id', $(element).attr('id') + 'IFrame');

                
                this.editor.css({'height': '400px'}); // max
                    
                if ( $.browser.msie )
                {
                    // this.editor.css('height', ( newY ).toString() + 'px'); //max
                    

                    /**
                    var editor = $('<span></span>').css({
                        width     : ( newX - 6 ).toString() + 'px',
                        height    : ( newY - 8 ).toString() + 'px'
                    }).attr('id', $(element).attr('id') + 'IFrame');

                    editor.outerHTML = this.editor.outerHTML;
                     */
                }
            }

            var panel = this.panel = $('<ul></ul>').addClass('panel');

            this.appendControls();
            this.element = $('<div></div>').css({
                //width : ( newX > 0 ) ? ( newX ).toString() + 'px' : '100%' // max
            }).addClass('wysiwyg')
              .append(panel)
              .append( $('<div><!-- --></div>').css({ clear : 'both' }) )
              .append(editor);

            $(element)
            // .css('display', 'none')
            .hide()
            .before(this.element);

            this.viewHTML = false;

            this.initialHeight = newY - 8;
            this.initialContent = $(element).text();

            this.initFrame();

            if ( this.initialContent.length == 0 )
                this.setContent('');

            if ( this.options.autoSave )
            {
				// $('form').submit(function() { self.saveContent(); });
            }
            
			$('form').submit(function() 
			{ 
				
				if (self.submitContent()) { self.saveContent(); }
				
			});
                
        },

        initFrame : function()
        {
            var self = this;
            var style = '';

            /**
             * @link http://code.google.com/p/jwysiwyg/issues/detail?id=14
             */
            if ( this.options.css && this.options.css.constructor == String )
               style = this.options.css; // max

            this.editorDoc = $(this.editor).document();
            this.editorDoc_designMode = false;

            try {
                this.editorDoc.designMode = 'on';
                this.editorDoc_designMode = true;
            } catch ( e ) {
                // Will fail on Gecko if the editor is placed in an hidden container element
                // The design mode will be set ones the editor is focused

                $(this.editorDoc).focus(function()
                {
                    self.designMode();
                });
            }

            this.editorDoc.open();
            
            this.editorDoc.write(
                this.options.html
                    .replace(/INITIAL_CONTENT/, this.initialContent)
                    .replace(/STYLE_SHEET/, style)
            );
            this.editorDoc.close();
            this.editorDoc.contentEditable = 'true';
            
            
            if ( $.browser.msie )
            {
                /**
                 * Remove the horrible border it has on IE.
                 */
                setTimeout(function() { $(self.editorDoc.body).css('border', 'none'); }, 0);
            }
            
            //setTimeout(function() { alert('save!'); }, 500);
            
            // max autosave
            $(self.editorDoc.body).everyTime(autosavetime, function() { self.autosaveContent(); } );
            
            $(this.editorDoc).click(function( event )
            {
                self.checkTargets( event.target ? event.target : event.srcElement);
            });

            /**
             * @link http://code.google.com/p/jwysiwyg/issues/detail?id=20
             */
            $(this.original).focus(function()
            {
                $(self.editorDoc.body).focus();
            });

            if ( this.options.autoSave )
            {
                /**
                 * @link http://code.google.com/p/jwysiwyg/issues/detail?id=11
                 */
                $(this.editorDoc).keydown(function() { self.saveContent(); })
                                 .mousedown(function() { self.saveContent(); });
            }
            
            if ( this.options.css )
            {
                setTimeout(function()
                {
                    if ( self.options.css.constructor == String )
                    {
                        /**
                         * $(self.editorDoc)
                         * .find('head')
                         * .append(
                         *     $('<link rel="stylesheet" type="text/css" media="screen" />')
                         *     .attr('href', self.options.css)
                         * );
                         */
                    }
                    else
                        $(self.editorDoc).find('body').css(self.options.css);
                }, 0);
            }

            $(this.editorDoc).keydown(function( event )
            {
                if ( $.browser.msie && self.options.brIE && event.keyCode == 13 )
                {
                    var rng = self.getRange();
                        rng.pasteHTML('<br />');
                        rng.collapse(false);
                        rng.select();

    				return false;
                }
               
               // ctrl+s = autosave
               if (event.keyCode == 83 && event.ctrlKey) 
               { 
					// alert('save'); 
					self.autosaveContent();
					return false; 
               }
            },
            
            // последнее сохранение автосаве
            
            $('span.autosave-editor').html('<a target="_blank" href="' + autosaveold + '">Последнее автосохранение</a> (Ctrl+S - сохранить)').css('margin-left', '10px')
            
            // $('span.autosave-editor').html('Ctrl+S - автосохранение').css('margin-left', '10px')
            
        
            );
        },

        designMode : function()
        {
            if ( !( this.editorDoc_designMode ) )
            {
                try {
                    this.editorDoc.designMode = 'on';
                    this.editorDoc_designMode = true;
                } catch ( e ) {}
            }
        },

        getSelection : function()
        {
            return ( window.getSelection ) ? window.getSelection() : document.selection;
        },

        getRange : function()
        {
            var selection = this.getSelection();

            if ( !( selection ) )
                return null;

            return ( selection.rangeCount > 0 ) ? selection.getRangeAt(0) : selection.createRange();
        },

        getContent : function()
        {
            // return $( $(this.editor).document() ).find('body').html(); // max
            
            kkk = $( $(this.editor).document() ).find('body').html();
			
			
			kkk = kkk.replace(/<P>&nbsp;<\/P>/g , '<br>'); // ie + opera
			kkk = kkk.replace(/\n</g , '<'); // ie

			kkk = kkk.replace(/<td>/g , '<br><td>');
			kkk = kkk.replace(/<tr>/g , '<br><tr>');
			kkk = kkk.replace(/<\/tr>/g , '<br><\/tr>');
			
			kkk = kkk.replace(/<br><br><br><br>/g , '<br>');
			kkk = kkk.replace(/<br><br><br>/g , '<br>');
			
			kkk = kkk.replace(/<\/h1><br>/g , '</h1>\n');
			kkk = kkk.replace(/<\/h2><br>/g , '</h2>\n');
			kkk = kkk.replace(/<\/h3><br>/g , '</h3>\n');
			kkk = kkk.replace(/<\/h4><br>/g , '</h4>\n');
			kkk = kkk.replace(/<\/h5><br>/g , '</h5>\n');
			kkk = kkk.replace(/<\/h6><br>/g , '</h6>\n');
			
			//kkk = kkk.replace(/<\/table><br>/g , '</table>\n');
			//kkk = kkk.replace(/<\/tr><br>/g , '</tr>\n');
			//kkk = kkk.replace(/<\/td><br>/g , '</td>\n');
			//kkk = kkk.replace(/<\/tbody><br>/g , '</tbody>\n');
			
			
			kkk = kkk.replace(/<br>/g , '\n');
			kkk = kkk.replace(/<BR>/g , '\n');
			
					
			/*
			kkk = kkk.replace(/&lt;pre&gt;/g , '<pre>');
			kkk = kkk.replace(/&lt;\/pre&gt;/g , '</pre><br>');
			kkk = kkk.replace(/&lt;PRE&gt;/g , '<pre>');
			kkk = kkk.replace(/&lt;\/PRE&gt;/g , '</pre><br>');
			
			kkk = kkk.replace(/&lt;code&gt;/g , '<code>');
			kkk = kkk.replace(/&lt;\/code&gt;/g , '</code><br>');
			kkk = kkk.replace(/&lt;CODE&gt;/g , '<code>');
			kkk = kkk.replace(/&lt;\/CODE&gt;/g , '</code><br>');
			*/
			
			// kkk = kkk.replace(/^\s+|\s+$/g, ""); // trim
			
			
			kkk = $.trim(kkk);
			kkk = kkk + '\n\n';
			
			//kkk = kkk + '_n_';
			
			//kkk = kkk.replace(/_n__n_/g , '_n_');
			//kkk = kkk.replace(/_n_/g , '');
			
			
			return kkk;
        },

        setContent : function( newContent )
        {
            // $( $(this.editor).document() ).find('body').html(newContent); //max
            
			kkk = newContent;
		
			kkk = kkk.replace(/\n/g , '<br>');
			
			kkk = kkk.replace(/<br><td>/g , '<td>');
			kkk = kkk.replace(/<br><tr>/g , '<tr>');
			kkk = kkk.replace(/<br><\/tr>/g , '<\/tr>');
			
			/*
			kkk = kkk.replace('<pre><br>' , '<pre>');
			kkk = kkk.replace('<br></pre>' , '</pre>');
			kkk = kkk.replace('<PRE><BR>' , '<PRE>');
			kkk = kkk.replace('<BR></PRE>' , '</PRE>');
			
			kkk = kkk.replace('<code><br>' , '<code>');
			kkk = kkk.replace('<br></code>' , '</code>');
			kkk = kkk.replace('<CODE><BR>' , '<CODE>');
			kkk = kkk.replace('<BR></CODE>' , '</CODE>');
			*/
				
			$( $(this.editor).document() ).find('body').html(kkk);
        },

        saveContent : function()
        {
            if ( this.original )
            {
                var content = this.getContent();
                if ( this.options.rmUnwantedBr )
                   content = ( content.substr(-4) == '<br>' ) ? content.substr(0, content.length - 4) : content;

                $(this.original).val(content);
            }
        },
        
        // max +
        autosaveContent : function()
        {
			autotext = this.getContent();
			
			if (autosavetextold != autotext)
			{
				$('span.autosave-editor').html('Сохранение...');
					
				$.post(autosaveurl, { "text": autotext, 'id': autosaveid }, function(data)
				{ 
					var dd = new Date();
					$('span.autosave-editor').html('<a target="_blank" href="' + data + '">Автосохранение в ' + dd.toLocaleTimeString() + '</a>');
					
					autosavetextold = autotext;
				} );
			}
			else
			{
				// $('span.autosave-editor').html('Сохранение не требуется');
			}
			
        },
        // max -
        
        // max +
        submitContent : function()
        {
			if ( this.viewHTML )
			{
				tt = $(this.original).val();
				$(this.editor).val(tt);
				//alert(1);
				return false;
			}
			else 
			{ 
				return true;
			}
        },       
        // max -

        appendMenu : function( cmd, args, className, fn, title )
        {
            var self = this;
            var args = args || [];
            var title = title || className || cmd;

            $('<li title="' + title + '"></li>').append(
                $('<a><!-- --></a>').addClass(className || cmd)
            ).mousedown(function() {
                if ( fn ) fn.apply(self); else self.editorDoc.execCommand(cmd, false, args);
                if ( self.options.autoSave ) self.saveContent();
            }).appendTo( this.panel );
        },

        appendMenuSeparator : function()
        {
            $('<li class="separator"></li>').appendTo( this.panel );
        },

        appendControls : function()
        {
            for ( var name in this.options.controls )
            {
                var control = this.options.controls[name];

                if ( control.separator )
                {
                    if ( control.visible !== false )
                        this.appendMenuSeparator();
                }
                else if ( control.visible )
                {
                    this.appendMenu(
                        control.command || name, control.arguments || [],
                        control.className || control.command || name || 'empty', control.exec, control.title
                    );
                }
            }
        },

        checkTargets : function( element )
        {
            for ( var name in this.options.controls )
            {
                var control = this.options.controls[name];
                var className = control.className || control.command || name || 'empty';

                $('.' + className, this.panel).removeClass('active');

                if ( control.tags )
                {
                    var elm = element;

                    do {
                        if ( elm.nodeType != 1 )
                            break;

                        if ( $.inArray(elm.tagName.toLowerCase(), control.tags) != -1 )
                            $('.' + className, this.panel).addClass('active');
                    } while ( elm = elm.parentNode );
                }

                if ( control.css )
                {
                    var elm = $(element);

                    do {
                        if ( elm[0].nodeType != 1 )
                            break;

                        for ( var cssProperty in control.css )
                            if ( elm.css(cssProperty).toString().toLowerCase() == control.css[cssProperty] )
                                $('.' + className, this.panel).addClass('active');
                    } while ( elm = elm.parent() );
                }
            }
        },

        getElementByAttributeValue : function( tagName, attributeName, attributeValue )
        {
            var elements = this.editorDoc.getElementsByTagName(tagName);

            for ( var i = 0; i < elements.length; i++ )
            {
                var value = elements[i].getAttribute(attributeName);

                if ( $.browser.msie )
                {
                    /** IE add full path, so I check by the last chars. */
                    value = value.substr(value.length - attributeValue.length);
                }

                if ( value == attributeValue )
                    return elements[i];
            }

            return false;
        }
    });
})(jQuery);