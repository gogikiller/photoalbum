<?php
if(!isset($_SESSION))session_start();
header ("Content-type: image/jpeg");
$im = imagecreate (300, 47);
$white=imagecolorallocate($im, 247,247,247);
$black1=imagecolorallocate ($im, 128, 130, 133);
$text=rand(11111,99999);
$_SESSION['capthaid']=$text;
$ugol=rand(-10,20);
$rast=rand(10,60);
$rast2=rand(30,40);
imagettftext ($im, 18, $ugol, $rast, $rast2, $black1, "helvet.OTF",$text);
imagejpeg ($im);
imagedestroy ($im);
?>