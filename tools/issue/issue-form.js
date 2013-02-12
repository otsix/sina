/**
 * @author michogarcia
 */

$(function() {

	$.fn.serializeIssue = function() {
		var forms = $("form", this);
		var jsonfinal = new Object();

		for( i = 0; i < forms.length; i++) {
			if($(forms[i]).serializeArray().length != 0) {
				//console.log($(forms[i]).serializeJSON());
				jsonfinal[$(forms[i]).attr('id')] = $(forms[i]).serializeJSON();
			}
		};
		jsonfinal['issue'] = {
			id_status : 1,
			active : 'true'
		}
		return jsonfinal;
	};

	function countelements(arr) {
		contador = 0;
		for(var key in arr) {
			contador++;
		}
		return contador;
	}


	$.fn.serializeForm = function() {
		var $inputs = $(this).find(':input');
		return $inputs;
	}

	$.fn.serializeJSON = function() {
		var json = {};
		jQuery.map($(this).serializeForm(), function(n, i) {
			if(n['value'] === "") {
				return;
			}
			if($(n).attr('tag') === 'string')
				if($("#" + n['name']).attr('auxvalue') != undefined)
					json[n['name']] = "'" + $(n).attr('tag').attr('auxvalue') + "'";
				else {
					json[n['name']] = "'" + n['value'] + "'";
				}
			else if($("#" + n['name']).attr('auxvalue') != undefined)
				json[n['name']] = $("#" + n['name']).attr('auxvalue');
			else {
				if($("#" + n['name']).attr('type') === 'checkbox') {
					if($("#" + n['name']).is(':checked')) {
						json[n['name']] = 'true';
					} else {
						json[n['name']] = 'false';
					}
				} else
					json[n['name']] = n['value'];
			}
		});
		if(countelements(json) != 0) {
			return json;
		} else {
			return -1;
		}
	};

	$("#dialog:ui-dialog").dialog("destroy");

	function updateTips(t) {
		tips.text(t).addClass("ui-state-highlight");
		setTimeout(function() {
			tips.removeClass("ui-state-highlight", 1500);
		}, 500);
	}

	////////////////////////////////////////////////////
	////////// Here funtions to use on the dialogs

	var createIssueButtonFunction = function() {
		if(!validateForm($("#"+prefix_type_issue+"-dialog-form"))) {
			return;
		}
		var formdata = $("#"+prefix_type_issue+"-dialog-form").serializeIssue();
		var file_name = '';
		if($('#file-upload').attr('tag') === 'loaded') {
			file_name = $('#file-upload').val().slice(12, $('#file-upload').val().length);
			$('#file-upload-submit').click();
		}
		var data = (file_name != '') ? "json=" + JSON.stringify(formdata) + "&file=" + file_name : "json=" + JSON.stringify(formdata);
		$.ajax({
			url : "../php/insert-data.php",
			type : "POST",
			data : data+'&issue_type='+prefix_type_issue,
			dataType : "json",
			success : function(response) {
				removeLastFeatureItem();
				var feature = new OpenLayers.Format.GeoJSON().read(response['feature'], 'Feature');
				if (!feature){
					alert("Error en la inserción");
					return;
				}
				vlayer.addFeatures(new Array(feature));
				addFeatureItem(feature);
				vlayer.redraw();
				alert("Incidencia enviada con éxito");
			}
		});
		$(this).dialog("close");
	}
	
	var editIssueButtonFunction = function() {
		var file_name = '';
		if(!validateForm($("#"+prefix_type_issue+"-dialog-form"))) {
			return;
		}
		$('#upload-hidden-form').children("input[id=id_issue]").attr('value', selectedFeature.fid);
		var formdata = $("#"+prefix_type_issue+"-dialog-form").serializeIssue();
		if($('#file-upload').attr('tag') === 'loaded') {
			$('#file-upload-submit').attr('id_issue', selectedFeature.fid);
			file_name = $('#file-upload').val().slice(12, $('#file-upload').val().length);
			$('#file-upload-submit').click();
		}
		var data = (file_name != '') ? "json=" + JSON.stringify(formdata) + "&id=" + selectedFeature.fid + "&file=" + file_name : "json=" + JSON.stringify(formdata) + "&id=" + selectedFeature.fid;
		$.ajax({
			url : "../php/update-data.php",
			type : "POST",
			data : data+'&issue_type='+prefix_type_issue,
			dataType : "json",
			success : function(response) {
				removeFeatureItem(selectedFeature.fid);
				var feature = new OpenLayers.Format.GeoJSON().read(response['feature'], 'Feature');
				if (!feature){
					alert("Error en la edición");
					return;
				}
				vlayer.addFeatures(new Array(feature));
				addFeatureItem(feature);
				vlayer.redraw();
				alert("Cambios notificados con éxito.");
			}
		});
		$(this).dialog("close");
	}
	
	var deleteIssueButtonFunction = function() {
		//TODO Confirmacion del borrado
		if(confirm('ATENCION: ¿Seguro que desea ELIMINAR esta incidencia?')) {
			$.ajax({
				url : "../php/delete-data.php",
				type : "POST",
				data : "id=" + selectedFeature.fid,
				dataType : "text",
				success : function(response) {
					removeFeatureItem(selectedFeature.fid);
					alert("Incidencia eliminada.");
				}
			});
			$(this).dialog("close");
		}
	}
	
	var addFile = function(evt) {
		// create upload file control
		$('#file-upload').click().change(function(e) {
			$('#file-upload').attr('tag', 'loaded');
			if (selectedFeature != undefined){
				$("#"+prefix_type_issue+"-dialog-form").dialog('option', 'buttons', dialog_edit_file_buttons);
			} else {
				$("#"+prefix_type_issue+"-dialog-form").dialog('option', 'buttons', dialog_new_file_buttons);
				$("#"+prefix_type_issue+"-download-file-link").children("#file_path").html(response.ruta)
			}
			$("#"+prefix_type_issue+"-download-file-pane").css('visibility', '');
		})
	}

	var deleteNewFile = function(evt) {
		$("#" + prefix_type_issue + "-download-file-pane").css('visibility', 'hidden');
		$('#file-upload').attr('tag', '');
		$('#file-upload').val('');
		$("#" + prefix_type_issue + "-dialog-form").dialog('option', 'buttons', new_buttons);
	}

	var deleteEditFile = function(evt) {
		if(confirm('ATENCION: ¿Seguro que desea ELIMINAR o ficheiro asociado?')) {
			$.ajax({
				url : "../php/delete-arquivo.php",
				type : "POST",
				data : "id_issue=" + selectedFeature.fid,
				dataType : "json",
				success : function(response) {
					$("#"+prefix_type_issue+"-download-file-pane").css('visibility', 'hidden');
					$('#file-upload').attr('tag', '');
					$('#file-upload').val('');
					$("#"+prefix_type_issue+"-dialog-form").dialog('option', 'buttons', edit_buttons);
				},
				error : function() {
					// TODO error message
				}
			});
		}
	}
	
	var dialog_opts = {
		autoOpen : false,
		height : 600,
		width : 580,
		modal : true,
		hide : {
			effect : 'fade',
			duration : 800
		},
		open : function() {
			$('.ui-widget-overlay').hide().fadeIn(800);
		},
		close : function() {
		}
	}

	var new_buttons = {
		"Crea novo adverso" : createIssueButtonFunction,
		"Cancelar" : function() {
			removeLastFeatureItem();
			$(this).dialog("close");
		}
	}

	var dialog_new_buttons = {
		title : "Notificar novo adverso",
		buttons : new_buttons,
		close : function() {
			removeLastFeatureItem();
		}
	}

	var dialog_new_file_buttons = {
		"Crea novo adverso" : createIssueButtonFunction,
		"Eliminar arquivo" : deleteNewFile,
		"Cancelar" : function() {
				removeLastFeatureItem();
				$(this).dialog("close");
		}
	}

    var dialog_opts = {
	    autoOpen : false,
	    height : 600,
	    width : 580,
	    modal : true,
	    hide : { effect: 'fade', duration: 800 },
	    open: function(){
		$('.ui-widget-overlay').hide().fadeIn(800);
		$("ul.form-section2").show();
		$("ul.form-section").hide();
	    },
	    close : function() {
	    	$(this).dialog("close");
	    }
	}

	var edit_buttons = {
		//TODO
		"Enviar cambios" : editIssueButtonFunction,
		"Eliminar incidencia**" : deleteIssueButtonFunction,
		"Engadir arquivo" : addFile,
		"Cancelar" : function() {
			$(this).dialog("close");
		}
	}

	var dialog_edit_buttons = {
		title : "Datos do adverso",
		buttons : edit_buttons,
		close : function() {
		}
	}

	var dialog_edit_file_buttons = {
		//TODO
		"Enviar cambios" : editIssueButtonFunction,
		"Eliminar incidencia**" : deleteIssueButtonFunction,
		"Eliminar arquivo" : deleteEditFile,
		"Cancelar" : function() {
			$(this).dialog("close");
		}
	}

	// Dialog is created with different options depending on the action to perform (new or edit)
	$("#"+prefix_type_issue+"-dialog-form").dialog($.extend({}, dialog_opts, dialog_new_buttons));

	$.fn.loaddata = function(feature) {
		$(this).dialog.buttons = undefined;
		$(this).dialog($.extend({}, dialog_opts, dialog_edit_buttons));
		var array_attr = feature.attributes;
		for(var attribute in array_attr) {
			if(array_attr[attribute] === 't') {
				$(this).find("[name=" + attribute+"]").attr('checked', true);
			} else {
				if($(this).find("[name=" + attribute+"]").is('select')) {
					//console.log(attribute + " : " + array_attr[attribute]);
					if(mapeo_campos[attribute] !== undefined) {
						// No permite modificar el lugar Para eso habrá que hacer una consulta al servidor
						html = "<option value='" + array_attr[attribute] + "'>" + array_attr[mapeo_campos[attribute]] + "</option>";
						$(this).find("[name=" + attribute+"]").html(html)
					} else {
						$(this).find("[name=" + attribute+"]").val(array_attr[attribute]);
					}
				}
			}
			if(mapeo_campos[attribute] !== undefined) {
				$(this).find("[name=" + attribute+"]").val(array_attr[mapeo_campos[attribute]].replace(/@#!/gi, '\r\n'))
			} else {
				$(this).find("[name=" + attribute+"]").val(array_attr[attribute].replace(/@#!/gi, '\r\n'));
			}
		}
		$(this).showFileImage(feature.fid);
		$("#lat").val(feature.geometry.x);
		$("#lon").val(feature.geometry.y);
		return $(this);
	}

	$.fn.showFileImage = function(id_issue) {
		$.ajax({
			url : "../php/arquivo-exits.php",
			type : "POST",
			data : "id_issue=" + id_issue,
			dataType : "json",
			success : function(response) {
				if(response.id != undefined) {
					$("#"+prefix_type_issue+"-dialog-form").dialog('option', 'buttons', dialog_edit_file_buttons);
					$("#"+prefix_type_issue+"-download-file-pane").css('visibility', '');
					$("#"+prefix_type_issue+"-download-file-link").children("#file_path").html(response.ruta)
					$("#"+prefix_type_issue+"-download-file-link").click(function() {
						window.open('http://' + location.host + '/' + response.ruta);
					})
				} else {
					$("#"+prefix_type_issue+"-download-file-pane").css('visibility', 'hidden');
				}
			},
			error : function() {
				// TODO error message
			}
		});
	}

	$.fn.emptydata = function() {
		$(this).dialog.buttons = undefined;
		$(this).dialog($.extend({}, dialog_opts, dialog_new_buttons));
		$(this).find('form').eliminateErrorStyle();
		$.map($("#"+prefix_type_issue+"-dialog-form :input"), function(n, i) {
			if($(n).is('select'))
				$(n).val(0);
			else if($(n).attr('type') === 'checkbox')
				$(n).attr('checked', false);
			else if($(n).attr('type') === 'text')
				$(n).val("");
			else if($(n).is('textarea'))
				$(n).val("");
		});
		$("#"+prefix_type_issue+"-download-file-pane").css('visibility', 'hidden');
		$('#file-upload').attr('tag', '');
		$('#file-upload').val('');
		return $(this);
	}
});
