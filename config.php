<?php 
 
// Establecer los detalles del producto  
// Minimum amount is $0.50 US  
$productName = "Pago de colegiatura";
$descriptionName = "Pago de colegiatura del mes de Agosto";
$productID = "DP12345";  
$productPrice = 4167.60; 
$currency = "mxn"; 
$mainPage = 'http://localhost/stripe_checkout_con_php/payment-success.php';

  
/* 
 *Configurar las claves secretas y publicas de stripe
 */ 
define('STRIPE_API_KEY', 'sk_test_51MgqzbFsLXR5H817qiFBGL4OixLEEiAWKPPtuOAPUCzTdGM7ZowHxz4WPOgPbr2gJ5v6thOSJxJ25BwYimGHGNvz00nGyNOPTx'); 
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51MgqzbFsLXR5H817ERftCB8k0o5EQwZ2O1kZIVGMowZ8IHwynOLZBu36kl6uD0DBglSypRzQYecgSqWfvs0TuTRh000YGNf2Id');
define('STRIPE_SUCCESS_URL', $mainPage); //Payment success URL 
define('STRIPE_CANCEL_URL', 'http://localhost/stripe_checkout_con_php/payment-cancel.php'); //Payment cancel URL 
    
// Configuraciin de la base de datos   
define('DB_HOST', 'localhost');   
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', '');   
define('DB_NAME', 'stripe'); 

 
?>
