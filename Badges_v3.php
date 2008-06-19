<?

clearstatcache();

$Pathinfo=$_SERVER['PATH_INFO'];
$pathinfo=explode("/", $Pathinfo);
$script=explode("/", $_SERVER['SCRIPT_NAME']);

$username=$pathinfo[1];
$hash=$pathinfo[2];

include("Config.php");

$UserName = str_replace("_", " ", ucfirst($username));

$Stats=CACHE_FOLDER."/Stats/".strtolower(rawurlencode($username)).".xml";
$Cache=CACHE_FOLDER."/Pictures/".strtolower(rawurlencode($username))."_$type-$style-$color.png";
if ($username == "gugusse") $Cache="";

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
	system("wget --no-cache http://ws.audioscrobbler.com/1.0/user/".rawurlencode($username)."/profile.xml -O $Stats.tmp 2>/dev/null");
	system("mv $Stats.tmp $Stats 2>/dev/null");
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
	var $size = 150; // High values to better quality
	var $angle = 0;
	var $color = 0;
	var $value = "";

	function initiate($size) {
		$this->width = abs(
			max($size[0], $size[2], $size[4], $size[6])
		  - min(0, $size[0], $size[2], $size[4], $size[6])
		  );
		$this->height= abs(
		    max($size[1], $size[3], $size[5], $size[7])
		  - min(0, $size[1], $size[3], $size[5], $size[7])
		  );

		$ratio = WIDTH / $this->width;

		$this->width *= WIDTH;
		$this->height *= $ratio;
		$this->size *= $ratio;
	}
}


/*-----------------------------------------------------------

  		Ok, now we are ready.

	The idea of this version is to let the Last.fm user
	decide exactly what she/he does wants the badge to show
	up.
	
	I should offer the way to compute stats values, and get
	their result into a string.

	To get stats, we should limit to one user. So we get the
	username first.
	Then we propose to the user a bunch of results, such as,
	for example,


  -----------------------------------------------------------*/



$Line1 = new Text;
$Line2 = new Text;

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
	$Line1->value="Sorry, $username is not";
	$Line2->value="a valid Last.fm account";
	$Line1->angle=2;
	$Line2->angle=1;
	//define(HEIGHT, 50);
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
			$Line1->value = "$perday tracks per Day";
			$Line1->angle = 2;
			$Line2->value = "";
			break;
		case "PerWeek":
			$Line1->value = "$perweek tracks per Week";
			$Line1->angle = 2;
			$Line2->value = "";
			break;
		case "PerMonth":
			$Line1->value = "$permonth tracks per Month";
			$Line1->angle = 2;
			$Line2->value = "";
			break;
		case "Trueness":
			$Line1->value = "is ";
			$Line1->value .= ($permonth > TRUENESS ? "an" : "a");
			$Line1->angle = 3;
			//define(HEIGHT, 50);
			$Line2->value = ($permonth > TRUENESS ? "untrue" : "true");
			$Line2->value .= " listener";
			$Line2->angle = 2;

			if (strlen($username." ".$Line1->value) >= strlen($Line2->value))
			{
				$Line2->value = $Line1->value." ".$Line2->value;
				$Line1->value = $UserName;
			}
			else
			{
				$Line1->value = $UserName." ".$Line1->value;
			}
			break;
		case "Listens":
			$Line1->value = $UserName . " listens to";
			$Line1->angle = 2;
			break;
		case "Since":
			$Line1->value = strftime("since %B %Y", $statsstart);
			$Line1->angle = 1;
			break;
		case "Total":
			$Line1->value = "$playcount tracks played";
			$Line1->angle = 1;
			//define(HEIGHT, 40);
			break;
		default:
			$Line1->value = "Sorry, unavailable !";
			break;
	}

	$Line1->font = "import/" . $Styles[$style];	
	$Line2->font = $Line1->font;
}

$size=imageftbbox($Line1->size, $Line1->angle, $Line1->font, $Line1->value);
$Line1->initiate($size);

$Line1->x=0;
$Line1->y=$Line1->height - 1;
if ($Line2->value != "")
{
	$size=imageftbbox($Line2->size, $Line2->angle, $Line2->font, $Line2->value);
	$Line2->initiate($size);
	$Line2->x=0;
	$Line2->y=$Line1->height + $Line2->height - 1;

	$Line1->size = min($Line1->size, $Line2->size);
	$Line2->size = $Line1->size;
}

$Image = new Text;
$Image->width   = WIDTH;
$Image->height  = $Line1->height;
$Image->height += $Line2->height;
$Image->height += $Image->height * 0.3;

$Line1->x = max(0, floor(($Image->width - $Line1->width) / 2));
$Line1->y = 0;
$Line2->x = max(0, floor(($Image->width - $Line2->width) / 2));
$Line2->y = $Line1->height + 1;

$img=imagecreatetruecolor($Image->width, $Image->height);
imagealphablending($img, FALSE);
imagesavealpha($img, TRUE);

$Line1->color=imagecolorallocate($img, GetColor("r", $Colors[$color]),
										  GetColor("g", $Colors[$color]),
										  Getcolor("b", $Colors[$color]));
$Line2->color=$Line1->color;


$transparent=imagecolorallocatealpha($img, 255, 255, 255, 127);

imagefilledrectangle($img, 0, 0, $Image->width, $Image->height, $transparent);
imagettftext($img, $Line1->size, $Line1->angle, $Line1->x, $Line1->y + $Line1->height - 1, $Line1->color, $Line1->font, $Line1->value);
if ($Line2->value != "")
	imagettftext($img, $Line2->size, $Line2->angle, $Line2->x, $Line2->y + $Line2->height - 1, $Line2->color, $Line2->font, $Line2->value);
imagepng($img);

if ($Cache != "") imagepng($img, $Cache);
imagedestroy($img);

?>
