<?php
include ('config.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="stylesheet" href="../styles/style_map.css" type="text/css">
		<link rel="stylesheet" href="../lib/ol/theme/style.css" type="text/css">
		<script type="text/javascript" src="../tools/form_format.js"></script>
		<script type="text/javascript" src="../styles/vector_ol_styles.js"></script>
		<script src="../lib/ol/OpenLayers.js"></script>
		<script src="../lib/jquery/jquery-1.6.4.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				function query2pg() {
					//$("#general_info").html("DONE2");
					$.get('admin_queries.php', {
						query : 'summarize_table',
						concello : '15005'
					}, function(data) {
						$("#results").html("<h3>Arteixo:</h3>" + data);
					});
				};


				$("#general_info").click(query2pg);
				query2pg();
			});

		</script>
	</head>
	<body>
		<div id="banner" class="banner">
			<div id="logo_banner" style="position:relative;float:left;margin-right:20px;">
				<img src="images/xunta-logo.png" width="150">
			</div>
			<div id="titulo_banner" style="position:relative;float:left;margin-right:10px;">
				<h1>SIPAA</h1>
				<h2>Sistema de Identificación de Problemas de Abastecemento de Auga</h2>
			</div>
		</div>
		<!--	<div id="map" class="smallmap" style="width: 500px; height:500px;"></div>  -->
		<div style="position:absolute; margin-left:90%; margin-top:5%;" onclick="location.href='http://www.example.com';" >
			<img src="../images/help.png" width="20"/><a href="main_login.php"> Axuda </a>
		</div>
		<div id="concello"></div>
		<div id="docs">
			<div id="help">
				<div style="float: left; margin: 0em 2em 2em 1em; ">
					<img src="images/help.png" width="50" style="float: left; margin: 0em 2em 6em 1em; "/>
				</div>
				<p>
					<h1> Resumen de datos: </h1>
					<div id="results"></div>
					<div id="general_info">
						<strong>Actualizar </strong>
					</div>
				</p>
			</div>
		</div>
		<!--
		<div id="coords"></div>
		-->
		<div id="footer" class="footer">
			<div id="logo_banner" style="position:relative;float:left;margin-right:20px;">
				<img src="../images/xunta-logo.png" width="110">
			</div>
			<div id="titulo_footer" style="position:relative;float:left;margin-right:10px;">
				<p style="font-size:0.8em;color:white;">
					Aplicación informática realizada pola Consellería de Medio Ambiente, Territorio e Infraestruturas </br> para a Consellería de Presidencia, Administracións Públicas e Xustiza
				</p>
			</div>
		</div>
	</body>
</html>
