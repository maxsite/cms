/**
* Cornerz 0.6 - Bullet Proof Corners
* Jonah Fox (jonah@parkerfox.co.uk) 2008
* 
* Usage: $('.myclass').curve(options)
* options is a hash with the following parameters. Bracketed is the default
*   radius (10)
*   borderWidth (read from BorderTopWidth or 0)
*   background ("white"). Note that this is not calculated from the HTML as it is expensive
*   borderColor (read from BorderTopColor)
*   corners ("tl br tr bl"). Specify which borders
*   fixIE ("padding") - attmepts to fix IE by incrementing the property by 1 if the outer width/height is odd.

CHANGELIST from  v0.4

0.5 - Now attempts to fix the odd dimension problem in IE 
0.6 - Added semicolons for packing and fixed a problem with odd border width's in IE

*/
    
;(function($){

  if($.browser.msie && document.namespaces["v"] == null) {
    document.namespaces.add("v", "urn:schemas-microsoft-com:vml", "#default#VML");
  }

  $.fn.cornerz = function(options){
    
    function canvasCorner(t,l, r,bw,bc,bg){
	    var sa,ea,cw,sx,sy,x,y, p = 1.57, css="position:absolute;";
	    if(t) 
		    {sa=-p; sy=r; y=0; css+="top:-"+bw+"px;";  }
	    else 
		    {sa=p; sy=0; y=r; css+="bottom:-"+bw+"px;"; }
	    if(l) 
		    {ea=p*2; sx=r; x=0;	css+="left:-"+bw+"px;";}
	    else 
		    {ea=0; sx=0; x=r; css+="right:-"+bw+"px;";	}
		
	    var canvas=$("<canvas width="+r+"px height="+ r +"px style='" + css+"' ></canvas>");
	    var ctx=canvas[0].getContext('2d');
	    ctx.beginPath();
	    ctx.lineWidth=bw*2;	
	    ctx.arc(sx,sy,r,sa,ea,!(t^l));
	    ctx.strokeStyle=bc;
	    ctx.stroke();
	    ctx.lineWidth = 0;
	    ctx.lineTo(x,y);
	    ctx.fillStyle=bg;
	    ctx.fill();
	    return canvas;
    };

    function canvasCorners(corners, r, bw,bc,bg) {
	    var hh = $("<div style='display: inherit' />"); // trying out style='float:left' 
	    $.each(corners.split(" "), function() {
	      hh.append(canvasCorner(this[0]=="t",this[1]=="l", r,bw,bc,bg));
	    });
	    return hh;
    };

    function vmlCurve(r,b,c,m,ml,mt, right_fix) {
        var l = m-ml-right_fix;
        var t = m-mt;
        return "<v:arc filled='False' strokeweight='"+b+"px' strokecolor='"+c+"' startangle='0' endangle='361' style=' top:" + t +"px;left: "+ l + "px;width:" + r+ "px; height:" + r+ "px' />";
    }
    

    function vmlCorners(corners, r, bw, bc, bg, w) {
      var h ="<div style='text-align:left; '>";
      $.each($.trim(corners).split(" "), function() {
        var css,ml=1,mt=1,right_fix=0;
        if(this.charAt(0)=="t") {
          css="top:-"+bw+"px;";
        }
        else {
          css= "bottom:-"+bw+"px;";
          mt=r+1;
        }
        if(this.charAt(1)=="l")
          css+="left:-"+bw+"px;";
        else {
          css +="right:-"+(bw)+"px; "; // odd width gives wrong margin?
           ml=r;
           right_fix = 1;
        }

        h+="<div style='"+css+"; position: absolute; overflow:hidden; width:"+ r +"px; height: " + r + "px;'>";
        h+= "<v:group  style='width:1000px;height:1000px;position:absolute;' coordsize='1000,1000' >";
        h+= vmlCurve(r*3,r+bw,bg, -r/2,ml,mt,right_fix); 
        if(bw>0)
          h+= vmlCurve(r*2-bw,bw,bc, Math.floor(bw/2+0.5),ml,mt,right_fix);
        h+="</v:group>";
        h+= "</div>"; 
      });
      h += "</div>";
      
      return h;
    };

    var settings = {
      corners : "tl tr bl br",
      radius : 10,
      background: "white",
      borderWidth: 0,
      fixIE: true };              
    $.extend(settings, options || {});
    
    var incrementProperty = function(elem, prop, x) {
      var y = parseInt(elem.css(prop), 10) || 0 ;
      elem.css(prop, x+y);
    };
    
    
    return this.each(function() {
      
      var $$ = $(this);
      var r = settings.radius*1.0;
      var bw = (settings.borderWidth || parseInt($$.css("borderTopWidth"), 10) || 0)*1.0;
      var bg = settings.background;
      var bc = settings.borderColor;
      bc = bc || ( bw > 0 ? $$.css("borderTopColor") : bg);
            
      var cs = settings.corners;

      if($.browser.msie) {//need to use innerHTML rather than jQuery
        h = vmlCorners(cs,r,bw,bc,bg, $(this).width() );     
        this.insertAdjacentHTML('beforeEnd', h);
        
      }
      else  //canvasCorners returns a DOM element
        $$.append(canvasCorners(cs,r,bw,bc,bg));
      
      
      if(this.style.position != "absolute")
        this.style.position = "relative";
     
       this.style.zoom = 1; // give it a layout in IE
      
       if($.browser.msie && settings.fixIE) {
          var ow = $$.outerWidth();
          var oh = $$.outerHeight();
          
          if(ow%2 == 1) {
            incrementProperty($$, "padding-right", 1);
            incrementProperty($$, "margin-right", 1);
          }

          if(oh%2 == 1) { 
            incrementProperty($$, "padding-bottom", 1);
            incrementProperty($$, "margin-bottom", 1);
          }
        }
          
      }
      
    );
 
  };
})(jQuery);
