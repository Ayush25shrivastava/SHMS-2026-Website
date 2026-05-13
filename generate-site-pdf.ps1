Param(
    [string]$Output = "SHMS2026_Conference_Website_Compiled.pdf"
)

# Change to the script's directory so relative paths to HTML work correctly
Set-Location -Path (Split-Path -Parent $MyInvocation.MyCommand.Path)

# List of HTML pages in the desired order
$pages = @(
    "index.html",
    "about.html",
    "tracks.html",
    "committees.html",
    "schedule.html",
    "call.html",
    "speakers.html",
    "registration.html",
    "venue.html",
    "contact.html"
)

Write-Host "Generating combined PDF '$Output' from the following pages:" -ForegroundColor Cyan
$pages | ForEach-Object { Write-Host " - $_" }

Write-Host ""
Write-Host "NOTE: This script requires 'wkhtmltopdf' to be installed and available in PATH." -ForegroundColor Yellow
Write-Host "You can download it from https://wkhtmltopdf.org/ if it's not already installed." -ForegroundColor Yellow
Write-Host ""

# Build the wkhtmltopdf command
$cmd = @("wkhtmltopdf")
$cmd += $pages
$cmd += $Output

Write-Host "Running:" ($cmd -join " ") -ForegroundColor Green

& $cmd

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "PDF generated successfully: $Output" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "wkhtmltopdf exited with code $LASTEXITCODE. Please check that it is installed and reachable from PATH." -ForegroundColor Red
}

