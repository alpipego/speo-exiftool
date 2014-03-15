<?php

/*
Plugin Name: Speotyto Exiftool
Plugin URI: http://www.speotyto.com/
Description: Make <a href="http://owl.phy.queensu.ca/~phil/exiftool/">Phil Harvey's Exiftool</a> available in WordPress
Version: 0.1
Author: alpipego
Author URI: http://alpipego.com/
*/

error_reporting(-1);

//include the php exiftool
include( 'ExifToolBatch.php' );

//setup the connection to exiftool
$path = plugin_dir_path( __FILE__ ) . 'Image-ExifTool-9.54/exiftool';
$exif = new ExifToolBatch();
$exif->setExifToolPath( $path );

//open connection
function speo_open() {
	global $exif;
	$exif->start();
}
add_action( 'init', 'speo_open' );

//get the exif data for given image
function speo_exif( $att_id ) {
	global $exif;
	//get the imagepath
	$attachment = get_attached_file( $att_id );
	// $attachment = '/Users/alexgoller/SkyDrive/SecureWAMP_Portable/htdocs/speotyto/uploads';

	$data = array( '-h', '-g', '-j', $attachment ); 
	$exif->add( $data );
	// $result = $exif->fetch();
	$result = $exif->fetchAllDecoded();

	foreach ($result[0] as $section => $value) {
		echo $section . '<br />';
	}
	echo '<code><pre>';
		var_dump( $result[0]->EXIF );
	echo '</pre></code>';

	echo '<code><pre>';
		var_dump($result);
	echo '</pre></code>';

	error_log( 'test', 0, plugin_dir_path( __FILE__ ) . 'output.log' );

	$exif->close();
}