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
require_once JPATH_COMPONENT_ADMINISTRATOR .DS.'ccboard-config.php';

class ccboardViewEditProfile extends JView
{
    function display( $tmpl = null )
    {
        $uri =& JFactory::getURI();
        $config = new ccboardConfig();
    	$model = &$this->getModel();

        $id = JRequest::getVar('id', JRequest::getVar('cid'));

        if (is_array($id)) {
            $id = $id[0];
        }

        JToolBarHelper::title(JText::_('CCB_EDIT_USER_PROFILE'), 'useredit');
        JToolBarHelper::save('saveProfile');
        JToolBarHelper::cancel('cancelProfile', JText::_('CCB_CLOSE'));

        $model->setUserId($id);
        $item = $model->getData();

        if( (isset($item['user_id']) ? $item['user_id'] : 0 ) < 1 ) {
        	$mainframe = &JFactory::getApplication();
        	$mainframe->enqueueMessage( JText::_('CCB_UN_AUTHORIZED_PROCESS'), 'error');
            return;
        }

        $moderatorradio = JHTML::_( 'select.booleanlist', 'moderator', null, $item['moderator']);
        $ranklist = $this->getRanks( $item['rank']);
        $forumlist = $this->getForums($id);

		$labels = array();
        $labels['pagetitle']	= JText::_('CCB_EDIT_USER_PROFILE');
        $labels['rank_title']	= JText::_('CCB_RANK_TITLE');
        $labels['name'] 		= JText::_( 'CCB_USER_REAL_NAME' );
        $labels['username'] 	= JText::_( 'CCB_USERNAME' );
        $labels['group'] 		= JText::_( 'CCB_GROUP' );
        $labels['avatar'] 		= JText::_( 'CCB_AVATAR' );
        $labels['location'] 	= JText::_( 'CCB_LOCATION' );
        $labels['save'] 		= JText::_( 'CCB_SAVE' );
        $labels['cancel'] 		= JText::_( 'CCB_CANCEL' );
        $labels['signature'] 	= JText::_( 'CCB_SIGNATURE' ) . ':<BR /><small>(' . $config->sigmax . ' ' . JText::_( 'CCB_SIG_MAX' ) . ')</small>';
        $labels['moderator'] 	= JText::_( 'CCB_MODERATOR' );
        $labels['forums'] 		= JText::_( 'CCB_MODERATED_FORUMS' );

        $this->assignRef('item', $item);
		$this->assignRef('moderatorradio', $moderatorradio);
		$this->assignRef('ranklist', $ranklist);
		$this->assignRef('forumlist', $forumlist);
		$this->assignRef('path',JURI::root());
		$this->assignRef('labels', $labels);
        $this->assignRef('action', $uri->toString());

        parent::display($tmpl);
    }

    function getRanks( $rank )
    {
    	$query = 'SELECT * FROM #__ccb_ranks WHERE rank_special = 1';
    	$data = array();
    	$db = &JFactory::getDBO();
    	$db->setQuery($query);
    	$data = ($data = $db->loadObjectList()) ? $data : array();

	   	$items = array( JHTML::_('select.option', 0, JText::_('CCB_NO_RANK_ASSIGNED') ));
	   	foreach( $data as $elem ) {
	   		$items[] = JHTML::_('select.option', $elem->id, $elem->rank_title);
	   	}

        return JHTML::_('select.genericlist', $items, 'rank', 'style="width:200px;"', 'value', 'text', $rank , 'rank');
    }

    function getForums( $userid )
    {
    	$query = 'SELECT f.id, f.forum_name, m.id AS modid ' .
    			'FROM #__ccb_forums AS f ' .
    			'LEFT JOIN #__ccb_moderators AS m on f.id = m.forum_id AND m.user_id=' . $userid .
    			' WHERE f.published = 1 AND f.moderated = 1';

    	$data = array();
    	$db = &JFactory::getDBO();
    	$db->setQuery($query);
    	$data = ($data = $db->loadObjectList()) ? $data : array();

    	$lookup = array();
    	$selections = array();
	   	foreach( $data as $elem ) {
	   		$modid = isset($elem->modid)?$elem->modid : 0;
	   		if( $modid ) {
	   			$lookup[] = $elem->id;
	   		}
	   		$selections[]  = JHTML::_('select.option', $elem->id, $elem->forum_name);
	   	}

        return JHTML::_('select.genericlist', $selections, 'selections[]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $lookup, 'selections' );
    }

}
?>

