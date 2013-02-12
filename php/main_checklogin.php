<?php

include ('config.php');

// Connect to server and select database.
$connectionstring = "host=$host dbname=$db_name user=$username password=$password";
$dbconn = pg_connect($connectionstring);

if (!$dbconn) {
	if ($DEBUG_MODE) {
		die('Could not connect: ' . pg_last_error($dbconn));
	} else {
		debug("Error connecting to the database");
		echo "Error connecting to the database";
		header("location:main_login.php?estado=nologin");
	}
}

$myusername = $_POST['myusername'];
$mypassword = $_POST['mypassword'];

//TODO: To protect SQL injection
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);

////////////
//TODO -- IMPORTANT: Change password_old TO password
$sql = "SELECT u.*, a.ambito, a.cd_ambito as cd_ambito FROM $tbl_users u, ambito a ".
  "WHERE user_name='$myusername' and password_old='$mypassword' and u.id_ambito::text = a.cd_ambito;";
debug($sql);
$result = pg_query($dbconn, $sql);

if (!$dbconn) {
	if ($DEBUG_MODE) {
		die('Query failed: ' . pg_last_error($dbconn));
	} else {
		echo "Error checking user/password";
		header("location:main_login.php?estado=nologin");
	}
}

$count = pg_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

if ($count == 1) {

	$row = pg_fetch_row($result);

	$user_id = $row[pg_field_num($result, 'user_id')];
	$ambito = $row[pg_field_num($result, 'cd_ambito')];
	$ambito_code = $row[pg_field_num($result, 'cd_ambito')];

	session_start();

	$_SESSION['username'] = $myusername;
	$_SESSION['user_id'] = $user_id;
	$_SESSION['ambito_type'] = $ambito;
	$_SESSION['ambito_name'] = $ambito;
	$_SESSION['ambito_code'] = $ambito_code;

	// Now it get the BBOX of the council
	//TODOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
	$sqlbbox = 'SELECT st_xmax(the_geom) as xmax, st_ymax(the_geom) as ymax, ' . 'st_xmin(the_geom) as xmin, st_ymin(the_geom) as ymin ' . 'FROM ambito_geom WHERE cod=' . $ambito_code . ';';

	$result_bbox = pg_query($dbconn, $sqlbbox);

	if (!$dbconn) {
		if ($DEBUG_MODE) {
			die('Query failed: ' . pg_last_error($dbconn));
		} else {
			echo "Error getting council entities";
			header("location:main_login.php?estado=nologin");
		}
	}

	$count_bbox = pg_num_rows($result_bbox);

	if ($count_bbox == 1) {
		$row = pg_fetch_row($result_bbox);

		$xmax = $row[pg_field_num($result_bbox, 'xmax')];
		$ymax = $row[pg_field_num($result_bbox, 'ymax')];
		$xmin = $row[pg_field_num($result_bbox, 'xmin')];
		$ymin = $row[pg_field_num($result_bbox, 'ymin')];

		$_SESSION['xmax'] = $xmax;
		$_SESSION['ymax'] = $ymax;
		$_SESSION['xmin'] = $xmin;
		$_SESSION['ymin'] = $ymin;

		$bbox = $xmax . ',' . $ymax . ',' . $xmin . ',' . $ymin;

		$_SESSION['max_min'] = $bbox;
	}
}

if ($count == 1 and $count_bbox == 1) {
  //header("location:xeoportal.php?codine=".$codine.'&conc='.$conc_name.'&bbox='.$bbox);
  //header("location:mapa.php?codine=" . $codine . '&cd_ambito=' . $cd_ambito . '&bbox=' . $bbox. '&ambito_code=' . $ambito_code);
  header("location:mapa.php?");
} else {
  echo "Wrong Username or Password";
  header("location:main_login.php?estado=nologin");
}
?>