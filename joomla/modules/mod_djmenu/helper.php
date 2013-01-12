<?php
/**
* @version 1.5.0 beta 2
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

class modDJMenuHelper {

    var $name = null;
    
    var $params = null;
    
    var $tabakt = null;

    
    function render(&$paramsa) {

    
        $this->params = $paramsa;

        
        if (is_null($this->name)) {
        
            $this->name = $this->params->get('menu');
            
        }
        
        echo '<ul id="dj-main'.$this->params->get('module_id').'" class="dj-main">';
        
        $this->ShowMenu();
        
        echo '</ul>';
        
    }
    
    function ShowMenu() {
    
        $menu = &JSite::getMenu();
        
        $user = &JFactory::getUser();
        
        //get menu items
        
        $rows = $menu->getItems('menutype', $this->name);
        
        $children = array();
		
        foreach ($rows as $v) {
            if ($v->access > 0) {
                if ($user->id == '0') {
                    continue;
                }
                if ($v->access == '2' && ($user->usertype == 'Registered')) {
                    continue;
                }
            }

            $pt = $v->parent;

            
            $list = @$children[$pt] ? $children[$pt] : array();

            
            array_push($list, $v);

            
            $children[$pt] = $list;

            
        }

        
        $this->tabakt = $this->aktywne($children);

        
        $this->mosRecurseListMenu(0, 0, $children);

        
        return true;
        
    }

    
    function aktywne(&$children) {
    
        global $Itemid;

        
        foreach ($children as $tab) {
        
            foreach ($tab as $obj) {
            
                if ($obj->id == $Itemid)
                    return $obj->tree;

                    
            }
            
        }
        
        return array();
    }
    
    /**
     
     * Utility function for writing a menu link
     
     */
     
    function mosGetMenuLink($mitem, $level = 0, &$params, $havechild = null) {
    
        global $Itemid;
        
        $txt = '';
        
        // Menu Link is a special type that is a link to another item
        
        if ($mitem->type == 'menulink') {
        
            $menu = &JSite::getMenu();
            
            if ($tmp = $menu->getItem($mitem->query['Itemid'])) {
            
                $name = $mitem->name;
                
                $mid = $mitem->id;
                
                $parent = $mitem->parent;
                
                $mitem = clone ($tmp);
                
                $mitem->name = $name;
                
                $mitem->mid = $mid;
                
                $mitem->parent = $parent;
                
            } else {
            
                return;
                
            }
            
        }
        
        switch ($mitem->type) {
        
            case 'separator':
            
				$mitem->browserNav = 3;
                break;
                
            case 'url':
            
                if (eregi('index.php\?', $mitem->link)) {
                
                    if (!eregi('Itemid=', $mitem->link)) {
                    
                        $mitem->link .= '&amp;Itemid='.$mitem->id;
                        
                    }
                    
                }
                
                break;
                
            default:
            
                $mitem->link = 'index.php?Itemid='.$mitem->id;
                
                break;
                
        }
        
        // Active Menu highlighting
        
        $current_itemid = intval($Itemid);

        
        $classplus = '';

        
        if (in_array($mitem->id, $this->tabakt)) {
        
            $classplus = 'active ';
            
        }

        
        $class = 'class="'.$classplus.'"';
        
        if ($level == 0)
            $class = 'class="dj-up_a '.$classplus.'"';
            
        if ($havechild && $level != 0) {
        
            if ( empty($classplus))
                $class = ' class="dj-more" ';
                
            else
                $class = ' class="dj-more-'.$classplus.'" ';
                
        }

        
        // replace & with amp; for xhtml compliance
        
        $menu_params = new stdClass ();
        
        $menu_params = new JParameter($mitem->params);
        
        $menu_secure = $menu_params->def('secure', 0);
        
        if (strcasecmp(substr($mitem->link, 0, 4), 'http')) {
        
            $mitem->url = JRoute::_($mitem->link, true, $menu_secure);
            
        } else {
        
            $mitem->url = $mitem->link;
            
        }

        
        $spanclass = '';
        
        if ($havechild && $level == 0) {
        
            $spanclass = ' class="dj-drop" ';
            
        }

        // get image if selected
		
		$menu_img = '';
        $menu_image = $menu_params->get('menu_image');
        
        if ($menu_image != '-1') {
            $menu_img = '<img src="'.JURI::base().'images/stories/'.$menu_image.'" alt="'.$menu_image.'" />';
        }
		
        // replace & with amp; for xhtml compliance
        
        // remove slashes from excaped characters
        
        $mitem->name = stripslashes(htmlspecialchars($mitem->name));
        
        $mitemname = $menu_img.$mitem->name;
        
        if ($level == 0) {
        
            $mitemname = '<span '.$spanclass.'>'.$mitemname.'</span>';
            
        }
        
        switch ($mitem->browserNav) {
        
            // cases are slightly different
            
            case 1:
            
                // open in a new window

                
                $txt = '<a href="'.$mitem->url.'" target="_blank"  '.$class.'>'.$mitemname.'</a>';
                
                break;
                
            case 2:
            
                // open in a popup window
                
                $txt = "<a href=\"#\" onclick=\"javascript: window.open('".$mitem->url."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false\" class=\"$menuclass\" ".$id.">".$mitemname."</a>\n";
                
                break;
                
            case 3:
            
                // don't link it
                
                $txt = '<a '.$class.'>'.$mitemname.'</a>';
                
                break;
                
            default:
            
                // open in parent window
                
                $txt = '<a href="'.$mitem->url.'"  '.$class.'>'.$mitemname.'</a>';
                
                break;
                
        }

        
        return $txt;
        
    }
    
    /**
     
     * Search for Itemid in link
     
     */
     
    function ItemidContained($link, $Itemid) {
    
        $link = str_replace('&amp;', '&', $link);
        
        $temp = explode("&", $link);
        
        $linkItemid = "";
        
        foreach ($temp as $value) {
        
            $temp2 = explode("=", $value);
            
            if ($temp2[0] == "Itemid") {
            
                $linkItemid = $temp2[1];
                
                break;
                
            }
            
        }
        
        if ($linkItemid != "" && $linkItemid == $Itemid) {
        
            return true;
            
        } else {
        
            return false;
            
        }
        
    }
    
    /**
     
     *  Module Menu
     
     */
     
    function mosRecurseListMenu($id, $level, &$children) {
    
        global $Itemid;
        
        global $HTTP_SERVER_VARS,$mosConfig_live_site;
        
        if (@$children[$id]) {
        
            $elements = count($children[$id]);
            
            $counter = 0;
            
            foreach ($children[$id] as $row) {
            
                $counter++;
                
                $separator = false;
                
                switch ($row->type) {
                
                    case 'separator':
                    
                        // do nothing
                        
                        $separator = true;
                        
                        break;
                        
                    case 'url':
                    
                        if (eregi('index.php\?', $row->link)) {
                        
                            if (!eregi('Itemid=', $row->link)) {
                            
                                $row->link .= '&Itemid='.$row->id;
                                
                            }
                            
                        }
                        
                        break;
                        
                    default:
                    
                        if (!eregi('Itemid=', $row->link)) {
                        
                            $row->link .= "&Itemid=$row->id";
                            
                        }
                        
                        break;
                        
                }

                
                @$havechild = is_array($children[$row->id]);
                
                $classname = "";

                
                if ($level == 0) {
                
                    $classname .= "dj-up Itemid".$row->id." ";
                    
                }

                
                if ($counter == 1) {
                
                    $classname .= "first ";
                    
                } else if ($counter == $elements) {
                
                    $classname .= "last ";
                    
                }

                
                if (in_array($row->id, $this->tabakt)) {
                
                    $classname .= "active";
                    
                }
				
                if ($level > 0) {
                    $classname .= " Itemid".$row->id;
                }
				
                if ($separator) {
                    $classname .= " separator";
                }
                
                $class = "";
                
                if (! empty($classname)) {
                
                    $class = " class=\"".$classname."\"";
                    
                }

                if ($havechild) {
                
                    echo "<li".$class.">".$this->mosGetMenuLink($row, $level, $this->params, 1);
                    
                    echo "\n";
                    
                    if ($level == 0) {
                    
                        echo "<ul class=\"dj-submenu\">\n";
						echo "<li class=\"submenu_top\" style=\"display: none\"> </li>\n";
                        
                        $this->mosRecurseListMenu($row->id, $level + 1, $children);
                        
						echo "<li class=\"submenu_bot\" style=\"display: none\"> </li>\n";
                        echo "</ul>\n";
                        
                    } else {
                    
                        echo "<ul>\n";
                        echo "<li class=\"submenu_top\" style=\"display: none\"> </li>\n";
						
                        $this->mosRecurseListMenu($row->id, $level + 1, $children);
                        
						echo "<li class=\"submenu_bot\" style=\"display: none\"> </li>\n";
                        echo "</ul>\n";

                        
                    }
                    
                    echo "</li>\n";
                    
                } else {
                
                    echo "<li".$class.">".$this->mosGetMenuLink($row, $level, $this->params)."</li>";
                    
                    echo "\n";
                    
                }
                
            }
            
        }
        
    }
}


?>