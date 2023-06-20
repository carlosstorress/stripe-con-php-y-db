<?php 
 // ESTE ARCHIVO ES PARA INICIALIZAR EL PAGO EN STRIPE CHECKOUT SOLICITANDO LA INFORMACION COMO LO ES LA DIRECCION
// AGREGAR EL CAMPO PERSONALIZADO, UNA IMAGEN DEL PRODUCTO Y DEMAS DATOS
// EN DONDE DICE CUSTOM_FIELDS ES PARA AGREGAR EL CAMPO PERSONALIZADO Y ESTABLECIENDO QUE MINIMO SON 10 CARACTERES LOS QUE DEBE DE INGRESAR EN EL CAMPO
// Y COMO MAXIMO 50 CARACTERES PARA INGRESAR EN EL CAMPO PERSONALIZADO
// Include the configuration file 
require_once 'config.php'; 
 
// Include the Stripe PHP library 
require_once 'stripe-php/init.php'; 
 
// Set API key 
$stripe = new \Stripe\StripeClient(STRIPE_API_KEY); 
 
$response = array( 
    'status' => 0, 
    'error' => array( 
        'message' => 'Invalid Request!'    
    ) 
); 
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $input = file_get_contents('php://input'); 
    $request = json_decode($input);     
} 
 
if (json_last_error() !== JSON_ERROR_NONE) { 
    http_response_code(400); 
    echo json_encode($response); 
    exit; 
} 
 
if(!empty($request->createCheckoutSession)){ 
    // Convert product price to cent 
    $stripeAmount = round($productPrice*100, 2); 
 
    // Create new Checkout Session for the order 
    try { 
        $checkout_session = $stripe->checkout->sessions->create([ 
            'billing_address_collection' => 'required',
            'custom_fields' => [
                [
                  'key' => 'engraving',
                  'label' => [
                    'type' => 'custom',
                    'custom' => 'Nombre del responsable del pago',
                  ],
                  'type' => 'text',
                  'text' => [
                    'maximum_length' => 50,
                    'minimum_length' => 10,
                  ],
                ],
              ],
            'line_items' => [[ 
                'price_data' => [ 
                    'product_data' => [ 
                        'name' => $productName,
                        'description' => $descriptionName,
                        'images' => ['https://pbs.twimg.com/media/EN4I3RhUwAIYGrB?format=png&name=medium'], 
                        'metadata' => [ 
                            'pro_id' => $productID 
                        ] 
                    ], 
                    'unit_amount' => $stripeAmount, 
                    'currency' => $currency, 
                ], 
                'quantity' => 1 
            ]], 
            'mode' => 'payment', 
            'success_url' => STRIPE_SUCCESS_URL.'?session_id={CHECKOUT_SESSION_ID}', 
            'cancel_url' => STRIPE_CANCEL_URL, 
        ]); 
    } catch(Exception $e) {  
        $api_error = $e->getMessage();  
    } 
     
    if(empty($api_error) && $checkout_session){ 
        $response = array( 
            'status' => 1, 
            'message' => 'Checkout Session created successfully!', 
            'sessionId' => $checkout_session->id 
        ); 
    }else{ 
        $response = array( 
            'status' => 0, 
            'error' => array( 
                'message' => 'Checkout Session creation failed! '.$api_error    
            ) 
        ); 
    } 
} 
 
// Return response 
echo json_encode($response); 
 
?>
