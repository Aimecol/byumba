<?php
/**
 * Payment Callback Handler for IntouchPay API
 * This file handles payment completion notifications from IntouchPay
 */

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log file for debugging
$logFile = 'payment_logs.txt';

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    
    // Get the raw POST data
    $rawInput = file_get_contents('php://input');
    logMessage("Raw input: " . $rawInput);
    
    // Try to decode JSON payload
    $jsonData = json_decode($rawInput, true);
    
    if ($jsonData && isset($jsonData['jsonpayload'])) {
        $paymentData = $jsonData['jsonpayload'];
    } else {
        // Try to parse as form data
        parse_str($rawInput, $paymentData);
    }
    
    logMessage("Parsed payment data: " . json_encode($paymentData));
    
    // Validate required fields
    $requiredFields = ['requesttransactionid', 'transactionid', 'responsecode', 'status'];
    foreach ($requiredFields as $field) {
        if (!isset($paymentData[$field])) {
            logMessage("Missing required field: $field");
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            exit;
        }
    }
    
    // Extract payment information
    $requestTransactionId = $paymentData['requesttransactionid'];
    $transactionId = $paymentData['transactionid'];
    $responseCode = $paymentData['responsecode'];
    $status = $paymentData['status'];
    $statusDesc = isset($paymentData['statusdesc']) ? $paymentData['statusdesc'] : '';
    $referenceNo = isset($paymentData['referenceno']) ? $paymentData['referenceno'] : '';
    
    // Log the payment notification
    logMessage("Payment notification received:");
    logMessage("Request Transaction ID: $requestTransactionId");
    logMessage("Transaction ID: $transactionId");
    logMessage("Response Code: $responseCode");
    logMessage("Status: $status");
    logMessage("Status Description: $statusDesc");
    logMessage("Reference Number: $referenceNo");
    
    // Store payment result in a file (in production, use a database)
    $paymentResult = [
        'requesttransactionid' => $requestTransactionId,
        'transactionid' => $transactionId,
        'responsecode' => $responseCode,
        'status' => $status,
        'statusdesc' => $statusDesc,
        'referenceno' => $referenceNo,
        'timestamp' => date('Y-m-d H:i:s'),
        'received_at' => time()
    ];
    
    // Save to payments directory
    $paymentsDir = 'payments';
    if (!is_dir($paymentsDir)) {
        mkdir($paymentsDir, 0755, true);
    }
    
    $paymentFile = $paymentsDir . '/payment_' . $requestTransactionId . '.json';
    file_put_contents($paymentFile, json_encode($paymentResult, JSON_PRETTY_PRINT));
    
    logMessage("Payment result saved to: $paymentFile");
    
    // Determine if payment was successful
    $isSuccessful = ($responseCode === '01' && strtolower($status) === 'successful');
    
    if ($isSuccessful) {
        logMessage("Payment successful for transaction: $requestTransactionId");
        
        // Here you would typically:
        // 1. Update the application status in your database
        // 2. Send confirmation email to the applicant
        // 3. Generate the certificate or update processing status
        
        // For now, we'll just log the success
        logMessage("Certificate application payment completed successfully");
        
    } else {
        logMessage("Payment failed for transaction: $requestTransactionId - $statusDesc");
        
        // Handle payment failure
        // 1. Update application status to payment failed
        // 2. Notify applicant of payment failure
        // 3. Allow retry of payment
    }
    
    // Send success response to IntouchPay
    $response = [
        'message' => 'success',
        'success' => true,
        'request_id' => $requestTransactionId
    ];
    
    logMessage("Sending response: " . json_encode($response));
    
    http_response_code(200);
    echo json_encode($response);
    
} catch (Exception $e) {
    logMessage("Error processing payment callback: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'message' => 'error',
        'success' => false,
        'error' => 'Internal server error'
    ]);
}

/**
 * Function to get payment status by request transaction ID
 * This can be called via AJAX from the frontend
 */
function getPaymentStatus($requestTransactionId) {
    $paymentFile = 'payments/payment_' . $requestTransactionId . '.json';
    
    if (file_exists($paymentFile)) {
        $paymentData = json_decode(file_get_contents($paymentFile), true);
        return $paymentData;
    }
    
    return null;
}

// Handle GET requests for payment status checking
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_status'])) {
    $requestTransactionId = $_GET['transaction_id'] ?? '';
    
    if (empty($requestTransactionId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Transaction ID required']);
        exit;
    }
    
    $paymentStatus = getPaymentStatus($requestTransactionId);
    
    if ($paymentStatus) {
        echo json_encode([
            'success' => true,
            'payment_status' => $paymentStatus
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Payment status not found'
        ]);
    }
}
?>
