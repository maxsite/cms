/**
 * jQuery Ajax Rater Plugin
 *
 * This rater is based on the code Ritesh Agrawal did. Unfortunatly his CSS and the hover technique breaks in some browsers.
 * So i thought, why not use the best CSS star-rater known to man kind and throw it in the mix.
 * I have used the CSS and technique from Komodo Media since it is stable and tested on many, many browsers.
 *
 * This rater compared, has no cancel button. But i think we can live with that :)
 * To avoid conflicts i have changed the function name.
 * 
 * Licensed under The MIT License
 * 
 * @version     1.0
 * @since       03.01.2007
 * @author      Kjell Bublitz <m3nt0r.de@gmail.com
 * @link        http://www.m3nt0r.de/devel/raterDemo/ Demonstration and Documentation
 * @link        http://php.scripts.psu.edu/rja171/widgets/rating.php Based on Ritesh Agrawal Star Rating System
 * @link        http://komodomedia.com/blog/index.php/2007/01/20/css-star-rating-redux/ The Komodo Media CSS Rater Blogpost
 * @license     http://www.opensource.org/licenses/mit-license.php MIT 
 * @package     jQuery Plugins
 * @subpackage  Rater
 */ 

/**
 * Usage: $('#rating').rater('your_servlet', {style:'basic', maxvalue:5, curvalue:0});
 *  
 * @param url The address you want to post the result to. 
 * @param options The style and value attributes
 *
 * Valid options:
 * ---------------------------------------
 *       style:       'basic', 'inline' OR 'small'
 *       maxvalue:    the maximum value / number of stars
 *       curvalue:    the initial value / selected stars
 */ 
jQuery.fn.rater = function(url, options)
{
	if(url == null) return;
	var settings = {
		url       : url, // post changes to 
		maxvalue  : 5,   // max number of stars
		curvalue  : 0,    // number of selected stars
		slug  : ''    // page slug - MAX
	};
	
	if(options) { jQuery.extend(settings, options); };
	jQuery.extend(settings, {cancel: (settings.maxvalue > 1) ? true : false});
	
	var container = jQuery(this);
	jQuery.extend(container, { averageRating: settings.curvalue, url: settings.url });

	if(!settings.style || settings.style == null || settings.style == 'basic') {
		var raterwidth = settings.maxvalue * 25;
		var ratingparent = '<ul class="star-rating" style="width:'+raterwidth+'px">';
	}
	if(settings.style == 'small') {
		var raterwidth = settings.maxvalue * 10;
		var ratingparent = '<ul class="star-rating small-star" style="width:'+raterwidth+'px">';
	}
	if(settings.style == 'inline') {
		var raterwidth = settings.maxvalue * 10;
		var ratingparent = '<span class="inline-rating"><ul class="star-rating small-star" style="width:'+raterwidth+'px">';
	}
	container.append(ratingparent);
	
	// create rater
	var starWidth, starIndex, listitems = '';
	var curvalueWidth = Math.floor(100 / settings.maxvalue * settings.curvalue);
	for(var i = 0; i <= settings.maxvalue ; i++) {
		if (i == 0) {
			listitems+='<li class="current-rating" style="width:'+curvalueWidth+'%;">'+settings.curvalue+'/'+settings.maxvalue+'</li>';
		} else {
			starWidth = Math.floor(100 / settings.maxvalue * i);
			starIndex = (settings.maxvalue - i) + 2;
			listitems+='<li class="star"><a href="#'+i+'" title="'+i+'/'+settings.maxvalue +'" style="width:'+starWidth+'%;z-index:'+starIndex+'">'+i+'</a></li>';
		}
	}
	container.find('.star-rating').append(listitems); // i am using find here, because the span wrapped in the small style would break children()

	if(settings.maxvalue > 1) // add a container for the ajax result
	{
		container.append('<span class="star-rating-result"></span>'); 
	}
	var stars = jQuery(container).find('.star-rating').children('.star');
	stars.click(function()
	{
		if(settings.maxvalue == 1) // on / off
		{
			settings.curvalue = (settings.curvalue == 0) ? 1 : 0;
			jQuery(container).find('.star-rating').children('.current-rating').css({width:(settings.curvalue*100)+'%'});
			jQuery.post(container.url, { "rating": settings.curvalue, "slug": settings.slug });
			return false;
		}
		else
		{
			settings.curvalue = stars.index(this) + 1;
			raterValue = jQuery(this).children('a')[0].href.split('#')[1];
			jQuery.post(container.url, { "rating": raterValue, "slug": settings.slug  }, function(response){
				container.children('.star-rating-result').html(response)	
			});
			return false;
		}
		return true;
	});

	return this; // strict warning: anonymous function does not always return a value. fix?
}