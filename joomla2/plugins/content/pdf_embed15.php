<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
$mainframe->registerEvent( 'onPrepareContent', 'plgContentpdfembed' );

function plgContentpdfembed( &$row, &$params, $page=0 ) {
	
	// expression to search for
	$regex = "#{pdf[\=|\s]?(.+)}#s";
 	$regex1 = '/{(pdf=)\s*(.*?)}/i';

	// find all instances of mambot and put in $matches
	preg_match_all( $regex1, $row->text, $matches );

 	// Number of mambots
	$count = count( $matches[0] );

	for ($i=0; $i<$count; $i++) {	
		$r	=	str_replace( '{pdf=', '', $matches[0][$i]);
		$r	=	str_replace( '}', '', $r); 
		$ex	=	explode('|',$r);
		
		$ploc	=	$ex[0]; 		
		$w	=	$ex[1];
		$h	=	$ex[2];
		
		$replace = plg_pdfembed_replacer($ploc , $w, $h );
		$row->text = str_replace( '{pdf='.$ex[0].'|'.$ex[1].'|'.$ex[2].'}', $replace, $row->text);
	} 
	return true;
}
	
function plg_pdfembed_replacer($ploc , $w, $h ) {
		
		return '<embed src="'.$ploc.'" width="'.$w.'" height="'.$h.'"/>';
	}
?>


