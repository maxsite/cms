  function addSmile(t, elem_id){
    // Derived from Alex King's JS Quicktags code (http://www.alexking.org/). Released under LGPL license. IE support. Modified Max (http://maxsite.org/). Cutted by quantum (http://maxsitecms.ru/).
    
	if (!elem_id) 
	{ 
		elem_id = 'comments_content'; 
	}
	
    var comment = document.getElementById(elem_id);
    
    if (document.selection) {
      comment.focus();
      sel = document.selection.createRange();
      sel.text = sel.text + t;
      comment.focus();
    }
    else if (comment.selectionStart || comment.selectionStart == '0') {
      var cursorPos = comment.selectionEnd;
      var scrollTop = comment.scrollTop;

      comment.value = comment.value.substring(0, cursorPos)
                  + t
                  + comment.value.substring(cursorPos, comment.value.length);
        cursorPos = cursorPos + t.length;

      comment.focus();
      comment.selectionStart = cursorPos;
      comment.selectionEnd = cursorPos;
      comment.scrollTop = scrollTop;
    }
    else {
      comment.value += t;
    }
  }
  

$(document).ready(function() {

    $(".btn-smiles").hover(function() {
        $(this).toggleClass("btn-hov1");
    });
    
    $(".btn-smiles").click(function() {
        $(".comment_smiles").slideToggle("slow").toggleClass("show");
        $(this).toggleClass("btn-hov2");
    });
});
