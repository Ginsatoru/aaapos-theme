# AAAPOS Theme Packager
# Version: 1.0.1

$themeName = "aaapos"
$version = "1.0.0"
$zipFile = "$themeName-v$version.zip"

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  AAAPOS Theme Packager" -ForegroundColor Cyan
Write-Host "  Version: $version" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Remove old zip if exists
if (Test-Path $zipFile) {
    Write-Host "Removing old zip file..." -ForegroundColor Yellow
    Remove-Item $zipFile -Force
}

# Items to EXCLUDE from the zip
$excludeItems = @(
    ".vscode",
    ".git",
    ".gitignore",
    "node_modules",
    "package.json",
    "package-lock.json",
    "build.ps1",
    "*.zip"
)

Write-Host "Building theme package..." -ForegroundColor Green
Write-Host ""

# Get all items in current directory
$allItems = Get-ChildItem -Path . -Force | Where-Object {
    $item = $_
    $shouldExclude = $false
    
    foreach ($exclude in $excludeItems) {
        if ($item.Name -like $exclude) {
            $shouldExclude = $true
            Write-Host "  Excluding: $($item.Name)" -ForegroundColor DarkGray
            break
        }
    }
    
    -not $shouldExclude
}

Write-Host ""
Write-Host "Creating zip archive..." -ForegroundColor Green

# Create the zip
try {
    Compress-Archive -Path $allItems -DestinationPath $zipFile -CompressionLevel Optimal -Force
    
    if (Test-Path $zipFile) {
        Write-Host ""
        Write-Host "================================================" -ForegroundColor Green
        Write-Host "  SUCCESS!" -ForegroundColor Green
        Write-Host "================================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "  File: $zipFile" -ForegroundColor White
        $size = (Get-Item $zipFile).Length / 1MB
        Write-Host ("  Size: {0:N2} MB" -f $size) -ForegroundColor White
        Write-Host ""
        Write-Host "  Ready to upload to WordPress!" -ForegroundColor Cyan
        Write-Host ""
    }
} catch {
    Write-Host ""
    Write-Host "ERROR: Failed to create zip file" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")