function getLayers() {

		sitgaPnoaLayer = new OpenLayers.Layer.WMS("PNOA-IDEG",
						     "http://ideg.xunta.es/wms_orto_2007-08/request.aspx", {
							 layers: "Ortofoto_2007_08"
						     }, {
							 isBaseLayer: true,
							 buffer: 0,
							 transitionEffect: 'resize',
							 visibility: true
						     });

	    pnoaLayer = new OpenLayers.Layer.WMS("IDEE-PNOA",
						     "http://www.idee.es/wms/PNOA-MR/PNOA-MR", {
							 layers: "PNOA-MR"
						     }, {
							 isBaseLayer: true,
							 buffer: 0,
							 transitionEffect: 'resize',
							 visibility: false
						     });
						    						     
		mtnLayer = new OpenLayers.Layer.WMS("Topografico (MTN)", 
							"http://www.idee.es/wms/MTN-Raster/MTN-Raster", {
							layers: "mtn_rasterizado"
						    }, {
							 isBaseLayer: true,
							 buffer: 0,
							 opacity: 0.8,
							 transitionEffect: 'resize',
							 visibility: false
						     
							});
	
		topoLayer = new OpenLayers.Layer.WMS("IGN-Base",
						     "http://www.ign.es/wms-inspire/ign-base", {
							 layers: "IGNBaseTodo"
					    	 }, {
						 	 isBaseLayer: true,
						 	 transitionEffect: 'resize',
						 	 buffer: 0,
						 	 opacity: 0.6,
						 	 visibility: false
					     	 }
					     	 );
					     	 
		sitgaLayer = new OpenLayers.Layer.WMS("SITGA-Base",
						     "http://ideg.xunta.es/wms/request.aspx", {
					     	 layers: ["Toponimia_txt_1_5000","CONCELLO","Hidrografia_txt_1_5000",
					     	 		 "NOMECONCELLO","TOPONIMIA_COSTA","TXT_CIDADES","TXT_CIDADES_B",
					     	 		 "TXT_PARROQUIA","TXT_VILAS","Edificacions_1_5000","Hidrografia_1_5000",
					     	 		 "IGREXA","VILAS","CIDADES_B","CIDADES","SECUNDARIA","AUTOESTRADA_AUTOVIA",
					     	 		 "REDE_ESTATAL","PRIMARIA_BASICA","CORREDOR","PRIMARIA_COMPLEMENT","PRESA",
					     	 		 "ENCORO","RIO_DOBLE","RIOS","PARROQUIA","PROVINCIA"]					     	 
							 }, {
						 	 isBaseLayer: true,
						 	 transitionEffect: 'resize',
						 	 buffer: 0,
						 	 opacity: 0.6,
						 	 singleTile: true,
						 	 visibility: false
					     	 }
					     	 );

   		catastroLayer = new OpenLayers.Layer.WMS("Catastro",
        					"http://ovc.catastro.meh.es/Cartografia/WMS/ServidorWMS.aspx", {
                            layers: "CATASTRO"
                            }, {
                            isBaseLayer: true,
                           	buffer: 0,
                            	transitionEffect: 'resize',
				singleTile: true,
                                visibility: false
                            }
                            );
    	var emptyLayer = new OpenLayers.Layer.Image("empty",
						'images/white.png',
						map.maxExtent,
						new OpenLayers.Size(1, 1));
                            
		sitgaLimitesLayer = new OpenLayers.Layer.WMS("SITGA-Limites",
						     "http://ideg.xunta.es/wms/request.aspx", {
					     	 layers: ["PRESA",
					     	 		 "ENCORO","RIO_DOBLE","RIOS","CONCELLO","PARROQUIA","PROVINCIA"],
					     	 transparent: true					     	 
							 }, {
						 	 isBaseLayer: false,
						 	 transitionEffect: 'resize',
						 	 
						 	 buffer: 0,
						 	 opacity: 0.9,
						 	 singleTile: false,
						 	 visibility: true
					     	 }
					     	 );
		sitgaToponimosLayer = new OpenLayers.Layer.WMS("SITGA-Toponimos",
						     "http://ideg.xunta.es/wms/request.aspx", {
					     	 layers: ["Toponimia_txt_1_5000","Hidrografia_txt_1_5000",
					     	 		 "NOMECONCELLO","TOPONIMIA_COSTA","TXT_CIDADES","TXT_CIDADES_B",
					     	 		 "TXT_PARROQUIA","TXT_VILAS"],
					     	 transparent: true     	 
							 }, {
						 	 isBaseLayer: false,
						 	 transitionEffect: 'resize',
						 	 
						 	 buffer: 0,
						 	 opacity: 0.9,
						 	 singleTile: false,
						 	 visibility: false,
					     	 }
					     	 );

    return [sitgaLimitesLayer, sitgaToponimosLayer, sitgaPnoaLayer, pnoaLayer, mtnLayer, topoLayer, sitgaLayer, catastroLayer, emptyLayer];
}