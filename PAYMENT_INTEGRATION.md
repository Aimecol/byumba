# Payment Integration Documentation
## Diocese of Byumba Certificate Application System

### Overview
This document describes the integration of MTN Mobile Money and Airtel Money payment systems using the IntouchPay API for processing certificate application fees.

### Integration Components

#### 1. Frontend Components
- **Payment Configuration** (`js/payment-config.js`)
  - IntouchPay API credentials and settings
  - Mobile money provider configurations
  - Payment validation rules

- **Payment API Service** (`js/payment-api.js`)
  - Payment request handling
  - Transaction status monitoring
  - Local storage management

- **Payment UI** (`js/review.js` + `css/payment-styles.css`)
  - Provider selection interface
  - Payment form validation
  - Real-time status updates

#### 2. Backend Components
- **Payment Request API** (`api/payment-request.php`)
  - Secure payment request processing
  - IntouchPay API communication
  - Transaction logging

- **Payment Callback Handler** (`payment-callback.php`)
  - Receives payment completion notifications
  - Updates transaction status
  - Handles success/failure scenarios

#### 3. Configuration Files
- **Apache Configuration** (`.htaccess`)
  - API routing
  - CORS headers
  - Security settings

### API Credentials
```
Username: testa
Partner Password: +$J<wtZktTDs&-Mk("h5=<PH#Jf769P5/Z<*xbR~
Account ID: 250160000011
Base URL: https://www.intouchpay.co.rw/api
```

### Supported Mobile Money Providers

#### MTN Mobile Money
- **Prefixes**: 078, 079
- **Color**: #FFCC00 (Yellow)
- **Icon**: Mobile phone icon

#### Airtel Money
- **Prefixes**: 073, 072
- **Color**: #FF0000 (Red)
- **Icon**: Mobile phone icon

### Payment Flow

1. **Application Submission**
   - User completes certificate application
   - Clicks "Submit Application" on review page
   - Payment section becomes visible

2. **Payment Provider Selection**
   - User selects MTN or Airtel
   - Enters mobile money phone number
   - System auto-detects provider from phone number

3. **Payment Request**
   - Frontend validates form data
   - Sends request to backend API
   - Backend generates secure request to IntouchPay
   - User receives USSD prompt on phone

4. **Payment Completion**
   - User completes payment via USSD
   - IntouchPay sends callback to system
   - System updates transaction status
   - User receives confirmation

### Security Features

#### Password Generation
```php
$password = hash('sha256', $username . $accountno . $partnerpassword . $timestamp);
```

#### Request Validation
- Phone number format validation
- Amount range validation (RWF 100 - 1,000,000)
- Provider compatibility checking
- Duplicate transaction prevention

#### Data Protection
- Sensitive files protected via .htaccess
- Transaction logging for audit trail
- Secure API communication
- CORS headers for cross-origin requests

### File Structure
```
/
├── js/
│   ├── payment-config.js      # Payment configuration
│   ├── payment-api.js         # Payment API service
│   └── review.js              # Updated review page logic
├── css/
│   └── payment-styles.css     # Payment UI styles
├── api/
│   └── payment-request.php    # Backend payment API
├── payment-callback.php       # Payment callback handler
├── test-payment.html          # Payment testing page
└── .htaccess                  # Apache configuration
```

### Testing

#### Test Page
Access `test-payment.html` to test:
- Configuration loading
- Phone number validation
- Payment request functionality
- Status checking
- API connectivity

#### Test Scenarios
1. **Valid MTN Number**: 0788123456
2. **Valid Airtel Number**: 0732123456
3. **Invalid Number**: 0123456789
4. **Amount Validation**: Test with amounts below 100 and above 1,000,000

### Error Handling

#### Common Error Codes
- `0002`: Missing Username Information
- `0005`: Invalid Password
- `1002`: Mobile number not registered
- `1100`: Number not supported on network
- `2100`: Amount should be greater than 0
- `2400`: Duplicate Transaction ID

#### Frontend Error Handling
- Form validation before submission
- User-friendly error messages
- Retry mechanisms for failed requests
- Timeout handling for status checks

### Monitoring and Logging

#### Log Files
- `payment_api_logs.txt`: API request/response logs
- `payment_logs.txt`: Callback notification logs
- `transactions/`: Individual transaction files
- `payments/`: Payment completion records

#### Transaction Tracking
- Unique transaction IDs generated
- Status monitoring every 10 seconds
- Maximum 5-minute timeout
- Local storage for client-side tracking

### Deployment Checklist

1. **Server Requirements**
   - PHP 7.4+ with cURL extension
   - Apache with mod_rewrite enabled
   - Write permissions for log directories

2. **Configuration**
   - Update IntouchPay credentials if needed
   - Set correct callback URL in config
   - Configure SSL certificate for production

3. **Testing**
   - Run test-payment.html scenarios
   - Verify callback URL accessibility
   - Test with actual mobile money accounts

4. **Security**
   - Ensure log files are protected
   - Verify HTTPS is enforced
   - Check file permissions

### Support and Maintenance

#### Regular Tasks
- Monitor log files for errors
- Clean up old transaction files
- Update payment provider configurations
- Review security headers

#### Troubleshooting
- Check IntouchPay API status
- Verify callback URL accessibility
- Review transaction logs
- Test with different phone numbers

### Contact Information
For technical support or integration questions, contact the Diocese of Byumba IT department.
