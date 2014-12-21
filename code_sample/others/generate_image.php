<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$text = $_GET['x'];

//Set the Content Type
header('Content-type: image/jpeg');

//Open image and reserve transparency:
$srcImage = imagecreatefrompng( 'diamond.png' ); 
$targetImage = imagecreatetruecolor( 70, 40 );   
imagealphablending( $targetImage, false );
imagesavealpha( $targetImage, true );
imagecopyresampled( $targetImage, $srcImage, 
                    0, 0, 
                    0, 0, 
                    70, 40, 
                    70, 40 );
$color = imagecolorallocate($targetImage,0, 0, 0); 

//Print number
if(strlen($text) == 1){
	imagestring($targetImage, 5, 30, 11, $text, $color);
}elseif(strlen($text) == 2){
	imagestring($targetImage, 5, 26, 11, $text, $color);
}elseif(strlen($text) == 3){
	imagestring($targetImage, 5, 22, 11, $text, $color);
}
  
// Send Image to Browser
imagepng($targetImage);

// Clear Memory
imagedestroy($jpg_image);