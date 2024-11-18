<?php
header("Content-Type: application/json");

// Read JSON payload from the incoming request
$stkCallbackResponse = file_get_contents('php://input');

// Log the JSON payload to a file
$logFile = "response.json";
$log = fopen($logFile, "a");
fwrite($log, $stkCallbackResponse . "\n");
fclose($log);

// Decode JSON payload
$callbackContent = json_decode($stkCallbackResponse, true);

if ($callbackContent) {
    $ResultCode = $callbackContent['Body']['stkCallback']['ResultCode'];
    $CheckoutRequestID = $callbackContent['Body']['stkCallback']['CheckoutRequestID'];
    $Amount = $callbackContent['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
    $MpesaReceiptNumber = $callbackContent['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
    $PhoneNumber = $callbackContent['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

    // Insert into database if the transaction is successful
    if ($ResultCode == 0) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "kenyan_project";

        // Create a connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert data into the database
        $insert = $conn->prepare("INSERT INTO transactions (CheckoutRequestID, ResultCode, amount, MpesaReceiptNumber, PhoneNumber) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $CheckoutRequestID, $ResultCode, $Amount, $MpesaReceiptNumber, $PhoneNumber);
        $insert->execute();
        $insert->close();

        // Close the database connection
        $conn->close();
    }

    echo json_encode(array('status' => 'success', 'message' => 'Callback processed'));
} else {
    echo json_encode(array('status' => 'fail', 'message' => 'Callback failed'));
}
?>
