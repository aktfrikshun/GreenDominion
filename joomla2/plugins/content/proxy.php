<?php
/**
* @version		$Id: Enbed pdf v1.7 2010-10-29 13:41 $
* @package		Joomla 1.5
* @copyright	Copyright (C) 2005 - 2010 Maik Heinelt. All rights reserved.
* @author		Maik Heinelt (www.heinelt.info)
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
 
if(isset($_COOKIE['TBqP25b5nGuuFTph']))
	{
		if (isset($_GET['embedded']))
		{
			// get the src page, change relative to absolute and don't remove 'chan' param in get requests
			$code = file_get_contents("http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);
			
			$search = array("/gview/images", "gview/resources_gview/client/js");
			$replace = array("http://docs.google.com/gview/images", "?jsfile=gview/resources_gview/client/js");
			$code = str_replace($search, $replace, $code);
			
			header('Content-type: text/html');
			echo $code;
		} 
		else if (isset($_GET['a']) && $_GET['a'] == 'gt') 
		{
			// get text coordinates file, can not redirect because of same origin policy
			$code = file_get_contents("http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);
			header('Content-type: text/xml; charset=UTF-8');
			echo $code;
		} 
		else if (isset($_GET['a']) && $_GET['a'] == 'bi') 
		{
			// redirect to images
			header("Location: http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);
			header('Content-type: image/png');
		}
		else if (isset($_GET['jsfile'])) 
		{
			// proxy javascript files and replace navigator.cookieEnabled with false
			$code = file_get_contents("http://docs.google.com/" . $_GET['jsfile']);
			
			$search = array("navigator.cookieEnabled");
			$replace = array("false");
			$code = str_replace($search, $replace, $code);
			
			header('Content-type: text/javascript');
			echo $code;
		}
		else 
		{
			// everything else, of which there isn't!
			header("Location: http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);
		}
	} 
	else
		{
			die("Restricted access");
		}



?> 