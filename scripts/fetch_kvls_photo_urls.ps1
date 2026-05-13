param(
  [string]$Url = "https://iith.ac.in/ce/kvls/"
)

$ErrorActionPreference = "Stop"

$html = (Invoke-WebRequest -Uri $Url -UseBasicParsing).Content

# Extract common image URLs from the HTML (jpg/jpeg/png/webp)
$pattern = 'https?://[^\s\"'']+\.(?:jpg|jpeg|png|webp)(?:\?[^\s\"'']*)?'
$matches = [regex]::Matches($html, $pattern)

if ($matches.Count -eq 0) {
  Write-Output "NO_IMAGE_URLS_FOUND"
  exit 1
}

$matches |
  Select-Object -ExpandProperty Value |
  Select-Object -Unique |
  Select-Object -First 20 |
  ForEach-Object { Write-Output $_ }

