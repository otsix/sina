<?php
include 'config.php';


// Se introdujo un <input name="id_issue"> para minimizar problemas con archivos repetidos
$id = $_POST['id_issue'];

// defino la carpeta para subir
$uploaddir = $file_server_path;
// defino el nombre del archivo
$uploadfile = $uploaddir . $id . '-' .basename($_FILES['file-upload']['name']);

// Lo mueve a la carpeta elegida
debug($uploadfile);
if (move_uploaded_file($_FILES['file-upload']['tmp_name'], $uploadfile)) {
  echo "success";
} else {
  echo "error";
}
?>