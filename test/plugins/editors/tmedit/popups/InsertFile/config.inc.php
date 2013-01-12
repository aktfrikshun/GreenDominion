<?php
// $Id: config.inc.php, v 1.2 2009/06/23 14:47:53 bpfeifer Exp $
/**
* TMEdit InsertFile
* @ package TMEdit
* @ Copyright © 2004-2009 Bernhard Pfeifer - www.xhtmlsuite.com
* @ All rights reserved
* @ Released under the GNU/GPL http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
* @version $Revision: 1.2 $
**/

$MY_LOCAL_PATH 			= $_SERVER['DOCUMENT_ROOT'];
$MY_DOCUMENT_ROOT 		= $MY_LOCAL_PATH."/images/stories";
$MY_BASE_URL 			= '/images/stories';			//if your installation resides in a sub-directory belwo the root modify like this: "/[name of sub-directory]/images/stories";
														//if you are using Docman change this to '/dmdocuments';
$MY_ALLOW_EXTENSIONS	= array('html', 'doc', 'xls', 'txt', 'gif', 'pdf', 'gz', 'tar', 'zip', 'rar', 'bzip', 'sql', 'swf', 'mov', 'jpeg', 'jpg', 'png'); //add file types here, e. g. 'gif', 'jpeg', 'jpg', 'png', 'pdf'
$MY_DENY_EXTENSIONS		= array('php', 'php3', 'php4', 'phtml', 'shtml', 'cgi', 'pl'); //add file types here
$MY_LIST_EXTENSIONS		= array('html', 'doc', 'xls', 'txt', 'pdf', 'gz', 'tar', 'zip', 'rar', 'sql', 'swf', 'mov', 'gif', 'jpeg', 'jpg', 'png');	//add file types here
$MY_DATETIME_FORMAT		= "d.m.Y H:i";// set your date and time format

$MY_LANG 				= 'en';	// change this to 'de'; for german language

// DO NOT EDIT BELOW
$MY_NAME = 'insertfiledialog';

$lang_file = 'lang/lang-'.$MY_LANG.'.php';
if (is_file('lang/lang-'.$MY_LANG.'.php')) {
	require($lang_file);
} else {
	require('lang/lang-en.php');
}
$MY_PATH = '/';
$MY_UP_PATH = '/';