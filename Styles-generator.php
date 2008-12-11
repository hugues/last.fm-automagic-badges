<?

include("Config.php");

define(WIDTH, 150);
define(HEIGHT, 50);

foreach ($Styles as $style => $font)
{
	$img=imagecreatetruecolor(WIDTH, HEIGHT);
	imagealphablending($img, FALSE);
	imagesavealpha($img, TRUE);

	$transparent=imagecolorallocatealpha($img, 255, 255, 255, 127);
	$black=imagecolorallocatealpha($img, 0, 0, 0, 0);

	imagefilledrectangle($img, 0, 0, WIDTH, HEIGHT, $transparent);

	imagettftext($img, 15, 0, 0, HEIGHT * 80/100 , $black, "import/".$font, $style);

	imagepng($img, "./$style.png");
}

?>
