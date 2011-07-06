<!doctype html>
<html>
<head>
<style>

body,
section,
div.section {
	display: block;
	background: #eee;
	margin: 0;
	padding: 0;

	font-family: sans-serif;
}

header,
div.header {
	display: block;
	padding: 1em 0;
	text-align: center;
	border-bottom: 1px solid #666;
	background: #aaa ;/*url("/Flowers_247.jpg");*/
	width: 100%;
	color: rgba(100%,100%,100%,0.6);

	-moz-box-shadow: 0 0 2em black;
	-webkit-box-shadow: 0 0 2em black;
}

article,
div.article {
	display: block;
	margin: 0;
	padding: 0 20%;
	width: auto;
}

.code {
	border: 3px solid #ccc;
	background: #ddd;
	color: #666;
	font-size: small;
	font-family: monospace, fixed;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.code:hover {
	overflow: visible;
	width: auto;
}

:required {
	background: #fcc;
}

input:valid[type=text] {
	background: #cfc;
}

.center {
	text-align: center;
}

img#preview {
	margin: 1em 0;
}

img#preview:hover {
	background: #f0f0f0;
}

a {
	text-decoration: none;
	color: #666;
}
a:hover {
	color: #999;
}

</style>

</head>

<body  onLoad="total_users_counter();">
<section>
<header>
<h2>Last.fm automagic Badges</h2>
<?

mysql_connect("localhost","lastfm");
mysql_select_db("lastfm");

$res=mysql_query("select count(username) as users from users where playcount != 0;");
$data=mysql_fetch_assoc($res);

if ($data["users"])
{
?>
<script>

var total_users = <? echo $data["users"]; ?>;
var users_count = 0;
var step = 1024;

function total_users_counter()
{
	document.getElementById("Go").style.visibility="hidden";
	document.getElementById("counter").innerHTML = users_count;

	while (total_users < users_count + step)
	{
		step /= 2;
	}
	users_count += step;

	if(users_count < total_users)
	{
		setTimeout(total_users_counter, 60);
	}
	else
	{
		document.getElementById("counter").innerHTML = total_users;
		document.getElementById("count").fadeOut('slow');
		document.getElementById("count").fadeIn('slow');
	}
}

</script>

<p id="Wow">Wow, <span id="counter"><? echo $data["users"]; ?></span> people are using it on <a href="http://www.last.fm/">Last.fm</a> right now !</p>
</header>

<script>
function changeValue(name, value)
{
	items = document.getElementsByName(name);
	for (i=0; i<items.length; i++)
	{
		items[i].innerHTML = value;
	}
}

function change()
{
	user = document.getElementsByName("user")[0].value;
	type = document.getElementsByName("type")[0].value;
	style = document.getElementsByName("style")[0].value;
	color = document.getElementsByName("color")[0].value;

	document.getElementById("preview").src = "http://www.hiegel.fr/~hugues/images/navigation/loading.gif";
	document.getElementById("preview").src = "/BigLine/" + user + "/" + type + "/" + style + "/" + color + "/";

	changeValue( "_user", user);
	changeValue( "_type", type);
	changeValue( "_style", style);
	changeValue( "_color", color);
}
</script>

<?
}

$user  = @$_POST['user'];
$type  = @$_POST['type'];
$style = @$_POST['style'];
$color = @$_POST['color'];

$pathinfo = explode("/", $_SERVER['PATH_INFO']);

if ($type  == "") $type  = @$pathinfo[1]; if ($type  == "") $type  = "TracksPerDay";
if ($style == "") $style = @$pathinfo[2];
if ($color == "") $color = @$pathinfo[3];
if ($user  == "") $user  = @$pathinfo[4];

include("Config.BigLine.php");

$PATH="<span name=\"_type\">".$type."</span>/<span name=\"_style\">$style</span>/<span name=\"_color\">".$color."</span>/<span name=\"_user\">".$user."</span>";
$URL=preg_replace('/<[^>]*>/', '', $PATH);

?>

<article>
<h3>Get yours!</h3>

<form action="<? echo $_SERVER['REQUEST_URI']; ?>" method="post" onblur="change()" onchange="change()">
<table>
<tr><td>
Enter your last.fm account name: </td><td><input autofocus autocomplete required type="text" size="20" name="user" placeholder="account name" value="<? echo $user; ?>"/></td>
</tr>
<tr><td>
Choose the type : </td><td><select onkeyup="change()" name="type"><?
foreach ($Types as $Type => $Comment)
{?>
<option value="<? echo $Type; ?>"<? if ($type==$Type) echo " selected ";?>><? echo str_replace("_", "", $Comment); ?></option>
<?
} //foreach
?>
</select></td></tr>
<tr><td>Choose the style :</td><td><select onkeyup="change()" name="style"><?
foreach ($Styles as $Style => $Font)
{
?><option value="<? echo $Style; ?>"<? if ($style==$Style) echo " selected ";?>><? echo $Style; ?></option><?
} //foreach
?></select></td></tr>

<tr><td>Choose the color :</td><td><select onkeyup="change()" name="color"><?
foreach ($Colors as $Color => $ColorCode)
{?>
<option value="<? echo $Color; ?>"<? if ($color==$Color) echo " selected ";?>><? echo $Color; ?></option>
<?
} //foreach
?>
</select></td></tr>
<tr id="Go"><td></td>
<td><input type="submit" value="Go!" /></td></tr>
<tr>
<td colspan=2 class="center">
<img id="preview" src="/<? echo $URL ;?>.png" alt="<? echo str_replace("_", $user, $Comment); ?>" />
</td>
</table>
</form>

<? if ($type != "UNAVAILABLE") { ?>
<div class="code">
	[url=http://lastfm.hiegel.fr/<? echo $PATH; ?>]<br />
	[img]http://lastfm.hiegel.fr/<? echo $PATH; ?>.png[/img]<br />
	[/url]
</div>
<? } ?>


<p><? if ($type!="UNAVAILABLE") { ?> Copy/paste the above BBcode to your <a href="http://www.last.fm/settings">profile settings</a>.<br /><?}?>
And feel free to join my <a href="http://www.last.fm/group/Automagic+Badges/">Last.fm group</a> ;)</p>
</article>
</section>

</body>
