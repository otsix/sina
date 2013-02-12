/**
 * 
 */
// Validamos el formulario

//===================================================
// formularios a validar
var validationRequired = {'issue_seca': true};

// campos que se van a validar y validación necesaria
var validations = {
	'afectados': 'required',
	'observacions': 'characters',
	'proposta_solucion': 'characters'
}

// caracteres permitidos no permitidos en los campos
var errorcharacters = new Array('"', "'");
//====================================================

function validateForm(forms) {
	var forms = $(forms).find('form');
	var valid = true;
	for (i=0; i<forms.length; i++) {
		if (validationRequired[forms[i].id] == true) {
			$.map($(forms[i]).serializeArray(), function (n,i) {
				switch (validations[n['name']])
				{
					case 'required':
						if ($("#" + n['name']).val() === "") {
							valid = false;
							$("#" + n['name']).errorStyle();							
						}
						break;
					case 'characters':
						if ($("#" + n['name']).detectBadCharacters()) {
							valid = false;
							$("#" + n['name']).errorStyle();
						}
						break;

				}
			});
		}
	};
	return valid;
}

$.fn.errorStyle = function() {
	$(this).css('background-color', '#FE2E2E');
}

$.fn.eliminateErrorStyle = function() {
	$.map($(this).serializeArray(), function (n,i) {
		$("#" + n['name']).css({'background-color' : ''});
	});
}
$.fn.detectBadCharacters = function() {
	var result = false;	
	for (var character in errorcharacters) {
		if ($(this).val().indexOf(errorcharacters[character], 0) !== -1) {
			result = true;
			break;
		}
	}
	return result;
}
