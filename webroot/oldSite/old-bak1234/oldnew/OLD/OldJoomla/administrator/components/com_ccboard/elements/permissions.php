<?php
/**
 * @version		$Id: permissions.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

class JElementPermissions extends JElement
{
    var $_name = 'Permissions';
    function fetchElement($name, $value, &$node, $control_name)
    {
        $acl = &JFactory::getACL();
        $fieldName = $name;

        $items = array(
            JHTML::_('select.option', '0', 'Guest'),
            JHTML::_('select.option', $acl->get_group_id(null, 'Registered'), JText::_('REGISTERED') ),
            JHTML::_('select.option', $acl->get_group_id(null, 'Author'), JText::_('AUTHOR')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Editor'), JText::_('EDITOR')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Publisher'), JText::_('PUBLISHER')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Manager'), JText::_('MANAGER')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Administrator'), JText::_('ADMINISTRATOR')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Super Administrator'), JText::_('CCB_SUPER_ADMINISTRATOR')),
        );

        return JHTML::_('select.genericlist', $items, $fieldName, 'style="width:200px;"', 'value', 'text', $value, $control_name.$name);
    }

    function fetchModElement($name, $value, &$node, $control_name)
    {
        $acl = &JFactory::getACL();
        $fieldName = $name;

        $items = array(
            JHTML::_('select.option', $acl->get_group_id(null, 'Registered'), JText::_('REGISTERED') ),
            JHTML::_('select.option', $acl->get_group_id(null, 'Author'), JText::_('AUTHOR')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Editor'), JText::_('EDITOR')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Publisher'), JText::_('PUBLISHER')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Manager'), JText::_('MANAGER')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Administrator'), JText::_('ADMINISTRATOR')),
            JHTML::_('select.option', $acl->get_group_id(null, 'Super Administrator'), JText::_('SUPER_ADMINISTRATOR')),
        );

        return JHTML::_('select.genericlist', $items, $fieldName, 'style="width:200px;"', 'value', 'text', $value, $control_name.$name);
    }

    function getRegistered()
    {
    	$acl = &JFactory::getACL();
    	return $acl->get_group_id(null, 'Registered');
    }

 	function getManager()
    {
    	$acl = &JFactory::getACL();
    	return $acl->get_group_id(null, 'Manager');
    }

 	function getAdministrator()
    {
    	$acl = &JFactory::getACL();
    	return $acl->get_group_id(null, 'Administrator');
    }
}
