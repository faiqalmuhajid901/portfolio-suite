param(
    [string]$OutputPath = ""
)

Add-Type -AssemblyName System.Drawing

$projectRoot = Split-Path -Parent $PSScriptRoot

if ([string]::IsNullOrWhiteSpace($OutputPath)) {
    $OutputPath = Join-Path $projectRoot "public\images\og-default.png"
}

$outputDirectory = Split-Path -Parent $OutputPath

if (-not (Test-Path $outputDirectory)) {
    New-Item `
        -ItemType Directory `
        -Path $outputDirectory `
        -Force | Out-Null
}

$width = 1200
$height = 630

$bitmap = [System.Drawing.Bitmap]::new($width, $height)
$graphics = [System.Drawing.Graphics]::FromImage($bitmap)

$graphics.SmoothingMode = `
    [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias

$graphics.TextRenderingHint = `
    [System.Drawing.Text.TextRenderingHint]::AntiAliasGridFit

$canvas = [System.Drawing.Rectangle]::new(
    0,
    0,
    $width,
    $height
)

$backgroundBrush = `
    [System.Drawing.Drawing2D.LinearGradientBrush]::new(
        $canvas,
        [System.Drawing.Color]::FromArgb(15, 23, 42),
        [System.Drawing.Color]::FromArgb(4, 120, 87),
        25
    )

$graphics.FillRectangle(
    $backgroundBrush,
    $canvas
)

$accentBrush = `
    [System.Drawing.SolidBrush]::new(
        [System.Drawing.Color]::FromArgb(
            55,
            52,
            211,
            153
        )
    )

$graphics.FillEllipse(
    $accentBrush,
    850,
    -180,
    520,
    520
)

$graphics.FillEllipse(
    $accentBrush,
    -180,
    390,
    420,
    420
)

$smallFont = [System.Drawing.Font]::new(
    "Segoe UI",
    24,
    [System.Drawing.FontStyle]::Bold,
    [System.Drawing.GraphicsUnit]::Pixel
)

$titleFont = [System.Drawing.Font]::new(
    "Segoe UI",
    64,
    [System.Drawing.FontStyle]::Bold,
    [System.Drawing.GraphicsUnit]::Pixel
)

$subtitleFont = [System.Drawing.Font]::new(
    "Segoe UI",
    30,
    [System.Drawing.FontStyle]::Regular,
    [System.Drawing.GraphicsUnit]::Pixel
)

$whiteBrush = `
    [System.Drawing.SolidBrush]::new(
        [System.Drawing.Color]::White
    )

$mutedBrush = `
    [System.Drawing.SolidBrush]::new(
        [System.Drawing.Color]::FromArgb(
            220,
            226,
            232,
            240
        )
    )

$greenBrush = `
    [System.Drawing.SolidBrush]::new(
        [System.Drawing.Color]::FromArgb(
            110,
            231,
            183
        )
    )

$graphics.DrawString(
    "SOFTWARE ENGINEER",
    $smallFont,
    $greenBrush,
    84,
    112
)

$titleRectangle = [System.Drawing.RectangleF]::new(
    76,
    172,
    1020,
    180
)

$graphics.DrawString(
    "Muhammad Faiq",
    $titleFont,
    $whiteBrush,
    $titleRectangle
)

$subtitleRectangle = [System.Drawing.RectangleF]::new(
    82,
    360,
    950,
    120
)

$graphics.DrawString(
    "Selected projects, technical case studies, and professional experience.",
    $subtitleFont,
    $mutedBrush,
    $subtitleRectangle
)

$linePen = [System.Drawing.Pen]::new(
    [System.Drawing.Color]::FromArgb(
        110,
        231,
        183
    ),
    5
)

$graphics.DrawLine(
    $linePen,
    84,
    520,
    360,
    520
)

$graphics.DrawString(
    "muhammadfaiq-portfolio.vercel.app",
    $smallFont,
    $mutedBrush,
    84,
    540
)

$bitmap.Save(
    $OutputPath,
    [System.Drawing.Imaging.ImageFormat]::Png
)

$linePen.Dispose()
$greenBrush.Dispose()
$mutedBrush.Dispose()
$whiteBrush.Dispose()
$subtitleFont.Dispose()
$titleFont.Dispose()
$smallFont.Dispose()
$accentBrush.Dispose()
$backgroundBrush.Dispose()
$graphics.Dispose()
$bitmap.Dispose()

Write-Host ""
Write-Host "OG image generated:"
Write-Host $OutputPath
