/*
 * jTPS - table sorting, pagination, and animated page scrolling
 *	version 0.5.1
 * Author: Jim Palmer
 * Released under MIT license.
 */
 (function($) {

	// apply table controls + setup initial jTPS namespace within jQuery
	$.fn.jTPS = function ( opt ) {

		$(this).data('tableSettings', $.extend({
			perPages:			[5, 10, 20, 50, 'ALL'],				// the "show per page" selection
			perPageText:		'Show per page:',						// text that appears before perPages links
			perPageDelim:		'<span style="color:#ccc;">|</span>',	// text or dom node that deliminates each perPage link 
			perPageSeperator:	'..',									// text or dom node that deliminates split in select page links
			scrollDelay:		20,										// delay (in ms) between steps in anim. - IE has trouble showing animation with < 30ms delay
			scrollStep:			1,										// how many tr's are scrolled per step in the animated vertical pagination scrolling
			fixedLayout:		true,									// autoset the width/height on each cell and set table-layout to fixed after auto layout
			clickCallback:		function () {}							// callback function after clicks on sort, perpage and pagination
		}, opt));
		
		// generic pass-through object + other initial variables
		var pT = $(this), page = page || 1, perPages = $(this).data('tableSettings').perPages, perPage = perPage || perPages[0],
			rowCount = $('>tbody', this).find('tr').length;

		// append jTPS class "stamp"
		$(this).addClass('jTPS');
	
		// кусок кода нужен, что б не прыгала ширина столбцов, приводит к проблемам в хроме  
/*		// setup the fixed table-layout so that the animation doesn't bounce around - faux grid for table
		if ( $(this).data('tableSettings').fixedLayout ) {
			// "fix" the table layout and individual cell width & height settings
			if ( $(this).css('table-layout') != 'fixed' ) {
				// find max tbody td cell height
				var maxCellHeight = 0;

				// set width style on the TH headers (rely on jQuery with computed styles support)
				$('>thead', this).find('th,td').each(function () { $(this).css('width', $(this).width()); });

				// ensure browser-formated widths for each column in the thead and tbody
				var tbodyCh = $('>tbody',this)[0].childNodes, tmpp = 0;
				// loop through tbody children and find the Nth <TR>
				for ( var tbi=0, tbcl=tbodyCh.length; tbi < tbcl; tbi++ )
					if ( tbodyCh[ tbi ].nodeName == 'TR' )
						maxCellHeight = Math.max( maxCellHeight, tbodyCh[ tbi ].offsetHeight );

				// now set the height attribute and/or style to the first TD cell (not the row)
				for ( var tbi=0, tbcl=tbodyCh.length; tbi < tbcl; tbi++ )
					if ( tbodyCh[ tbi ].nodeName == 'TR' )
						for ( var tdi=0, trCh=tbodyCh[ tbi ].childNodes, tdcl=trCh.length; tdi < tdcl; tdi++ )
							if ( trCh[ tdi ].nodeName == 'TD' ) {
								trCh[ tdi ].style.height = maxCellHeight + 'px';
								tdi = tdcl;
							}
				// now set the table layout to fixed
				$(this).css('table-layout','fixed');
			}
		}*/

		// remove all stub rows
		$('.stubCell', this).remove();

		// add the stub rows
		var stubCount=0, cols = Math.max( $('>thead:first tr:last th,>thead:first tr:last td', this).length, parseInt( $('>thead:first tr:last th,>thead:first tr:last td').attr('colspan') || 0 ) ), 
				stubs = ( perPage - ( $('>tbody>tr', this).length % perPage ) ),
				stubHeight = ($('.jTPS>tbody>tr:first>td:first').outerHeight()) + 'px';
			if(perPage < $('.jTPS tbody tr').length){
				for ( ; stubCount < stubs && stubs != perPage; stubCount++ )
					$('>tbody>tr:last', this).after( '<tr class="stubCell"><td colspan="' + cols + '" style="height: ' + stubHeight + ';">&nbsp;</td></tr>' );
				}
		// paginate the result
		if ( rowCount > perPage && perPage != 0 )
			$('>tbody>tr:gt(' + (perPage - 1) + ')', this).addClass('hideTR');

		// bind sort functionality to theader
		if (perPage != 0)
			$('>thead [sort],>thead .sort', this).each(
				function (tdInd) {
					$(this).addClass('sortableHeader').unbind('click').bind('click',
						function () {
							var columnNo = $('>thead tr:last', pT).children().index( $(this) ),
								desc = $('>thead [sort],>thead .sort', pT).eq(columnNo).hasClass('sortAsc') ? true : false;
							// sort the rows
							sort( pT, columnNo, desc );
							// show first perPages rows
							var page = parseInt( $('.hilightPageSelector:first', pT).html() ) || 1;
							$('>tbody>tr', pT).removeClass('hideTR').filter(':gt(' + ( ( perPage - 1 ) * page ) + ')').addClass('hideTR');
							$('>tbody>tr:lt(' + ( ( perPage - 1 ) * ( page - 1 ) ) + ')', pT).addClass('hideTR');
							// scroll to first page if not already
							if ($('.pageSelector', pT).index($('.hilightPageSelector', pT)) > 0)
								$('.pageSelector:first', pT).click();
							// hilight the sorted column header
							$('>thead .sortDesc,>thead .sortAsc', pT).removeClass('sortDesc').removeClass('sortAsc');
							$('>thead [sort],>thead .sort', pT).eq(columnNo).addClass( desc ? 'sortDesc' : 'sortAsc' );
							// hilight the sorted column
							$('>tbody>tr>td.sortedColumn', pT).removeClass('sortedColumn');
							$('>tbody>tr:not(.stubCell)', pT).each( function () { $('>td:eq(' + columnNo + ')', this).addClass('sortedColumn'); } );
							clearSelection();
							// callback function after pagination renderd
							$(pT).data('tableSettings').clickCallback();
						}
					);
				}
			);

/*		// add perPage selection link + delim dom node
		$('>.nav .selectPerPage', this).empty();
		var pageSel = perPages.length;
		while ( pageSel-- ) 
			$('>.nav .selectPerPage', this).prepend( ( (pageSel > 0) ? $(this).data('tableSettings').perPageDelim : '' ) + 
				'<span class="perPageSelector">' + perPages[pageSel] + '</span>' );*/

		// now draw the page selectors
		drawPageSelectors( this, page || 1 );

/*		// prepend the instructions and attach select hover and click events
		$('>.nav .selectPerPage', this).prepend( $(this).data('tableSettings').perPageText ).find('.perPageSelector').each(
			function () {
				if ( ( parseInt($(this).html()) || rowCount ) == perPage )
					$(this).addClass('perPageSelected');
				$(this).bind('mouseover mouseout', 
					function (e) { 
						e.type == 'mouseover' ? $(this).addClass('perPageHilight') : $(this).removeClass('perPageHilight');
					}
				);
				$(this).bind('click', 
					function () { 
						// set the new number of pages
						perPage = parseInt( $(this).html() ) || rowCount;
						if ( perPage > rowCount ) perPage = rowCount;
						// remove all stub rows
						$('.stubCell', this).remove();
						// redraw stub rows
						var stubCount=0, cols = $('>thead th,>thead td', pT).length, 
							stubs = ( perPage - ( $('>tbody>tr', pT).length % perPage ) ), 
							stubHeight = $('>tbody>tr:first>td:first', pT).css('height');
						for ( ; stubCount < stubs && stubs != perPage; stubCount++ )
							$('>tbody>tr:last', pT).after( '<tr class="stubCell"><td colspan="' + cols + '" style="height: ' + stubHeight + ';">&nbsp;</td></tr>' );
						// set new visible rows
						$('>tbody>tr', pT).removeClass('hideTR').filter(':gt(' + ( ( perPage - 1 ) * page ) + ')').addClass('hideTR');
						$('>tbody>tr:lt(' + ( ( perPage - 1 ) * ( page - 1 ) ) + ')', pT).addClass('hideTR');
						// back to the first page
						$('.pageSelector:first', pT).click();
						$(this).siblings('.perPageSelected').removeClass('perPageSelected');
						$(this).addClass('perPageSelected');
						// redraw the pagination
						drawPageSelectors( pT, 1 );
						// update status bar
						var cPos = $('>tbody>tr:not(.hideTR):first', pT).prevAll().length,
							ePos = $('>tbody>tr:not(.hideTR):not(.stubCell)', pT).length;
						$('>.nav .status', pT).html( 'Показано: ' + ( cPos + 1 ) + ' - ' + ( cPos + ePos ) + ' из ' + rowCount + '' );
						clearSelection();
						// callback function after pagination renderd
						$(pT).data('tableSettings').clickCallback();
					}
				);
			}
		);*/
		
		// show the correct paging status
		var cPos = $('>tbody>tr:not(.hideTR):first', this).prevAll().length, 
			ePos = $('>tbody>tr:not(.hideTR):not(.stubCell)', this).length;
		$('>.nav .status', this).html( ( cPos + 1 ) + ' - ' + ( cPos + ePos ) + ' / ' + rowCount );

		// clear selected text function
		function clearSelection () {
			if ( document.selection && typeof(document.selection.empty) != 'undefined' )
				document.selection.empty();
			else if ( typeof(window.getSelection) === 'function' && typeof(window.getSelection().removeAllRanges) === 'function' )
				window.getSelection().removeAllRanges();
		}

		// render the pagination functionality
		function drawPageSelectors ( target, page ) {

			// add pagination links
			$('>.nav .pagination', target).empty();
			var pages = ( perPage >= rowCount || perPage == 0 ) ? 0 : Math.ceil( rowCount / perPage ), totalPages = pages;
			while ( pages-- )
				$('>.nav .pagination', target).prepend( '<div class="pageSelector">' + ( pages + 1 ) + '</div>' );
			var pageCount = $('>.nav:first .pageSelector', target).length;
			$('>.nav', target).each(function () {
				$('.hidePageSelector', this).removeClass('hidePageSelector');
				$('.hilightPageSelector', this).removeClass('hilightPageSelector');
				$('.pageSelectorSeperator', this).remove();
				$('.pageSelector:lt(' + ( ( page > ( pageCount - 4 ) ) ? ( pageCount - 5 ) : ( page - 2 ) ) + '):not(:first)', this).addClass('hidePageSelector')
					.eq(0).after( '<div class="pageSelectorSeperator">' + $(target).data('tableSettings').perPageSeperator + '</div>' );
				$('.pageSelector:gt(' + ( ( page < 4 ) ? 4 : page ) + '):not(:last)', this).addClass('hidePageSelector')
					.eq(0).after( '<div class="pageSelectorSeperator">' + $(target).data('tableSettings').perPageSeperator + '</div>' );
				$('.pageSelector:eq(' + ( page - 1 ) + ')', this).addClass('hilightPageSelector');
			});

			// remove the pager title if no pages necessary
			if ( perPage >= rowCount )
				$('>.nav .paginationTitle', target).css('display','none');
			else
				$('>.nav .paginationTitle', target).css('display','');
			
			// bind the pagination onclick
			$('>.nav .pagination .pageSelector', target).each(
				function () {
					$(this).bind('click',
						function () {

							// if double clicked - stop animation and jump to selected page - this appears to be a tripple click in IE7
							if ( $(this).hasClass('hilightPageSelector') ) {
								if ( $(this).parent().queue().length > 0 ) {
									// really stop all animations and create new queue
									$(this).parent().stop().queue( "fx", [] ).stop();
									// set the user directly on the correct page without animation
									var beginPos = ( ( parseInt( $(this).html() ) - 1 ) * perPage ), endPos = beginPos + perPage;
									$('>tbody> tr', pT).removeClass('hideTR').addClass('hideTR');
									$('>tbody>tr:gt(' + (beginPos - 2) + '):lt(' + ( perPage ) + ')', pT).andSelf().removeClass('hideTR');
									// update status bar
									var cPos = $('>tbody>tr:not(.hideTR):first', pT).prevAll().length,
										ePos = $('>tbody>tr:not(.hideTR):not(.stubCell)', pT).length;
									$('>.nav .status', pT).html( ( cPos + 1 ) + ' - ' + ( cPos + ePos ) + ' / ' + rowCount + '' );
								}
								clearSelection();
								return false;
							}

							// hilight the specific page button
							$(this).addClass('hilightPageSelector');

							// really stop all animations
							$(this).parent().stop().queue( "fx", [] ).stop().dequeue();

							// setup the pagination variables
							var beginPos = $('>tbody>tr:not(.hideTR):first', pT).prevAll().length,
								endPos = ( ( parseInt( $(this).html() ) - 1 ) * perPage );
							if ( endPos > rowCount )
								endPos = (rowCount - 1);
							// set the steps to be exponential for all the page scroll difference - i.e. faster for more pages to scroll
							var sStep = $(pT).data('tableSettings').scrollStep * Math.ceil( Math.abs( ( endPos - beginPos ) / perPage ) );
							if ( sStep > perPage ) sStep = perPage;
							var steps = Math.ceil( Math.abs( beginPos - endPos ) / sStep );

							// start scrolling
							while ( steps-- ) {
								$(this).parent().animate({'opacity':1}, $(pT).data('tableSettings').scrollDelay,
									function () {
										// reset the scrollStep for the remaining items
										if ( $(this).queue("fx").length == 0 )
											sStep = ( Math.abs( beginPos - endPos ) % sStep ) || sStep;
										/* scoll up */
										if ( beginPos > endPos ) {
											$('>tbody>tr:not(.hideTR):first', pT).prevAll(':lt(' + sStep + ')').removeClass('hideTR');
											if ( $('>tbody>tr:not(.hideTR)', pT).length > perPage )
												$('>tbody>tr:not(.hideTR):last', pT).prevAll(':lt(' + ( sStep - 1 ) + ')').andSelf().addClass('hideTR');
											// if scrolling up from less rows than perPage - compensate if < perPage
											var currRows =  $('>tbody>tr:not(.hideTR)', pT).length;
											if ( currRows < perPage )
												$('>tbody>tr:not(.hideTR):last', pT).nextAll(':lt(' + ( perPage - currRows ) + ')').removeClass('hideTR');
										/* scroll down */
										} else {
											var endPoint = $('>tbody>tr:not(.hideTR):last', pT);
											$('>tbody>tr:not(.hideTR):lt(' + sStep + ')', pT).addClass('hideTR');
											$(endPoint).nextAll(':lt(' + sStep + ')').removeClass('hideTR');
										}
										// update status bar
										var cPos = $('>tbody>tr:not(.hideTR):first', pT).prevAll().length,
											ePos = $('>tbody>tr:not(.hideTR):not(.stubCell)', pT).length;
										$('>.nav .status', pT).html( ( cPos + 1 ) + ' - ' + ( cPos + ePos ) + ' / ' + rowCount + '' );
									}
								);
							}
							
							// redraw the pagination
							drawPageSelectors( pT, parseInt( $(this).html() ) );
							
							// callback function after pagination renderd
							$(pT).data('tableSettings').clickCallback();
							
						}
					);
				}
			);
			
		};
		// sort wrapper function
		function sort ( target, tdIndex, desc ) {
			var fCol = $('>thead th,>thead th', target).get(tdIndex),
				sorted = $(fCol).hasClass('sortAsc') || $(fCol).hasClass('sortDesc') || false,
				nullChar = String.fromCharCode(0), 
				re = /([-]?[0-9\.]+)/g,
				rows = $('>tbody>tr:not(.stubCell)', target).get(), 
				procRow = [];

			$(rows).each(
				function(key, val) {
					procRow.push( $('>td:eq(' + tdIndex + ')', val).text() + nullChar + procRow.length );
				}
			);
			if ( !sorted ) {
				// natural sort
				procRow.sort(
					function naturalSort (a, b) {
						// setup temp-scope variables for comparison evauluation
						var re = /(-?[0-9\.]+)/g,
							nC = String.fromCharCode(0),
							x = a.toString().toLowerCase().split(nC)[0] || '',
							y = b.toString().toLowerCase().split(nC)[0] || '',
							xN = x.replace( re, nC + '$1' + nC ).split(nC),
							yN = y.replace( re, nC + '$1' + nC ).split(nC),
							xD = (new Date(x)).getTime(),
							yD = xD ? (new Date(y)).getTime() : null;
						// natural sorting of dates
						if ( yD )
							if ( xD < yD ) return -1;
							else if ( xD > yD )	return 1;
						// natural sorting through split numeric strings and default strings
						for( var cLoc = 0, numS = Math.max(xN.length, yN.length); cLoc < numS; cLoc++ ) {
							oFxNcL = parseFloat(xN[cLoc]) || xN[cLoc];
							oFyNcL = parseFloat(yN[cLoc]) || yN[cLoc];
							if (oFxNcL < oFyNcL) return -1;
							else if (oFxNcL > oFyNcL) return 1;
						}
						return 0;
					});
				if ( !desc ) procRow.reverse(); // properly position order of sort
			}
			// now re-order the parent tbody based off the quick sorted tbody map
			$('>tbody', target).addClass('jtpstemp').before('<tbody></tbody>');
			var nr = procRow.length, tf = $('>tbody', target)[0];
			// move the row from old tbody to new tbody in order of new tbody with replaceWith to retain original tbody row positioning
			if ( sorted )
				while ( nr-- )
					tf.appendChild( rows[ nr ] );
			else
				while ( nr-- )
					tf.appendChild( rows[ parseInt( procRow[ nr ].split(nullChar).pop() ) ] );
			// remove the old table
			$('>tbody.jtpstemp', target).remove();
			// redraw stub rows
			var stubCount=0, cols = $('>thead>tr:last th', target).length, 
				stubs = ( perPage - ( $('>tbody>tr', target).length % perPage ) );
			if(perPage < $('.jTPS tbody tr').length){
				for ( ; stubCount < stubs && stubs != perPage; stubCount++ )
					$('>tbody>tr:last', target).after( '<tr class="stubCell"><td colspan="' + cols + '" style="height: ' + stubHeight + ';">&nbsp;</td></tr>' );
			}
		}
		// chainable
		return this;
	};

})(jQuery);
