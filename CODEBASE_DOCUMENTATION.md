# Codebase Documentation

This document provides a detailed explanation of the core functionalities of the application, including login, registration, and the admin dashboard.

## 1. Login and Registration

The login and registration system is the entry point for all users. It is designed to handle different user roles (admin and faculty) and provide a secure way to authenticate and register users.

### 1.1. Overview

The process begins on the `index.php` page, which serves as the public-facing landing page. This page is primarily a gateway to the login and registration forms, which are presented to the user in modals. The system uses a combination of PHP for server-side processing and JavaScript for client-side interactions, including form validation and asynchronous communication with the server.

### 1.2. File Breakdown

Here are the key files involved in the login and registration process:

#### `index.php`
- **Purpose**: This is the main entry point of the application. It displays the school's banner and a "Menu" button.
- **Functionality**:
  - **Line 267**: The "Menu" button (`#loginBtn`) triggers a modal (`#myModal`).
  - **Line 282**: The main modal (`#myModal`) provides three options: "Admin Login," "Faculty Login," and "Register."
  - **Lines 311, 316, 321**: Each option in the main modal opens a corresponding modal (`#adminLoginModal`, `#facultyLoginModal`, `#registerModal`).
  - **JavaScript**: This file contains extensive JavaScript code to handle form submissions for login, registration, and password resets. It uses the `fetch` API to send data to the server without a page reload and displays feedback to the user with SweetAlert.

#### `modals/admin_login_modal.php`
- **Purpose**: This file contains the HTML structure for the admin login form.
- **Functionality**:
  - The form includes fields for "DepEd ID No.", "Email Address", and "Password".
  - It includes a hidden CSRF token for security.
  - **Line 28**: A "Forgot your password?" link calls the `showResetForm('admin')` JavaScript function, initiating the password reset process for an admin user.

#### `modals/faculty_login_modal.php`
- **Purpose**: This file contains the HTML structure for the faculty login form.
- **Functionality**:
  - The form is nearly identical to the admin login form.
  - **Line 30**: The "Forgot your password?" link calls `showResetForm()` without an argument, relying on the JavaScript to determine the user type.

#### `modals/registration_modal.php`
- **Purpose**: This file contains the HTML for the user registration form.
- **Functionality**:
  - It's a comprehensive form that collects the user's name, DepEd ID, email, contact number, and an optional profile photo.
  - **Line 28**: The email input is cleverly designed to only require the prefix, automatically appending "@deped.gov.ph" to ensure consistency and reduce errors.
  - The form includes client-side validation for all fields.

#### `functions/admin_login_process.php`
- **Purpose**: This PHP script handles the server-side logic for admin login.
- **Functionality**:
  - It receives the form data via a POST request.
  - **Line 17**: It queries the `user_data` table to find a user with a matching "DepEd ID No." and "email".
  - **Line 23**: It uses `password_verify()` to securely check the password.
  - **Line 24**: It verifies that the user has the `role` of 'admin'.
  - If authentication is successful, it sets session variables and returns a JSON success message.

#### `functions/faculty_login_process.php`
- **Purpose**: This script is similar to the admin login process but for faculty members.
- **Functionality**:
  - **Line 24**: It checks for a `role` of 'faculty' instead of 'admin'.

#### `functions/register_process.php`
- **Purpose**: This script handles new user registration.
- **Functionality**:
  - It performs extensive server-side validation of the submitted data.
  - **Line 60**: It checks for duplicate "DepEd ID No." or "email" to prevent duplicate accounts.
  - **Line 46**: It hashes the user's password using `PASSWORD_BCRYPT` for secure storage.
  - **Line 87**: It inserts the new user into the `user_data` table with a default `role` of 'faculty'.
  - It can also handle a profile photo upload.

## 2. Admin Dashboard

The admin dashboard is the central hub for administrators after they log in. It provides a high-level overview of the system's status and provides quick access to key administrative functions.

### 2.1. Overview

The dashboard is primarily a data-driven page that presents key metrics in a clear and accessible format. It is designed to be a starting point for administrative tasks, with links to more detailed pages for managing users, files, and notifications.

### 2.2. File Breakdown

#### `pages/dashboard-overview.php`
- **Purpose**: This is the main file for the admin dashboard.
- **Functionality**:
  - **Line 3**: It checks if the user is logged in and redirects them to the login page if they are not.
  - **Lines 14-60**: It performs a series of database queries to fetch various statistics, such as the number of pending users, pending files, total users, and total files.
  - The fetched data is displayed in "info boxes" that serve as a high-level summary.
  - **Lines 82, 92, 112**: Several of these boxes are clickable, linking to pages like `pending-users.php`, `pending-files.php`, and `notification.php`, allowing the admin to quickly navigate to management pages.

### 2.3. JavaScript Interaction

#### `assets/search_js.js`
- **Purpose**: This file provides a live search functionality that is available throughout the admin panel.
- **Functionality**:
  - **Line 2**: It attaches an event listener to the search input field (`#searchInput2`).
  - **Line 21**: As the user types, it sends a `fetch` request to `../functions/search_suggestions.php` with the search query.
  - The script then dynamically displays the returned suggestions in a dropdown list.
  - It includes features for keyboard navigation (up/down arrows, Enter) and a button to clear the search input.
  - This search functionality is a global feature of the admin panel, likely included via `includes/top-navbar.php`.

## 3. Sidebar Navigation

The sidebar is the primary navigation tool within the admin and faculty dashboard areas, providing access to all major sections of the application.

### 3.1. File Breakdown

#### `includes/sidebar.php`
- **Purpose**: This file generates the collapsible, multi-level navigation menu on the left side of the dashboard.
- **Functionality**:
  - **Role-Based Access Control**: The sidebar is highly dynamic and uses PHP to display different menu items based on the logged-in user's role (`$_SESSION['user_role']`). For example, "Settings" and "Pending Users" are only visible to 'admin' users, while "My Profile" is only for 'faculty'.
  - **Dynamic Notification Badges**: It directly queries the database to fetch counts for pending users, pending files, and unseen notifications, displaying these counts in red badges next to the relevant menu items. This gives users an immediate visual cue for items that require their attention.
  - **Active State Highlighting**: The script checks the current page's filename and adds an `active` class to the corresponding link, making it easy for users to see which section they are currently in.
  - **`content.php` Integration**: A key feature is its integration with `pages/content.php`. Most of the file management links (e.g., "Administrative Files," "Proposals") point to `content.php` with a `current_page` URL parameter (e.g., `content.php?current_page=admin_files`). This shows that `content.php` acts as a generic template for displaying different file categories.

## 4. Generic Content Page

The `content.php` page is a reusable and central component for displaying and managing all file-related data in the application.

### 4.1. File Breakdown

#### `pages/content.php`
- **Purpose**: This file acts as a generic template to render lists of files based on the category selected from the sidebar.
- **Functionality**:
  - **Dynamic Content Rendering**: It uses the `current_page` URL parameter to dynamically determine which set of files to display. For instance, if the URL is `content.php?current_page=aeld_files`, the page fetches and displays files from the `aeld_files` and `aeld_files_versions` database tables.
  - **Security**: It uses a whitelist (`$allowedPages`) to validate the `current_page` parameter, preventing potential SQL injection vulnerabilities.
  - **Full CRUD and Version Control**: The page provides a complete set of tools for file management, all handled through modals and AJAX:
    - **Add File**: Upload new files to the current category.
    - **Preview Revisions**: View a detailed history of all versions of a file, with options to preview each version in a compatible document viewer (e.g., PDF.js for PDFs, Office Web Apps for Word/Excel).
    - **Add/Edit/Rename/Delete Revisions**: Users can manage the entire lifecycle of a file's revisions.
    - **Delete File**: Admins can permanently delete a file and all its associated versions.
  - **Client-Side Enhancement**: The page is heavily enhanced with JavaScript:
    - **jQuery/AJAX**: Powers all the interactive features, ensuring that actions happen without requiring a full page reload.
    - **DataTables**: The file list is rendered in an HTML table that is enhanced with the DataTables.js library, providing out-of-the-box sorting, searching, and pagination.
    - **SweetAlert**: Used for user-friendly confirmation dialogs and notifications.
