<?php
include_once( "include/settings.php" );

$settingshandler = new Settings();

$imagedata = base64_decode($settingshandler->getSettingValue( 'logo' ));

$im = imagecreatefromstring($imagedata);
$imageinfo = getimagesizefromstring( $imagedata );
if ( $im !== false ) {
    if ( isset( $_REQUEST[ 'small' ] ) ) {
        $im = imagescale( $im, 70 ); 
    }
    imagesavealpha($im, true);
    header('Content-Type: '.$imageinfo[ 'mime' ]);

    switch( $imageinfo[ 'mime' ] ) { 
        case "image/jpeg": 
            imagejpeg($im);
            break; 
        case "image/png": 
            imagepng($im);
            break; 
    }

    imagedestroy($im);
}

?>
