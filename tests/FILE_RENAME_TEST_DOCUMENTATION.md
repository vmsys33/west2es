# File Rename Testing Suite Documentation

## Overview

This testing suite provides comprehensive testing for the file renaming functionality in the WEST2ES system. It includes both PHPUnit tests and a simple test runner that can be executed without PHPUnit.

## Test Files

### 1. `FileRenameTest.php` (PHPUnit Test Class)
- **Purpose**: Full PHPUnit test suite for file renaming functionality
- **Usage**: Run with `php vendor/bin/phpunit tests/FileRenameTest.php`
- **Features**: 
  - 10 comprehensive test methods
  - Database integration testing
  - Frontend JavaScript validation
  - Security and validation testing

### 2. `run_file_rename_tests.php` (Simple Test Runner)
- **Purpose**: Standalone test runner that doesn't require PHPUnit
- **Usage**: Run with `php tests/run_file_rename_tests.php`
- **Features**:
  - 7 core test functions
  - User-friendly output with emojis
  - Detailed error reporting
  - Test summary with success rate

## Test Coverage

### 1. Required Files Check
**Test**: `testRequiredFilesExist()` / `testRequiredFiles()`
- Verifies all required files exist:
  - `functions/file_functions/edit_filename.php`
  - `functions/file_functions/rename_revision_file.php`
  - `pages/content.php`
  - `functions/notification_helper.php`

### 2. Database Structure Validation
**Test**: `testDatabaseTablesStructure()` / `testDatabaseTables()`
- Checks all file tables have required columns:
  - Main tables: `id`, `filename`, `file_path`, `status`
  - Version tables: `id`, `file_id`, `filename`, `file_path`, `status`
- Validates table relationships

### 3. Main File Rename Functionality
**Test**: `testMainFileRename()` / `testMainFileRename()`
- Tests renaming main files (not revisions)
- Verifies database updates
- Checks file path updates
- Includes cleanup to restore original filename

### 4. Revision File Rename Functionality
**Test**: `testRevisionFileRename()` / `testRevisionFileRename()`
- Tests renaming revision files
- Verifies database updates in versions table
- Checks file path updates
- Includes cleanup to restore original filename

### 5. Specific Filename Testing
**Test**: `testSpecificFilenameRename()` / `testSpecificFilenameRename()`
- Tests renaming to specific predefined filenames:
  - `document_2024.pdf`
  - `report_final.docx`
  - `presentation_v2.pptx`
  - `data_analysis.xlsx`
  - `contract_agreement.pdf`
- Verifies each specific filename is set correctly

### 6. Invalid Filename Handling
**Test**: `testInvalidFilenameHandling()` / `testInvalidFilenameHandling()`
- Tests rejection of invalid filenames:
  - Empty filenames
  - Whitespace-only filenames
  - Filenames with invalid characters (`/`, `\`, `:`, `*`, `?`, `"`, `<`, `>`, `|`)
- Ensures security validation works

### 7. Duplicate Filename Prevention
**Test**: `testDuplicateFilenameHandling()` (PHPUnit only)
- Tests that duplicate filenames are rejected
- Renames first file to a specific name
- Attempts to rename second file to same name
- Verifies second rename is rejected

### 8. File Extension Preservation
**Test**: `testFileExtensionPreservation()` (PHPUnit only)
- Tests that file extensions are preserved when renaming
- Renames file without extension
- Verifies original extension is maintained

### 9. AJAX Endpoint Validation
**Test**: `testAjaxEndpointsAvailable()` (PHPUnit only)
- Verifies AJAX endpoints exist and return JSON
- Checks content-type headers
- Validates endpoint structure

### 10. Frontend JavaScript Validation
**Test**: `testFrontendJavaScript()` / `testFrontendJavaScript()`
- Checks for required JavaScript functionality:
  - `edit-filename` class
  - `rename-revision` class
  - `Swal.fire` (SweetAlert2)
  - `$.ajax` (jQuery AJAX)

## Running the Tests

### Option 1: Using PHPUnit (Recommended)
```bash
# Run all file rename tests
php vendor/bin/phpunit tests/FileRenameTest.php

# Run specific test method
php vendor/bin/phpunit --filter testMainFileRename tests/FileRenameTest.php

# Run with verbose output
php vendor/bin/phpunit --verbose tests/FileRenameTest.php
```

### Option 2: Using Simple Test Runner
```bash
# Run the simple test runner
php tests/run_file_rename_tests.php
```

## Test Requirements

### Database Requirements
- All file tables must exist with proper structure
- At least one approved file in each table for testing
- Database connection must be configured

### File System Requirements
- All required PHP files must exist
- File upload directories must be writable
- Test files must be accessible

### Session Requirements
- Session must be started
- User must be logged in as admin
- Proper session variables must be set

## Test Data Management

### Automatic Cleanup
- Tests automatically restore original filenames after testing
- Database changes are reverted
- File system changes are undone

### Test File Selection
- Tests automatically find suitable test files
- Uses files with `status = 'approve'`
- Skips tests if no suitable files are found

## Expected Test Results

### Successful Test Run
```
🚀 File Rename Test Runner
==========================

🧪 Running: Required Files Check
   ✅ All required files exist
✅ PASSED: Required Files Check

🧪 Running: Database Tables Structure
   ✅ All database tables have required columns
✅ PASSED: Database Tables Structure

🧪 Running: Main File Rename Functionality
   ✅ Main file rename test passed
✅ PASSED: Main File Rename Functionality

🧪 Running: Revision File Rename Functionality
   ✅ Revision file rename test passed
✅ PASSED: Revision File Rename Functionality

🧪 Running: Specific Filename Rename
   ✅ Specific filename rename test passed
✅ PASSED: Specific Filename Rename

🧪 Running: Invalid Filename Handling
   ✅ Invalid filename handling test passed
✅ PASSED: Invalid Filename Handling

🧪 Running: Frontend JavaScript
   ✅ Frontend JavaScript test passed
✅ PASSED: Frontend JavaScript

📊 Test Summary
===============
Total Tests: 7
Passed: 7
Failed: 0
Success Rate: 100%

🎉 All tests passed! File rename functionality is working correctly.
```

### Failed Test Example
```
🧪 Running: Main File Rename Functionality
   ❌ Main file rename failed: File not found
❌ FAILED: Main File Rename Functionality

📊 Test Summary
===============
Total Tests: 7
Passed: 6
Failed: 1
Success Rate: 85.71%

⚠️  Some tests failed. Please check the errors above.
```

## Troubleshooting

### Common Issues

1. **"No test file available"**
   - Solution: Add approved files to the database
   - Check that files have `status = 'approve'`

2. **"Database connection failed"**
   - Solution: Check database configuration in `functions/db_connection.php`
   - Verify database server is running

3. **"Missing required files"**
   - Solution: Ensure all required PHP files exist
   - Check file paths are correct

4. **"Permission denied"**
   - Solution: Check file system permissions
   - Ensure upload directories are writable

### Debug Mode

To enable debug output, modify the test files to include:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Integration with CI/CD

### GitHub Actions Example
```yaml
name: File Rename Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
    - name: Install dependencies
      run: composer install
    - name: Run file rename tests
      run: php tests/run_file_rename_tests.php
```

## Security Considerations

### Input Validation Testing
- All tests include input validation checks
- Invalid filenames are properly rejected
- SQL injection prevention is tested
- XSS prevention is validated

### File System Security
- Tests verify file path sanitization
- Directory traversal prevention is checked
- File extension validation is tested

## Performance Considerations

### Test Execution Time
- Individual tests should complete within 5 seconds
- Full test suite should complete within 30 seconds
- Database queries are optimized for speed

### Memory Usage
- Tests use minimal memory footprint
- Database connections are properly managed
- File handles are properly closed

## Maintenance

### Adding New Tests
1. Add test method to `FileRenameTest.php`
2. Add corresponding function to `run_file_rename_tests.php`
3. Update this documentation
4. Test the new functionality

### Updating Test Data
- Tests automatically adapt to available data
- No manual test data setup required
- Tests are self-contained and portable

## Support

For issues with the testing suite:
1. Check the troubleshooting section above
2. Review error messages carefully
3. Verify all requirements are met
4. Test with a simple file rename manually first

## Version History

- **v1.0**: Initial test suite creation
- **v1.1**: Added specific filename testing
- **v1.2**: Enhanced error reporting and debugging
- **v1.3**: Added comprehensive documentation 