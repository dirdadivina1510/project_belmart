<?php
$src = 'C:/Users/dirdaa/.gemini/antigravity-ide/brain/db94ca02-6fdc-439c-a5db-367f19afd3ab/media__1786595335866.png';
if (!file_exists($src)) {
    echo "Source image not found.";
    exit;
}

$img = imagecreatefrompng($src);
$width = imagesx($img);
$height = imagesy($img);

$transparent = imagecreatetruecolor($width, $height);
imagealphablending($transparent, false);
imagesavealpha($transparent, true);

$trans_color = imagecolorallocatealpha($transparent, 0, 0, 0, 127);
imagefill($transparent, 0, 0, $trans_color);

for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        // Turn white pixels transparent
        if ($r > 220 && $g > 220 && $b > 220) {
            imagesetpixel($transparent, $x, $y, $trans_color);
        } else {
            $color = imagecolorallocatealpha($transparent, $r, $g, $b, 0);
            imagesetpixel($transparent, $x, $y, $color);
        }
    }
}

imagepng($transparent, 'c:/xampp/htdocs/belmart_project/public/images/logo.png');
imagepng($transparent, 'c:/xampp/htdocs/belmart_project/public/assets/images/logo.png');
echo "SUCCESS_TRANSPARENT_LOGO";
