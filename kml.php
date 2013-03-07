<?php 
if(isset($_GET['sources']))
	show_source(__FILE__);
else
	header('Content-Type: application/vnd.google-earth.kml+xml');
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";

//-87.67283499240875,42.019110918045044,0

$cnx = mysql_connect('localhost', 'tracker', '');
mysql_select_db('tracker', $cnx);


$res = mysql_query("SELECT * FROM gprmc WHERE gpsSignalIndicator = 'F' ORDER BY id DESC");
$line_coordinates = "";
$ballons = "";
while($data = mysql_fetch_assoc($res))
{
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
//echo "$latitudeDecimalDegrees,$longitudeDecimalDegrees,0\n";
$line_coordinates .= "$longitudeDecimalDegrees,$latitudeDecimalDegrees,0\n";

$ballons .= '
<Placemark>
    <name>Point '.$data['id'].'</name>
    <description>Vitesse : '.floor($speed).'Km/h - Date : '.ereg_replace("^(..)(..)(..)(..)(..)$","\\3/\\2/\\1 \\4:\\5",$data['trackerdate']).'</description>
    <styleUrl>#exampleBalloonStyle</styleUrl>
    <Point>
      <coordinates>'."$longitudeDecimalDegrees,$latitudeDecimalDegrees,0".'</coordinates>
    </Point>
</Placemark>
';
	
}
mysql_close($cnx);
?>
<kml xmlns="http://earth.google.com/kml/2.1">
  <Document>
    <name>Tracker Map</name>
    <description>Tracker de Cyril</description>

    <Style id="redLine">
      <LineStyle>
        <color>ff0000ff</color>
        <width>4</width>
      </LineStyle>
    </Style>

    <Style id="balloonStyle">
      <BalloonStyle>
        <!-- a background color for the balloon -->
        <bgColor>ffffffbb</bgColor>
        <!-- styling of the balloon text -->
        <text><![CDATA[
        <b><font color="#CC0000" size="+3">$[name]</font></b>
        <br/><br/>
        <font face="Courier">$[description]</font>
        <br/><br/>
        Extra text that will appear in the description balloon
        <br/><br/>
        <!-- insert the to/from hyperlinks -->
        $[geDirections]
        ]]></text>
      </BalloonStyle>
    </Style>

    <Style id="greenPoint">
      <LineStyle>
        <color>ff009900</color>
        <width>4</width>
      </LineStyle>
    </Style>

    <Placemark>
      <name>Red Line</name>
      <styleUrl>#redLine</styleUrl>
      <LineString>
        <altitudeMode>relative</altitudeMode>
        <coordinates>
<?php echo $line_coordinates; ?>
        </coordinates>
      </LineString>
    </Placemark>

<?php echo $ballons; ?>

  </Document>
</kml>
