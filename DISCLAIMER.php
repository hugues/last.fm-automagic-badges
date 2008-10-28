<?

clearstatcache();

include "Config.php";
$Cache=CACHE_FOLDER."/Pictures/Maintenance.png";

if (   is_file($Cache)
   )
{
	$fd=fopen($Cache, "r");
	echo fread($fd, filesize($Cache));
	fclose($fd);
	exit;
}

class Text {
	var $width = 0;
	var $height = 0;
	var $x = 0;
	var $y = 0;

	var $font = "";
	var $size = 150; // High values to better quality
	var $angle = 2;
	var $color = 0;
	var $value = "";

	function initiate($size) {
		$this->x = 0;
		$this->width = abs(
			max($size[0], $size[2], $size[4], $size[6])
		  - min($size[0], $size[2], $size[4], $size[6])
		  );
		$this->height= abs(
		    max($size[1], $size[3], $size[5], $size[7])
		  - min($size[1], $size[3], $size[5], $size[7])
		  );

		$ratio = WIDTH / $this->width;

		$this->width = WIDTH;
		$this->height *= $ratio;
		$this->size = floor($this->size * $ratio);
	}
}
$Lines = array();
$Lines[] = new Text;

$Lines[0]->value = "Sorry";
$Lines[0]->angle = 2;
$Lines[] = new Text;
$Lines[1]->value = "due to too heavy load";
$Lines[] = new Text;
$Lines[2]->value = "badges will be suspended";
$Lines[] = new Text;
$Lines[3]->value = "for some time...";

foreach ($Lines as $Line)
	$Line->font = "import/Georgia";	

$y=0;
foreach ($Lines as $Line)
{
	$size=imageftbbox($Line->size, $Line->angle, $Line->font, $Line->value);
	$Line->initiate($size);
	$y+=$Line->height;
	$Line->y=$y;
}

$Image = new Text;
$Image->width   = WIDTH;
$Image->height  = $y;

$img=imagecreatetruecolor($Image->width, $Image->height);
imagealphablending($img, FALSE);
imagesavealpha($img, TRUE);

foreach ($Lines as $Line)
{
	$Line->color=imagecolorallocate($img, 0,
										  0,
										  0);
}

$transparent=imagecolorallocatealpha($img, 255, 255, 255, 127);

imagefilledrectangle($img, 0, 0, $Image->width, $Image->height, $transparent);

foreach ($Lines as $Line)
	imagettftext($img, $Line->size, $Line->angle, $Line->x, $Line->y, $Line->color, $Line->font, $Line->value);

imagepng($img);

if ($Cache != "") imagepng($img, $Cache);
imagedestroy($img);

?>
