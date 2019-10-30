<?php
/**
 * @version		$Id: view.html.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );

class ccboardViewRanks extends JView
{
    function display( $tmpl = null )
    {
        jimport('joomla.html.pagination');
    	$mainframe = &JFactory::getApplication();
        $uri =& JFactory::getURI();

		JToolBarHelper::deleteListX('','delRank');
    	JToolBarHelper::editListX('editRank');
		JToolBarHelper::addNewX('addRank');

        $model = &$this->getModel(); // Categories

        $filter_orderDirection = $mainframe->getUserStateFromRequest('com_ccboard.filter_order_Dir', 'filter_order_Dir', 'ASC','word');
        $filter_order = $mainframe->getUserStateFromRequest('com_ccboard.filter_order', 'filter_order', 'rank_title', 'word');
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart',0);
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $model->setLimits($limitstart, $limit);
        $model->setOrdering($filter_order, $filter_orderDirection);

        $pagination = new JPagination($model->getTotal(), $limitstart, $limit);
        $items = $model->getData();


        // pass the filters on to our view
        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('filter_order', $filter_order);
        $this->assignRef('filter_orderDir', $filter_orderDirection);
		$this->assignRef('action', $uri->toString());

		parent::display($tmpl);
    }

}
?>
