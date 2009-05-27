<?php
/**
  *
		Licensed under WTFPL - DoWhatTheFuckYouWant Public License
		(c) Hugues Hiegel 2006-2008 <hugues@hiegel.fr>
		
		Thanks to Pavel Zbytovský - www.zby.cz
			for saving me time with the MySQL stuff !


CREATE TABLE `users` (
	`username` varchar(100) NOT NULL,
	`statsstart` bigint(11) NOT NULL,
	`playcount` int(10) unsigned NOT NULL,
	`lastupdate` bigint(11) unsigned NOT NULL,
	PRIMARY KEY  (`username`)
) ;

CREATE TABLE `badges` (
	`username` varchar(100) NOT NULL,
	`type` varchar(100) NOT NULL,
	`style` varchar(100) NOT NULL,
	`color` varchar(100) NOT NULL,
	`lastupdate` bigint(11) default NULL,
	`hits` bigint(20) unsigned NOT NULL,
	`lasthit` bigint(11) unsigned default NULL,
	`png` longblob,
	PRIMARY KEY  (`username`,`type`)
);
  
  *
  */

/*get the parameters*/
$Pathinfo=$_SERVER['PATH_INFO'];
$pathinfo=explode("/", $Pathinfo);
$script=explode("/", $_SERVER['SCRIPT_NAME']);

$username=$pathinfo[1];
$type=$pathinfo[2];
$style=$pathinfo[3];
$color=$pathinfo[4];


include("Config.BigLine.php");


mysql_connect(MYSQL_HOST, MYSQL_USER);
mysql_select_db(MYSQL_DB);

/*make cache data (array $data)*/
$res = mysql_query("SELECT * FROM users WHERE username='" . gpc_addslashes(strtolower($username)) . "'");
$data = mysql_fetch_assoc($res);

if(($username AND !mysql_num_rows($res))
OR ($data["lastupdate"] AND $data["lastupdate"]+CACHE < time()))
  make_db_cache($username);

/*output image cache*/
$Cache=CACHE_FOLDER."/Pictures/".strtolower(rawurlencode($username))."_$type-$style-$color.png";

clearstatcache();

//if (is_file($Cache) AND (filemtime($Cache) >= $data['lastupdate'])){
  //header("Location: ".$Cache); //its faster, but you should set CACHE_FOLDER = "."
//}

/*-----------------------------------------------------------
  		Ok, now we are ready to create the image with GD.
*/

$playcount = $data['playcount'];
$statsstart = $data['statsstart'];

$Lines = array();
$Lines[] = new Text;

if (! $playcount)
{
	$Lines[0]->value="Sorry, $username is not";
	$Lines[0]->angle=rand(-1,2);
	$Lines[] = new Text;
	$Lines[1]->value="a valid Last.fm account";
	$Lines[1]->angle=rand(-2,1);
	define(HEIGHT, 50);
	$Cache="";
}
else
{
	$res = mysql_query("SELECT * FROM badges WHERE username='" . gpc_addslashes(strtolower($username)) . "' AND type='$type' AND style='$style' AND color='$color';");
	$badge = mysql_fetch_assoc($res);

	if (  !is_file($Cache)
	   OR (filemtime($Cache) < $data['lastupdate'])
	   OR !filesize($Cache)
	   OR $username == "gugusse")
	{

		$duration =  $_SERVER['REQUEST_TIME'] - $statsstart;
		$months = $duration / (60*60*24*30);
		$weeks  = $duration / (60*60*24*7);
		$days   = $duration / (60*60*24);

		$TracksPerDay = floor($playcount / $days);
		$TracksPerWeek = floor($playcount / $weeks);
		$TracksPerMonth = floor($playcount / $months);
		define(TRACKS_PER_ALBUM, 13);
		$AlbumsPerDay = floor($TracksPerDay / TRACKS_PER_ALBUM);
		$AlbumsPerWeek = floor($TracksPerWeek / TRACKS_PER_ALBUM);
		$AlbumsPerMonth = floor($TracksPerMonth / TRACKS_PER_ALBUM);

		$formats = array("DEFAULT"	=> "\$number \$albumtrack per \$dayweekmonth",
						 "Total"	=> "\$number \$albumtrack played",
						 "Trueness"	=> "\$username is \$trueness listener",
						 "Since"	=> "Since \$since",
						 "FAILBACK"	=> "Sorry, '\$type' is unavailable.");

		switch($type)
		{
			case "AlbumsPerDay":
			case "AlbumsPerWeek":
			case "AlbumsPerMonth":
			case "TracksPerDay":
			case "TracksPerWeek":
			case "TracksPerMonth":
				$format="DEFAULT";
				ereg("^(Albums|Tracks)Per(Day|Week|Month)$",$type,$match);
				$albumtrack=$match[1];
				$dayweekmonth=$match[2];
				eval("\$number=\$".$albumtrack."Per".$dayweekmonth.";");
				break;
			case "Trueness":
				$format=$type;
				$trueness = ($TracksPerMonth < TRUENESS ? "a true" : "an untrue");
				break;
			case "Since":
				$format=$type;
				$since=strftime("%B %Y", $statsstart);
				break;
			case "TotalTracks":
			case "TotalAlbums":
				$format="Total";
				ereg("^Total(Tracks|Albums)$",$type,$match);
				$albumtrack=$match[1];
				switch ($albumtrack)
				{
					case "Tracks":
						$number=$playcount;
						break;
					case "Albums":
						$number = floor($number / ALBUM_TRACKS);
						break;
				}
				break;
			default:
				$format="FAILBACK";
				break;
		}

		define(ANGLE,1);

		eval("\$Lines[0]->value=\"$formats[$format]\";");
		foreach ($Lines as $Line)
		{
			$Line->font = "import/" . $Styles[$style];	
			$Line->angle=ANGLE;
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


		imagepng($img, $Cache);
		imagedestroy($img);

		$QUERY=sprintf("REPLACE INTO badges (username, type, style, color, lastupdate, png) VALUES ('\$s','\$s','\$s','\$s', \$s, '\$s');", 
			  $username,
			  $type,
			  $style,
			  $color,
			  time(),
			  $Cache );
		//echo $QUERY;
		mysql_query($QUERY);
	}

	if (is_file($Cache))
	{
		touch_badge($username, $type, $style, $color);
		header("Content-Type: image/png");
		//echo $Cache;
		echo file_get_contents($Cache);
	}
}



function make_db_cache($username){
  global $data;
  $profile_xml = file_get_contents("http://ws.audioscrobbler.com/1.0/user/".rawurlencode($username)."/profile.xml");
  
  $feed=new XMLReader();
  if($feed->xml($profile_xml)){
  	while ($feed->read())
  	{
  		switch ($feed->name)
  		{
  			case "playcount":
  				$feed->read();
  				$data['playcount']=intval($feed->value);
  				$feed->read();
  				break;
  
  			case "registered":
  			case "statsreset":
  				$data['statsstart']=$feed->getAttribute("unixtime");
  				$feed->read();
  				$feed->read();
  				break;
  
  			case "profile":
  				$data['username']=$feed->getAttribute("username");
  				break;
  		}
  	}
  	
	if ($data['playcount'] != 0)
	{
		$QUERY=(sprintf("REPLACE INTO users (statsstart,playcount,lastupdate,username) VALUES ('\$s',\$s,'\$s','\$s');", 
		  time(), $data['playcount'], gpc_addslashes($data['statsstart']), gpc_addslashes(strtolower($username))));
		mysql_query($QUERY);
	}
  }
}

function touch_badge($username, $type, $style, $color)
{
	$res = mysql_query("SELECT hits FROM badges WHERE username='" . gpc_addslashes(strtolower($username)) . "' AND type='$type' AND style='$style' AND color='$color';");
	$data = mysql_fetch_assoc($res);

	if(mysql_num_rows($res))
		$hits = $data["hits"];
	$hits++;

	$QUERY=sprintf("UPDATE badges SET hits=\$s, lasthit='\$s' WHERE username='\$s' AND type='$type' AND style='$style' AND color='$color';", 
		$hits, time(), gpc_addslashes(strtolower($username)));
	//echo $QUERY;
	mysql_query($QUERY);
}

function gpc_addslashes($str){
  return (get_magic_quotes_gpc() ? $str : addslashes($str));
}

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

