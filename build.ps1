$zipFile = "aaapos-v1.0.0.zip"

# Remove old zip if exists
if (Test-Path $zipFile) {
    Remove-Item $zipFile -Force
}

# Create array of items to include
$itemsToInclude = @(
    "assets",
    "images",
    "inc",
    "page-templates",
    "template-parts",
    "woocommerce",
    "404.php",
    "LICENSE.txt",
    "README.md",
    "archive.php",
    "comments.php",
    "footer.php",
    "front-page.php",
    "functions.php",
    "header.php",
    "index.php",
    "page.php",
    "postcss.config.js",
    "rtl.css",
    "screenshot.png",
    "search.php",
    "sidebar.php",
    "single.php",
    "style.css"
)

# Filter to only existing items
$existingItems = $itemsToInclude | Where-Object { Test-Path $_ }

# Create the zip
Compress-Archive -Path $existingItems -DestinationPath $zipFile -CompressionLevel Optimal

if (Test-Path $zipFile) {
    Write-Host ""
    Write-Host "Success! Created: $zipFile" -ForegroundColor Green
    $size = (Get-Item $zipFile).Length / 1MB
    Write-Host ("Size: {0:N2} MB" -f $size) -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "Error: Failed to create zip file" -ForegroundColor Red
}

Write-Host ""
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")