Write-Host "🚀 Building production package..."

# Run build
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Error "Build failed"
    exit 1
}

$themeName = "aaapos"
$version   = "1.0.0"
$deployDir = "deploy\$themeName"
$zipFile   = "aaapos-theme-v$version.zip"

Write-Host "🧹 Preparing deploy directory..."

Remove-Item deploy -Recurse -Force -ErrorAction SilentlyContinue
New-Item -ItemType Directory -Path $deployDir | Out-Null

Write-Host "📁 Copying theme files..."

robocopy . $deployDir /E `
    /XD node_modules .git deploy `
    /XF package.json package-lock.json postcss.config.js .prettierrc deploy.ps1

Write-Host "📦 Creating ZIP..."

Compress-Archive -Path $deployDir -DestinationPath $zipFile -Force

Write-Host "✅ Package ready: $zipFile"
