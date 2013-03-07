<?php echo '<?xml version="1.0" encoding="iso-8859-1"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr_FR">
<head>
<title>Tracker Tk102</title>
</head>
<body>

<?php
if(isset($_GET['sources']))
	show_source(__FILE__);

$cnx = mysql_connect('localhost', 'tracker', '');
mysql_select_db('tracker', $cnx);


$res = mysql_query("SELECT * FROM gprmc ORDER BY id DESC");
while($data = mysql_fetch_assoc($res))
{
	/*
	Array
	(
	    [id] => 1217
	    [date] => 2009-08-26
	    [imei] => 359587017313647
	    [phone] => 0663282263
	    [trackerdate] => 0908261623
	    [satelliteDerivedTime] => 212442.000
	    [satelliteFixStatus] => A
	    [latitudeDecimalDegrees] => 4849.0475
	    [latitudeHemisphere] => N
	    [longitudeDecimalDegrees] => 00219.4763
	    [longitudeHemisphere] => E
	    [speed] => 2.29
	    [bearing] => 0
	    [utcDate] => 220809
	    [checksum] => A*70
	    [gpsSignalIndicator] => L
	    [other] => 983¤  
	)

	*/
	$trackerdate = ereg_replace("^(..)(..)(..)(..)(..)$","\\3/\\2/\\1 \\4:\\5",$data['trackerdate']);
	strlen($data['latitudeDecimalDegrees']) == 9 && $data['latitudeDecimalDegrees'] = '0'.$data['latitudeDecimalDegrees'];
	$g = substr($data['latitudeDecimalDegrees'],0,3);
	$d = substr($data['latitudeDecimalDegrees'],3);
	$latitudeDecimalDegrees = $g + ($d/60);

	strlen($data['longitudeDecimalDegrees']) == 9 && $data['longitudeDecimalDegrees'] = '0'.$data['longitudeDecimalDegrees'];
	$g = substr($data['longitudeDecimalDegrees'],0,3);
	$d = substr($data['longitudeDecimalDegrees'],3);
	$longitudeDecimalDegrees = $g + ($d/60);
	$speed = $data['speed'] * 1.609;
	echo "$trackerdate $g $d $latitudeDecimalDegrees $longitudeDecimalDegrees -- $speed --  <a href=\"http://maps.google.fr/maps?q=$latitudeDecimalDegrees $longitudeDecimalDegrees\">GoogleMap</a><br />\n";
}
mysql_close($cnx);
?>
</body>
</html>
