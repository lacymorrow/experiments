<?php
Header("Content-type: image/png");
  if(!isset($s)) $s=11;
  if(!isset($text)) $text="undefined";
  if(!isset($font)) $font="TIMES";
  $size = imagettfbbox($s,0,"/fonts/".$font.".ttf",$text);
$image = imageCreateTrueColor(abs($size[2]) + abs($size[0]), abs($size[7]) + abs($size[1]));
imageSaveAlpha($image, true);
ImageAlphaBlending($image, false);
$transparentColor = imagecolorallocatealpha($image, 0, 0, 0, 127);
imagefill($image, 0, 0, $transparentColor);
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$textColor = $black;
imagettftext($image, $s, 0, 0, abs($size[5]), $textColor, "/fonts/".$font.".ttf", $text);
imagepng($image);
imagedestroy($image);
?>
