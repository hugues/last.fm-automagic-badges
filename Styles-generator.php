<?

include("Config.BigLine.php");

define(WIDTH, 100);
define(HEIGHT, 50);

foreach ($Styles as $style => $font)
{
	$img=imagecreatetruecolor(WIDTH, HEIGHT);
	imagealphablending($img, FALSE);
	imagesavealpha($img, TRUE);

	$transparent=imagecolorallocatealpha($img, 255, 255, 255, 127);
	$black=imagecolorallocatealpha($img, 0, 0, 0, 0);

	imagefilledrectangle($img, 0, 0, WIDTH, HEIGHT, $transparent);
	imagettftext($img, 30, 3, 0, HEIGHT * 80/100 , $black, "".$font, $style);
	imagepng($img, "styles/$style.png");
}

?>
