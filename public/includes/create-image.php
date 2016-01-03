<?php
/**
 * Generate Captcha code
 * 
 * @package   			Feature-Request
 * @author    			Averta
 * @license   			GPL-2.0+
 * @link      			http://averta.net
 * @copyright 			2015 Averta
 *
 */	
 	
session_start();
$code=rand(1000,9999);
$_SESSION["code"]=$code;
$im = imagecreatetruecolor(65, 30);
$bg = imagecolorallocate($im, 45, 45, 45); //background color black
$fg = imagecolorallocate($im, 255, 255, 255); //text color white
imagefill($im, 0, 0, $bg);
imagestring($im, 5, 15, 7,  $code, $fg);
header("Cache-Control: no-cache, must-revalidate");
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
?>