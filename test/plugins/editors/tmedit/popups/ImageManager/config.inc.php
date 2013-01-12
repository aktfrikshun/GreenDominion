<?php
// $Id: config.inc.php, v 1.4 2009/06/23 17:17:45 bpfeifer Exp $
/**
* TMEdit addon - ImageManager
* @ package TMEdit
* @ Copyright © 2004-2009 Bernhard Pfeifer - www.xhtmlsuite.com
* @ All rights reserved
* @ Released under the GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
* @version $Revision: 1.4 $
**/

$BASE_DIR = $_SERVER['DOCUMENT_ROOT'];
$BASE_URL = "/";
$BASE_ROOT = "images/stories"; //if your installation resides in a sub-directory belwo the root modify like this: "[name of sub-directory]/images/stories";
$SAFE_MODE = false;
$IMG_ROOT = $BASE_ROOT;

if(strrpos($BASE_DIR, '/')!= strlen($BASE_DIR)-1)
	$BASE_DIR .= '/';

if(strrpos($BASE_URL, '/')!= strlen($BASE_URL)-1)
	$BASE_URL .= '/';

function dir_name($dir) {
	$lastSlash = intval(strrpos($dir, '/'));
	if($lastSlash == strlen($dir)-1){
		return substr($dir, 0, $lastSlash);
	} else {
		return dirname($dir);
	}
}