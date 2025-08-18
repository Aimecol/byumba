<?php
/**
 * Payment Request API for IntouchPay Integration
 * Handles payment requests from the frontend
 */

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// IntouchPay Configuration
$config = [
    'username' => 'testa',
    'partnerpassword' => '+$J<wtZktTDs&-Mk("h5=<PH#Jf769P5/Z<*xbR~',
    'accountid' => '250160000011',
    'baseUrl' => 'https://www.intouchpay.co.rw/api',
    'callbackUrl' => 'https://' . $_SERVER['HTTP_HOST'] . '/payment-callback.php'
];

// Log file for debugging
$logFile = 'payment_api_logs.txt';

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

function generateTimestamp() {
    return date('YmdHis');
}

function generateTransactionId() {
    return 'CERT_' . time() . '_' . rand(100, 999);
}

function generatePassword($username, $accountno, $partnerpassword, $timestamp) {
    $message = $username . $accountno . $partnerpassword . $timestamp;
    return hash('sha256', $message);
}

function validatePhoneNumber($phoneNumber) {
    $cleanNumber = preg_replace('/\D/', '', $phoneNumber);
    
    // Rwanda phone number validation
    if (strlen($cleanNumber) === 12 && substr($cleanNumber, 0, 3) === '250') {
        return $cleanNumber;
    }
    
    if (strlen($cleanNumber) === 9 && (substr($cleanNumber, 0, 2) === '07' || substr($cleanNumber, 0, 2) === '73' || substr($cleanNumber, 0, 2) === '72')) {
        return '250' . $cleanNumber;
    }
    
    return false;
}

function makeIntouchPayRequest($endpoint, $data) {
    global $config;
    
    $url = $config['baseUrl'] . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception("cURL Error: $error");
    }
    
    if ($httpCode !== 200) {
        throw new Exception("HTTP Error: $httpCode");
    }
    
    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON response: $response");
    }
    
    return $decodedResponse;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }
        
        logMessage("Payment request received: " . json_encode($input));
        
        // Validate required fields
        $requiredFields = ['phoneNumber', 'amount', 'description'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        $phoneNumber = $input['phoneNumber'];
        $amount = floatval($input['amount']);
        $description = $input['description'];
        
        // Validate phone number
        $formattedPhone = validatePhoneNumber($phoneNumber);
        if (!$formattedPhone) {
            throw new Exception('Invalid phone number format');
        }
        
        // Validate amount
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than 0');
        }
        
        if ($amount < 100) {
            throw new Exception('Amount must be at least RWF 100');
        }
        
        if ($amount > 1000000) {
            throw new Exception('Amount cannot exceed RWF 1,000,000');
        }
        
        // Generate request parameters
        $timestamp = generateTimestamp();
        $transactionId = generateTransactionId();
        $password = generatePassword(
            $config['username'],
            $config['accountid'],
            $config['partnerpassword'],
            $timestamp
        );
        
        // Prepare IntouchPay request
        $requestData = [
            'username' => $config['username'],
            'timestamp' => $timestamp,
            'amount' => $amount,
            'password' => $password,
            'mobilephone' => $formattedPhone,
            'requesttransactionid' => $transactionId,
            'accountno' => $config['accountid'],
            'callbackurl' => $config['callbackUrl']
        ];
        
        logMessage("Sending request to IntouchPay: " . json_encode($requestData));
        
        // Make request to IntouchPay
        $response = makeIntouchPayRequest('/requestpayment/', $requestData);
        
        logMessage("IntouchPay response: " . json_encode($response));
        
        // Store transaction details
        $transactionDetails = [
            'requestTransactionId' => $transactionId,
            'phoneNumber' => $formattedPhone,
            'amount' => $amount,
            'description' => $description,
            'timestamp' => $timestamp,
            'status' => 'pending',
            'intouchpayResponse' => $response,
            'createdAt' => date('Y-m-d H:i:s')
        ];
        
        // Save transaction to file
        $transactionsDir = 'transactions';
        if (!is_dir($transactionsDir)) {
            mkdir($transactionsDir, 0755, true);
        }
        
        $transactionFile = $transactionsDir . '/transaction_' . $transactionId . '.json';
        file_put_contents($transactionFile, json_encode($transactionDetails, JSON_PRETTY_PRINT));
        
        // Return response
        echo json_encode([
            'success' => true,
            'transactionId' => $transactionId,
            'intouchpayTransactionId' => $response['transactionid'] ?? null,
            'status' => $response['status'] ?? 'pending',
            'message' => $response['message'] ?? 'Payment request sent successfully',
            'responseCode' => $response['responsecode'] ?? null
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Handle transaction status requests
        if (isset($_GET['status']) && isset($_GET['transactionId'])) {
            $transactionId = $_GET['transactionId'];
            $transactionFile = 'transactions/transaction_' . $transactionId . '.json';
            
            if (file_exists($transactionFile)) {
                $transactionData = json_decode(file_get_contents($transactionFile), true);
                
                // Check for payment completion
                $paymentFile = 'payments/payment_' . $transactionId . '.json';
                if (file_exists($paymentFile)) {
                    $paymentData = json_decode(file_get_contents($paymentFile), true);
                    $transactionData['paymentStatus'] = $paymentData;
                }
                
                echo json_encode([
                    'success' => true,
                    'transaction' => $transactionData
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Transaction not found'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request parameters'
            ]);
        }
    } else {
        throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Payment request failed'
    ]);
}
?>
