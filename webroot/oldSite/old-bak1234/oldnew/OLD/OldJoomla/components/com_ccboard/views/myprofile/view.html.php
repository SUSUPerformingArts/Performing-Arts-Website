<?php
/**
 * @version		$Id: view.html.php 162 2009-08-22 18:33:26Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.view' );
jimport('joomla.filesystem.file');

class ccboardViewMyProfile extends JView
{

    function display( $tmpl = null )
    {
        global $ccbConfig;
        global $mainframe;
        $user = &JFactory::getUser();
    	$uri =& JFactory::getURI();
    	$model = $this->getModel('myprofile');

        if( $user->guest ) {
        	$mainframe->enqueueMessage( JText::_('RESTRICTED_AREA'), 'warning');
            return;
        }

        $id = JRequest::getVar('id', JRequest::getVar('cid'));
        $item = ccboardHelperConfig::getUserInfo($id);
        $item['user_id'] = isset($item['user_id']) ? $item['user_id'] : 0 ;
        if( $item['user_id'] < 1 ) {
        	$mainframe->enqueueMessage( JText::_('UN_AUTHORIZED_PROCESS'), 'error');
            return;
        }

        $editmode = ($user->get('id') == $id)?1:0;

        $theme = ccboardHelperConfig::getTheme();
		$userprofile = ccboardHelperConfig::getUserProfile();

		$path = 'components/com_ccboard/assets/avatar/';
		$fullpath = JURI::root() . $path;
		$customJS = 'onchange="javascript:
			if (document.forms.adminForm.avatar.options[selectedIndex].value!=\'\') {
				document.imagelib.src=\''.$fullpath.'\' + document.forms.adminForm.avatar.options[selectedIndex].value;
			}
			else
			{
				document.imagelib.src=\'../images/blank.png\';
			}""';

        $avatarcombo = '<img src="'. $item['avatar'] .'" name="imagelib" width="' . $ccbConfig->avatarwidth . '" height="' . $ccbConfig->avatarheight . '" border="0" alt="' . JText::_( 'PREVIEW' ) . '" />';
        if( $editmode > 0 ) {
			$avatarcombo .= '<br />' . JHTML::_( 'list.images', 'avatar', JFile::getName($item['avatar']) , $customJS, $path );
        }

        $gendar =  array(
        	JHTML::_('select.option', 'Male', JText::_('MALE')),
           	JHTML::_('select.option', 'Female', JText::_('FEMALE')) );
      	$gendercombo = JHTML::_( 'Select.genericlist', $gendar, 'gender','style="width:100px;"','value','text',$item['gender']);

		// Increment Only When Others View
      	if( $editmode < 1) {
	        $query = 'UPDATE #__ccb_users SET hits = hits + 1 WHERE user_id ='.$id;
	        $db = &JFactory::getDBO();
	        $db->setQuery( $query  );
	        $db->query();
      	}

      	$labels = array();
        $labels['pagetitle']	= JText::_( 'EDIT_PROFILE');
        $labels['rank_title']	= JText::_( 'RANK');
        $labels['name'] 		= JText::_( 'USER_REAL_NAME' );
        $labels['username'] 	= JText::_( 'USERNAME' );
        $labels['group'] 		= JText::_( 'GROUP' );
        $labels['avatar'] 		= JText::_( 'AVATAR' );
        $labels['postcount']	= JText::_( 'POST_COUNT' );
        $labels['karma'] 		= JText::_( 'KARMA' );
        $labels['location'] 	= JText::_( 'LOCATION' );
        $labels['dob'] 			= JText::_( 'DATE_OF_BIRTH' );
        $labels['gender'] 		= JText::_( 'GENDER' );
        $labels['upload'] 		= JText::_( 'AVATAR_UPLOAD' );
        $labels['save'] 		= JText::_( 'SAVE' );
        $labels['cancel'] 		= JText::_( 'CANCEL' );
        $labels['signature'] 	= JText::_( 'SIGNATURE' ) . ':<BR /><small>(' . $ccbConfig->sigmax . ' ' . JText::_( 'CCB_SIG_MAX' ) . ')</small>';
        $labels['view'] 		= JText::_( 'PROFILE_HITS' );
        $labels['www'] 			= JText::_( 'WEBSITE' );
        $labels['icq'] 			= JText::_( 'ICQ_NUMBER' );
        $labels['aol']	 		= JText::_( 'AOL_INSTANT_MESSENGER' );
        $labels['msn'] 			= JText::_( 'MSN_MESSENGER' );
        $labels['yahoo'] 		= JText::_( 'YAHOO_MESSENGER' );
        $labels['jabber'] 		= JText::_( 'JABBER_ADDRESS' );
        $labels['skype'] 		= JText::_( 'SKYPE_ADDRESS' );
        $labels['registereddate'] = JText::_('REGISTERED_DATE');
        $labels['lastvisitdate'] = JText::_('LAST_VISIT_DATE');

        $this->assignRef('item', $item);
		$this->assignRef('labels', $labels);
		$this->assignRef('userprofile', $userprofile);
		$this->assignRef('editmode', $editmode);
		$this->assignRef('theme', $theme);
		$this->assignRef('avatarcombo', $avatarcombo);
		$this->assignRef('gendercombo', $gendercombo);
        $this->assignRef('action', $uri->toString());

		ccboardHelperConfig::setBreadCrumb('ccbhome');
	    ccboardHelperConfig::setBreadCrumb(JText::_('MYPROFILE_LINK'));

        parent::display($tmpl);
    }

}
?>

