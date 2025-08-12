# Database Table Structure Documentation

## 🗄️ West2ES Database Schema

### Overview
The West2ES system uses a MySQL database with 25+ tables organized into logical categories: user management, file storage, system configuration, and audit logging.

## 👥 User Management Tables

### 1. user_data
**Purpose**: Primary user information and authentication

```sql
CREATE TABLE user_data (
    id_no INT PRIMARY KEY AUTO_INCREMENT,
    deped_id_no VARCHAR(20) UNIQUE NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    role ENUM('faculty', 'admin') NOT NULL,
    password VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255),
    photo VARCHAR(255),
    email_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Key Fields**:
- `id_no`: Primary key, auto-increment
- `deped_id_no`: 7-digit DepEd ID (unique)
- `role`: User role (faculty/admin)
- `status`: Account status
- `photo`: Profile photo path
- `email_verified`: Email verification status

### 2. user_data_details
**Purpose**: Extended user information

```sql
CREATE TABLE user_data_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    suffix VARCHAR(10),
    date_of_birth DATE,
    birthplace VARCHAR(100),
    sex ENUM('Male', 'Female'),
    position VARCHAR(100),
    contact_no VARCHAR(20),
    personal_gmail VARCHAR(100),
    bachelors_degree VARCHAR(100),
    post_graduate VARCHAR(100),
    major VARCHAR(100),
    employee_no VARCHAR(20),
    plantilla_no VARCHAR(20),
    philhealth_no VARCHAR(20),
    bp_no VARCHAR(20),
    pagibig_no VARCHAR(20),
    tin_no VARCHAR(20),
    prc_no VARCHAR(20),
    prc_validity_date DATE,
    phlisys_id_no VARCHAR(20),
    salary_grade VARCHAR(10),
    current_step VARCHAR(50),
    date_first_appointment DATE,
    date_latest_promotion DATE,
    first_day_service DATE,
    retirement_day DATE,
    FOREIGN KEY (user_id) REFERENCES user_data(id_no)
);
```

## 📁 File Storage Tables

### 3. pending_files
**Purpose**: Temporary storage for uploaded files awaiting approval

```sql
CREATE TABLE pending_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    download_path VARCHAR(255) NOT NULL,
    file_size VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    table1 VARCHAR(100) NOT NULL,
    table2 VARCHAR(100) NOT NULL,
    version_no VARCHAR(10) DEFAULT '1',
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES user_data(id_no)
);
```

### 4. master_files
**Purpose**: Central registry of all approved files

```sql
CREATE TABLE master_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    version_no VARCHAR(10) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    datetime DATETIME NOT NULL,
    file_size VARCHAR(50) NOT NULL,
    table1 VARCHAR(100) NOT NULL,
    table2 VARCHAR(100) NOT NULL,
    download_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5. File Category Tables
**Structure**: Each category has a main table and version table

#### Example: admin_files
```sql
CREATE TABLE admin_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user_data(id_no)
);
```

#### Example: admin_files_versions
```sql
CREATE TABLE admin_files_versions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    version_no VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    file_size VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    FOREIGN KEY (file_id) REFERENCES admin_files(id)
);
```

**File Categories**:
- `admin_files` / `admin_files_versions`
- `aeld_files` / `aeld_files_versions`
- `cild_files` / `cild_files_versions`
- `if_completed_files` / `if_completed_files_versions`
- `if_proposals_files` / `if_proposals_files_versions`
- `lulr_files` / `lulr_files_versions`
- `rp_completed_berf_files` / `rp_completed_berf_files_versions`
- `rp_completed_nonberf_files` / `rp_completed_nonberf_files_versions`
- `rp_proposal_berf_files` / `rp_proposal_berf_files_versions`
- `rp_proposal_nonberf_files` / `rp_proposal_nonberf_files_versions`
- `t_lr_files` / `t_lr_files_versions`
- `t_pp_files` / `t_pp_files_versions`
- `t_rs_files` / `t_rs_files_versions`
- `approved_proposal` / `approved_proposal_versions`

## 🔔 System Tables

### 6. notifications
**Purpose**: System notifications and alerts

```sql
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role VARCHAR(50) NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seen TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES user_data(id_no)
);
```

### 7. events
**Purpose**: School events and activities

```sql
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    datetime DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 8. general_setting
**Purpose**: System configuration settings

```sql
CREATE TABLE general_setting (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_name VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 9. schoolmissionvision
**Purpose**: School mission and vision statements

```sql
CREATE TABLE schoolmissionvision (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('mission', 'vision', 'goals') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 📊 Audit and Logging Tables

### 10. file_approval_logs
**Purpose**: Track file approval activities

```sql
CREATE TABLE file_approval_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pending_file_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    approved_by INT NOT NULL,
    approved_by_name VARCHAR(100) NOT NULL,
    approval_date DATETIME NOT NULL,
    target_table1 VARCHAR(100) NOT NULL,
    target_table2 VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 11. file_preview_mapping
**Purpose**: File preview and access mapping

```sql
CREATE TABLE file_preview_mapping (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_path VARCHAR(255) NOT NULL,
    preview_type VARCHAR(50) NOT NULL,
    access_count INT DEFAULT 0,
    last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 🔗 Table Relationships

### Primary Relationships
```
user_data (1) ←→ (N) user_data_details
user_data (1) ←→ (N) pending_files
user_data (1) ←→ (N) master_files
user_data (1) ←→ (N) notifications

admin_files (1) ←→ (N) admin_files_versions
aeld_files (1) ←→ (N) aeld_files_versions
cild_files (1) ←→ (N) cild_files_versions
... (similar for all file categories)
```

### Foreign Key Constraints
- All file tables reference `user_data(id_no)`
- Version tables reference their parent file tables
- Audit logs reference both users and files

## 📈 Database Statistics

### Table Counts
- **User Management**: 2 tables
- **File Storage**: 28 tables (14 categories × 2 tables each)
- **System Tables**: 4 tables
- **Audit Tables**: 2 tables
- **Total**: 36 tables

### Indexes
- **Primary Keys**: All tables have auto-increment primary keys
- **Unique Indexes**: 
  - `user_data.deped_id_no`
  - `user_data.email`
  - `general_setting.setting_name`
- **Foreign Key Indexes**: All foreign key columns are indexed

## 🔧 Database Configuration

### Connection Settings
**Location**: `functions/db_connection.php`

```php
$host = 'localhost';
$dbname = 'west2es_db';
$username = 'your_username';
$password = 'your_password';
$charset = 'utf8mb4';
```

### Performance Optimization
- **Query Optimization**: Prepared statements for all queries
- **Index Strategy**: Strategic indexing on frequently queried columns
- **Connection Pooling**: PDO connection management
- **Transaction Management**: ACID compliance for critical operations

## 🛡️ Security Features

### Data Protection
- **Password Hashing**: bcrypt hashing for all passwords
- **Input Sanitization**: All user inputs are sanitized
- **SQL Injection Prevention**: PDO prepared statements
- **Access Control**: Role-based data access

### Backup Strategy
- **Daily Backups**: Automated database backups
- **Point-in-Time Recovery**: Transaction log backups
- **Data Retention**: Configurable retention policies

---

**Last Updated**: August 12, 2025  
**Database Version**: 2.0  
**Related Files**: 
- `functions/db_connection.php`
- `functions/file_operations.php`
- All file management functions
