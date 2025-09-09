// Payment API Service for IntouchPay Integration
class PaymentAPI {
    constructor() {
        this.paymentService = new PaymentService();
        this.config = PaymentConfig;
    }
    
    // Request Payment from Mobile Money Account
    async requestPayment(phoneNumber, amount, description = 'Certificate Application Fee') {
        try {
            // Validate inputs
            const phoneValidation = this.validatePaymentRequest(phoneNumber, amount);
            if (!phoneValidation.valid) {
                throw new Error(phoneValidation.message);
            }
            
            // Format phone number
            const formattedPhone = this.paymentService.formatPhoneNumber(phoneNumber);
            
            // Generate request parameters
            const timestamp = this.paymentService.generateTimestamp();
            const transactionId = this.paymentService.generateTransactionId();
            const password = await this.paymentService.generatePassword(
                this.config.intouchpay.username,
                this.config.intouchpay.accountid,
                this.config.intouchpay.partnerpassword,
                timestamp
            );
            
            // Prepare request data
            const requestData = {
                username: this.config.intouchpay.username,
                timestamp: timestamp,
                amount: parseFloat(amount),
                password: password,
                mobilephone: formattedPhone,
                requesttransactionid: transactionId,
                accountno: this.config.intouchpay.accountid,
                callbackurl: this.config.settings.callbackUrl
            };
            
            // Store transaction details locally
            this.storeTransactionDetails(transactionId, {
                phoneNumber: formattedPhone,
                amount: amount,
                description: description,
                timestamp: timestamp,
                status: 'pending'
            });
            
            // Make API request
            const response = await this.makeAPIRequest('requestPayment', requestData);
            
            return {
                success: true,
                transactionId: transactionId,
                intouchpayTransactionId: response.transactionid,
                status: response.status,
                message: response.message,
                responseCode: response.responsecode
            };
            
        } catch (error) {
            console.error('Payment request failed:', error);
            return {
                success: false,
                error: error.message,
                message: 'Failed to initiate payment request'
            };
        }
    }
    
    // Check Transaction Status via Backend
    async getTransactionStatus(requestTransactionId, intouchpayTransactionId) {
        try {
            const url = `api/payment-request.php?status=1&transactionId=${requestTransactionId}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                // If backend is not available, return pending status
                console.warn(`Backend API not available (${response.status}), assuming transaction is still pending`);
                return {
                    success: true,
                    status: 'Pending',
                    message: 'Transaction status check unavailable, assuming pending',
                    responseCode: '1000'
                };
            }

            const result = await response.json();

            if (!result.success) {
                // If transaction not found in backend, it might still be pending
                if (result.message && result.message.includes('not found')) {
                    console.warn('Transaction not found in backend, assuming still pending');
                    return {
                        success: true,
                        status: 'Pending',
                        message: 'Transaction pending',
                        responseCode: '1000'
                    };
                }
                throw new Error(result.message || 'Failed to get transaction status');
            }

            const transaction = result.transaction;
            let status = 'Pending';
            let responseCode = '1000';

            // Check if payment callback was received
            if (transaction.paymentStatus) {
                status = transaction.paymentStatus.status;
                responseCode = transaction.paymentStatus.responsecode;
            }

            return {
                success: true,
                status: status,
                message: status,
                responseCode: responseCode
            };

        } catch (error) {
            console.error('Transaction status check failed:', error);
            console.log('Transaction not found in backend, assuming still pending');

            // Return pending status instead of failing completely
            // This allows the monitoring to continue
            return {
                success: true,
                status: 'Pending',
                message: 'Transaction not found in backend, assuming still pending',
                responseCode: '1000',
                error: error.message
            };
        }
    }
    
    // Get Account Balance
    async getBalance() {
        try {
            const timestamp = this.paymentService.generateTimestamp();
            const password = await this.paymentService.generatePassword(
                this.config.intouchpay.username,
                this.config.intouchpay.accountid,
                this.config.intouchpay.partnerpassword,
                timestamp
            );
            
            const requestData = {
                username: this.config.intouchpay.username,
                timestamp: timestamp,
                accountno: this.config.intouchpay.accountid,
                password: password
            };
            
            const response = await this.makeAPIRequest('getBalance', requestData);
            
            return {
                success: response.success,
                balance: response.balance,
                message: response.message || 'Balance retrieved successfully'
            };
            
        } catch (error) {
            console.error('Balance inquiry failed:', error);
            return {
                success: false,
                error: error.message,
                message: 'Failed to retrieve account balance'
            };
        }
    }
    
    // Make API Request via Backend
    async makeAPIRequest(endpoint, data) {
        let url;

        if (endpoint === 'requestPayment') {
            url = 'api/payment-request.php';

            // Transform data for backend API
            const requestData = {
                phoneNumber: data.mobilephone,
                amount: data.amount,
                description: 'Certificate Application Fee'
            };

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.error || result.message || 'Request failed');
                }

                return {
                    success: result.success,
                    transactionid: result.intouchpayTransactionId,
                    status: result.status,
                    message: result.message,
                    responsecode: result.responseCode
                };

            } catch (error) {
                console.error('API request failed:', error);
                throw new Error(`API request failed: ${error.message}`);
            }

        } else {
            // For other endpoints, use direct IntouchPay API (if needed)
            url = this.config.intouchpay.baseUrl + this.config.intouchpay.endpoints[endpoint];

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                return result;

            } catch (error) {
                console.error('API request failed:', error);
                throw new Error(`API request failed: ${error.message}`);
            }
        }
    }
    
    // Validate Payment Request
    validatePaymentRequest(phoneNumber, amount) {
        // Validate phone number
        if (!this.paymentService.validatePhoneNumber(phoneNumber)) {
            return { valid: false, message: 'Invalid phone number format' };
        }
        
        // Validate amount
        const amountValidation = this.paymentService.validateAmount(amount);
        if (!amountValidation.valid) {
            return amountValidation;
        }
        
        // Check if provider is supported
        const provider = this.paymentService.detectProvider(phoneNumber);
        if (!provider) {
            return { valid: false, message: 'Mobile money provider not supported' };
        }
        
        return { valid: true, provider: provider };
    }
    
    // Store Transaction Details in Local Storage
    storeTransactionDetails(transactionId, details) {
        const transactions = JSON.parse(localStorage.getItem('paymentTransactions') || '{}');
        transactions[transactionId] = {
            ...details,
            createdAt: new Date().toISOString()
        };
        localStorage.setItem('paymentTransactions', JSON.stringify(transactions));
    }
    
    // Get Transaction Details from Local Storage
    getTransactionDetails(transactionId) {
        const transactions = JSON.parse(localStorage.getItem('paymentTransactions') || '{}');
        return transactions[transactionId] || null;
    }
    
    // Update Transaction Status
    updateTransactionStatus(transactionId, status, additionalData = {}) {
        const transactions = JSON.parse(localStorage.getItem('paymentTransactions') || '{}');
        if (transactions[transactionId]) {
            transactions[transactionId] = {
                ...transactions[transactionId],
                status: status,
                updatedAt: new Date().toISOString(),
                ...additionalData
            };
            localStorage.setItem('paymentTransactions', JSON.stringify(transactions));
        }
    }
}

// Export for use in other modules
window.PaymentAPI = PaymentAPI;
