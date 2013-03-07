#!/usr/bin/perl

use IO::Socket;
use DBI;
$| = 1;

$dbh = DBI->connect('DBI:mysql:tracker', 'tracker', '') || die "Could not connect to database: $DBI::errstr";

my $sock = new IO::Socket::INET ( LocalHost => 'localhost', LocalPort => '7070', Proto => 'tcp', Listen => 1, Reuse => 1,  );

die "Could not create socket: $!\n" unless $sock;


sub sendtomysql
{
	my $data = $_;
	chomp($data);
	# 0908242216,0033663282263,GPRMC,212442.000,A,4849.0475,N,00219.4763,E,2.29,,220809,,,A*70,F,imei:359587017313647,101Q,
	my @datas = split(/,/, $data);
	# $dbh->do('INSERT INTO exmpl_tbl VALUES(1, ?)', undef, 'Hello');
	
	my $trackerdate 			= $dbh->quote($datas[0]);
	my $phone 				= $dbh->quote($datas[1]);
	my $gprmc 				= $dbh->quote($datas[2]);
	my $satelliteDerivedTime 		= $dbh->quote($datas[3]);
	my $satelliteFixStatus 			= $dbh->quote($datas[4]);
	my $latitudeDecimalDegrees	 	= $dbh->quote($datas[5]);
	my $latitudeHemisphere 			= $dbh->quote($datas[6]);
	my $longitudeDecimalDegrees 		= $dbh->quote($datas[7]);
	my $longitudeHemisphere 		= $dbh->quote($datas[8]);
	my $speed 				= $dbh->quote($datas[9]);
	my $bearing 				= $dbh->quote($datas[10]);
	my $utcDate 				= $dbh->quote($datas[11]);
	# = $datas[12];
	# = $datas[13];
	my $checksum 				= $dbh->quote($datas[14]);
	my $gpsSignalIndicator 			= $dbh->quote($datas[15]);
	if($datas[16] =~ /imei/)
	{
		$imei 				= $datas[16];
		$other 				= $dbh->quote($datas[17].' '.$datas[18]);
	}
	else
	{
		$imei 				= $datas[17];
		$other 				= $dbh->quote($datas[18].' '.$datas[19]);
	}
	
	my $imei = $dbh->quote(substr($imei,5));

		print "INSERT INTO gprmc (date, imei, phone, trackerdate, satelliteDerivedTime, satelliteFixStatus, latitudeDecimalDegrees, latitudeHemisphere, longitudeDecimalDegrees, longitudeHemisphere, speed, Bearing, utcDate, Checksum, gpsSignalIndicator, other) VALUES (now(), $imei, $phone, $trackerdate, $satelliteDerivedTime, $satelliteFixStatus, $latitudeDecimalDegrees, $latitudeHemisphere, $longitudeDecimalDegrees, $longitudeHemisphere, $speed, $bearing, $utcDate, $checksum, $gpsSignalIndicator, $other)";
	if($gpsSignalIndicator == "'F'")
	{
		$dbh->do("INSERT INTO gprmc (date, imei, phone, trackerdate, satelliteDerivedTime, satelliteFixStatus, latitudeDecimalDegrees, latitudeHemisphere, longitudeDecimalDegrees, longitudeHemisphere, speed, Bearing, utcDate, Checksum, gpsSignalIndicator, other) VALUES (now(), $imei, $phone, $trackerdate, $satelliteDerivedTime, $satelliteFixStatus, $latitudeDecimalDegrees, $latitudeHemisphere, $longitudeDecimalDegrees, $longitudeHemisphere, $speed, $bearing, $utcDate, $checksum, $gpsSignalIndicator, $other)");
	}
	else
	{
		print $gpsSignalIndicator;
	}
}

my $new_sock;
while($new_sock = $sock->accept())
{
	while(<$new_sock>)
	{
		sendtomysql($_);
	}
}
close($sock);
$dbh->disconnect();



