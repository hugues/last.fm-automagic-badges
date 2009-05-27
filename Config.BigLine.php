<?

define(TRUENESS, 4000);
define(CACHE,    (3600*48));
define(WIDTH,     300);
define(HEIGHT,     50);
putenv("GDFONTPATH=/usr/share/fonts/truetype");

define(CACHE_FOLDER, "/var/cache/www/Lastfm");

include("Config.mysql");

$Styles = array ("Modern"   => "It_wasn_t_me",
				 "Letters"  => "JackOLantern",
				 "Romantic" => "Shelley_Volante",
				 "Elegant"  => "ITCEdScr",
				 "Screamy"  => "Junkyard",
				 "Girlie"   => "girlw___",
				 "Funny"    => "PenguinAttack",
				 "Curly"    => "Curlz___",
				 "Ruritania"=> "Ruritania",
		 		 "Simple"   => "Georgia",
				 "Morpheus" => "Morpheus",
				 "Flamy"	=> "Baileysc",
				 "FaceLift" => "facerg__",
				 "TypeO"    => "typeo___",
				 "Grindy"   => "Jack_the_Hipper",
				 "Horrorful"=> "horrh___"
				 );

$Colors = array(
				"Black"  => 0x000000,
				"Red"    => 0xd11f3c,
				"Green"  => 0x32dc32,
				"Yellow" => 0xdcdc32,
				"Blue"   => 0x3232dc,
				"LightBlue" => 0x6666aa,
				"Gray"   => 0xdcdcdc,
				"White"  => 0xffffff
			);

$Types = array(
				"TotalTracks" 	  => "Total tracks_",
				"TotalAlbums"    => "Total albums_",
		   		"TracksPerDay"   => "Daily tracks_",
		   		"TracksPerWeek"   => "Weekly tracks_",
		   		"TracksPerMonth"   => "Monthly tracks_",
		   		"AlbumsPerDay"   => "Daily albums_",
		   		"AlbumsPerWeek"   => "Weekly albums_",
		   		"AlbumsPerMonth"   => "Monthly albums_",
				"Since"    => "Since",
		   		"Trueness" => "Trueness",
				);

$Description = array("Trueness" => "<a href=\"http://www.last.fm/group/true+listener\">What's the heck is this </a> ?");

// DEFAULT VALUES //
if ($user == "")                        $user="gugusse";
if (!array_key_exists($style, $Styles)) $style="TypeO";
if (!array_key_exists($color, $Colors)) $color="Black";
//if (!array_key_exists($type,  $Types))  { $type="UNAVAILABLE" ; $color="Black" ; $username="gugusse" ; }

?>
