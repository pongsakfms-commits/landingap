<?php
/**
 * Custom Captcha Generator
 */
session_start();

// Generate random captcha code
$captcha_code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
$_SESSION['captcha_code'] = $captcha_code;

// Create image
$width = 150;
$height = 50;
$image = imagecreatetruecolor($width, $height);

// Colors
$bg_color = imagecolorallocate($image, 30, 41, 59);
$text_color = imagecolorallocate($image, 99, 102, 241);
$noise_color = imagecolorallocate($image, 100, 100, 150);

// Fill background
imagefill($image, 0, 0, $bg_color);

// Add noise lines
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $noise_color);
}

// Add noise dots
for ($i = 0; $i < 100; $i++) {
    imagesetpixel($image, rand(0, $width), rand(0, $height), $noise_color);
}

// Add text
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($captcha_code);
$x = ($width - $text_width) / 2;
$y = ($height - imagefontheight($font_size)) / 2;

// Draw each character with slight variation
for ($i = 0; $i < strlen($captcha_code); $i++) {
    $char_x = $x + ($i * imagefontwidth($font_size) * 1.5);
    $char_y = $y + rand(-5, 5);
    imagestring($image, $font_size, $char_x, $char_y, $captcha_code[$i], $text_color);
}

// Output
header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
imagepng($image);
imagedestroy($image);
