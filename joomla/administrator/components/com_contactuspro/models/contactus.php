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

// Import Joomla! libraries
jimport('joomla.application.component.model');

class ContactusModelContactus extends JModel {
    function __construct() {
		parent::__construct();
    }
}
?>