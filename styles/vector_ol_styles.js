var seca_def_style = new OpenLayers.Style({       
    fillColor: "blue",
    fillOpacity: 0.7, 
    hoverFillColor: "white",
    hoverFillOpacity: 0.8,
    strokeColor: "#DDD",
    strokeOpacity: 1,
    strokeWidth: 2,
    strokeLinecap: "round",
    strokeDashstyle: "solid",
    hoverStrokeColor: "red",
    hoverStrokeOpacity: 1,
    hoverStrokeWidth: 0.2,
    pointRadius: 8,
    hoverPointRadius: 1,
    hoverPointUnit: "%",
    pointerEvents: "visiblePainted",
    cursor: "inherit"
});

var seca_sel_style = new OpenLayers.Style({
    fillColor: "#fff618",//"#ee9900",
    fillOpacity: 0.6, 
    hoverFillColor: "#DDD",
    hoverFillOpacity: 0.8,
    strokeColor: "#DDD",//"#ee9900",
    strokeOpacity: 1,
    strokeWidth: 3,
    strokeLinecap: "round",
    strokeDashstyle: "solid",
    hoverStrokeColor: "red",
    hoverStrokeOpacity: 1,
    hoverStrokeWidth: 0.2,
    pointRadius: 10,
    hoverPointRadius: 1,
    hoverPointUnit: "%",
    pointerEvents: "visiblePainted",
    cursor: "pointer"

});

var seca_mapStyle = new OpenLayers.StyleMap({
    'default': seca_def_style,
    'select': seca_sel_style
});

var inundacion_def_style = new OpenLayers.Style({       
    fillColor: "red",
    fillOpacity: 0.7, 
    hoverFillColor: "white",
    hoverFillOpacity: 0.8,
    strokeColor: "#DDD",
    strokeOpacity: 1,
    strokeWidth: 2,
    strokeLinecap: "round",
    strokeDashstyle: "solid",
    hoverStrokeColor: "red",
    hoverStrokeOpacity: 1,
    hoverStrokeWidth: 0.2,
    pointRadius: 8,
    hoverPointRadius: 1,
    hoverPointUnit: "%",
    pointerEvents: "visiblePainted",
    cursor: "inherit"
});

var inundacion_sel_style = new OpenLayers.Style({
    fillColor: "#fff618",//"#ee9900",
    fillOpacity: 0.6, 
    hoverFillColor: "#DDD",
    hoverFillOpacity: 0.8,
    strokeColor: "#DDD",//"#ee9900",
    strokeOpacity: 1,
    strokeWidth: 3,
    strokeLinecap: "round",
    strokeDashstyle: "solid",
    hoverStrokeColor: "red",
    hoverStrokeOpacity: 1,
    hoverStrokeWidth: 0.2,
    pointRadius: 10,
    hoverPointRadius: 1,
    hoverPointUnit: "%",
    pointerEvents: "visiblePainted",
    cursor: "pointer"

});

var inundacion_mapStyle = new OpenLayers.StyleMap({
    'default': inundacion_def_style,
    'select': inundacion_sel_style
});


/*
// ADD THEMING COLORS DEPENDING OF THE estado_pto
var lookup_def = {
    "ACTUALIZADO" : {fillColor: "#ff2222", strokeColor: "#aa0120"},
    "NUEVO": {fillColor: "#618fff"},
    "": {fillColor: "#fff618"}
}


var lookup_sel = {
    "ACTUALIZADO" : {fillColor: "#dd11122", strokeColor: "#bb1111"},
    "NUEVO": {fillColor: "#618fff"},
    "": {fillColor: "blue"}
}

sipaa_mapStyle.addUniqueValueRules("default", "estado_pto", lookup_def);
sipaa_mapStyle.addUniqueValueRules("select", "estado_pto", lookup_sel);*/

/*
	  styleMap: new OpenLayers.StyleMap({
	  // Set the external graphic and background graphic images.
	  externalGraphic: "../lib/OpenLayers-2.11/img/marker-gold.png",
	  backgroundGraphic: "../lib/OpenLayers-2.11/examples/img/marker_shadow.png",
                        
	  // Makes sure the background graphic is placed correctly relative
	  // to the external graphic.
	  backgroundXOffset: 0,
	  backgroundYOffset: -7,
                        
	  // Set the z-indexes of both graphics to make sure the background
	  // graphics stay in the background (shadows on top of markers looks
	  // odd; let's not do that).
	  graphicZIndex: MARKER_Z_INDEX,
	  backgroundGraphicZIndex: SHADOW_Z_INDEX,
                        
	  pointRadius: 10
	  }),
*/
