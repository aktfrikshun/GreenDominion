<?php
/*
* mod_phpfile provides a simple way to include a custom PHP script into a Joomla module.
* @copyright (C) Copyright: Bernhard Wehinger, www.dynatec.at
* @author office@dynatec.at
* @date Sep 17, 2009
* @package Joomla1.5
*/

defined('_JEXEC') or die('Restricted access');

$ver = '1.0.0';
$phpfile = $params->get( 'phpfile' );
$suppress_errors = $params->get( 'suppress_errors' );
$suppress_errors = ($suppress_errors == 0) ? 0 : 1;

if (file_exists($phpfile) && is_file($phpfile)) {
	include $phpfile;
} else {
	if (!$suppress_errors)
		echo "mod_phpfile:<br />$phpfile was not found or is not a file!";
}

?>