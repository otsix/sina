<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<link href="../styles/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="../styles/style_map.css" type="text/css">
		<link rel="stylesheet" href="../lib/ol/theme/default/style.css" type="text/css">
		<script type="text/javascript" src="../lib/js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript">
			// IE stupidity explanation!!
			$(document).ready(function(){
				if ($.browser.msie){
					$("#login").find("input").attr("disabled", "disabled");
					var html = ['<div id="wrong" class="boxes">',
					'<p> <br /> <strong>ERROR: </strong>',
					'O seu navegador <strong>Internet Explorer </strong> non ten as caracteristicas necesarias para poder traballar con esta aplicacion.<br />',
					'<br />Recomendamoslle que utilice alternativas libres e gratuitas como Google Chrome, Mozilla Firefox ou similar. <br />',
					'</p><br /></div>'].join('');
					$("body").append(html);
				} 
			});

		//--></script> 

	</head>
	<body>
		<h1 id="login_title">SINA - Sistema de Indentificacion e Notificacion de Adversos</h1>
		<?php
		require_once ('PhpConsole.php');
		PhpConsole::start();
		
		$estado = '';
		
		if (isset($_GET['estado'])){
			$estado = $_GET['estado'];
		}

		if ($estado == "nologin") {
			echo '<div id="wrong" class="boxes"><p> <strong>ERROR:"nome de usuario" ou "contrasinal" NON correctos.</strong></p></div>';
		}

		if ($estado == "correct") {
			echo '<div id="correct" class="boxes"><p> <strong> Nome de usuario e contrasinal CORRECTOS.</strong></p></div>';
		}
		?>

		<div id="login" class="boxes">
			<img src="../images/xunta-logo.png" width="200">
			<form name="form1" method="post" action="main_checklogin.php">
				<table class="login_label">
					<tr>
						&nbsp;
					</tr>
					<tr>
						<td class="login_label">Nome</td>
						<td class="login_sep">&nbsp;</td>
						<td width="294">
						<input name="myusername" type="text" id="myusername">
						</td>
					</tr>
					<tr>
						<td class="login_label">Contrasinal</td>
						<td class="login_sep">&nbsp;</td>
						<td>
						<input name="mypassword" type="password" id="mypassword">
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
						<input id="login_btn" type="submit" name="Submit" value="Login">
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div id="login_contact" class="boxes">
			<p>
				Para notificar problemas ou obter mais informacion contacte:
			</p>
			<ul>
				<li>
					email: scit.cmati@xunta.es
				</li>
				<li>
					tlfno: 981 541751
				</li>
			</ul>
		</div>
	</body>
</html>
