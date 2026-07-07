<?php
declare(strict_types=1);

if ($argc !== 3) {
    fwrite(STDERR, "Usage: php scripts/normalise-logo-svg.php <source.svg> <destination.svg>\n");
    exit(1);
}

[$script, $source, $destination] = $argv;
$svg = @file_get_contents($source);
if ($svg === false || stripos($svg, '<svg') === false) {
    fwrite(STDERR, "Source file is not a readable SVG.\n");
    exit(1);
}

// The supplied SVG inherited fill=none from its outer group and included copied page-layout CSS.
$svg = preg_replace('/<svg\\b([^>]*)\\sstyle="[^"]*"([^>]*)>/i', '<svg$1$2>', $svg, 1) ?? $svg;
$svg = preg_replace('/<g\\b([^>]*\\bid="symbols"[^>]*)\\bfill="none"/i', '<g$1 fill="#ED3F23"', $svg, 1) ?? $svg;
$svg = str_replace(' class="logofillr"', '', $svg);
$svg = preg_replace('/<svg\\b/', '<svg role="img" aria-label="Scouts logo"', $svg, 1) ?? $svg;

$dir = dirname($destination);
if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
    fwrite(STDERR, "Could not create destination directory.\n");
    exit(1);
}
if (file_put_contents($destination, $svg) === false) {
    fwrite(STDERR, "Could not write destination SVG.\n");
    exit(1);
}
echo "Normalised logo written to {$destination}\n";
