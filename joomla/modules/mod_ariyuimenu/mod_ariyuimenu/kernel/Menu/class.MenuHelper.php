<?php
/*
 * ARI Framework Lite
 *
 * @package		ARI Framework Lite
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('Joomla.JoomlaUtils');

class AriJoomlaMenuHelper
{
	function getHierarchialMenu($menuType, $isRegister = false, $startLevel = 0, $endLevel = -1, $activeMenuId = null)
	{
		if ($startLevel > $endLevel && $endLevel > -1)
			list($startLevel, $endLevel) = array($endLevel, $startLevel);
		
		$db =& AriJoomlaUtils::getDBO();
		$query = sprintf('SELECT M.*' .
			' FROM #__menu AS M' .
			' WHERE menutype = %s AND published = 1 AND access < %d%s' .
			' ORDER BY parent, ordering',
			$db->Quote($menuType),
			$isRegister ? 3 : 1,
			$endLevel > -1 ? ' AND sublevel <= ' . $endLevel : '');
		$db->setQuery($query);
		$menu = $db->loadObjectList('id');
		
		if ($db->getErrorNum())
		{
			return null;
		}

		if (!is_array($menu) || count($menu) < 1)
		{
			return null;
		}

		foreach ($menu as $id => $menuItem)
		{			
			$parentId = $menuItem->parent; 
			if (!$parentId || !isset($menu[$parentId]))
				continue ;

			$parent =& $menu[$parentId];
			if (!isset($parent->children))
			{
				$parent->children = array();
			}

			$parent->children[] =& $menu[$id];
		}

		if ($startLevel > 0)
		{
			if ($activeMenuId)
				$activeMenuId = AriJoomlaMenuHelper::findParentMenuId($menu, $activeMenuId);
			
			$cMenu = $menu;
			foreach ($cMenu as $id => $menuItem)
			{
				if ($menuItem->sublevel != $startLevel)
					continue ;

				$parentId = AriJoomlaMenuHelper::findParentMenuId($menu, $id);
				if (!$parentId || ($activeMenuId && $parentId != $activeMenuId))
					unset($menu[$id]);
				else 
					$menu[$id]->parent = 0;
			}
			
			foreach ($cMenu as $id => $menuItem)
			{
				if ($menuItem->sublevel < $startLevel)
					unset($menu[$id]);
			}
		}

		return $menu;
	}
	
	function findParentMenuId($menu, $childId)
	{
		if (!is_array($menu) || empty($childId) || !isset($menu[$childId])) return 0;

		$menuItem = $menu[$childId];
		while ($menuItem->parent)
		{
			if (!isset($menu[$menuItem->parent])) return 0;
			
			$menuItem = $menu[$menuItem->parent];
		}

		return $menuItem->id;
	}
	
	function getMenuItemIndex($menu, $menuId)
	{
		$index = -1;
		if (!is_array($menu) || empty($menuId) || !isset($menu[$menuId])) return $index;
		
		$parentId = $menu[$menuId]->parent;
		
		$i = 0;
		foreach ($menu as $id => $menuItem)
		{
			if ($id == $menuId)
			{
				$index = $i;
				break;
			}
			
			if ($menuItem->parent == $parentId) ++$i;
		}
	
		return $index;
	}

	function getItemId($query)
	{
		$matches = array();
		preg_match('/Itemid=([0-9]+)/', $query, $matches);

		return isset($matches[1]) ? $matches[1] : 0;
	}
}
?>