# Automated Documentation Update Guide

## 🔄 Overview

The West2ES system includes an automated documentation update system that can automatically update your documentation and push changes to GitHub when you make code modifications.

## 🚀 Quick Start

### Option 1: Using Batch File (Easiest)
```bash
# Update documentation only
scripts\auto-update.bat docs

# Auto-push to GitHub
scripts\auto-update.bat push

# Full update (docs + push)
scripts\auto-update.bat full
```

### Option 2: Using PowerShell Directly
```powershell
# Update documentation only
powershell -ExecutionPolicy Bypass -File "scripts\auto-doc-update.ps1" -UpdateDocs

# Auto-push to GitHub
powershell -ExecutionPolicy Bypass -File "scripts\auto-doc-update.ps1" -AutoPush

# Full update (docs + push)
powershell -ExecutionPolicy Bypass -File "scripts\auto-doc-update.ps1" -FullUpdate
```

## 📋 How It Works

### 1. Documentation Update Process
The system automatically detects changes in your code files and updates the corresponding documentation:

- **Authentication Files**: Updates `docs/authentication/login-process.md`
- **File Management Files**: Updates `docs/file-management/upload-process.md`
- **Database Files**: Updates `docs/database/table-structure.md`
- **New Features**: Detects new functions/classes and prompts for documentation updates

### 2. Git Integration
- Automatically stages updated documentation files
- Pushes changes to GitHub repository
- Provides feedback on success/failure

### 3. File Monitoring
The system monitors these file patterns:
- `modals/*login*.php` - Login-related files
- `functions/*file*.php` - File management functions
- `functions/*db*.php` - Database functions
- `*.php` - All PHP files for new feature detection

## 🔧 Configuration

### PowerShell Execution Policy
If you encounter execution policy errors, run:
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Customizing File Patterns
Edit `scripts/auto-doc-update.ps1` to modify which files trigger documentation updates:

```powershell
# Add your custom patterns here
switch -Wildcard ($FilePath) {
    "*your_pattern*" {
        Update-YourCustomDocumentation
    }
    # ... existing patterns
}
```

## 📝 Manual Documentation Updates

### Updating Specific Documentation
If you need to manually update documentation:

1. **Edit the documentation file**:
   ```markdown
   # In docs/authentication/login-process.md
   **Last Updated**: August 12, 2025
   ```

2. **Run the update script**:
   ```bash
   scripts\auto-update.bat docs
   ```

3. **Commit and push**:
   ```bash
   git add docs/
   git commit -m "Updated documentation"
   git push origin main
   ```

## 🔍 Monitoring and Logs

### Viewing Update Logs
The script provides colored output:
- 🟢 **Green**: Successful updates
- 🟡 **Yellow**: Warnings or suggestions
- 🔴 **Red**: Errors
- 🔵 **Cyan**: Information

### Debug Mode
To see detailed information, edit the script and add:
```powershell
$VerbosePreference = "Continue"
```

## 🛠️ Troubleshooting

### Common Issues

1. **PowerShell Execution Policy Error**
   ```powershell
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   ```

2. **Git Not Found**
   - Ensure Git is installed and in your PATH
   - Restart your terminal after Git installation

3. **Permission Denied**
   - Run as Administrator if needed
   - Check file permissions

4. **GitHub Push Failed**
   - Check your internet connection
   - Verify GitHub credentials
   - Ensure you have push permissions

### Manual Recovery
If automation fails, you can always update manually:

```bash
# Update documentation dates
# Edit the files manually and update "Last Updated" dates

# Stage and commit
git add docs/
git commit -m "Manual documentation update"
git push origin main
```

## 🔄 Integration with Your Workflow

### Recommended Workflow
1. **Make code changes**
2. **Stage your changes**: `git add .`
3. **Run auto-update**: `scripts\auto-update.bat full`
4. **Review changes**: The script will show what was updated
5. **Commit**: `git commit -m "Your commit message"`

### IDE Integration
You can integrate this into your IDE:

**VS Code**:
- Add to tasks.json:
```json
{
    "label": "Update Documentation",
    "type": "shell",
    "command": "scripts\\auto-update.bat",
    "args": ["docs"]
}
```

**PHPStorm**:
- Create External Tool pointing to `scripts\auto-update.bat`
- Assign keyboard shortcut

## 📈 Advanced Features

### Custom Documentation Templates
Create custom templates for new features:

```powershell
# Add to auto-doc-update.ps1
function Update-CustomDocumentation {
    $template = @"
# New Feature Documentation

## Overview
[Describe the new feature]

## Implementation
[Code examples and explanations]

**Last Updated**: $(Get-Date -Format "MMMM d, yyyy")
"@
    
    Set-Content "docs/your-new-feature.md" $template
}
```

### Automated Testing
Add documentation validation:

```powershell
# Validate documentation completeness
function Test-Documentation {
    $requiredFiles = @(
        "docs/system-overview.md",
        "docs/authentication/login-process.md",
        "docs/file-management/upload-process.md"
    )
    
    foreach ($file in $requiredFiles) {
        if (-not (Test-Path $file)) {
            Write-Host "❌ Missing: $file" -ForegroundColor Red
        }
    }
}
```

## 🎯 Best Practices

1. **Regular Updates**: Run the script after each significant code change
2. **Review Changes**: Always review what the script updated
3. **Backup**: Keep backups of important documentation
4. **Version Control**: All documentation is version controlled
5. **Consistency**: Maintain consistent formatting across all docs

## 📞 Support

If you encounter issues:
1. Check the troubleshooting section above
2. Review the script logs for error messages
3. Verify your Git and PowerShell setup
4. Test with manual updates first

---

**Last Updated**: August 12, 2025  
**Script Version**: 1.0  
**Maintainer**: System Administrator
