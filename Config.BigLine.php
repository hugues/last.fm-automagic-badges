<?

define(TRUENESS, 4000);
define(CACHE,    (3600*48));
define(WIDTH,     200);
define(HEIGHT,     50);
putenv("GDFONTPATH=/usr/share/fonts/truetype");

define(CACHE_FOLDER, "/var/cache/www/Lastfm");

include("Config.mysql");

$Styles = array (
			"Astonished" => "Astonish",
			"Broken" => "Broken15",
			"Curly"    => "Curlz___",
			"DirtyEgo" => "Dirtyego",
			"DisgustingBehaviour" => "Disgb___",
			"DownCome" => "Downcome",
			"FaceLift" => "facerg__",
			"Flamy"	=> "Baileysc",
			"Funny"    => "PenguinAttack",
			"Girlie"   => "girlw___",
			"Grindy"   => "Jack_the_Hipper",
			"Guilty" => "Guilty__",
			"Hooper"=> "Hooper_D",
			"Horrorful"=> "horrh___",
			"HorsePuke" => "Horsp___",
			"Letters"  => "JackOLantern",
			"MaxRhodes" => "Maxrhode",
			"MemoryLapses" => "Memol___",
			"MisProject" => "Misproje",
			"Modern"   => "It_wasn_t_me",
			"Morpheus" => "Morpheus",
			"Nails" => "Nails___",
			"Nasty" => "Nasty___",
			"Pastelaria" => "Pastelar",
			"Porcelain" => "Porcelai",
			"PrintError" => "Prine___",
			"Rochester" => "Rocheste",
			"Romantic" => "Shelley_Volante",
			"Ruritania"=> "Ruritania",
			"Screamy"  => "Junkyard",
			"Selfish" => "Selfish_",
			"Shortcut" => "Shortcut",
			"Simple"   => "Georgia",
			"TypeO"    => "typeo___"
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
		   		"TracksPerDay"   => "Daily tracks_",
		   		"TracksPerWeek"   => "Weekly tracks_",
		   		"TracksPerMonth"   => "Monthly tracks_",
				"TotalTracks" 	  => "Total tracks_",
		   		"AlbumsPerDay"   => "Daily albums_",
		   		"AlbumsPerWeek"   => "Weekly albums_",
		   		"AlbumsPerMonth"   => "Monthly albums_",
				"TotalAlbums"    => "Total albums_",
				"Since"    => "Since",
		   		"Trueness" => "Trueness",
				);

$Description = array("Trueness" => "<a href=\"http://www.last.fm/group/true+listener\">What's the heck is this </a> ?");

// DEFAULT VALUES //
if ($user == "")                        $user="gugusse";
if (!array_key_exists($style, $Styles)) $style="Astonished";
if (!array_key_exists($color, $Colors)) $color="Black";
//if (!array_key_exists($type,  $Types))  { $type="UNAVAILABLE" ; $color="Black" ; $username="gugusse" ; }

?>
