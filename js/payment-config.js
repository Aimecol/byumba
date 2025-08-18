// Payment Configuration for IntouchPay API
const PaymentConfig = {
    // IntouchPay API Configuration
    intouchpay: {
        username: 'testa',
        partnerpassword: '+$J<wtZktTDs&-Mk("h5=<PH#Jf769P5/Z<*xbR~',
        accountid: '250160000011',
        baseUrl: 'https://www.intouchpay.co.rw/api',
        endpoints: {
            requestPayment: '/requestpayment/',
            requestDeposit: '/requestdeposit/',
            getTransactionStatus: '/gettransactionstatus/',
            getBalance: '/getbalance/'
        }
    },
    
    // Mobile Money Providers
    providers: {
        mtn: {
            name: 'MTN Mobile Money',
            code: 'MTN',
            icon: 'fas fa-mobile-alt',
            color: '#FFCC00',
            prefixes: ['078', '079'],
            description: 'Pay with MTN Mobile Money'
        },
        airtel: {
            name: 'Airtel Money',
            code: 'AIRTEL',
            icon: 'fas fa-mobile-alt',
            color: '#FF0000',
            prefixes: ['073', '072'],
            description: 'Pay with Airtel Money'
        }
    },
    
    // Payment Settings
    settings: {
        minAmount: 100, // Minimum payment amount in RWF
        maxAmount: 1000000, // Maximum payment amount in RWF
        timeout: 60000, // Request timeout in milliseconds
        retryAttempts: 3,
        callbackUrl: window.location.origin + '/payment-callback.php'
    },
    
    // Response Codes
    responseCodes: {
        '1000': 'Pending',
        '01': 'Successful',
        '0002': 'Missing Username Information',
        '0003': 'Missing Password Information',
        '0004': 'Missing Date Information',
        '0005': 'Invalid Password',
        '0006': 'User Does not have an IntouchPay Account',
        '0007': 'No such user',
        '0008': 'Failed to Authenticate',
        '2100': 'Amount should be greater than 0',
        '2200': 'Amount below minimum',
        '2300': 'Amount above maximum',
        '2400': 'Duplicate Transaction ID',
        '2500': 'Route Not Found',
        '2600': 'Operation Not Allowed',
        '2700': 'Failed to Complete Transaction',
        '1005': 'Failed Due to Insufficient Funds',
        '1002': 'Mobile number not registered on mobile money',
        '1008': 'General Failure',
        '1200': 'Invalid Number',
        '1100': 'Number not supported on this Mobile money network',
        '1300': 'Failed to Complete Transaction, Unknown Exception'
    }
};

// Payment Service Class
class PaymentService {
    constructor() {
        this.config = PaymentConfig;
    }
    
    // Generate timestamp in required format (yyyymmddhhmmss)
    generateTimestamp() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        return `${year}${month}${day}${hours}${minutes}${seconds}`;
    }
    
    // Generate unique transaction ID
    generateTransactionId() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000);
        return `CERT_${timestamp}_${random}`;
    }
    
    // Generate password hash (SHA256)
    async generatePassword(username, accountno, partnerpassword, timestamp) {
        const message = username + accountno + partnerpassword + timestamp;
        const msgBuffer = new TextEncoder().encode(message);
        const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        return hashHex;
    }
    
    // Detect mobile money provider from phone number
    detectProvider(phoneNumber) {
        const cleanNumber = phoneNumber.replace(/\D/g, '');
        let prefix = '';

        // Extract the 3-digit prefix from different number formats
        if (cleanNumber.length === 12 && cleanNumber.startsWith('250')) {
            // Format: 250780578786 -> extract 078
            prefix = '0' + cleanNumber.substring(3, 5); // Get 78 and add 0 to make 078
        } else if (cleanNumber.length === 9) {
            // Format: 780578786 -> extract 078
            prefix = '0' + cleanNumber.substring(0, 2);
        } else if (cleanNumber.length === 10 && cleanNumber.startsWith('0')) {
            // Format: 0780578786 -> extract 078
            prefix = cleanNumber.substring(0, 3);
        }

        // Check against provider prefixes
        for (const [key, provider] of Object.entries(this.config.providers)) {
            if (provider.prefixes.includes(prefix)) {
                return {
                    ...provider,
                    key: key
                };
            }
        }

        return null;
    }
    
    // Validate phone number format
    validatePhoneNumber(phoneNumber) {
        const cleanNumber = phoneNumber.replace(/\D/g, '');
        
        // Rwanda phone number validation (should be 12 digits starting with 250)
        if (cleanNumber.length === 12 && cleanNumber.startsWith('250')) {
            return true;
        }
        
        // Also accept 9-digit format (without country code)
        if (cleanNumber.length === 9 && (cleanNumber.startsWith('07') || cleanNumber.startsWith('73') || cleanNumber.startsWith('72'))) {
            return true;
        }
        
        return false;
    }
    
    // Format phone number to international format
    formatPhoneNumber(phoneNumber) {
        const cleanNumber = phoneNumber.replace(/\D/g, '');
        
        if (cleanNumber.length === 9) {
            return '250' + cleanNumber;
        }
        
        if (cleanNumber.length === 12 && cleanNumber.startsWith('250')) {
            return cleanNumber;
        }
        
        return null;
    }
    
    // Validate payment amount
    validateAmount(amount) {
        const numAmount = parseFloat(amount);
        
        if (isNaN(numAmount) || numAmount <= 0) {
            return { valid: false, message: 'Amount must be a positive number' };
        }
        
        if (numAmount < this.config.settings.minAmount) {
            return { valid: false, message: `Amount must be at least RWF ${this.config.settings.minAmount}` };
        }
        
        if (numAmount > this.config.settings.maxAmount) {
            return { valid: false, message: `Amount cannot exceed RWF ${this.config.settings.maxAmount}` };
        }
        
        return { valid: true };
    }
    
    // Get response message from code
    getResponseMessage(code) {
        return this.config.responseCodes[code] || 'Unknown response code';
    }
}

// Export for use in other modules
window.PaymentService = PaymentService;
window.PaymentConfig = PaymentConfig;
