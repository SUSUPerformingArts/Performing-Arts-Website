<?php
/**
 * @version		$Id: myprofile.php 152 2009-06-20 17:26:28Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );


class ccboardModelMyProfile extends JModel
{

    function ccboardModelMyProfile()
	{
		parent::__construct();
	}

	function store( $data )
	{
		jimport('joomla.filesystem.file');
		global $ccbConfig;
		$filter = new JFilterInput(array(), array(), 1, 1);
		$user = &JFactory::getUser();

		$uid = $filter->clean($data['user_id'], 'integer');
		$location = htmlspecialchars($filter->clean($data['location'], 'string'), ENT_QUOTES, 'UTF-8');;
		$gender = $filter->clean($data['gender'], 'string');
		$signature = $data['signature'];
		$signature = substr($signature,0, $ccbConfig->sigmax); /* limit Sig Chars */
		$signature = htmlspecialchars( $signature, ENT_QUOTES, 'UTF-8');
		$avatar = $filter->clean($data['avatar'], 'string');
		$dob = strtotime($filter->clean($data['dob'], 'string'));
		$www = $filter->clean($data['www'], 'string');
		$icq = $filter->clean($data['icq'], 'string');
		$aol = $filter->clean($data['aol'], 'string');
		$msn = $filter->clean($data['msn'], 'string');
		$yahoo = $filter->clean($data['yahoo'], 'string');
		$jabber	= $filter->clean($data['jabber'], 'string');
		$skype = $filter->clean($data['skype'], 'string');

		if( $user->get('id') <> $uid ) {
			$this->setError(JText::_('INVALID_OPERATION'));
			return false;
		}

        ccboardHelperConfig::checkUserExists( $uid );

        $query = 'UPDATE #__ccb_users SET ' .
					'location = "' . $location . '", ' .
					'signature = "' . $signature . '", ' .
					'gender = "' . $gender. '", ' .
					'dob = ' . $dob .', ' .
					'www = "' . $www .'", ' .
					'icq = "' . $icq .'", ' .
					'aol = "' . $aol .'", ' .
					'msn = "' . $msn .'", ' .
					'yahoo = "' . $yahoo.'", ' .
					'jabber = "' . $jabber .'", ' .
					'skype = "' . $skype .'" ';
		if( $avatar <> '' ) {
			$query .= ', avatar = "' . $avatar . '", thumb = "' . $avatar . '" ';
		}

		$query .= ' WHERE user_id=' . $uid;

		$db = &JFactory::getDBO();
		$db->setQuery( $query );
		if( !$db->query() ) {
			$this->setError( $db->getError());
			return false;
		}
		return true;
	}
}
?>
