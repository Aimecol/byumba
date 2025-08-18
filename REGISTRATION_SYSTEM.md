# Registration System Documentation

## Overview
The Diocese of Byumba registration system allows users to create accounts with secure password hashing and automatic parish membership assignment.

## Features Implemented

### 1. User Registration
- **Frontend**: `register.html` - Complete registration form with validation
- **Backend**: `api/auth.php` - RESTful API endpoint for registration
- **Database**: Secure storage in `users` table with hashed passwords

### 2. Password Security
- Passwords are hashed using PHP's `password_hash()` function with `PASSWORD_DEFAULT`
- Minimum password length of 6 characters
- Client-side password strength indicator
- Password confirmation validation

### 3. Form Validation
- **Client-side**: Real-time validation with visual feedback
- **Server-side**: Comprehensive validation including:
  - Required field validation
  - Email format validation
  - Phone number format validation (Rwanda format)
  - Duplicate email/national ID checking
  - Password strength and confirmation

### 4. Parish Membership
- Automatic parish membership creation upon registration
- Parish selection from predefined list
- Membership role assignment based on membership status
- Baptism date tracking

### 5. Database Integration
- User data stored in `users` table
- Parish membership stored in `user_parish_membership` table
- Transaction-based operations for data consistency
- Foreign key relationships maintained

## API Endpoints

### Registration
- **URL**: `api/auth.php?action=register`
- **Method**: POST
- **Content-Type**: application/json

**Request Body:**
```json
{
    "firstName": "John",
    "lastName": "Doe",
    "email": "john.doe@example.com",
    "phone": "+250788123456",
    "password": "securepassword",
    "confirmPassword": "securepassword",
    "parish": "st-mary",
    "gender": "male",
    "dateOfBirth": "1990-01-01",
    "nationalId": "1234567890123456",
    "address": "Test Address",
    "membershipStatus": "baptized",
    "baptismDate": "2000-01-01"
}
```

**Success Response:**
```json
{
    "success": true,
    "message": "Registration successful! Please check your email for verification.",
    "data": {
        "user_id": 123,
        "email": "john.doe@example.com",
        "name": "John Doe"
    }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": "Email address is already registered",
        "password": "Password must be at least 6 characters long"
    }
}
```

### Login
- **URL**: `api/auth.php?action=login`
- **Method**: POST
- **Content-Type**: application/json

**Request Body:**
```json
{
    "email": "john.doe@example.com",
    "password": "securepassword"
}
```

## Database Schema

### Users Table
```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `phone` varchar(20) DEFAULT NULL,
  `national_id` varchar(20) DEFAULT NULL UNIQUE,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `preferred_language` varchar(5) DEFAULT 'en',
  `email_verified` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
);
```

### User Parish Membership Table
```sql
CREATE TABLE `user_parish_membership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `parish_id` int(11) NOT NULL,
  `membership_date` date DEFAULT NULL,
  `baptism_date` date DEFAULT NULL,
  `role` enum('member','choir','catechist','youth_leader','committee','volunteer') DEFAULT 'member',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`parish_id`) REFERENCES `parishes` (`id`)
);
```

## Security Features

1. **Password Hashing**: Uses PHP's secure password hashing functions
2. **Input Sanitization**: All user inputs are sanitized before database storage
3. **SQL Injection Prevention**: Prepared statements used for all database queries
4. **XSS Prevention**: HTML special characters escaped in output
5. **CSRF Protection**: Can be added with token validation
6. **Email Uniqueness**: Prevents duplicate accounts
7. **National ID Uniqueness**: Prevents identity fraud

## Testing

Run the comprehensive test suite:
```
http://localhost/new/byumba/test_auth.php
```

This test verifies:
- Database connectivity
- Registration API functionality
- Password hashing
- Parish membership creation
- Login API functionality
- Data integrity

## Files Modified/Created

1. **Created**: `api/auth.php` - Authentication API endpoints
2. **Modified**: `register.html` - Updated form submission to use API
3. **Modified**: `login.html` - Updated login to use API
4. **Created**: `test_auth.php` - Comprehensive testing script

## Usage

1. **Registration**: Users fill out the registration form at `register.html`
2. **Validation**: Form validates data client-side and server-side
3. **Storage**: User data is securely stored with hashed password
4. **Parish Assignment**: User is automatically assigned to selected parish
5. **Login**: Users can log in using their email and password
6. **Session Management**: Successful login creates user session

## Future Enhancements

1. Email verification system
2. Password reset functionality
3. Two-factor authentication
4. Account lockout after failed attempts
5. Password complexity requirements
6. CAPTCHA integration
7. Social media login integration
