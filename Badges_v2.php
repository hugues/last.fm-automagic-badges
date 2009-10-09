<?

clearstatcache();

$Pathinfo=$_SERVER['PATH_INFO'];
$pathinfo=explode("/", $Pathinfo);
$script=explode("/", $_SERVER['SCRIPT_NAME']);

$username=$pathinfo[0];
$type=$pathinfo[1];
$style=$pathinfo[2];
$color=$pathinfo[3];

include("Config.php");

$UserName = str_replace("_", " ", ucfirst($username));

$Stats=CACHE_FOLDER."/Stats/".escapeshellcmd(strtolower($username)).".xml";
$XmlStats=CACHE_FOLDER."/Stats/".strtolower($username).".xml";
$Cache=CACHE_FOLDER."/Pictures/".escapeshellcmd(strtolower($username))."_$type-$style-$color.png";
if (strtolower($username) == "gugusse") $Cache="";

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
	system("wget -q --no-cache http://ws.audioscrobbler.com/1.0/user/".rawurlencode($username)."/profile.xml -O $Stats.tmp");
	if (!filesize("$Stats.tmp"))
		system("rm $Stats.tmp");
	else
		system("mv $Stats.tmp $Stats");
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

$feed=new XMLReader();
if ($feed->open($XmlStats))
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
			//case "statsreset":
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
	$Lines[0]->value="Sorry, $username is not";
	$Lines[0]->angle=2;
	$Lines[] = new Text;
	$Lines[1]->value="a valid Last.fm account";
	$Lines[1]->angle=1;
	define(HEIGHT, 50);
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
			$Lines[0]->value = "$perday tracks per Day";
			$Lines[0]->angle = 2;
			break;
		case "PerWeek":
			$Lines[0]->value = "$perweek tracks per Week";
			$Lines[0]->angle = 2;
			break;
		case "PerMonth":
			$Lines[0]->value = "$permonth tracks per Month";
			$Lines[0]->angle = 2;
			break;
		case "PerDay2":
			$Lines[] = new Text;
			$Lines[0]->value = "$perday";
			$Lines[1]->value = "tracks per Day";
			break;
		case "PerWeek2":
			$Lines[] = new Text;
			$Lines[0]->value = "$perweek";
			$Lines[1]->value = "tracks per Week";
			break;
		case "PerMonth2":
			$Lines[] = new Text;
			$Lines[1]->value = "tracks per Month";
			$Lines[0]->value = "$permonth";
			break;
		case "Trueness":
			$Lines[0]->value = "is ";
			$Lines[0]->value .= ($permonth > TRUENESS ? "an" : "a");
			$Lines[0]->angle = 3;
			define(HEIGHT, 50);
			$Lines[] = new Text;
			$Lines[1]->value = ($permonth > TRUENESS ? "untrue" : "true");
			$Lines[1]->value .= " listener";
			$Lines[1]->angle = 2;

			if (strlen($username." ".$Lines[0]->value) >= strlen($Lines[1]->value))
			{
				$Lines[1]->value = $Lines[0]->value." ".$Lines[1]->value;
				$Lines[0]->value = $UserName;
			}
			else
			{
				$Lines[0]->value = $UserName." ".$Lines[0]->value;
			}
			break;
		case "Trueness2":
			$Lines[] = new Text;
			$Lines[] = new Text;
			$Lines[0]->value = "$UserName is ";
			$Lines[0]->value .= ($permonth > TRUENESS ? "an" : "a" ) ;
			$Lines[1]->value = ($permonth > TRUENESS ? "Untrue" : "True");
			$Lines[2]->value = "listener";
			break;
		case "Since":
			$Lines[0]->value = strftime("since %B %Y", $statsstart);
			$Lines[0]->angle = 1;
			break;
		case "Since2":
			$Lines[] = new Text;
			$Lines[] = new Text;
			$Lines[0]->value = "listening since";
			$Lines[1]->value = strftime("%B", $statsstart);
			$Lines[2]->value = strftime("%Y", $statsstart);
			break;
		case "Total":
			$Lines[0]->value = "$playcount tracks played";
			$Lines[0]->angle = 1;
			define(HEIGHT, 40);
			break;
		case "Total2":
			$Lines[0]->value = "$playcount";
			$Lines[] = new Text;
			$Lines[1]->value = "tracks played";
			break;
		default:
			$Lines[0]->value = "Sorry !";
			$Lines[] = new Text;
			$Lines[1]->value = "Not available anymore";
			define(HEIGHT, 50);
			break;
	}

	foreach ($Lines as $Line)
		$Line->font = "import/" . $Styles[$style];	

}

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
	$Line->color=imagecolorallocate($img, GetColor("r", $Colors[$color]),
										  GetColor("g", $Colors[$color]),
										  Getcolor("b", $Colors[$color]));
}

$transparent=imagecolorallocatealpha($img, 255, 255, 255, 127);

imagefilledrectangle($img, 0, 0, $Image->width, $Image->height, $transparent);

foreach ($Lines as $Line)
	imagettftext($img, $Line->size, $Line->angle, $Line->x, $Line->y, $Line->color, $Line->font, $Line->value);

imagepng($img);

if ($Cache != "") imagepng($img, $Cache);
imagedestroy($img);

?>
