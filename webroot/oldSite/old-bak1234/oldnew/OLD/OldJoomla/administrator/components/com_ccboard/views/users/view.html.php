<?php
/**
 * @version		$Id: view.html.php 142 2009-05-02 15:56:18Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/


defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );

class ccboardViewUsers extends JView
{

    function display($tmpl = null)
    {
    	jimport('joomla.html.pagination');
    	$mainframe = &JFactory::getApplication();
    	$uri =& JFactory::getURI();
        JToolBarHelper::editListX('editProfile');

        $model = &$this->getModel('users','ccboard' ); // Users
    	//$items = $model->getData();

    	$filter_order		= $mainframe->getUserStateFromRequest( "com_ccboard.filter_order",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "com_ccboard.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( "com_ccboard.search",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( 'com_ccboard.limitstart', 'limitstart', 0, 'int' );

        $where = '';
		if (isset( $search ) && $search!= '')
		{
			$db = &JFactory::getDBO();
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where = ' a.username LIKE '.$searchEscaped.' OR a.email LIKE '.$searchEscaped .' OR a.name LIKE '.$searchEscaped .' OR a.usertype LIKE '.$searchEscaped ;
		}

        $model->setLimits($limitstart, $limit);
        $model->setOrdering($filter_order, $filter_order_Dir);

        $pagination = new JPagination($model->getTotal( $where ), $limitstart, $limit);
        $items = $model->getData( $where );

    	$this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('search',		$search);
        $this->assignRef('filter_order', $filter_order);
        $this->assignRef('filter_order_Dir', $filter_order_Dir);
        $this->assignRef('action', $uri->toString());

		parent::display($tmpl);
    }
}
?>
