<?php
if (isset($_POST['submit'])) {
    date_default_timezone_set('Africa/Nairobi');

    // Extract form data
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $donationType = $_POST['donationType'] ?? '';
    $food = $_POST['food'] ?? null;
    $foodLocation = $_POST['foodLocation'] ?? null;
    $clothes = $_POST['clothes'] ?? null;
    $clothesLocation = $_POST['clothesLocation'] ?? null;
    $mpesaNumber = $_POST['mpesaNumber'] ?? null;
    $amount = $_POST['amount'] ?? null;

    // Validate inputs
    if (!$name || !$phone || !$donationType) {
        die("Required fields are missing.");
    }

    // Database connection setup
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

    // Insert donation details into the database
    $stmt = $conn->prepare("INSERT INTO donation (name, phone, donationType, food, foodLocation, clothes, clothesLocation, mpesaNumber, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepared statement failed: " . $conn->error);
    }
    $stmt->bind_param("sssssssss", $name, $phone, $donationType, $food, $foodLocation, $clothes, $clothesLocation, $mpesaNumber, $amount);
    $stmt->execute();
    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();

    // Handle M-Pesa specific logic if selected
    if ($donationType === 'mpesa' && $mpesaNumber && $amount) {
        $consumerKey = 'nk16Y74eSbTaGQgc9WF8j6FigApqOMWr'; // Fill with your app Consumer Key
        $consumerSecret = '40fD1vRXCq90XFaU'; // Fill with your app Secret

        $BusinessShortCode = '174379';
        $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $PartyA = $mpesaNumber;
        $AccountReference = 'kenyan donation';
        $TransactionDesc = 'Donation Payment';
        $Timestamp = date('YmdHis');
        $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);

        // Access token request
        $headers = ['Content-Type:application/json; charset=utf8'];
        $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init($access_token_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        $access_token = $result->access_token;
        curl_close($curl);

        // Initiate the transaction
        $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];
        $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl_post_data = array(
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $Password,
            'Timestamp' => $Timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $PartyA,
            'PartyB' => $BusinessShortCode,
            'PhoneNumber' => $PartyA,
            'CallBackURL' => 'https://yourserver.com/callback.php', // Update with your actual callback URL
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc
        );
        $data_string = json_encode($curl_post_data);
        $curl = curl_init($initiate_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);

        // Decode response
        $response = json_decode($curl_response, true);
        curl_close($curl);

        // Check if the request was accepted for processing
        if ($response['ResponseCode'] == '0') {
            // Successful, process response
            $MerchantRequestID = $response['MerchantRequestID'];
            $CheckoutRequestID = $response['CheckoutRequestID'];
            $ResponseDescription = $response['ResponseDescription'];
            $CustomerMessage = $response['CustomerMessage'];

            // Insert the response into the database
            $stmt = $conn->prepare("INSERT INTO mpesa_responses (MerchantRequestID, CheckoutRequestID, ResponseCode, ResponseDescription, CustomerMessage) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $MerchantRequestID, $CheckoutRequestID, $response['ResponseCode'], $ResponseDescription, $CustomerMessage);
            $stmt->execute();
            if ($stmt->error) {
                die("Execute failed: " . $stmt->error);
            }
            $stmt->close();

            echo "M-Pesa request successful: " . $CustomerMessage;
        } else {
            // Handle error
            echo "M-Pesa request failed: " . $response['errorMessage'];
        }
    }

    // Close the database connection
    $conn->close();
    echo "Donation recorded successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Form</title>
    <link rel="stylesheet" href="mpesa.css">
</head>
<header>
    <h1>Donation Form</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="contact.php">Contact Us</a>
        <a href="donationhistory.html">History</a>
        <a href="usereport.php">Report</a>

    </nav>
</header>
<body>
    <div class="container">
        <div class="card">
            <h3 class="text-center">Donate To save a life</h3>
            <form id="donationForm" action="process_donation.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                </div>
                <div class="form-group">
                    <label for="donationType">Donation Type</label>
                    <select id="donationType" name="donationType" required>
                        <option value="">Select Donation Type</option>
                        <option value="food">Food</option>
                        <option value="clothes">Clothes</option>
                        <option value="mpesa">M-Pesa</option>
                    </select>
                </div>
                <div id="mpesaFields" class="form-group" style="display: none;">
                    <label for="mpesaNumber">M-Pesa Number</label>
                    <input type="tel" id="mpesaNumber" name="mpesaNumber" placeholder="Enter your M-Pesa number">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" placeholder="Enter amount">
                </div>
                <div id="foodFields" class="form-group" style="display: none;">
                    <label for="food">Food Description</label>
                    <input type="text" id="food" name="food" placeholder="Describe the food">
                    <label for="foodLocation">Food Pickup Location</label>
                    <input type="text" id="foodLocation" name="foodLocation" placeholder="Enter food pickup location">
                </div>
                <div id="clothesFields" class="form-group" style="display: none;">
                    <label for="clothes">Clothes Description</label>
                    <input type="text" id="clothes" name="clothes" placeholder="Describe the clothes">
                    <label for="clothesLocation">Clothes Pickup Location</label>
                    <input type="text" id="clothesLocation" name="clothesLocation" placeholder="Enter clothes pickup location">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn" name="submit">Donate</button>
                </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
