function initInundacionDialog() {
	
	if ($("#issue_dialogs").children("#issue_inundacion").length == 0) {
	//Load html code
		$.get('../tools/dialogs/inundacionDialog.htm', function(template) {				
			$(template).appendTo("#issue_dialogs");
			$("#issue_dialogs").hide();
		});
	}
}