/**
 * Property to control when the popup is shown.
 */
var _showPopup = true;

function onPopupClose(evt) {
    selectCtrl.unselect(selectedFeature);
}

function createAndShowPopup(){
    if (selectedFeature.popup){
		return;
    };
    popup = new OpenLayers.Popup.FramedCloud("chicken",
					     selectedFeature.geometry.getBounds().getCenterLonLat(),
					     null,

					     '<div class="feat-item-popup"> ' + 
							'	<TABLE class="feat-item-popup" bgcolor="#D0D0D0" border=0 bordercolorlight="#D0D0D0" bordercolordark="#D0D0D0" cellspacing=1>' +
							 '	    <TR valign=top>' +
							 '		<TD width=63 bgcolor="#EAEAEA">' +
							 '			Concello:' +
							 '		</TD>' +
							 '		<TD width=177 bgcolor="#FFFFFF">' +
										selectedFeature.data['concello']+
							 '		</TD>' +
							 '		</TR>' +
							'		<TR valign=top>' +
							'		<TD width=63 bgcolor="#EAEAEA">' +
							'			Parroquia:' +
							'		</TD>' +
							'		<TD width=177 bgcolor="#FFFFFF">' +
										selectedFeature.data['parroquia']+
							'		</TD>' +
							'		</TR>' +
							'		<TR valign=top>' +
							'		<TD width=63 bgcolor="#EAEAEA">' +
							'			Lugar:' +
							'		</TD>' +
							'		<TD width=177 bgcolor="#FFFFFF">' +
										selectedFeature.data['entidade']+
							'		</TD>' +
							'		</TR>' +
							'		<TR valign=top bgcolor="#EAEAEA">' +
							'		<TD width=63>' +
							'			Afectados:' +
							'		</TD>' +
							'		<TD width=177 bgcolor="#FFFFFF">' +
										selectedFeature.data['afectados']+
							'		</TD>' +
							'	</TR>' +
							'	</TABLE>' +
						'</div>' + 
					     '<div><center><button type="button" id="showform" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"' + 
					     ' role="button" aria-disabled="false"><span class="ui-button-text">Ver formulario</span>' + 
					     '</button></center></div>',
					     null, true, 
					     onPopupClose);
    selectedFeature.popup = popup;
    map.addPopup(popup);
    $("#showform").click(function() {
		$("#"+prefix_type_issue+"-dialog-form").emptydata().loaddata(selectedFeature).dialog('open');
    });
    // If the selection is made via feat-item, selectController is called twice...
    selectController(selectedFeature.fid);
}

function onFeatureSelect(event) {
    if (event.feature == selectedFeature){
		return;
    }
    if (event == selectedFeature){
		return;
    }
    if (event.hasOwnProperty('feature')==false){
		return;
    }
    selectedFeature = event.feature;
    if (selectedFeature.feature){
		selectedFeature = selectedFeature.feature;
    }
    if (_showPopup){
		createAndShowPopup();
    };
    _showPopup = true;
}

function onFeatureUnselect(feature) {
    //TODO: Check if there is a popup
    if (feature.popup){
	map.removePopup(feature.popup);
	feature.popup.destroy();
	feature.popup = null;
    }
    selectedFeature = undefined;
    unselectController(feature.fid);
    _showPopup = true;
}

function initFeatureList(vector_layer){
    var features = vector_layer.object.features;
    var i=0;
    for (; i<features.length; i++){
        addFeatureItem(features[i]);
    }
    
    $("#"+prefix_type_issue+"-feature-list").mCustomScrollbar();
	$("#"+prefix_type_issue+"-feature-list").mCustomScrollbar("update");
}

function selectController(feat_fid){
    //Set selectFeatureControl when user navegate on the feat-items
    var activeControls =  map.getControlsBy('active',true);
    for (var i = 0; i < activeControls.length; i++) {
	//console.log(activeControls[i]);
	//It does not work... Why??
	//activeControls[i].deactivate();
    }
    map.getControlsBy('displayClass', 'olControlDrawFeature')[0].deactivate();
    map.getControlsBy('displayClass', 'olControlZoomBox')[0].deactivate();
    selectCtrl.activate();

    var feat = vlayer.getFeatureByFid(feat_fid);
    //Check if already selected on the featList
    var isSelectedOnBox = $("#featitem"+feat_fid).data('selected');
    if (!isSelectedOnBox){
		$("#featitem"+feat_fid).data('selected', true);
		$("#featitem"+feat_fid).addClass('item-selected');
		// If the selection is made via feat-item, selectController is called twice; that check avoids infinity bucles :S
		if (feat != selectedFeature){
	    	if (selectedFeature){
				selectCtrl.unselectAll();
	    		};
	    	_showPopup = false;
	    	selectCtrl.select(feat);
		}
    }
}

function unselectController(feat_fid){
    var feat = vlayer.getFeatureByFid(feat_fid);
    var isSelectedOnBox = $("#featitem"+feat_fid).data('selected');
    if (isSelectedOnBox){
	$("#featitem"+feat_fid).data('selected', false);
	$("#featitem"+feat_fid).removeClass('item-selected');
    }
}

/**
 * Add a featureItem on the alphanumericList and on the map.
 * It creates the handlers to syncronize selections.
 */
function addFeatureItem(feature){
    var attributes = feature.attributes;
    
    if (feature.attributes.id_type==1){
    	prefix_type_issue='seca';
    } else if (feature.attributes.id_type==2){
    	prefix_type_issue='inund';
    }
    var id = attributes.id_issue;
    var fid = feature.fid;
    var feat_fid = 'featitem'+fid;
    
    // When jquery scroll is created we need add ".mCSB_container" on the selector 
    var my_feat_list = $("#"+prefix_type_issue+"-feature-list");
    if ($("#"+prefix_type_issue+"-feature-list .mCSB_container").length != 0){
    	my_feat_list = $("#"+prefix_type_issue+"-feature-list .mCSB_container");
    } 
    my_feat_list.append('<p><div id="'+feat_fid+'" class="feat-item">'+
		'<li><b>Lugar:</b> '+attributes.entidade+'</li>'+
		'<li><b>ID: </b> '+attributes.id_issue+'&nbsp;&nbsp;&nbsp;&nbsp;'+
	    '<b>Afectados: </b> '+attributes.afectados+'</li></div></p>');

    // panTo() when click 
    $("#"+feat_fid).click(function(){
    	selectController(fid);
		map.panTo(selectedFeature.geometry.getBounds().getCenterLonLat());
    });

    $("#"+feat_fid).dblclick(function(){
    	selectController(fid);
		map.panTo(selectedFeature.geometry.getBounds().getCenterLonLat());
		map.zoomTo(map.getZoom()+2);
		createAndShowPopup();
		//// ZoomMaximum
		//map.zoomTo(map.getNumZoomLevels()-1);
    });

    $("#"+feat_fid).mouseenter(function(){
		$(this).fadeOut(10).fadeIn(900);
    });

    $("#"+feat_fid).mouseleave(function(){
		unselectController(fid);
    });
    
    if ($("#"+prefix_type_issue+"-feature-list .mCSB_container").length != 0){
    	$("#"+prefix_type_issue+"-feature-list").mCustomScrollbar("update");
    	$("#"+prefix_type_issue+"-feature-list").mCustomScrollbar("scrollTo","last");
    }
}

function removeFeatureItem(feat_fid){
    var feat = vlayer.getFeatureByFid(feat_fid);
    if (feat){
	onFeatureUnselect(feat);
	$("#featitem"+feat_fid).remove();
	vlayer.removeFeatures(feat);
    }
}

function removeLastFeatureItem(){
    if ($(".new-feat-item:last").length>0){
	$(".new-feat-item:last").remove();
	var num_feat = vlayer.features.length;
	vlayer.removeFeatures(vlayer.features[vlayer.features.length-1]);
    }
}

function fetchNewPoint(event) {
    feature = event.feature;

    $("#"+ prefix_type_issue +"-feature-list").append('<div class="new-feat-item"><li>NOVA INCIDENCIA... </li></div>');

    $(".new-feat-item:last").mouseenter(function(){
		$(this).fadeOut(200).fadeIn(900);
    });

    $(".new-feat-item:last").click(function(){
		removeLastFeatureItem();
    });

	// add parroquia
	$.ajax({
		url: "../php/getposition.php",
		type: "POST",
		data: "lat=" + feature.geometry.x + "&lon=" + feature.geometry.y,
		dataType: "json",
		success: function(response) {
			$("#"+prefix_type_issue+"-dialog-form").emptydata().dialog('open');    
		    $("#"+prefix_type_issue+"-lat").val(feature.geometry.x);
    		$("#"+prefix_type_issue+"-lon").val(feature.geometry.y);

			if (response['concello'] == null || response['provincia'] == null){
				return;
			}
			var conc_cod = response['concello']['cdconc'];
			var conc_name = response['concello']['nome'];
			html = "<option value='" + conc_cod + "'>" +
			 	    conc_name+ "</option>";
			$("#"+prefix_type_issue+"-dialog-form").find('.cd_conc').html(html);

			prov_cod = response['provincia']['cdprov'];
			prov_name = response['provincia']['nome'];
			html = "<option value='" + prov_cod + "'>" +
			 	    prov_name+ "</option>";
			$("#"+prefix_type_issue+"-dialog-form").find('.cd_prov').html(html);	
			
			var parro_name = response['parroquia']['nome'];
			var parro_cod = response['parroquia']['codparro'];
			html = "<option value='" + parro_cod + "'>" +
			 	    parro_name+ "</option>";
			$("#"+prefix_type_issue+"-dialog-form").find('.cd_parro').html(html);

			html = "";
			for (var entidad in response['entidades']) {
				html += "<option value='" + 
							response['entidades'][entidad]['cod_ine9'] + "'>" + 	
							response['entidades'][entidad]['nomb10'] + "</option>";
			}
			$("#"+prefix_type_issue+"-dialog-form").find('.cd_entidade').html(html);
		},
		error: function(response_fail){
			alert(response_fail.responseText);
			removeLastFeatureItem();
		}
	});
	
    ///////////////////////////////
    //TODO Remove this element just when cancel
    //$("#feature-list").children().filter(".new-feat-item").remove();
    ///////////////////////////////
    //TODO Redraw
    //    this.redraw();
    
}

function set_secalayer(){
	vlayer = seca_vlayer;
	if (selectCtrl && !secaIssueCtrl.active){
		inundacionIssueCtrl.deactivate();
		selectCtrl.activate();
	}
	if (vlayer){
		vlayer.setVisibility(true);
		inund_vlayer.setVisibility(false);
	}
	
	prefix_type_issue = 'seca';
	if ($("#tabs").tabs("option", "selected")!=0){
		$("#tabs").tabs("select", 0);
	}
	//alert("Cambiado a seca");
}

function set_inundlayer(){
	vlayer = inund_vlayer;
	if (selectCtrl && !inundacionIssueCtrl.active){
		secaIssueCtrl.deactivate();
		selectCtrl.activate();
	}
	if (vlayer){
		vlayer.setVisibility(true);
		seca_vlayer.setVisibility(false);
	}

    prefix_type_issue = 'inund';
    if ($("#tabs").tabs("option", "selected")!=1){
		$("#tabs").tabs("select", 1);
	}
	//alert("Cambiado a Inundacion");
}

    
function getSecaIssueLayerControl(vector_layer) {
    var issue_lyr_ctl = new OpenLayers.Control.DrawFeature(vector_layer, 
							   OpenLayers.Handler.Point,
							   {title:'Engade un novo punto de seca'});
    vector_layer.events.on({
	    'featureselected': onFeatureSelect,
	    'sketchcomplete': fetchNewPoint});
	    
	issue_lyr_ctl.events.register('activate', issue_lyr_ctl, set_secalayer);

    return issue_lyr_ctl;
}

function getInundacionIssueLayerControl(vector_layer) {
    var issue_lyr_ctl = new OpenLayers.Control.DrawFeature(vector_layer, 
							   OpenLayers.Handler.Point,
							   {title:'Engade un novo punto de Inundación',
							   displayClass: "olControlOtherIssueBtn"});
    vector_layer.events.on({
	    'featureselected': onFeatureSelect,
	    'sketchcomplete': fetchNewPoint});
	    
	issue_lyr_ctl.events.register('activate', issue_lyr_ctl, set_inundlayer);
    return issue_lyr_ctl;
}

function launchHelpDialog(){
    alert("Non se poden introducir incidencias de inundacións neste momento.");
} 

/*
//TODO Remove this function
function getOtherIssueLayerControl(vector_layer) {
    var issue_lyr_ctl = new OpenLayers.Control.Button({
		displayClass: "olControlOtherIssueBtn",
		trigger: launchHelpDialog,
		title:'Engade un novo punto de inundación'
	});

    return issue_lyr_ctl;
}
*/