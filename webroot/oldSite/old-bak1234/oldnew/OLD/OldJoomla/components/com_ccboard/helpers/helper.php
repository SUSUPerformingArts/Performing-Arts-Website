<?php
/**
 * @version		$Id: helper.php 175 2009-09-21 16:57:32Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport('joomla.filesyste.file');

class ccboardHelperConfig
{

	function getUserInfo( $userid )
	{
		global $ccbConfig;
		$db = &JFactory::getDBO();
        $data = array();
        if( $userid < 1 ) {
        	return $data;
        }
    	$query = 'SELECT a.id AS userid, a.name, a.username, a.usertype, a.email, ' .
    					 'a.registerDate, a.lastvisitDate, a.gid, b.*, '.
    					 'r.rank_title, r.rank_image, r.rank_special ' .
 		                 'FROM #__users AS a ' .
		                 'LEFT JOIN #__ccb_users AS b ON b.user_id = a.id ' .
		                 'LEFT JOIN #__ccb_ranks AS r ON b.rank = r.id  ' .
    	                 'WHERE a.id = '. (int)$userid;
        $db->setQuery($query);
        $data = ($data = $db->loadObject()) ? $data : array();

        $udata = array();
        $udata['id'] 			= $data->id;
        $udata['user_id'] 		= $data->userid;
        $udata['name'] 			= $data->name;
        $udata['username'] 		= $data->username;
        $udata['usertype']		= $data->usertype;
        $udata['email'] 		= $data->email;
        $udata['location'] 		= isset($data->location)?$data->location:'';
        $udata['dob'] 			= isset($data->dob)?$data->dob:0;
        $udata['www'] 			= isset($data->www)?$data->www:'';
        $udata['icq'] 			= isset($data->icq)?$data->icq:'';
        $udata['aol'] 			= isset($data->aol)?$data->aol:'';
        $udata['msn'] 			= isset($data->msn)?$data->msn:'';
        $udata['yahoo']			= isset($data->yahoo)?$data->yahoo:'';
        $udata['jabber'] 		= isset($data->jabber)?$data->jabber:'';
        $udata['skype']			= isset($data->skype)?$data->skype:'';
        $udata['gender'] 		= isset($data->gender)?$data->gender: JText::_('MALE');
        $udata['hits'] 			= isset($data->hits)?$data->hits:0;
        $udata['signature'] 	= isset($data->signature)?$data->signature:'';
        $udata['avatar'] 		= CCBOARD_ASSETS_URL . '/avatar/' . (isset($data->avatar)?$data->avatar:'avatar1.png');
        $udata['karma'] 		= isset($data->karma)?$data->karma: 0;
        $udata['rank'] 			= isset($data->rank)?$data->rank: 0;
        $udata['rank_special']	= isset($data->rank_special)?$data->rank_special: 0;
        $udata['rank_title']	= isset($data->rank_title)?$data->rank_title: '';
        $udata['rank_image']	= CCBOARD_ASSETS_URL . '/ranks/' . isset($data->rank_title)?$data->rank_image: '';
        $udata['post_count'] 	= isset($data->post_count)?$data->post_count:0;
        $udata['registerdate']	= isset($data->registerDate)?$data->registerDate:'';
        $udata['lastvisitdate']	= isset($data->lastvisitDate)?$data->lastvisitDate:'';
        $udata['moderator'] 	= isset($data->moderator)?$data->moderator:0;

        $acl = &JFactory::getACL();
    	if( $data->gid >= $acl->get_group_id(null, 'Administrator') ) {
    		$udata['moderator'] = 1;
    	}

        if( $ccbConfig->userprofile == 'jomsocial') {
	        $query = 'SELECT a.* FROM #__community_users AS a WHERE a.userid = '.(int)$userid;
	        $db->setQuery($query);
	        $data = ($data = $db->loadObject()) ? $data : array();
			$udata['avatar'] =  'components/com_community/assets/default.jpg';
	        if( isset($data->avatar) ) {
				$udata['avatar'] = $data->avatar ;
	        }
	        $query = 'SELECT a.id, b.value AS vlv ' .
	                 	'FROM #__community_fields AS a ' .
	        			'INNER JOIN #__community_fields_values AS b ON b.field_id = a.id ' .
	                 	'WHERE a.fieldcode = "FIELD_CCB_LOCATION" AND b.user_id = '.(int)$userid;
	        $db->setQuery($query);
	        $data = $db->loadObject();
	        $udata['location'] = isset($data->vlv) ? $data->vlv : '';

	        $query = 'SELECT a.id, b.value AS vlv ' .
	                 	'FROM #__community_fields AS a ' .
	        			'INNER JOIN #__community_fields_values AS b ON b.field_id = a.id ' .
	                 	'WHERE a.fieldcode = "FIELD_CCB_SIGNATURE" AND b.user_id = '.(int)$userid;
	        $db->setQuery($query);
	        $data = $db->loadObject();
	        $udata['signature'] = isset($data->vlv) ? $data->vlv : '';

	        $query = 'SELECT a.points ' .
	                 	'FROM #__community_users AS a ' .
	                 	'WHERE a.userid = '.(int)$userid;
	        $db->setQuery($query);
	        $data = $db->loadObject();
	        $udata['karma'] = isset($data->points) ? $data->points: 0;

		} elseif( $ccbConfig->userprofile == 'combuilder') {
	        $query = 'SELECT a.firstname, a.middlename, a.lastname,  ' .
	        			'a.avatar, a.avatarapproved, a.ccblocation, a.ccbsignature, a.ccbkarma '.
	                 	'FROM #__comprofiler AS a ' .
	                 	'WHERE a.user_id = '.(int)$userid ;
	        $db->setQuery($query);
	        $data = ($data = $db->loadObject()) ? $data : array();
			$udata['avatar'] = 'components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png';
	        if( $data->avatarapproved == 1 && isset($data->avatar)) {
				$udata['avatar'] = 'images/comprofiler/' . $data->avatar ;
	        }
	        $firstname = isset($data->firstname) ? $data->firstname: '';
	        $middlename= isset($data->middlename) ? $data->middlename: '';
	        $lastname = isset($data->lastname) ? $data->lastname: '';
	        $uname = trim(trim($firstname . ' '. $middlename) . ' ' . $lastname);
			$udata['name'] = $uname != '' ? $uname : $udata['name'] ;
	        $udata['location'] = isset($data->ccblocation) ? $data->ccblocation: '';
	        $udata['signature'] = isset($data->ccbsignature) ? $data->ccbsignature: '';
	        $udata['karma'] = isset($data->ccbkarma) ? $data->ccbkarma: 0;
        }

        return $udata;


	}

	function getUserProfile( $uid = -1)
	{
		global $ccbConfig;
		$data = array();
		$ccbuserprofile = $ccbConfig->userprofile;

		if( $uid == -1 ) {
			$user = &JFactory::getUser();
			$uid = $user->get('id');
		}

		$data['boardusername'] = JText::_('CCB_GUEST') . '&nbsp;&nbsp;&nbsp;' ;

        if( $ccbuserprofile == 'combuilder') {
       		if($ccbConfig->showreglink > 0) $data['boardusername'] .= '<a rel="nofollow" href="' . JRoute::_('index.php?option=com_comprofiler&task=registers') . '">[' . JText::_('REGISTER') . ']</a>&nbsp;&nbsp;';
        	if($ccbConfig->showloginlink > 0) $data['boardusername'] .= '<a rel="nofollow" href="' . JRoute::_('index.php?option=com_comprofiler&task=login&Itemid=' . $ccbConfig->comprofilerId) . '">' . JText::_('CCB_LOGIN') . '</a>' ;
    	    $data['thumb'] = '<span class="topicavatar"><img src="components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png" style="height: ' . $ccbConfig->smallavatarheight . 'px; width: ' . $ccbConfig->smallavatarwidth . 'px;" /></span>';
			$data['avatar'] = '<img src="components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png" style="height: ' . $ccbConfig->avatarheight . 'px; width: ' . $ccbConfig->avatarwidth . 'px;" />';
       	} elseif( $ccbuserprofile == 'jomsocial') {
       		if($ccbConfig->showreglink > 0) $data['boardusername'] .= '<a rel="nofollow" href="' . JRoute::_('index.php?option=com_community&view=register&Itemid=' . $ccbConfig->jomsocialId ) . '">[' . JText::_('REGISTER') . ']</a>&nbsp;&nbsp;';
       		if($ccbConfig->showloginlink > 0) $data['boardusername'] .= '<a rel="nofollow" href="' . JRoute::_('index.php?option=com_community&view=frontpage&Itemid=' . $ccbConfig->jomsocialId ) . '">' . JText::_('CCB_LOGIN') . '</a>';
    	    $data['thumb'] = '<span class="topicavatar"><img src="components/com_community/assets/default.jpg" style="height: ' . $ccbConfig->smallavatarheight . 'px; width: ' . $ccbConfig->smallavatarwidth . 'px;" /></span>';
    	    $data['avatar'] = '<img src="components/com_community/assets/default.jpg" style="height: ' . $ccbConfig->avatarheight . 'px; width: ' . $ccbConfig->avatarwidth . 'px;" />';
       	} else {
       		if($ccbConfig->showreglink > 0) $data['boardusername'] .= '<a rel="nofollow" href="' . JRoute::_('index.php?option=com_user&task=register') . '">[' . JText::_('REGISTER') . ']</a>&nbsp;&nbsp;';
       		if($ccbConfig->showloginlink > 0) $data['boardusername'] .= '<a rel="nofollow" href="' . JRoute::_('index.php?option=com_user&view=login') . '">' . JText::_('CCB_LOGIN') . '</a>';
	        $data['thumb'] = '<span class="topicavatar"><img src="' . CCBOARD_ASSETS_URL . '/avatar/avatar1.png" style="height: ' . $ccbConfig->smallavatarheight . 'px; width: ' . $ccbConfig->smallavatarwidth . 'px;" /></span>';
	        $data['avatar'] = '<img src="' . CCBOARD_ASSETS_URL . '/avatar/avatar1.png" style="height: ' . $ccbConfig->avatarheight . 'px; width: ' . $ccbConfig->avatarwidth . 'px;" />';
       	}

       	$data['home'] = '<a rel="nofollow" href="' . JRoute::_('index.php?option=com_ccboard&view=forumlist&Itemid=' . $ccbConfig->itemid) .'" >'.  JText::_('BOARD_HOME') . '</a>';
        $data['user_id'] = 0;
        $data['link'] = '';
        $data['profilelink']='#';
        $data['mylist'] = '';
        $data['approval'] = '';
		$data['latest'] = JRoute::_('index.php?option=com_ccboard&view=recentlist&Itemid=' . $ccbConfig->itemid, true);
        $data['latest'] = '<a rel="nofollow" href="' . $data['latest'] . '">' . JText::_('LATEST_POSTS') . '</a>';
        $data['username'] 		= JText::_('CCB_GUEST');
        $data['usertype']		= JText::_('CCB_GUEST');
        $data['email'] 			= '';
        $data['location'] 		= '';
        $data['signature'] 		= '';
        $data['karma'] 			= 0;
        $data['rank'] 			= 0;
        $data['rank_special']	= 0;
        $data['rank_title']		= '';
        $data['rank_image']		= '';
        $data['post_count'] 	= 0;
        $data['www'] 			= '';
        $data['icq'] 			= '';
        $data['aol'] 			= '';
        $data['msn'] 			= '';
        $data['yahoo']			= '';
        $data['jabber'] 		= '';
        $data['skype']			= '';
        $data['registerdate']	= date($ccbConfig->dateformat);
        $data['lastvisitdate']	= date($ccbConfig->dateformat);

		if ( $uid > 0 ) {
			$udata = ccboardHelperConfig::getUserInfo( $uid );

			$data['user_id'] = $uid;
			if( $ccbConfig->showrealname > 0) {
				$data['username'] = $udata['name'];
			} else {
				$data['username'] = $udata['username'];
			}
	        $data['email'] 			= $udata['email'];
	        $data['location'] 		= $udata['location'];
	        $data['signature'] 		= $udata['signature'];
	        $data['karma'] 			= $udata['karma'];
	        $data['rank'] 			= $udata['rank'];
	        $data['rank_special']	= $udata['rank_special'];
	        $data['rank_title']		= $udata['rank_title'];
	        $data['rank_image']		= $udata['rank_image'];
	        $data['post_count'] 	= $udata['post_count'];
	        $data['www'] 			= $udata['www'];
	        $data['icq'] 			= $udata['icq'];
	        $data['aol'] 			= $udata['aol'];
	        $data['msn'] 			= $udata['msn'];
	        $data['yahoo']			= $udata['yahoo'];
	        $data['jabber'] 		= $udata['jabber'];
	        $data['skype']			= $udata['skype'];
	        $data['registerdate']	= $udata['registerdate'];
	        $data['lastvisitdate']	= $udata['lastvisitdate'];
    	    $data['thumb'] = '<span class="topicavatar"><img src="' . $udata['avatar'] . '" style="height: ' . $ccbConfig->smallavatarheight . 'px; width: ' . $ccbConfig->smallavatarwidth . 'px;" /></span>';
			$data['avatar'] = '<img src="' . $udata['avatar'] . '" style="height: ' . $ccbConfig->avatarheight . 'px; width: ' . $ccbConfig->avatarwidth . 'px;" />';
			if($udata['moderator'] > 0 ) {
				$data['approval'] = JRoute::_('index.php?option=com_ccboard&view=approval&Itemid=' . $ccbConfig->itemid, true);
				$data['approval'] = '<a rel="nofollow" href="' . $data['approval'] . '">' . JText::_('APPROVAL_LINK') . '</a>';
			}
			$data['mylist'] = JRoute::_('index.php?option=com_ccboard&view=mylist&viewmode=myposts&Itemid=' . $ccbConfig->itemid, true);
        	$data['mylist'] = '<a rel="nofollow" href="' . $data['mylist'] . '">' . JText::_('MYPOSTS_LINK') . '</a>';

        	if( $ccbConfig->userprofile == 'ccboard') {
	        	$data['link'] = JRoute::_('index.php?option=com_ccboard&view=myprofile&cid=' . $data['user_id'] . '&Itemid=' . $ccbConfig->itemid, true);
        		$data['boardusername'] = $data['username'];
                if($ccbConfig->showloginlink > 0) $data['boardusername'] .= '&nbsp;&nbsp;&nbsp;<a href="' . JRoute::_('index.php?option=com_user&view=login') . '">' . JText::_('CCB_LOGOUT') . '</a>';
                $data['boardusername'] .= '<br/><small>' . JText::_('LAST_VISIT_WAS') . ' ' . JHTML::_('date', $data['lastvisitdate'], $ccbConfig->dateformat ).'</small>' ;
        	}
        	elseif( $ccbConfig->userprofile == 'combuilder') {
        		$data['link'] = JRoute::_('index.php?option=com_comprofiler&task=userProfile&user=' . $data['user_id'] . '&Itemid=' . $ccbConfig->comprofilerId , true);
        		$data['boardusername'] = $data['username'];
                if($ccbConfig->showloginlink > 0) $data['boardusername'] .= '&nbsp;&nbsp;&nbsp;<a href="' . JRoute::_('index.php?option=com_comprofiler&task=logout&Itemid=' . $ccbConfig->comprofilerId) . '">' . JText::_('CCB_LOGOUT') . '</a>';
                $data['boardusername'] .= '<br/><small>' . JText::_('LAST_VISIT_WAS') . ' ' . JHTML::_('date', $data['lastvisitdate'], $ccbConfig->dateformat ).'</small>' ;
        	}
        	elseif( $ccbConfig->userprofile == 'jomsocial') {
	        	$data['link'] = JRoute::_('index.php?option=com_community&view=profile&userid=' . $data['user_id'] . '&Itemid=' . $ccbConfig->jomsocialId , true);
        		$data['boardusername'] = $data['username'];
                if($ccbConfig->showloginlink > 0) $data['boardusername'] .= '&nbsp;&nbsp;&nbsp;<a href="' . JRoute::_('index.php?option=com_community&view=frontpage&Itemid=' . $ccbConfig->jomsocialId) . '">' . JText::_('CCB_LOGOUT') . '</a>';
                $data['boardusername'] .= '<br/><small>' . JText::_('LAST_VISIT_WAS') . ' ' . JHTML::_('date', $data['lastvisitdate'], $ccbConfig->dateformat ).'</small>' ;
        	}
			$data['profilelink'] = $data['link'];
        	$data['link'] = '<a rel="nofollow" href="' . $data['link'] . '">' . JText::_('MYPROFILE_LINK') . '</a>';
		}

		return $data;
	}

	function getTheme()
	{
		global $ccbConfig;
		$defaultTheme = $ccbConfig->theme;
		if( $defaultTheme == 'mix') {
			$clr = array('blue','red','gray','green');
		    $defaultTheme = $clr[rand(0,3)];
		}

		return $defaultTheme;
	}


	function sendMail($email, $subject, $body)
	{
		$config		= &JFactory::getConfig();
		$from		= $config->getValue('mailfrom');
		$fromname	= $config->getValue('fromname');

		$body = JText::sprintf( $body );
		// $mode Added by N6REJ to force the email into html mode to
		// obscure the line encoding and allow for imagery
		$mode = '1';
		if (!JUtility::sendMail($from, $fromname, $email, $subject, $body, $mode))
		{
			$this->setError( JText::_('ERROR_SENDING_EMAIL'));
			return false;
		}

		return true;
	}

	function setBreadCrumb( $name, $link='')
	{
		global $mainframe;
		global $ccbConfig;
		$pathway = &$mainframe->getPathway();
        $par = $pathway->getPathwayNames();

		if ( $name == 'ccbhome') {
		 	$name = ccboardHelperConfig::getMenuName();
			$link = JRoute::_('index.php?option=com_ccboard&view=forumlist&Itemid=' . $ccbConfig->itemid);
		}
        $name = strlen($name) > 40 ? substr($name,0,40) . '...': $name;
		if( in_array($name, $par) ) return;
		$pathway->addItem( $name,  $link);
	}

	function getMenuName()
	{
	    $db = &JFactory::getDBO();
        $query = 'SELECT name
                  FROM #__menu WHERE link = "index.php?option=com_ccboard&view=forumlist"';
        $db->setQuery($query);
        $obj = $db->loadObject();
        return $obj->name;
	}

	function getUserName( $uid )
	{
		global $ccbConfig;
		$data = ccboardHelperConfig::getUserInfo( $uid );
		$uname = $ccbConfig->showrealname ?  $data['name'] : $data['username'];
		return $uname;
	}

	function updateKarma( $uid, $post_user, $karma )
    {
		global $ccbConfig;
		$db = &JFactory::getDBO();
		$message = JText::_('KARMA_NOT_UPDATED');
	    ccboardHelperConfig::checkUserExists();

		$query = 'SELECT karma_time from #__ccb_users WHERE user_id = ' .  $uid;
		$db->setQuery($query);
        $tm = ($tm=$db->loadResult())? $tm : 0;
        $curtime = strtotime("now");
        if( ($curtime - $tm) > 3600 && $uid <> $post_user ) {
    		$query = 'UPDATE #__ccb_users SET karma = karma + ' . $karma  .
    				' WHERE user_id = ' . $post_user ;
			$db->setQuery($query);
			$db->query();

    		if( $ccbConfig->userprofile == 'combuilder') {
	    		$query = 'UPDATE #__comprofiler SET ccbkarma = ccbkarma + '. $karma .
	                 	' WHERE user_id = ' . $post_user;
	        }
	        elseif( $ccbConfig->userprofile == 'jomsocial') {
	    		$query = 'UPDATE #__community_users SET points = points + ' . $karma .
	    				' WHERE userid = ' . $post_user;
	        }
			$db->setQuery($query);
			$db->query();
			$query = 'UPDATE #__ccb_users set karma_time = UNIX_TIMESTAMP() WHERE user_id = ' .  $uid;
			$db->setQuery($query);
        	$db->query();
			$message = JText::_('KARMA_UPDATED');
        }
    	return $message;
    }


    function checkUserExists( $uid=0)
    {
    	$user = &JFactory::getUser();
    	$uid = $uid > 0 ? $uid : $user->get('id');
    	if( $uid > 0 ) {
			$db = &JFactory::getDBO();
			$query = 'SELECT id from #__ccb_users WHERE user_id = ' .  $uid;
			$db->setQuery($query);
	        $id = ($id=$db->loadResult())? $id : 0;
	    	if( $id < 1 ) {
				$query = 'INSERT INTO #__ccb_users (user_id, avatar) VALUES ('. $uid . ', "avatar1.png") ';
				$db->setQuery($query);
				$db->query();
	    	}

    	}
    }

	function updateRank( $val, $uid=0 )
	{
		global $ccbConfig;
		$db = &JFactory::getDBO();
		$user = &JFactory::getUser();

		if( $user->guest ) return;
		if( $uid < 1 ) return;

		ccboardHelperConfig::checkUserExists( $uid );

		$query = 'UPDATE #__ccb_users SET post_count = post_count +  ' . $val . ' ' .
                  	'WHERE user_id = ' . $uid;
		$db->setQuery($query);
		$db->query();

		if( $ccbConfig->showrank < 1 ) return;

		//Update Rank based on Post Count
		$query = 'UPDATE #__ccb_users AS u ' .
					'SET u.rank = (SELECT r.id FROM #__ccb_ranks AS r WHERE r.rank_min <= u.post_count and r.rank_special = 0 order by r.rank_min DESC limit 1) ' .
					'WHERE (u.rank NOT IN (SELECT r2.id FROM #__ccb_ranks AS r2 WHERE r2.rank_special=1)) ' .
					'AND u.user_id = ' . $uid;
		$db->setQuery($query);
		$db->query();

		if( $ccbConfig->userprofile == 'combuilder') {
			$query = 'UPDATE #__comprofiler AS c SET ' .
	        		'c.ccbrankname  = (SELECT r.rank_title 	FROM #__ccb_users as u INNER JOIN #__ccb_ranks AS r ON u.rank = r.id WHERE u.user_id = c.user_id ), ' .
	        		'c.ccbpostcount = (SELECT u.post_count 	FROM #__ccb_users AS u  where  u.user_id = c.user_id) ' .
					'WHERE c.user_id = ' . $uid;
	    	$db->setQuery($query);
			$db->query();
		} elseif( $ccbConfig->userprofile == 'jomsocial') {
	    	$query = 'SELECT id FROM #__community_fields WHERE fieldcode = "FIELD_CCB_POST_COUNT"';
	    	$db->setQuery($query);
			$obj = $db->loadObject();
			$postfield = isset($obj->id) ? $obj->id :0;
	    	$query = 'SELECT id FROM #__community_fields WHERE fieldcode = "FIELD_CCB_RANK"';
	    	$db->setQuery($query);
			$obj = $db->loadObject();
			$rankfield = isset($obj->id) ? $obj->id :0;

			$query = 'DELETE FROM #__community_fields_values WHERE field_id IN ' .
    			'(' . $postfield . ', ' . $rankfield . ')  ' .
    			'AND user_id = ' . $uid;
        	$db->setQuery($query);
			$db->query();
			if( $postfield > 0 ) {
		    	$query = 'INSERT INTO #__community_fields_values (user_id, field_id, value) ' .
		    				'(SELECT user_id, '. $postfield .', post_count FROM #__ccb_users WHERE user_id = ' . $uid . ') ' ;
		        $db->setQuery($query);
				$db->query();
			}
			if( $rankfield  > 0 ) {
		    	$query = 'INSERT INTO #__community_fields_values (user_id, field_id, value) ' .
						'(SELECT u.user_id,' . $rankfield . ', r.rank_title FROM #__ccb_users AS u INNER JOIN #__ccb_ranks AS r ON u.rank = r.id WHERE u.user_id = '.$uid . ') ' ;
				$db->setQuery($query);
				$db->query();
			}
        }
		return;
	}

	function getEmailSubscribers($topic_email, $fid)
	{
		global $ccbConfig;
		$db = &JFactory::getDBO();
		$uemail = trim($topic_email);
		$arsubs = explode( "-", $uemail );

		// Reset Subscription if Email Subscription is OFF
		if( $ccbConfig->emailsub < 1 ) {
			$arsubs = array();
            return $arsubs;
		}

		// Send Email Alerts to Moderators
		if( $ccbConfig->emailalert == 1 ) {
			$query = 'SELECT m.user_id FROM #__ccb_moderators AS m ' .
					'WHERE m.forum_id = ' . $fid ;
			$db->setQuery( $query );
			$items = $db->loadObjectList();
			foreach($items as $item ) {
				if( !in_array($item->user_id, $arsubs) ) {
					$arsubs[] = $item->user_id;
				}
			}
		}

		// Send Email Alerts to Administrators 
		if( $ccbConfig->emailalert == 3 || $ccbConfig->emailalert == 2) {
			$query = 'SELECT id FROM #__users ' .
					'WHERE gid >= ' . $this->getAdministrator() ;
			$db->setQuery( $query );
			$items = $db->loadObjectList();
			foreach($items as $item ) {
				if( !in_array($item->id, $arsubs) ) {
					$arsubs[] = $item->id;
				}
			}
		}
		return $arsubs;
	}

	//_emailSubscription modified by Troy (N6REJ) Hall 10Jan2009
	// $post_text added to get the body of the topic post.
	// Modified by Thomas 15-Feb-2009, Send mail to multiple subscribers.
	// Modified by Thomas 30-Mar-2009, to Include Moderators/Administrators for Notification
	function emailSubscription( $topic_email, $subject, $fid, $tid, $post_text)
	{
		global $ccbConfig;
		$db	=& JFactory::getDBO();

		$topic_email = ccboardHelperConfig::getEmailSubscribers($topic_email,$fid);

		for($i=0; $i < sizeof($topic_email); $i++) {
			$usrid = (int) $topic_email[$i];
			if( $usrid > 0 ) {
				$query = 'SELECT username, email FROM #__users WHERE id = ' . $usrid;
				$db->setQuery( $query );
				$user = $db->loadObject();

				$subj = JText::_('EMAIL_NOTIFY_ALERT') . ' ' . $subject ;
				// We need to get the URL of the domain name incase the href's do not have them embedded.
				$_Host = JRoute::_(JURI::root());
				$_URL = JRoute::_( $_Host . "index.php?option=com_ccboard&view=postlist&forum=" . $fid . "&topic=" . $tid . "&Itemid=" . $ccbConfig->itemid);

				// Because we are mailing the body out we must change to HTML messages.
				//Therefore we need <br /> instead of \n
				// We also need to get out lables from the language file so that it can be translated easily.
				$body =  JText::_('EMAIL_GREETING') . "  " . $user->username . " <br /> ";
				$body .= JText::_('EMAIL_INTRO') . " <br />";
				$body .= JText::_('EMAIL_TOPIC_NAME') . " " . $subject . " <br />";
				$body .= JText::_('EMAIL_URL') . ' <a href="'.$_URL.'">'.$_URL. "</a><br />";
				$body .= JText::_('EMAIL_SENDER') . " <br />";

				// Lets make sure there is a domain name infront of any imagery so it will display in the email
				// A Million thanks to irc://irc.freenode.net/##php for the hours we spent pounding out this regex to get it to work 100%
				$post_text = preg_replace("/(<img(.*?)src=\")(?!http)/i ", "$1" . $_Host, $post_text);

				// Lets segregate the header from the topic
				$body .= " <br /><br /><--------------" . JText::_('EMAIL_DIVIDE') . " ----------------><br /><br />";

				// Now we can append the body to the rest of the email.
				$body .= $post_text . "<br />";

				// Design thought.  We should set reply address to null and add something like this is an automated message.
				ccboardHelperConfig::sendMail($user->email, $subj, $body );
			}
		}
		return true;
	}

    function getPageNumber($topic_id, $post_id)
    {
		global $ccbConfig;
        $pagelink = "";
        $mainframe = &JFactory::getApplication();
	    $db = &JFactory::getDBO();
	    $query = 'SELECT p.id FROM #__ccb_posts p ' .
                    'WHERE p.topic_id = ' . $topic_id .  ' ' .
                    'ORDER BY p.id '. $ccbConfig->postlistorder;

	    $db->setQuery($query);
        $items = ($items = $db->loadObjectList())?$items:array();
        $pgnum = 0;
        $pglimit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $pcnt = 0;
        foreach ($items as $item) {
            if( $item->id == $post_id ) break;
            $pcnt++;
            if( $pcnt == $pglimit) {
                $pcnt = 0;
                $pgnum += $pglimit;
            }
        }
        if( $pgnum > 0 ) {
            $pagelink = "&limitstart=" . $pgnum . "#ccbp".$post_id;
        }
        else {
            $pagelink = "#ccbp".$post_id;
        }
        return $pagelink;
    }


}


?>
