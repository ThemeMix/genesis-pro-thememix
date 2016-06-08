<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Watchya doin?' );
}

$path = 'https://github.com/FortAwesome/Font-Awesome/raw/master/';

$scss = file_get_contents( $path . 'scss/_variables.scss' );

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

/**
 * Copying CSS across.
 */
$contents = file_get_contents( $path . 'css/font-awesome.css' );
file_put_contents( dirname( __FILE__ ) . '/css/font-awesome.css', $contents );

$contents = file_get_contents( $path . 'css/font-awesome.css.map' );
file_put_contents( dirname( __FILE__ ) . '/css/font-awesome.css.map', $contents );

$contents = file_get_contents( $path . 'css/font-awesome.min.css' );
file_put_contents( dirname( __FILE__ ) . '/css/font-awesome.min.css', $contents );

echo '<br /><br />CSS files have been copied over.';

/**
 * Copying the fonts across.
 */
$files = array(
	'FontAwesome.otf',
	'fontawesome-webfont.eot',
	'fontawesome-webfont.svg',
	'fontawesome-webfont.ttf',
	'fontawesome-webfont.woff',
	'fontawesome-webfont.woff2',
);

die;