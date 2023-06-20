<?php 
// EN ESTE ARCHIVO REALIZA EL AGREGADO DE LA INFORMACION A LA BASE DE DATOS 
// Y HASTA LO ULTIMO QUE ESTA EN HTML ES PARA QUE SI EL PAGO FUE EXITOSO LO ENVIA A ESA PAGINA 
// MOSTRANDO LA INFORMACION DEL PAGO QUE SE REALIZO
// Include configuration file  
require_once 'config.php'; 
 
// Include database connection file  
require_once 'dbConnect.php'; 
 
$payment_id = $statusMsg = ''; 
$status = 'error'; 
 
// Check whether stripe checkout session is not empty 
if(!empty($_GET['session_id'])){ 
    $session_id = $_GET['session_id']; 
     
    // Fetch transaction data from the database if already exists 
    $sqlQ = "SELECT * FROM transactions WHERE stripe_checkout_session_id = ?"; 
    $stmt = $db->prepare($sqlQ);  
    $stmt->bind_param("s", $db_session_id); 
    $db_session_id = $session_id; 
    $stmt->execute(); 
    $result = $stmt->get_result(); 
 
    if($result->num_rows > 0){ 
        // Transaction details 
        $transData = $result->fetch_assoc(); 
        $payment_id = $transData['id']; 
        $transactionID = $transData['txn_id']; 
        $paidAmount = $transData['paid_amount']; 
        $paidCurrency = $transData['paid_amount_currency']; 
        $payment_status = $transData['payment_status']; 
         
        $customer_name = $transData['customer_name']; 
        $customer_email = $transData['customer_email']; 
         
        $status = 'success'; 
        $statusMsg = 'Su pago ha sido realizado con exito!'; 
    }else{ 
        // Include the Stripe PHP library 
        require_once 'stripe-php/init.php'; 
         
        // Set API key 
        $stripe = new \Stripe\StripeClient(STRIPE_API_KEY); 
         
        // Fetch the Checkout Session to display the JSON result on the success page 
        try { 
            $checkout_session = $stripe->checkout->sessions->retrieve($session_id); 
        } catch(Exception $e) {  
            $api_error = $e->getMessage();  
        } 
         
        if(empty($api_error) && $checkout_session){ 
            // Get customer details 
            $customer_details = $checkout_session->customer_details; 
 
            // Retrieve the details of a PaymentIntent 
            try { 
                $paymentIntent = $stripe->paymentIntents->retrieve($checkout_session->payment_intent); 
            } catch (\Stripe\Exception\ApiErrorException $e) { 
                $api_error = $e->getMessage(); 
            } 
             
            if(empty($api_error) && $paymentIntent){ 
                // Check whether the payment was successful 
                if(!empty($paymentIntent) && $paymentIntent->status == 'succeeded'){ 
                    // Transaction details  
                    $transactionID = $paymentIntent->id; 
                    $paidAmount = $paymentIntent->amount; 
                    $paidAmount = ($paidAmount/100); 
                    $paidCurrency = $paymentIntent->currency; 
                    $payment_status = $paymentIntent->status; 
                     
                    // Customer info 
                    $customer_name = $customer_email = ''; 
                    if(!empty($customer_details)){ 
                        $customer_name = !empty($customer_details->name)?$customer_details->name:''; 
                        $customer_email = !empty($customer_details->email)?$customer_details->email:''; 
                    } 
                     
                    // Check if any transaction data is exists already with the same TXN ID 
                    $sqlQ = "SELECT id FROM transactions WHERE txn_id = ?"; 
                    $stmt = $db->prepare($sqlQ);  
                    $stmt->bind_param("s", $transactionID); 
                    $stmt->execute(); 
                    $result = $stmt->get_result(); 
                    $prevRow = $result->fetch_assoc(); 
                     
                    if(!empty($prevRow)){ 
                        $payment_id = $prevRow['id']; 
                    }else{ 
                        // Insert transaction data into the database 
                        $sqlQ = "INSERT INTO transactions (customer_name,customer_email,item_name,item_number,item_price,item_price_currency,paid_amount,paid_amount_currency,txn_id,payment_status,stripe_checkout_session_id,created,modified) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())"; 
                        $stmt = $db->prepare($sqlQ); 
                        $stmt->bind_param("ssssdsdssss", $customer_name, $customer_email, $productName, $productID, $productPrice, $currency, $paidAmount, $paidCurrency, $transactionID, $payment_status, $session_id); 
                        $insert = $stmt->execute(); 
                        if ($insert) {
                            $payment_id = $stmt->insert_id;
                        } else {
                            echo 'Error: ' . $stmt->error; // Muestra el mensaje de error de la consulta
                            var_dump($stmt); // Muestra información detallada sobre la variable $stmt
                        }
                         
                        if($insert){ 
                            $payment_id = $stmt->insert_id; 
                        } 
                    } 
                     
                    $status = 'success'; 
                    $statusMsg = 'Su pago ha sido realizado con exito!'; 
                }else{ 
                    $statusMsg = "La transaccion ha fallado!"; 
                } 
            }else{ 
                $statusMsg = "Unable to fetch the transaction details! $api_error";  
            } 
        }else{ 
            $statusMsg = "Invalid Transaction! $api_error";  
        } 
    } 
}else{ 
    $statusMsg = "Consulta invalida!"; 
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .container h1 {
            font-size: 30px;
            margin-bottom: 20px;
        }

        .container h4 {
            font-size: 23px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .container p {
            margin-bottom: 10px;
        }

        .btn-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-link:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status">
            <?php if(!empty($payment_id)){ ?>
                <h1 class="<?php echo $status; ?>"><?php echo $statusMsg; ?></h1>
                
                <h4>-----Informacion del pago-----</h4>
                <p><b>Reference Number:</b> <?php echo $payment_id; ?></p>
                <p><b>ID de Transaccion:</b> <?php echo $transactionID; ?></p>
                <p><b>Cantidad a pagar:</b> <?php echo $paidAmount.' '.$paidCurrency; ?></p>
                <p><b>Estado del pago:</b> <?php echo $payment_status; ?></p>
                
                <h4>-----Informacion del cliente-----</h4>
                <p><b>Nombre:</b> <?php echo $customer_name; ?></p>
                <p><b>Email:</b> <?php echo $customer_email; ?></p>
                
                <h4>-----Informacion del producto-----</h4>
                <p><b>Nombre del productow:</b> <?php echo $productName; ?></p>
                <p><b>Precio:</b> <?php echo $productPrice.' '.$currency; ?></p>
            <?php }else{ ?>
                <h1 class="error">Su pago ha fallado!</h1>
                <p class="error"><?php echo $statusMsg; ?></p>
            <?php } ?>      
        </div>
    </div>
    <a href="index.php" class="btn-link">Volver a la pagina del producto</a>
</body>
</html>





