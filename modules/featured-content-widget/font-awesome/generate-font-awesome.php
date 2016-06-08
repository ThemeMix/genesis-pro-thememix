<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Watchya doin?' );
}

$path = 'https://github.com/FortAwesome/Font-Awesome/raw/master/';

/**
 * Generating new icon list in JS.
 */
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
 * Copying files across.
 */
$files = array(
	'css/font-awesome.css',
	'css/font-awesome.css.map',
	'css/font-awesome.min.css',
	'fonts/FontAwesome.otf',
	'fonts/fontawesome-webfont.eot',
	'fonts/fontawesome-webfont.svg',
	'fonts/fontawesome-webfont.ttf',
	'fonts/fontawesome-webfont.woff',
	'fonts/fontawesome-webfont.woff2',
);
foreach ( $files as $file ) {
	$contents = file_get_contents( $path . $file );
	file_put_contents( dirname( __FILE__ ) . '/' . $file, $contents );
}
echo '<br /><br />CSS files have been copied over.';

die;