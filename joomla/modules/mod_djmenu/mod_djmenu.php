<?php 
/**
* @version 1.5.0 beta 4
* @package DJ Menu
* @copyright Copyright (C) 2010 Blue Constant Media LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
*
*
* DJ Menu is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Menu is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Menu. If not, see <http://www.gnu.org/licenses/>.
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

global $mainframe;
$document = &JFactory::getDocument();

if($params->get('css',1) || !file_exists(JPATH_ROOT.DS.'templates'.DS.$mainframe->getTemplate().DS.'css'.DS.'djmenu_fx.css')) {
	$css_fx = JURI::base().'modules/mod_djmenu/assets/css/djmenu_fx.css';
} else {
	$css_fx = JURI::base().'templates/'.$mainframe->getTemplate().'/css/djmenu_fx.css';
}
if($params->get('css',1) || !file_exists(JPATH_ROOT.DS.'templates'.DS.$mainframe->getTemplate().DS.'css'.DS.'djmenu.css')) {
	$css = JURI::base().'modules/mod_djmenu/assets/css/djmenu.css';
} else {
	$css = JURI::base().'templates/'.$mainframe->getTemplate().'/css/djmenu.css';
}

if($params->get('moo',1)) {
	
	JHTML::_('behavior.mootools');
	$js = JURI::base().'modules/mod_djmenu/assets/js/djmenu.js';
	$document->addScript($js);
	$document->addStyleSheet($css);
	$document->addStyleSheet($css_fx);
	
	$effect = $params->get('effect');
	if($effect!='linear') $effect.='.easeOut';
	if(!is_numeric($duration = $params->get('duration'))) $duration = 300;
	if(!is_numeric($delay = $params->get('delay'))) $delay = 2000;
	$height_fx = ($params->get('height_fx')) ? 'true' : 'false';
	$width_fx = ($params->get('width_fx')) ? 'true' : 'false';
	$opacity_fx = ($params->get('opacity_fx')) ? 'true' : 'false';
	$height_fx_sub = ($params->get('height_fx_sub')) ? 'true' : 'false';
	$width_fx_sub = ($params->get('width_fx_sub')) ? 'true' : 'false';
	$opacity_fx_sub = ($params->get('opacity_fx_sub')) ? 'true' : 'false';
	
	$options = "{transition: Fx.Transitions.$effect, duration: $duration, delay: $delay,
		height_fx: $height_fx, width_fx: $width_fx, opacity_fx: $opacity_fx, mid: $module->id,
		height_fx_sub: $height_fx_sub, width_fx_sub: $width_fx_sub, opacity_fx_sub: $opacity_fx_sub }";
	
	$wrapper_id = $params->get('wrapper');
	
	$js = "
	(function($){ // Mootools Safe Mode ON
	
	window.addEvent('domready',function(){
		var DJMenusAll = $$('#dj-main$module->id li.dj-up');
		var wrapper = $('$wrapper_id');
		DJMenusAll.each(function(djmenu){
			if(djmenu.getElement('.dj-drop')) {
				DJMenus.include(new DJMenu(djmenu,0,wrapper,$options));
			} else {
				djmenu.addEvent('mouseenter',function(){
					djmenu.addClass('hover');
				});
				djmenu.addEvent('mouseleave',function(){
					djmenu.removeClass('hover');
				});
			}
		});	
	});
	
	})(document.id);";
	$document->addScriptDeclaration($js);
	
} else {
	
	if(preg_match('/MSIE 6.0/',$_SERVER['HTTP_USER_AGENT'])) { // IE6
		JHTML::_('behavior.mootools');
		$css = $css_fx;
		$js = JURI::base().'modules/mod_djmenu/assets/js/ie6Hover.js';
		$document->addScript($js);
	}
	$document->addStyleSheet($css);
}

$params->def('module_id',$module->id);
$djmenu = new modDJMenuHelper();

require(JModuleHelper::getLayoutPath('mod_djmenu'));

?>