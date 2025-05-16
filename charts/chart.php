<?php
header("Content-Type: image/svg+xml");

$width = 100;
$height = 40;

$raw = preg_replace('/[^0-9.,-]/', '', $_GET['prices'] ?? '');
$prices = explode(',', $raw);
$prices = array_filter($prices, 'is_numeric');
$prices = array_values($prices); 

if (empty($prices)) {
    echo "<svg width='$width' height='$height' xmlns='http://www.w3.org/2000/svg'></svg>";
    exit;
}

$isDark = isset($_GET['dark']) && $_GET['dark'] == '1';
$strokeColor = $isDark ? '#00ff99' : '#0077ff';
$pointColor = $isDark ? '#ffffff' : '#000000';

if (count($prices) < 2) {
    echo "<svg width='$width' height='$height' viewBox='0 0 $width $height' xmlns='http://www.w3.org/2000/svg'>
        <circle cx='" . ($width / 2) . "' cy='" . ($height / 2) . "' r='2' fill='$pointColor' />
    </svg>";
    exit;
}

$max = max($prices);
if ($max == 0) $max = 1;

$points = '';
$lastX = 0;
$lastY = 0;

foreach ($prices as $i => $p) {
    $x = $i * ($width / (count($prices) - 1));
    $y = $height - ($p / $max) * $height;
    $points .= round($x, 2) . "," . round($y, 2) . " ";
    $lastX = round($x, 2);
    $lastY = round($y, 2);
}

echo "<svg width='$width' height='$height' viewBox='0 0 $width $height' xmlns='http://www.w3.org/2000/svg'>
    <polyline points='$points' fill='none' stroke='$strokeColor' stroke-width='2' />
    <circle cx='$lastX' cy='$lastY' r='2' fill='$pointColor' />
</svg>";