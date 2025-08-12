# West2ES Auto Documentation Update Script
# This script automatically updates documentation and Git when code changes are detected

param(
    [switch]$UpdateDocs,
    [switch]$AutoPush,
    [switch]$FullUpdate
)

Write-Host "🔍 West2ES Auto Documentation Update Script" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green

# Function to update documentation based on file changes
function Update-Documentation {
    param([string]$FilePath)
    
    $fileName = Split-Path $FilePath -Leaf
    $fileDir = Split-Path $FilePath -Parent
    
    Write-Host "📝 Processing: $FilePath" -ForegroundColor Yellow
    
    # Update documentation based on file type
    switch -Wildcard ($FilePath) {
        "*login*" {
            Update-LoginDocumentation
        }
        "*file*" {
            Update-FileDocumentation
        }
        "*db*" {
            Update-DatabaseDocumentation
        }
        "*.php" {
            Check-NewFeatures $FilePath
        }
    }
}

# Function to update login documentation
function Update-LoginDocumentation {
    $docFile = "docs/authentication/login-process.md"
    if (Test-Path $docFile) {
        $content = Get-Content $docFile -Raw
        $newDate = Get-Date -Format "MMMM d, yyyy"
        $content = $content -replace "Last Updated.*", "Last Updated**: $newDate"
        Set-Content $docFile $content
        Write-Host "✅ Updated authentication documentation" -ForegroundColor Green
    }
}

# Function to update file management documentation
function Update-FileDocumentation {
    $docFile = "docs/file-management/upload-process.md"
    if (Test-Path $docFile) {
        $content = Get-Content $docFile -Raw
        $newDate = Get-Date -Format "MMMM d, yyyy"
        $content = $content -replace "Last Updated.*", "Last Updated**: $newDate"
        Set-Content $docFile $content
        Write-Host "✅ Updated file management documentation" -ForegroundColor Green
    }
}

# Function to update database documentation
function Update-DatabaseDocumentation {
    $docFile = "docs/database/table-structure.md"
    if (Test-Path $docFile) {
        $content = Get-Content $docFile -Raw
        $newDate = Get-Date -Format "MMMM d, yyyy"
        $content = $content -replace "Last Updated.*", "Last Updated**: $newDate"
        Set-Content $docFile $content
        Write-Host "✅ Updated database documentation" -ForegroundColor Green
    }
}

# Function to check for new features
function Check-NewFeatures {
    param([string]$FilePath)
    
    $gitDiff = git diff --cached $FilePath 2>$null
    if ($gitDiff -match "function|class|public|private|protected") {
        Write-Host "🔍 New code detected in $FilePath - consider updating documentation" -ForegroundColor Yellow
    }
}

# Function to auto-push to GitHub
function Auto-PushToGit {
    Write-Host "🚀 Auto-pushing changes to GitHub..." -ForegroundColor Cyan
    
    $currentBranch = git branch --show-current
    $pushResult = git push origin $currentBranch 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Successfully pushed to GitHub ($currentBranch branch)" -ForegroundColor Green
        Write-Host "📋 Last commit:" -ForegroundColor Cyan
        git log -1 --oneline
    } else {
        Write-Host "❌ Failed to push to GitHub. Please check your connection and try again." -ForegroundColor Red
        Write-Host "💡 You can manually push with: git push origin $currentBranch" -ForegroundColor Yellow
    }
}

# Main execution
if ($UpdateDocs -or $FullUpdate) {
    Write-Host "📝 Updating documentation..." -ForegroundColor Cyan
    
    # Get modified files
    $modifiedFiles = git diff --cached --name-only
    
    foreach ($file in $modifiedFiles) {
        if (Test-Path $file) {
            Update-Documentation $file
        }
    }
    
    # Stage updated documentation
    git add docs/ 2>$null
    Write-Host "✅ Documentation updates staged" -ForegroundColor Green
}

if ($AutoPush -or $FullUpdate) {
    Auto-PushToGit
}

Write-Host "✅ Auto documentation update completed!" -ForegroundColor Green
