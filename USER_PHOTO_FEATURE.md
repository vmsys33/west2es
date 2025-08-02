# User Photo Feature

This feature allows users to upload and manage their profile photos in the West 2 Elementary School system.

## Features Implemented

### 1. Database Changes
- Added `photo` field to `user_data` table (VARCHAR(255))
- Field stores the filename of the uploaded photo

### 2. File Structure
- Created `uploads/files/user_photos/` directory for storing user photos
- Photos are stored with unique filenames: `user_{userId}_{timestamp}.{extension}`

### 3. Navbar Integration
- Updated `includes/top-navbar.php` to display user photos
- Circular responsive images (40x40px) in the top navbar
- Fallback to FontAwesome user icon if no photo is uploaded
- Added dropdown menu with profile management options

### 4. Photo Management Page
- Created `pages/profile_photo.php` for photo upload/management
- Drag-and-drop file upload functionality
- Support for JPG, PNG, GIF formats (max 5MB)
- Photo preview with delete option
- Responsive design with Bootstrap

### 5. Functions
- `functions/upload_user_photo.php` contains all photo-related functions:
  - `uploadUserPhoto()` - Handles file upload and validation
  - `getUserPhoto()` - Retrieves user photo path
  - `deleteUserPhoto()` - Removes user photo and file

## Usage

### For Users
1. **Upload Photo**: Click on your profile photo in the navbar → "Manage Photo"
2. **Change Photo**: Drag and drop or click to browse for a new photo
3. **Delete Photo**: Use the delete button on the photo management page
4. **View Photo**: Your photo appears as a circular image in the top navbar

### For Developers
1. **Get User Photo**: Use `getUserPhoto($userId)` function
2. **Upload Photo**: Use `uploadUserPhoto($userId, $file)` function
3. **Delete Photo**: Use `deleteUserPhoto($userId)` function

## Security Features
- File type validation (JPG, PNG, GIF only)
- File size limit (5MB maximum)
- Unique filename generation to prevent conflicts
- Database transaction safety (deletes file if DB update fails)
- XSS protection with `htmlspecialchars()`

## Responsive Design
- Photos automatically scale to fit circular containers
- Uses `object-fit: cover` for proper image cropping
- Responsive sizing for different screen sizes
- Bootstrap classes for consistent styling

## File Locations
- **Photos**: `uploads/files/user_photos/`
- **Management Page**: `pages/profile_photo.php`
- **Functions**: `functions/upload_user_photo.php`
- **Navbar**: `includes/top-navbar.php`

## Database Schema
```sql
ALTER TABLE user_data ADD COLUMN photo VARCHAR(255) DEFAULT NULL AFTER email;
```

## Browser Support
- Modern browsers with drag-and-drop support
- Graceful fallback for older browsers
- Mobile-friendly touch interactions 