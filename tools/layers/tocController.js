function initToCLayers(){
	$("#toc-list").append('<form style="display:inline">');

	//Auxlayers
	$("#toc-list").append(
		'<li><input style="display:inline" type="checkbox" name="auxlayers" value="SITGA-Toponimos" > Toponimos </li>'
	);	
	$("#toc-list").append(
		'<li><input style="display:inline" type="checkbox" name="auxlayers" value="SITGA-Limites" checked > Limites </li>'
	);
	
	//Baselayers
	$("#toc-list").append(
		'<li><input style="display:inline" type="radio" name="baselayers" value="PNOA-IDEG" checked > Ortofoto </li>'
	);
	$("#toc-list").append(
		'<li><input style="display:inline" type="radio" name="baselayers" value="Topografico (MTN)" > Topografico (MTN)</li>'
		);
	$("#toc-list").append(
		'<li> <input style="display:inline" type="radio" name="baselayers" value="IGN-Base" > Cartografia IGN </li>'
		);
	$("#toc-list").append(
		'<li><input style="display:inline" type="radio" name="baselayers" value="SITGA-Base" > Cartografía SITGA </li>'
		);
		
	$("#toc-list").append(
		'<li><input style="display:inline" type="radio" name="baselayers" value="Catastro" > Catastro </li>'
		);
	$("#toc-list").append(
		'<li><input style="display:inline" type="radio" name="baselayers" value="empty" > Ningún </li>'
	);		

	$("#toc-list").append('</form>');
	
	
	$("#toc-list input[type=checkbox]").click(function (){
		var wmsLayers = map.getLayersByClass('OpenLayers.Layer.WMS');
		var idx_visible = 0;
		for (var i = 0; i < wmsLayers.length; i++){
			var lyr = wmsLayers[i];
			if (lyr.name == this.value){
				idx_visible = i;
				lyr.setVisibility(this.checked);
			}
		}
	});
	
	$("#toc-list input[type=radio]").click(function (){
		var wmsLayers = map.getLayersByClass('OpenLayers.Layer.WMS');
		//console.log(wmsLayers);
		//[nachouve] For some reason it is needed do this on two bucles  
		//           (first setBaseLayer, after setVisibility) and then set True. 
		var idx_visible = 0;
		for (var i = 0; i < wmsLayers.length; i++){
			var lyr = wmsLayers[i];
			if (!lyr.isBaseLayer){
				continue;
			}
			if (lyr.name == this.value){
				idx_visible = i;
			} else {
				map.setBaseLayer(lyr, false);
			}
		}
		for (var i = 0; i < wmsLayers.length; i++){
			var lyr = wmsLayers[i];
			if (!lyr.isBaseLayer){
				continue;
			}
			if (lyr.name == this.value){
				//lyr.setVisibility(true);
				//lyr.redraw(true);
			} else {
				lyr.setVisibility(false);
			}
		}
		var lyr = wmsLayers[idx_visible];
		lyr.setVisibility(true);
		map.setBaseLayer(lyr, true);
	});
};

