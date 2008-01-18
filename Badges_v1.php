<?

clearstatcache();

$Pathinfo=$_SERVER['PATH_INFO'];
$pathinfo=explode("/", $Pathinfo);
$script=explode("/", $_SERVER['SCRIPT_NAME']);

$username=$pathinfo[1];
$type=$pathinfo[2];
$style=$pathinfo[3];
$color=$pathinfo[4];

include("Config.php");

$Stats=CACHE_FOLDER."/Stats/".strtolower(rawurlencode($username)).".xml";
$Cache=CACHE_FOLDER."/Pictures/".strtolower(rawurlencode($username))."_$type-$style-$color.png";
//if ($username == "gugusse") $Cache="";

function GetColor($color, $code) {
	switch($color)
	{
		case "r":
			return ($code >> 16) & 0xff;
			break;
		case "g":
			return ($code >>  8) & 0xff;
			break;
		case "b":
			return ($code >>  0) & 0xff;
			break;
	}
}

header("Content-Type: image/png");

if ( ! is_file($Stats)
	||(filemtime($Stats) + CACHE < $_SERVER['REQUEST_TIME']))
{
	system("wget --no-cache http://ws.audioscrobbler.com/1.0/user/".rawurlencode($username)."/profile.xml -O $Stats");
}

if (   is_file($Cache)
	&& is_file($Stats)
    &&(filemtime($Cache) > filemtime($Stats))
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
	var $size = 24;
	var $angle = 0;
	var $color = 0;
	var $value = "";

	function initiate($size) {
		$this->width = max($size[0], $size[2], $size[4], $size[6]) - min(0, $size[0], $size[2], $size[4], $size[6]);
		$this->height= max($size[1], $size[3], $size[5], $size[7]) - min(0, $size[1], $size[3], $size[5], $size[7]);
	}
}

/*system("wget http://ws.audioscrobbler.com/1.0/user/gugusse/friends.txt -O - | grep -i $username > /dev/null", $exitcode);*/

$MainText = new Text;
$Info = new Text;

$feed=new XMLReader();
if ($feed->open($Stats))
	while ($feed->read())
	{
		switch ($feed->name)
		{
			case "playcount":
				$feed->read();
				$playcount=$feed->value;
				$feed->read();
				break;

			case "registered":
			case "statsreset":
				$statsstart=$feed->getAttribute("unixtime");
				$feed->read();
				$feed->read();
				break;

			case "profile":
				$username=$feed->getAttribute("username");
				break;
		}
	}

if (! $playcount)
{
	$MainText->value="Sorry, $username is not";
	$Info->value="a valid Last.fm account";
	$MainText->angle=2;
	$Info->angle=1;
	$FinalHeight=50;
	$Cache="";
}
else
{
	$duration =  $_SERVER['REQUEST_TIME'] - $statsstart;
	$months = $duration / (60*60*24*30);
	$weeks  = $duration / (60*60*24*7);
	$days   = $duration / (60*60*24);
	$permonth  = floor($playcount / $months);
	$perweek   = floor($playcount / $weeks);
	$perday    = floor($playcount / $days);

	switch($type)
	{
		case "PerDay":
			$MainText->value = "$perday tracks per Day";
			$MainText->angle = 2;
			$Info->value = "";
			$FinalHeight=20;
			break;
		case "PerWeek":
			$MainText->value = "$perweek tracks per Week";
			$MainText->angle = 2;
			$Info->value = "";
			$FinalHeight=20;
			break;
		case "PerMonth":
			$MainText->value = "$permonth tracks per Month";
			$MainText->angle = 2;
			$Info->value = "";
			$FinalHeight=20;
			break;
		case "Trueness":
			$MainText->value = ucfirst($username)." is ";
			$MainText->value .= ($permonth > TRUENESS ? "an" : "a");
			$MainText->angle = 3;
			$FinalHeight=50;
			$Info->value = ($permonth > TRUENESS ? "untrue" : "true");
			$Info->value .= " listener";
			$Info->angle = 2;
			break;
		case "Total":
			$MainText->value = "$playcount tracks played";
			$MainText->angle = 2;
			$Info->angle = 1;
			$Info->value = strftime("since %B %Y", $statsstart);
			$FinalHeight=40;
			break;
		default:
			$MainText->value = "Sorry, unavailable !";
			break;
	}

	$MainText->font = "import/" . $Styles[$style];	
	$Info->font = $MainText->font;


}

$size=imageftbbox($MainText->size, $MainText->angle, $MainText->font, $MainText->value);
$MainText->initiate($size);
$MainText->x=0;
$MainText->y=$MainText->height;
if ($Info->value != "")
{
	$size=imageftbbox($Info->size, $Info->angle, $Info->font, $Info->value);
	$Info->initiate($size);
	$Info->x=0;
	$Info->y=$MainText->height + $Info->height;
}

$Image = new Text;
$Image->width=max($MainText->width, $Info->width);
$Image->height=$MainText->height;
$Image->height+=$Info->height;

$MainText->x = max(0, floor(($Image->width - $MainText->width) / 2));
$MainText->y = 0;
$Info->x = max(0, floor(($Image->width - $Info->width) / 2));
$Info->y = $MainText->height + 1;

$img=imagecreatetruecolor($Image->width, $Image->height);
imagealphablending($img, FALSE);
imagesavealpha($img, TRUE);

$MainText->color=imagecolorallocate($img, GetColor("r", $Colors[$color]),
										  GetColor("g", $Colors[$color]),
										  Getcolor("b", $Colors[$color]));
$Info->color=$MainText->color;


$transparent=imagecolorallocatealpha($img, 255, 255, 255, 127);
$area=imagefilledrectangle($img, 0, 0, $Image->width, $Image->height, $transparent);

imagettftext($img, $MainText->size, $MainText->angle, $MainText->x, $MainText->y + $MainText->height - 1, $MainText->color, $MainText->font, $MainText->value);
if ($Info->value != "")
	imagettftext($img, $Info->size, $Info->angle, $Info->x, $Info->y + $Info->height - 1, $Info->color, $Info->font, $Info->value);

$new=imagecreatetruecolor(150, $FinalHeight);
imagealphablending($new, FALSE);
imagesavealpha($new, TRUE);

$area=imagefilledrectangle($new, 0, 0, 150, $FinalHeight, $transparent);

$y=floor($MainText->height * $FinalHeight / $Image->height);

imagecopyresampled($new,
				   																	$img,
				   0,
				   0,
																					$MainText->x,
																					$MainText->y,
				   150,
				   $y,
																					$MainText->width,
																					$MainText->height);

if ($Info->value != "")
	imagecopyresampled($new,
					   																$img,
					   0,
					   $y + 1,
					   																$Info->x,
																					$Info->y,
					   150,
					   ceil($Info->height * $FinalHeight / $Image->height),
					   																$Info->width,
																					$Info->height);
	//imagecopyresampled($new, $img, 0, 0, 0, 0, 150, $FinalHeight, $Image->width, $Image->height);
imagedestroy($img);

if ($Cache != "") imagepng($new, $Cache);
imagepng($new);
imagedestroy($new);

?>
