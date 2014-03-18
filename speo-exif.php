<?php

/*
Plugin Name: Speotyto Exiftool
Plugin URI: http://www.speotyto.com/
Description: Make <a href="http://owl.phy.queensu.ca/~phil/exiftool/">Phil Harvey's Exiftool</a> available in WordPress
Version: 0.1
Author: alpipego
Author URI: http://alpipego.com/
*/

//include the php exiftool
include( 'ExifToolBatch.php' );

//setup the connection to exiftool
$path = plugin_dir_path( __FILE__ ) . 'Image-ExifTool-9.54/exiftool';
$exif = new ExifToolBatch();
$exif->setExifToolPath( $path );

//include the options page
include( 'speo-options.php' );

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

	$data = array( '-h', '-j', '-common', $attachment ); 
	$exif->add( $data );
	// $result = $exif->fetch();
	$result = $exif->fetchAllDecoded();
	$result = $result[0];

	foreach ($result as $section => $value) {
		echo $section . '<br />';
	}
	echo '<code><pre>';
		var_dump( $result->EXIF );
	echo '</pre></code>';

	echo '<code><pre>';
		var_dump($result);
	echo '</pre></code>';

	error_log( 'test', 0, plugin_dir_path( __FILE__ ) . 'output.log' );

	$exif->close();
}

function speo_tags() {
	global $exif;
    $tags = array( '-listx', '-EXIF:All' );
	$exif->add( $tags );
	$result = $exif->fetchAll();

	$file = fopen( plugin_dir_path( __FILE__ ) . 'list.xml','w' );
	fwrite($file, $result[0]);
	fclose($file);
}

register_activation_hook( __FILE__, 'speo_tags' );