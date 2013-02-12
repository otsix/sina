<?php

require_once ('PhpConsole.php');
PhpConsole::start();

//DEBUG=1 when devel, else set to "0"
$DEBUG_MODE=0;

$host="localhost"; // Hostname
$db_name="sina"; // Name of the SINA DB
$username="sina"; //Username to access on the SINA DB 
$password="20seca10"; //Password to access on the SINA DB 
$connectionstring = "host=$host dbname=$db_name user=$username password=$password";

$tbl_users="users"; // Table of USERS
$tbl_issues="carto.entidade_singular"; // Table of "cities"
$file_server_path="/var/tmp/sina_files/"; // Folder where user files will be placed (use '/' at the end) 
$file_www_folder='files'; // URL path where user files will accesed on the web

?>