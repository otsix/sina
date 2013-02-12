<?php
   session_start();
   if (!isset($_SESSION['user_id'])) {
       header("Location: main_login.php?logout");
       exit;
   }
?>

<html>
  <head>
    <title>SINA -- Notificacion Adversos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link rel="stylesheet" href="../lib/jquery-style/style.css" type="text/css"/>
	<link rel="stylesheet" href="../styles/mapa_lot_of_styles.css" type="text/css" />
    <link type="text/css" href="../styles/custom-theme/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	

	<script type="text/javascript">
		var lon = 5;
		var lat = 40;
		var zoom = 5;
		var map, layer, vlayer, seca_vlayer, inund_vlayer;
		var prefix_type_issue = '';
		var pnoaLayer, topoLayer, sitgaLayer, catastroLayer;
		var drawControls, selectCtrl, selectedFeature;
		var secaIssueCtrl, inundacionIssueCtrl;  
		
	</script>
    <script type="text/javascript" src="../lib/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="../lib/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="../lib/ol/OpenLayers.js" ></script>
    <script type="text/javascript" src="../lib/js/jquery.tmpl.js" ></script>

    <script type="text/javascript" src="../tools/issue/issue-form.js" ></script>
    <script type="text/javascript" src="../tools/layers/tocController.js" ></script>
    <script type="text/javascript" src="../tools/layers/layers.js" ></script>
    <script type="text/javascript" src="../tools/issue/issueLayerControl.js"></script>
    <script type="text/javascript" src="../tools/search/searchTool.js" ></script>

    <!-- Dialogs -->
	<script type="text/javascript" src="../tools/dialogs/mapeos.js" ></script>
	<script type="text/javascript" src="../tools/dialogs/validation-sina.js" ></script>
    <script type="text/javascript" src="../tools/dialogs/secaDialog.js"></script>
    <script type="text/javascript" src="../tools/dialogs/inundacionDialog.js"></script>

    <!-- To lead with the IE stupidity -->
    <script type="text/javascript" src="../lib/js/json2.js" ></script>
    
    <!-- Nice scrollbars -->
	<link href="../lib/js/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
	<script src="../lib/js/jquery.mousewheel.min.js"></script>
	<script src="../lib/js/jquery.mCustomScrollbar.min.js"></script>

    <link type="text/css" href="../styles/feature-list.css" rel="stylesheet" />	
    <script type="text/javascript" src="../styles/vector_ol_styles.js"></script>    

    <script type="text/javascript">


	function init(){
	    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
	    OpenLayers.Util.onImageLoadErrorColor = "transparent";

	    //      layer = new OpenLayers.Layer.WMS( "OpenLayers WMS", "http://vmap0.tiles.osgeo.org/wms/vmap0", {layers: 'basic'} );
	
	    ////TODO Still reading 'entidades_singulares' layer instead 'incidencias'
	    seca_vlayer = new OpenLayers.Layer.Vector("SECA_LAYER", {
		    styleMap: seca_mapStyle,
		    strategies: [new OpenLayers.Strategy.Fixed()],
		    protocol: new OpenLayers.Protocol.HTTP({
			    url: "get_all_issues.php",
			    params: {
			    	"issue_type": 1 //"seca"
			    },
			    format: new OpenLayers.Format.GeoJSON({
				    extractStyles: true,
				    extractAttributes: true,
				    maxDepth: 2
				})
			})
		});
		
	    //JSON is totally received
	    seca_vlayer.events.register("loadend", seca_vlayer, initFeatureList);
	    
	    inund_vlayer = new OpenLayers.Layer.Vector("INUNDACION_LAYER", {
		    styleMap: inundacion_mapStyle,
		    strategies: [new OpenLayers.Strategy.Fixed()],
		    protocol: new OpenLayers.Protocol.HTTP({
			    url: "get_all_issues.php",
			    params: {
			    	"issue_type": 2 //"inundacion"
			    },
			    format: new OpenLayers.Format.GeoJSON({
				    extractStyles: true,
				    extractAttributes: true,
				    maxDepth: 2
				})
			})
		});
	    
	    //JSON is totally received
	    inund_vlayer.events.register("loadend", inund_vlayer, initFeatureList);
	
		//WARNING: vlayer have to change between seca_vlayer|inund_vlayer 
		set_secalayer();
	
	    var bounds = new OpenLayers.Bounds(460000,4625000,690000,4855000);
	
	    map = new OpenLayers.Map('map', {
	 	    projection: new OpenLayers.Projection("EPSG:23029"),
		    displayProjection: new OpenLayers.Projection("EPSG:23029"),
		    units: "m",
		    resolutions: [611.4962262814,305.7481131407,152.8740565704,76.4370282852,
				  38.2185141426,19.1092570713,9.5546285356,4.7773142678,
				  2.3886571339,1.1943285670,0.5971642835,0.2985821417],
			tileSize: new OpenLayers.Size(512,512),
		    maxExtent: bounds
		});
	    map.addControl(new OpenLayers.Control.PanZoom());
	
	    var panel = new OpenLayers.Control.Panel();
	    selectCtrl = new OpenLayers.Control.SelectFeature([seca_vlayer, inund_vlayer],
	    							{title:'navega polo mapa e selecciona punto', 
									onSelect: onFeatureSelect, 
								    onUnselect: onFeatureUnselect});
		//TODO
		//  "selectCtrl" para inundacion???
		//selectCtrl.events.register('activate', selectCtrl, function(){
		//	alert('oleeeee');
		//});
		
	    secaIssueCtrl = getSecaIssueLayerControl(seca_vlayer);
	    inundacionIssueCtrl = getInundacionIssueLayerControl(inund_vlayer);
	    // Create a new tool to zoom to council extent
	    var zoomToBoundsCtrl = new OpenLayers.Control.ZoomToMaxExtent(
		{title:'Zoom ao concello completo',
		 trigger: function() {
		     if (this.map){
			 this.map.zoomToExtent(bounds, true);
		     }
		 }
		});
	
	    panel.addControls([secaIssueCtrl,
	    		   inundacionIssueCtrl,
			       selectCtrl,
			       new OpenLayers.Control.ZoomBox({title:"Zoom de rectángulo"}),
			       zoomToBoundsCtrl
			      ]);
	    map.addControl(panel);
		map.addControl(new OpenLayers.Control.ScaleLine({bottomOutUnits:'', bottomInUnits:''}));
    	map.addControl(new OpenLayers.Control.MousePosition({numDigits:0}));

	    map.addLayers(getLayers());
	    map.addLayer(seca_vlayer);
	    map.addLayer(inund_vlayer);
	    
	    //<!-- Catch bbox of council and zoom -->
	    bounds = new OpenLayers.Bounds();
	    <?php
	        echo 'bounds.extend(new OpenLayers.Geometry.Point(' . $_SESSION['xmin'] . ',' . $_SESSION['ymin'] . "));\n";
	        echo 'bounds.extend(new OpenLayers.Geometry.Point(' . $_SESSION['xmax'] . ',' . $_SESSION['ymax'] . "));\n";
	    ?>
	    map.zoomToExtent(bounds, true);
	    secaIssueCtrl.activate();
	}
	$(function() {
		$("#tabs").tabs();
		$("#seca-issues-tab-name").click(set_secalayer);
		$("#inund-issues-tab-name").click(set_inundlayer);
		set_secalayer();
	});
	$("#toc-list").children(":first").click();

</script>

  </head>
  <body onload="init()">
    <div id="cabecera">
      <div id="iet"><img  height="54" src="../images/logoIET.png"></div>
      <div id="sina"><img  src="../images/logo_sina.jpg"></div>
      <div id="xunta"><center><img  height="54" src="../images/xunta-logo.png"></center></div>
	</div>
    <div id="mappanel">
          <div id="map" class="smallmap">
    	     <!-- myTools: find, information, help -->
				<div id="myControls" class="myControls">
					<a href="javascript:initSearchDialog();"><div id="myControlFind" class="myControlFind" title="Buscador xeografico"></div></a>
                  	<!-- <div id="myControlInfo" class="myControlInfo" title="Informacion da capa"></div> -->
                    <a href="../docs/axuda.pdf" target="_blank"><div id="myControlHelp" class="myControlHelp" title="Axuda"></div></a>
				</div>
       <!-- myTools End -->
         </div>
    </div>
    <div id="foot_panel" >
      <div style="float: left; width: 13%;">
	<img height="33px" src="../images/logo_cmati.png">
      </div>
      <div style="float: left; width:70%;"><ul style="color:white;font-size:9px;font-family:Verdana,Tahoma;margin-top:5px"><li>&copy; Xunta de Galicia. Información mantida e publicada na internet pola Xunta de Galicia</li><li><a target="_blank" style="text-decoration:none; color:white" href="http://www.xunta.es/oficina-de-rexistro-unico-e-informacion">Oficina de Rexistro Único e Información</a> | <a target="_blank" style="text-decoration:none; color:white" href="http://www.xunta.es/suxestions">Suxestión e queixas</a> | <a target="_blank" style="text-decoration:none; color:white" href="http://www.xunta.es/aviso-legal-do-portal-da-xunta">Aviso legal</a> | <a target="_blank" style="text-decoration:none; color:white" href="http://www.xunta.es/atendemolo">Atendémolo/a</a></li></ul></div>	</div>

      <div id="boxContainer">
      <div id="tabs"> 
	<ul>
	  <li><a href="#seca-issues-tab" id="seca-issues-tab-name">
	  	Seca
	  </a></li>
	  <li><a href="#inund-issues-tab" id="inund-issues-tab-name" >
	  	Inundac.
	  </a></li>
	  <li><a href="#toc-tab">
	  	Carto</a>
	  </li>
	</ul>
	<div id="seca-issues-tab">																	
	  <div style="font-weight:bold"><span id="concello-name"><?php echo $_SESSION['conc_name'] ?></span> </div>
	  <ul id="seca-feature-list" class="feature-list">
	  </ul>
	</div>
	
	<div id="inund-issues-tab">																	
	  <div style="font-weight:bold"><span id="concello-name"><?php echo $_SESSION['conc_name'] ?></span> </div>
	  <ul id="inund-feature-list" class="feature-list">
	  </ul>
	</div>
	
	<div id="toc-tab">																	
	 <!-- <div style="font-weight:bold"> TOC </div> -->		
	  <ul id="toc-list" style="list-style-type: none; display: left; margin-left: 0px; padding-left: 0px;">	    
	  </ul>
	</div> <!--toc-tab-->
	</div> <!--tabs-->
      </div> <!--boxContainer-->
		 <div id="issue_dialogs">
		 	<!-- Dialogs are loaded dinamically: see initSecaDialog() --> 
		 </div>
		 <div id="search-dialog" title="Buscador">
		 	<!-- Load dinamically -->
		 </div>
		 <!-- To Upload files -->
		 <div id="hidden-controls" style="position:absolute;width:510px;height:57px;left:20px;top:923px;z-index:1230;visibility: hidden">
		 	<form id="upload-hidden-form" enctype="multipart/form-data" method="post" action="../php/upload-file.php" target="iframeUpload">
		 		<input type="file" name="file-upload" id="file-upload" size="35" />
		 		<input type="hidden" name="id_issue" id="id_issue" value="" />
		 		<input type="submit" value="Enviar" id="file-upload-submit" />
		 		<iframe name="iframeUpload"></iframe>
		 	</form>
		 </div>
		<script>
			initToCLayers();
			initSearchDialog();	
			initSecaDialog();
			initInundacionDialog();
		</script>

	<div id="menu_panel" style="position:absolute;bottom:50px">
		<!--<ul>
			<li id="user_name">Usuario </li>
			<li id="user_concello">Concello</li> 
			<li id="logout"> -->
				<a href="/sina/">Salir</a>
		<!-- </li>
		</ul> -->
	</div>
		
		
    </body>
  </html>