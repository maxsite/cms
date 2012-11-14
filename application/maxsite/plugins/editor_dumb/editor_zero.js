	function addText(t, t2){
		// Derived from Alex King's JS Quicktags code (http://www.alexking.org/). Released under LGPL license. IE support. Modified Max (http://maxsite.org/)
		
		var comment = document.getElementById('f_content');
		if (document.selection) {
			comment.focus();
			sel = document.selection.createRange();
			sel.text = t + sel.text + t2;
			comment.focus();
		}
		else if (comment.selectionStart || comment.selectionStart == '0') {
			var startPos = comment.selectionStart;
			var endPos = comment.selectionEnd;
			var cursorPos = endPos;
			var scrollTop = comment.scrollTop;
			if (startPos != endPos) {
				comment.value = comment.value.substring(0, startPos)
							  + t
							  + comment.value.substring(startPos, endPos)
							  + t2
							  + comment.value.substring(endPos, comment.value.length);
				cursorPos = startPos + t.length
			}
			else {
				comment.value = comment.value.substring(0, startPos)
								  + t
								  + t2
								  + comment.value.substring(endPos, comment.value.length);
				cursorPos = startPos + t.length;
			}
			comment.focus();
			comment.selectionStart = cursorPos;
			comment.selectionEnd = cursorPos;
			comment.scrollTop = scrollTop;
		}
		else {
			comment.value += t + t2;
		}
	}