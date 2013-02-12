function zoomToBBox(bbox) {
	if(bbox) {
		var bounds = new OpenLayers.Bounds();
		bounds.extend(new OpenLayers.Geometry.Point(bbox['xmin'], bbox['ymin']));
		bounds.extend(new OpenLayers.Geometry.Point(bbox['xmax'], bbox['ymax']));
		map.zoomToExtent(bounds, true);
	} else {
		alert("Non respondiu nada");
	}
}

function updateEntidadesCBox(cdparro) {
	var cbox = $("#search-entid-cbox");
	cbox.children().remove();
	cbox.append('<option value="' + 0 + '">Escolla...</option>');	
	var search_div = $(".search-entid");
	if(cdparro == 0) {
		search_div.attr('disabled', 'disabled');
		$("#search-entid-btn").button({
			disabled : true
		});
		return;
	};
	cbox.removeAttr('disabled');
	$("#search-entid-btn").button({
		disabled : false
	});
	$.ajax({
		url : "search_place.php",
		type : "GET",
		data : "cdparro=" + cdparro,
		dataType : "json",
		success : function(response) {
			if(response) {
				result = response;
				cbox = $("#search-entid-cbox");
				for(var i = 0; i < result.length; i++) {
					cbox.append('<option value="' + result[i].cod_ine9 + '">' + result[i].nomb10 + '</option>');
				}
			} else {
				alert("Non respondiu nada");
			}
		},
		error : function(response) {
			//console.log(response);
			alert("Error: " + response);
		}
	});
	//ajax()
}; //updateEntidadesCbox()

function updateParroquiasCBox(cdconc) {
	var cbox = $("#search-parro-cbox");
	cbox.children().remove();
	cbox.append('<option value="' + 0 + '">Escolla...</option>');	
	if(cdconc == 0) {
		cbox.attr('disabled', 'disabled');
		$("#search-parro-btn").button({
			disabled : true
		});
		var cdparro = 0;
		updateEntidadesCBox(cdparro);
		return;
	};
	cbox.removeAttr('disabled');
	$("#search-parro-btn").button({
		disabled : false
	});
	$.ajax({
		url : "search_place.php",
		type : "GET",
		data : "cdconc=" + cdconc,
		dataType : "json",
		success : function(response) {
			if(response) {
				result = response;
				cbox = $("#search-parro-cbox");
				for(var i = 0; i < result.length; i++) {
					cbox.append('<option value="' + result[i].codparro + '">' + result[i].nome + '</option>');
				}
			} else {
				alert("Non respondiu nada");
			}
		},
		error : function(response) {
			//console.log(response);
			alert("Error: " + response);
		}
	});
	//ajax()
}; //updateParroquiasCbox()


function updateConcelloCBox(cdprov) {
	var cbox = $("#search-conc-cbox");
	cbox.children().remove();
	cbox.append('<option value="' + 0 + '">Escolla...</option>');	
	if(cdprov == 0) {
		cbox.attr('disabled', 'disabled');
		$("#search-conc-btn").button({
			disabled : true
		});
		var cdconc = 0;
		updateParroquiasCBox(cdconc);
		return;
	};
	cbox.removeAttr('disabled');
	$("#search-conc-btn").button({
		disabled : false
	});
	$.ajax({
		url : "search_place.php",
		type : "GET",
		data : "cdprov=" + cdprov,
		dataType : "json",
		success : function(response) {
			if(response) {
				result = response;
				cbox = $("#search-conc-cbox");
				for(var i = 0; i < result.length; i++) {
					cbox.append('<option value="' + result[i].cdconc + '">' + result[i].nome + '</option>');
				}
			} else {
				alert("Non respondiu nada");
			}
		},
		error : function(response) {
			//console.log(response);
			alert("Error: " + response);
		}
	});
	//ajax()
}; //updateConcelloCbox()

function initSearchDialogCombo(){
		/////////////////////////////
	//Init Provincia ComboBox
	var cbox = $("#search-prov-cbox");
	cbox.children().remove();
	cbox.append('<option value="' + 0 + '">Escolla...</option>');
	$.ajax({
		url : "search_place.php",
		type : "GET",
		dataType : "json",
		success : function(response) {
			if(response) {
				result = response;
				cbox = $("#search-prov-cbox");
				for(var i = 0; i < result.length; i++) {
					cbox.append('<option value="' + result[i].cdprov + '">' + result[i].nome + '</option>');
				}
			} else {
				alert("Non respondiu nada");
			}
		},
		error : function(response) {
			alert("Error");
		}
	});
	//ajax()

	$("#search-prov-btn").click(function() {
		var cbox = $("#search-prov-cbox");
		var value = cbox.val();
		if(value == 0) {
			return;
		}
		$.ajax({
			url : "get-bbox.php",
			type : "GET",
			data : "table=provincia&column=cdprov&code=" + cbox.val(),
			dataType : "json",
			success : zoomToBBox,
			error : function(response) {
				alert("Error na resposta.");
			}
		});
		//ajax()
	});//click()

	
	/////////////////////////////
	//Init Concello ComboBox
	var cbox = $("#search-conc-cbox");
	cbox.append('<option value="' + 0 + '">Escolla...</option>');
	cbox.attr('disabled', 'disabled');
	$("#search-conc-btn").button({
		disabled : true
	});
	$("#search-conc-btn").click(function() {
		var cbox = $("#search-conc-cbox");
		var value = cbox.val();
		if(value == 0) {
			return;
		}
		$.ajax({
			url : "get-bbox.php",
			type : "GET",
			data : "table=concello&column=cdconc&code=" + cbox.val(),
			dataType : "json",
			success : zoomToBBox,
			error : function(response) {
				alert("Error na resposta.");
			}
		});
		//ajax()
	});
	
	/////////////////////////////
	//Init Parroquias ComboBox

	var cbox = $("#search-parro-cbox");
	cbox.append('<option value="' + 0 + '">Escolla...</option>');
	cbox.attr('disabled', 'disabled');
	$("#search-parro-btn").button({
		disabled : true
	});
	$("#search-parro-btn").click(function() {
		var cbox = $("#search-parro-cbox");
		var value = cbox.val();
		if(value == 0) {
			return;
		}
		$.ajax({
			url : "get-bbox.php",
			type : "GET",
			data : "table=parroquia&column=codparro&code=" + cbox.val(),
			dataType : "json",
			success : zoomToBBox,
			error : function(response) {
				alert("Error na resposta.");
			}
		});
		//ajax()
	});

	/////////////////////////////
	//Init Entidad ComboBox
	var cbox = $("#search-entid-cbox");
	cbox.append('<option value="' + 0 + '">Escolla...</option>');
	cbox.attr('disabled', 'true');
	$("#search-entid-btn").button({
		disabled : true
	});
	$("#search-entid-btn").click(function() {
		var cbox = $("#search-entid-cbox");
		var value = cbox.val();
		if(value == 0) {
			return;
		}
		$.ajax({
			url : "get-bbox.php",
			type : "GET",
			data : "table=entidade_singular&column=cod_ine9&code='" + cbox.val()+"'",
			dataType : "json",
			success : zoomToBBox,
			error : function(response) {
				alert("Error na resposta.");
			}
		});
		//ajax()
	});

	$("#search-prov-cbox").change(function() {
	    var value = $(this).val();
	    updateConcelloCBox(value);
	    if (value != 0){
		updateParroquiasCBox(0);
		updateEntidadesCBox(0);
	    }
	    
	});

	$("#search-conc-cbox").change(function() {
	    var value = $(this).val();
	    updateParroquiasCBox(value);
	    if (value != 0){
		updateEntidadesCBox(0);
	    }
	});

	$("#search-parro-cbox").change(function() {
	    updateEntidadesCBox($(this).val());
	});
}

function initSearchDialog() {
	if ($("#search-dialog").children().length == 0) {
		$.get('../tools/search/searchToolDialog.htm', function(template) {
			$(template).appendTo("#search-dialog");
			$(".search-btn").button();
			$("#search-dialog").dialog();
			initSearchDialogCombo();
		});
	} else {
		$(".search-btn").button();
		$("#search-dialog").dialog();
		initSearchDialogCombo();
	} 
	
}