<?php

/*****************************************************************
 *
 * @version		v1.0.0
 * @package 	Contact Us Pro by CSW
 * @copyright	Copyright (c) 2010 C.S. Wilson and Associates, Inc. All rights reserved.
 * @website		www.c-s-wilson.com
 * @license		GNU/GPL, see http://www.c-s-wilson.com/license-agreement.html
 *
 * This program is NOT free software; you can NOT redistribute 
 * it, but do have the right to modify it under the terms of 
 * the GNU General Public License v2 as published by the Free 
 * Software Foundation;
 *
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public 
 * License along with this program; if not, write to:
 *
 *   C.S. Wilson and Associates
 *   7303 E Hampshire Ln,
 *   Nampa, ID 83687
 *   sales@c-s-wilson.com
 *   http://www.c-s-wilson.com/license-agreement.html
 *
 *****************************************************************/


// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Define constants for all pages
 */
define( 'COM_CONTACTUS_DIR', 'images'.DS.'contactus'.DS );
define( 'COM_CONTACTUS_BASE', JPATH_ROOT.DS.COM_CONTACTUS_DIR );
define( 'COM_CONTACTUS_BASEURL', JURI::root().str_replace( DS, '/', COM_CONTACTUS_DIR ));

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require the base controller
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

// Initialize the controller
$controller = new ContactusController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
?>