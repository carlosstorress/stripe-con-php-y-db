<?php

require_once 'config.php';
// Connect with the database   
$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);   
   
// Display error if failed to connect   
if ($db->connect_errno) {   
    printf("Connect failed: %s\n", $db->connect_error);   
    exit();   
}
$conexionMensaje="";
if ($db->connect_error) {
    $conexionMensaje = "No se pudo conectar a la base de datos: " . $db->connect_error;
} else {
    $conexionMensaje = "Conexión exitosa a la base de datos!";
}

