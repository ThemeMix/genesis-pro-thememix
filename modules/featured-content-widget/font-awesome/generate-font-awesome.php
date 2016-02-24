<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Watchya doin?' );
}

$file = dirname( __FILE__ ) . '/css/_variables.scss';
$scss = file_get_contents( $file );

$exploded = explode( '$fa-var-', $scss );
$js = "var all_font_awesome_icons = [\n";
foreach ( $exploded as $key => $var ) {
	$var_exploded = explode( ': "', $var );
	$selector = $var_exploded[0];
	if ( '' != $var_exploded[1] ) {
		$js .= "	'" . $selector . "',\n";
	}
}

$js .= "];";

file_put_contents( dirname( __FILE__ ) . '/js/font-awesome-icons.js', $js );

echo "New JS file has been generated and added to the following location: \n"  . dirname( __FILE__ ) . '/js/font-awesome-icons.js';

die;