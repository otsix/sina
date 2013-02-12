function initSecaDialog() {
	//Load html code
	$.get('../tools/dialogs/secaDialog.htm', function(template) {		
				
		$(template).appendTo("#issue_dialogs");
		$("#issue_dialogs").hide();
		
	});
}