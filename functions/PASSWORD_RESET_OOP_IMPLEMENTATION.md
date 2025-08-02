# Password Reset OOP Implementation

## Overview

This document describes the Object-Oriented Programming (OOP) implementation of the password reset functionality for the West 2 Elementary School system. The implementation provides clean, maintainable, and secure password reset functionality for both admin and faculty users.

## Architecture

### Core Components

1. **PasswordResetManager Class** (`functions/PasswordResetManager.php`)
   - Main class handling all password reset operations
   - Encapsulates database operations, email sending, and validation logic
   - Provides clean interface for password reset functionality

2. **Updated forget_password.php** (`functions/forget_password.php`)
   - Simplified handler that uses the PasswordResetManager class
   - Handles HTTP requests and generates SweetAlert responses
   - Clean separation of concerns

3. **Test Suite** (`tests/test_oop_password_reset.php`)
   - Comprehensive testing of the OOP implementation
   - Validates all functionality including edge cases

## Key Features

### 1. Database Verification
- **Admin Verification**: Checks if email exists in `user_data` table with `role = 'admin'` and `status = 'active'`
- **Faculty Verification**: Checks if email exists in `user_data` table with `role = 'faculty'` and `status = 'active'`
- **Cross-Role Protection**: Prevents admin emails from being used for faculty reset and vice versa

### 2. Security Features
- **Email Format Validation**: Ensures valid email format
- **User Type Validation**: Validates user type (admin/faculty)
- **Pending Reset Check**: Prevents multiple reset requests
- **Secure Token Generation**: Uses cryptographically secure random tokens
- **Token Expiration**: Reset links expire after 1 hour

### 3. User Experience
- **Clear Error Messages**: Specific error messages for different failure scenarios
- **Success Notifications**: Detailed success messages with next steps
- **Email Templates**: Professional HTML email templates with user's name

## Class Structure

### PasswordResetManager Class

```php
class PasswordResetManager {
    private $pdo;        // Database connection
    private $email;      // User email
    private $userType;   // User type (admin/faculty)
    private $user;       // User data from database
    
    // Public Methods
    public function __construct($email, $userType)
    public function validateEmailFormat()
    public function validateUserType()
    public function verifyUserInDatabase()
    public function hasPendingReset()
    public function generateResetToken()
    public function sendResetEmail($token)
    public function processResetRequest()
    public function getUser()
    public function getUserType()
    public function getEmail()
    
    // Private Methods
    private function getEmailBody($resetLink)
    private function getEmailAltBody($resetLink)
}
```

## Usage Examples

### Basic Usage

```php
// Create instance
$resetManager = new PasswordResetManager($email, $userType);

// Process reset request
$result = $resetManager->processResetRequest();

// Handle result
if ($result['status'] === 'success') {
    // Show success message
} else {
    // Show error message
}
```

### Individual Method Usage

```php
$resetManager = new PasswordResetManager($email, $userType);

// Validate email format
if (!$resetManager->validateEmailFormat()) {
    // Handle invalid email
}

// Validate user type
if (!$resetManager->validateUserType()) {
    // Handle invalid user type
}

// Check if user exists in database
if (!$resetManager->verifyUserInDatabase()) {
    // Handle user not found
}

// Check for pending reset
if ($resetManager->hasPendingReset()) {
    // Handle pending reset
}
```

## Database Schema

The implementation uses the existing `user_data` table:

```sql
CREATE TABLE user_data (
    id_no INT PRIMARY KEY AUTO_INCREMENT,
    deped_id_no VARCHAR(20) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    role ENUM('faculty', 'admin') NOT NULL,
    password VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255) DEFAULT NULL
);
```

## Error Handling

The implementation provides comprehensive error handling:

1. **Invalid Email Format**: Returns error for malformed email addresses
2. **Invalid User Type**: Returns error for unsupported user types
3. **User Not Found**: Returns specific error for non-existent users
4. **Wrong User Type**: Returns error when email doesn't match user type
5. **Pending Reset**: Returns warning when reset already requested
6. **Database Errors**: Logs and handles database connection issues
7. **Email Sending Errors**: Handles SMTP and email delivery failures

## Benefits of OOP Implementation

### 1. Maintainability
- **Single Responsibility**: Each method has a specific purpose
- **Encapsulation**: Database and email logic are hidden from calling code
- **Reusability**: Class can be used in different contexts

### 2. Testability
- **Unit Testing**: Individual methods can be tested independently
- **Mocking**: Dependencies can be easily mocked for testing
- **Comprehensive Coverage**: Test suite covers all scenarios

### 3. Security
- **Input Validation**: All inputs are validated before processing
- **Database Security**: Uses prepared statements to prevent SQL injection
- **Token Security**: Cryptographically secure token generation

### 4. User Experience
- **Clear Feedback**: Specific error messages for different scenarios
- **Professional Emails**: Well-formatted HTML email templates
- **Consistent Interface**: Same interface for admin and faculty users

## Testing

Run the test suite to verify functionality:

```bash
php tests/test_oop_password_reset.php
```

The test suite validates:
- Email format validation
- User type validation
- Database verification
- Cross-role validation
- Error handling
- Success scenarios

## Integration

The OOP implementation integrates seamlessly with the existing system:

1. **Frontend**: JavaScript calls remain the same
2. **Database**: Uses existing `user_data` table structure
3. **Email**: Uses existing PHPMailer configuration
4. **UI**: Generates same SweetAlert responses

## Future Enhancements

Potential improvements for future versions:

1. **Rate Limiting**: Add rate limiting to prevent abuse
2. **Audit Logging**: Log all password reset attempts
3. **Multiple Email Providers**: Support for different SMTP providers
4. **Template System**: Externalize email templates
5. **Configuration**: Move settings to configuration file

## Conclusion

The OOP implementation provides a robust, secure, and maintainable solution for password reset functionality. It follows best practices for object-oriented design and provides comprehensive error handling and user feedback. 