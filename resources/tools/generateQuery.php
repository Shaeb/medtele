<?php
if ($handle = opendir('C:/xampp/htdocs/concept/images/cards/CHK')) {
//if ($handle = opendir('C:/Temp/mtg/10E')) {

    while (false !== ($file = readdir($handle))) {
    	if( '.' != $file && '..' != $file ) {
    		$file = addslashes( $file );
    		echo "insert into card( card_name, card_set, card_rarity, card_color, card_number, card_image_full_size, " .
    		"card_image_reflection, card_image_thumbnail ) value( '{$file}', 1, 0, 0, 0, 1, 0, 0 );\n";
       	}
    }

    closedir($handle);
}
?>