<?

define(TRUENESS, 4000);
define(CACHE,    (3600*36));
define(WIDTH,     160);
define(HEIGHT,     20);
putenv("GDFONTPATH=/usr/share/fonts/truetype");

define(CACHE_FOLDER, "/var/cache/www/Lastfm");

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
				"Total" 	  => "Total tracks_ #1",
				"Total2"    => "Total tracks_ #2",
		   		"PerDay"   => "Daily tracks_ #1",
		   		"PerDay2"   => "Daily tracks_ #2",
				"PerWeek"  => "Weekly tracks_ #1",
				"PerWeek2"  => "Weekly tracks_ #2",
				"PerMonth" => "Monthly tracks_ #1",
				"PerMonth2" => "Monthly tracks_ #2",
				"Since"    => "Since #1",
				"Since2"   => "Since #2",
		   		"Trueness" => "Trueness #1",
		   		"Trueness2" => "Trueness #2"
				);

$Description = array("Trueness" => "<a href=\"http://www.last.fm/group/true+listener\">What's the heck is this </a> ?");

// DEFAULT VALUES //
if ($user == "")                        $user="gugusse";
if (!array_key_exists($style, $Styles)) $style="TypeO";
if (!array_key_exists($color, $Colors)) $color="Black";
if (!array_key_exists($type,  $Types))  { $type="UNAVAILABLE" ; $color="Black" ; $username="gugusse" ; }

?>
