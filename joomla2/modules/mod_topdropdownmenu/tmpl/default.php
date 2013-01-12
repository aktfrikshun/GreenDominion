<?php

// no direct access
defined('_JEXEC') or die('Restricted access');


if ( ! defined('modTopDrDnMenuXMLCallbackDefined') )
{
function modTopDrDnMenuXMLCallback(&$node, $args)
{
	$user	= &JFactory::getUser();
	$menu	= &JSite::getMenu();
	$active	= $menu->getActive();
	$path	= isset($active) ? array_reverse($active->tree) : null;

	if (($args['end']) && ($node->attributes('level') >= $args['end']))
	{
		$children = $node->children();
		foreach ($node->children() as $child)
		{
			if ($child->name() == 'div') { //changed from ul to div as it's now the first child
				$node->removeChild($child);
			}
		}
	}

	if ($node->name() == 'ul') {
		
		foreach ($node->children() as $child)
		{
			if ($child->attributes('access') > $user->get('aid', 0)) {
				$node->removeChild($child);
			}
		}
	}
	
	if (($node->name() == 'li') && $node->attributes('level') ==1) {
		$node->addAttribute('class', 'top');
	}
	
	if (($node->name() == 'li') && isset($node->div)) {
		print_r(isset($node->ul));
		$node->addAttribute('class', 'parent');
	}
	
	if (($node->name() == 'li') && $node->attributes('level') ==1 && isset($node->div)) {
		$node->addAttribute('class', 'top parent');
	}
	
	if (isset($path) && in_array($node->attributes('id'), $path))
	{
		if ($node->attributes('class')) {
			$node->addAttribute('class', $node->attributes('class').' active');
		} else {
			$node->addAttribute('class', 'active');
		}
	}
	else
	{
		if (isset($args['children']) && !$args['children'])
		{
			$children = $node->children();
			foreach ($node->children() as $child)
			{
				if ($child->name() == 'ul') {
					$node->removeChild($child);
				}
			}
		}
	}

	if (($node->name() == 'li') && ($id = $node->attributes('id'))) {
		if ($node->attributes('class')) {
			$node->addAttribute('class', $node->attributes('class').' item'.$id);
		} else {
			$node->addAttribute('class', 'item'.$id);
		}
	}

	if (isset($path) && $node->attributes('id') == $path[0]) {
		$node->addAttribute('id', 'current');
	} else {
		$node->removeAttribute('id');
	}
	$node->removeAttribute('level');
	$node->removeAttribute('access');

}
	define('modTopDrDnMenuXMLCallbackDefined', true);
}

modTopDrDnMenuHelper::render($params, 'modTopDrDnMenuXMLCallback');
