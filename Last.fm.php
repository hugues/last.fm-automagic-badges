<?

$Pathinfo=$_SERVER['PATH_INFO'];
$pathinfo=explode("/", $Pathinfo);
$script=explode("/", $_SERVER['SCRIPT_NAME']);

$type=$script[3];
$username=$pathinfo[1];
if ($username == "") $username="gugusse";

//$Stats=".lastfm/Stats/".strtolower(rawurlencode($username)).".xml";
$Cache="/blackhole/.lastfm/Oldies/UNAVAILABLE.png";
//$Cache="";
$CacheTime = array("Monthly"     => 7*24*60*60,
			   	   "TotalTracks" => 600,
				   "TrueFalse"   => 7*24*60*60);

if (! array_key_exists($type, $CacheTime))
	exit;

header("Content-Type: image/png");

/*if ( ! is_file($Stats)
	||(filemtime($Stats) + $CACHE < $_SERVER['REQUEST_TIME']))
{
//	system("wget --no-cache http://ws.audioscrobbler.com/1.0/user/".rawurlencode($username)."/profile.xml -O $Stats");
}*/

if (   is_file($Cache)
/*	&& is_file($Stats) 
    &&(filemtime($Cache) > filemtime($Stats))*/
   )
{
	$fd=fopen($Cache, "r");
	echo fread($fd, filesize($Cache));
	fclose($fd);
	exit;
}

putenv("GDFONTPATH=/usr/share/fonts/truetype");

class Text {
	var $width = 0;
	var $height = 0;
	var $x = 0;
	var $y = 0;

	var $font = "import/typeo___";
	var $size = 0;
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

//$feed=new XMLReader();
/*if ($feed->open("$Stats"))
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
				$registration=$feed->getAttribute("unixtime");
				$feed->read();
				$feed->read();
				break;

			case "profile":
				$username=$feed->getAttribute("username");
				break;
		}
	}*/

/*if (! $playcount)
{
	$MainText->value="Sorry, $username is not";
	$Info->value="a valid Last.fm account";
	$MainText->size=30;
	$MainText->angle=2;
	$Info->angle=1;
	$Info->size=30;
	$FinalHeight=50;
	$Cache="";
}
else*/
{
/*	$TrueFalse =  $_SERVER['REQUEST_TIME'] - $registration;
	$TrueFalse /= (60*60*24*30);
	$TrueFalse = floor($playcount / $TrueFalse);*/
	$MainText->size=180;

	switch($type)
	{
		default:
			$MainText->value = "Not available anymore";
			$MainText->size = 180;
			$Info->value = "Go check Updates";
			$Info->size = 150;
			$MainText->angle = 1;
			$Info->angle = 1;
			$FinalHeight=50;
			break;
	}
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

if (   isset($TrueFalse)
    && $TrueFalse > 3000)
	$MainText->color=imagecolorallocate($img, 0, 0, 0);
else
	$MainText->color=imagecolorallocate($img, 220, 50, 50);
$Info->color=$MainText->color;

imagealphablending($img, FALSE);
$transparent=imagecolorallocatealpha($img, 255, 255, 255, 127);
$area=imagefilledrectangle($img, 0, 0, $Image->width, $Image->height, $transparent);

imagealphablending($img, TRUE);
imagettftext($img, $MainText->size, $MainText->angle, $MainText->x, $MainText->y + $MainText->height - 1, $MainText->color, $MainText->font, $MainText->value);
if ($Info->value != "")
	imagettftext($img, $Info->size, $Info->angle, $Info->x, $Info->y + $Info->height - 1, $Info->color, $Info->font, $Info->value);

$new=imagecreatetruecolor(150, $FinalHeight);
imagealphablending($new, FALSE);
$white=imagecolorallocatealpha($new, 255, 255, 255, 0);
$area=imagefilledrectangle($new, 0, 0, 150, $FinalHeight, $white);
imagealphablending($new, FALSE);

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

if ($Cache != "") imagepng($new, $Cache);
imagepng($new);
imagedestroy($img);
imagedestroy($new);

?>
