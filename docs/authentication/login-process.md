# Login Process Documentation

## 🔐 Authentication System

### Overview
The West2ES system implements a secure authentication system with role-based access control. Users can log in as either Faculty or Administrators, with different permissions and access levels.

### User Roles
- **Faculty**: Can upload files, view their own files, and manage revisions
- **Admin**: Full system access including file approval, user management, and system settings

## 📋 Login Process Flow

### 1. Login Form Display
**Location**: `modals/admin_login_modal.php`, `modals/faculty_login_modal.php`

**Features**:
- Bootstrap modal-based forms
- Client-side validation
- 7-digit DepEd ID validation
- Password field with show/hide toggle

**Code Example**:
```html
<!-- Admin Login Modal -->
<div class="modal fade" id="adminLoginModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="adminLoginForm">
                <input type="text" name="deped_id_no" 
                       pattern="^\d{7}$" minlength="7" maxlength="7" 
                       placeholder="1234567" required>
                <input type="password" name="password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</div>
```

### 2. Form Validation
**Client-Side Validation**:
- DepEd ID: Must be exactly 7 digits
- Password: Required field
- Real-time validation feedback

**Server-Side Validation**:
**Location**: `functions/admin_login_process.php`, `functions/faculty_login_process.php`

**Validation Rules**:
```php
// DepEd ID validation
if (!preg_match('/^\d{7}$/', $deped_id_no)) {
    throw new Exception('DepEd ID must be exactly 7 digits');
}

// Password validation
if (empty($password)) {
    throw new Exception('Password is required');
}
```

### 3. Database Authentication
**Process**:
1. Sanitize input data
2. Query user_data table
3. Verify password hash
4. Check user status (active/inactive)
5. Create session data

**Database Query**:
```sql
SELECT id_no, deped_id_no, last_name, first_name, 
       middle_name, status, role, password 
FROM user_data 
WHERE deped_id_no = ? AND role = 'admin'
```

### 4. Session Management
**Session Data Created**:
```php
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = $user['id_no'];
$_SESSION['deped_id_no'] = $user['deped_id_no'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name'] = $user['last_name'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['login_time'] = time();
```

### 5. Redirect Logic
- **Success**: Redirect to dashboard
- **Failure**: Display error message in modal
- **Inactive User**: Show account status message

## 🔒 Security Features

### Password Security
- **Hashing**: Passwords stored using PHP `password_hash()`
- **Verification**: `password_verify()` for authentication
- **Salt**: Automatic salt generation by PHP

### Session Security
- **Session Timeout**: Configurable session lifetime
- **Session Regeneration**: New session ID on login
- **Secure Cookies**: HttpOnly and Secure flags

### Input Security
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Prevention**: `htmlspecialchars()` for output
- **CSRF Protection**: Form tokens (planned feature)

## 🚨 Error Handling

### Common Error Messages
- "Invalid DepEd ID or password"
- "Account is inactive"
- "DepEd ID must be exactly 7 digits"
- "Database connection error"

### Error Display
- Bootstrap alert components
- SweetAlert2 for enhanced UX
- Modal-based error display

## 📱 Responsive Design

### Mobile Optimization
- Touch-friendly form elements
- Responsive modal sizing
- Optimized button sizes for mobile

### Accessibility
- ARIA labels for screen readers
- Keyboard navigation support
- High contrast color schemes

## 🔧 Configuration

### Session Configuration
**Location**: `functions/db_connection.php`

```php
// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_start();
```

### Login Attempts
- **Rate Limiting**: Configurable login attempt limits
- **Account Lockout**: Temporary lockout after failed attempts
- **Audit Logging**: Login attempt logging

## 📊 Monitoring & Logging

### Login Analytics
- Login success/failure rates
- User activity tracking
- Session duration monitoring

### Security Monitoring
- Failed login attempts
- Suspicious activity detection
- IP address tracking

---

**Last Updated**: August 12, 2025  
**Related Files**: 
- `modals/admin_login_modal.php`
- `modals/faculty_login_modal.php`
- `functions/admin_login_process.php`
- `functions/faculty_login_process.php`
- `includes/header.php` (session checks)
