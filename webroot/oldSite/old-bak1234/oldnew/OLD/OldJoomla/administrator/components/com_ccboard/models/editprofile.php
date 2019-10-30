<?php
/**
 * @version		$Id: editprofile.php 124 2009-05-01 09:05:56Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

require_once JPATH_COMPONENT_ADMINISTRATOR .DS.'ccboard-config.php';

class ccboardModelEditProfile extends JModel
{
    var $_data;
    var $_user_id;
    var $_rank;
    var $_rank_title;
	var $_rank_special;
	var $_post_count;
	var $_rank_image;

    function ccboardModelEditProfile()
	{
		parent::__construct();
	}

    function setUserId($id)
    {
        $this->_user_id = (int) $id;
        $this->_data = null;
    }

    function getData()
    {
		$config = new ccboardConfig();
    	$this->_data = array();
    	$query = 'SELECT a.id AS userid, a.name, a.username, a.usertype,
    			 b.id, b.moderator, b.rank, b.signature, b.location, b.avatar
                 FROM #__users AS a
                 LEFT JOIN #__ccb_users AS b ON b.user_id = a.id
                 WHERE a.id = '.$this->_user_id;
        $this->_db->setQuery($query);
        $data = array();
        $data = ($data = $this->_db->loadObject()) ? $data : array();

        $this->_data['id'] 			= isset($data->id)?$data->id:0;
        $this->_data['user_id'] 	= $data->userid;
        $this->_data['name'] 		= $data->name;
        $this->_data['username'] 	= $data->username;
        $this->_data['usertype']	= $data->usertype;
        $this->_data['signature']	= isset($data->signature)?$data->signature:'';
        $this->_data['rank'] 		= isset($data->rank)?$data->rank: 0;
        $this->_data['moderator'] 	= isset($data->moderator)?$data->moderator: 0;
        $this->_data['location'] 	= isset($data->location)?$data->location: '';

        $this->_data['rank']		= $this->getProfileRank($this->_data['rank']);

		if( isset($data->avatar) ) {
			$this->_data['avatar'] = JURI::root() . 'components/com_ccboard/assets/avatar/'. $data->avatar ;
		} else {
			$this->_data['avatar'] = JURI::root() . 'components/com_ccboard/assets/avatar/avatar1.png';
		}

        if( $config->userprofile == 'jomsocial') {
			$this->getJomsocial();
        }
        elseif( $config->userprofile == 'combuilder') {
			$this->getComBuilder();
        }

        return $this->_data;
    }

    function getJomsocial()
    {
        $query = 'SELECT a.*
                 FROM #__community_users AS a
                 WHERE a.userid = '.(int)$this->_user_id;

        $db = &JFactory::getDBO();
        $db->setQuery($query);
        $data = ($data = $db->loadObject()) ? $data : array();
		$this->_data['avatar'] =  JURI::root() . 'components/com_community/assets/default.jpg';

        if( isset($data->avatar) ) {
			$this->_data['avatar'] = JURI::root() . $data->avatar ;
        }

        $query = 'SELECT a.id, b.value AS vlv ' .
                 	'FROM #__community_fields AS a ' .
        			'INNER JOIN #__community_fields_values AS b ON b.field_id = a.id ' .
                 	'WHERE a.fieldcode = "FIELD_CCB_LOCATION" AND b.user_id = '.(int)$this->_user_id;
        $db->setQuery($query);
        $data = $db->loadObject();
        $this->_data['location'] = isset($data->vlv) ? $data->vlv : '';

        $query = 'SELECT a.id, b.value AS vlv ' .
                 	'FROM #__community_fields AS a ' .
        			'INNER JOIN #__community_fields_values AS b ON b.field_id = a.id ' .
                 	'WHERE a.fieldcode = "FIELD_CCB_SIGNATURE" AND b.user_id = '.(int)$this->_user_id;
        $db->setQuery($query);
        $data = $db->loadObject();
        $this->_data['signature'] = isset($data->vlv) ? $data->vlv : '';

        return true;
    }

    function getComBuilder()
    {
        $query = 'SELECT a.avatar, a.avatarapproved, a.ccblocation, a.ccbsignature '.
                 	'FROM #__comprofiler AS a ' .
                 	'WHERE a.user_id = '.(int)$this->_user_id ;

        $db = &JFactory::getDBO();
        $db->setQuery($query);
        $data = ($data = $db->loadObject()) ? $data : array();
		$this->_data['avatar'] = JURI::root() . 'components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png';
        if( $data->avatarapproved == 1 && isset($data->avatar)) {
			$this->_data['avatar'] = JURI::root() . 'images/comprofiler/' . $data->avatar ;
        }
        $this->_data['location'] = isset($data->ccblocation) ? $data->ccblocation: '';
        $this->_data['signature'] = isset($data->ccbsignature) ? $data->ccbsignature: '';

        return true;
    }

    function store($udata)
    {
    	$row = $this->getTable('ccbuser', 'ccboardTable');
		$config = new ccboardConfig();

        $filter 			= new JFilterInput(array(), array(), 1, 1);

        $data = array();
        $id					= $filter->clean($udata['id'],'integer');
		$data['user_id']	= $filter->clean($udata['user_id'],'integer');
		$data['location'] 	= $filter->clean($udata['location'],'string');
		$data['signature'] 	= $filter->clean($udata['signature'],'string');
        $data['moderator'] 	= $filter->clean($udata['moderator'],'integer');
		$data['signature'] = substr($data['signature'],0, $config->sigmax); /* limit Sig Chars */
		$data['signature'] = htmlspecialchars( $data['signature'],ENT_QUOTES,'UTF-8');
		$rank 				= $filter->clean($udata['rank'],'integer');

		// Find rank based on posts if there is a change from moderator to normal user
		$this->setRank($rank, $data['user_id']);
		$data['rank'] = $this->_rank;


		if( $id > 0 ) {
			if( !$row->load($id) )	{
				$this->setError($row->getError());
            	return false;
			}
		} else {
			$row->id = null;
		}

		if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }

        if (!$row->store(true)) {
            $this->setError($row->getError());
            return false;
        }

        $query = 'DELETE FROM #__ccb_moderators WHERE user_id = ' . $data['user_id'];
        $db = &JFactory::getDBO();
    	$db->setQuery( $query );
		if (!$db->query()) {
			$this->setError($db->getError());
			return false;
		}

		if( $data['moderator'] ) {
			$selections = JRequest::getVar( 'selections', array(), 'post', 'array' );
			foreach ($selections as $forumid)
			{
				$query = 'INSERT INTO #__ccb_moderators'
					. ' SET user_id = ' . $data['user_id'] .', forum_id = '.(int) $forumid ;
				$db->setQuery( $query );
				if (!$db->query()) {
					$this->setError($db->getError());
					return false;
				}
			}
		}

		// Update latest post count and rank title to CB/Jom
		$data['rank_title'] = $this->_rank_title;
		$data['post_count'] = $this->_post_count;

		if( $config->userprofile == 'combuilder') {
			if( !$this->putCombuilder($data)) {
				return false;
			}

		} elseif ($config->userprofile == 'jomsocial') {
			if( !$this->putJomsocial($data)) {
				return false;
			}
		}
		return true;
    }



    function putCombuilder( $data )
    {
        $db = &JFactory::getDBO();
        $query = 'UPDATE #__comprofiler SET ' .
        		'ccblocation = "' . $data['location'] . '", ' .
        		'ccbsignature = "' . $data['signature'] . '", ' .
        		'ccbrankname = "' . $this->_rank_title . '", ' .
        		'ccbpostcount = ' . $this->_post_count. ' ' .
        		'WHERE user_id = '. $data['user_id'];

        $db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getError());
			return false;
		}

		return true;
    }

	function putJomsocial( $data )
    {
    	$db = &JFactory::getDBO();

    	$query = 'SELECT id FROM #__community_fields WHERE fieldcode = "FIELD_CCB_LOCATION"';
    	$db->setQuery($query);
		$obj = $db->loadObject();
		$locationid = isset($obj->id) ? $obj->id :0;

    	$query = 'SELECT id FROM #__community_fields WHERE fieldcode = "FIELD_CCB_SIGNATURE"';
    	$db->setQuery($query);
		$obj = $db->loadObject();
		$signatureid = isset($obj->id) ? $obj->id :0;

    	$query = 'SELECT id FROM #__community_fields WHERE fieldcode = "FIELD_CCB_POST_COUNT"';
    	$db->setQuery($query);
		$obj = $db->loadObject();
		$postcountid = isset($obj->id) ? $obj->id :0;

    	$query = 'SELECT id FROM #__community_fields WHERE fieldcode = "FIELD_CCB_RANK"';
    	$db->setQuery($query);
		$obj = $db->loadObject();
		$rankid = isset($obj->id) ? $obj->id :0;

    	$query = 'DELETE FROM #__community_fields_values WHERE field_id in ' .
    			'(' . $locationid . ', ' . $signatureid . ', ' . $postcountid . ', ' . $rankid . ')  ' .
    			'AND user_id = ' . $data['user_id'];
        $db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getError());
			return false;
		}

		if( $locationid > 0 ) {
	    	$query = 'INSERT INTO #__community_fields_values ' .
			    		'SET user_id = ' . $data['user_id'] . ', ' .
		    			'field_id = ' . $locationid . ', ' .
		    			'value = "' . $data['location'] . '" ';
	        $db->setQuery($query);
			if (!$db->query()) {
				$this->setError($db->getError());
				return false;
			}
		}

		if( $signatureid > 0 ) {
	    	$query = 'INSERT INTO #__community_fields_values ' .
			    		'SET user_id = ' . $data['user_id'] . ', ' .
		    			'field_id = ' . $signatureid . ', ' .
		    			'value = "' . $data['signature'] . '" ';
	        $db->setQuery($query);
			if (!$db->query()) {
				$this->setError($db->getError());
				return false;
			}
		}

		if( $postcountid > 0 ) {
	    	$query = 'INSERT INTO #__community_fields_values ' .
			    		'SET user_id = ' . $data['user_id'] . ', ' .
		    			'field_id = ' . $postcountid . ', ' .
		    			'value = "' . $data['post_count'] . '" ';
	        $db->setQuery($query);
			if (!$db->query()) {
				$this->setError($db->getError());
				return false;
			}
		}

		if( $rankid > 0 ) {
	    	$query = 'INSERT INTO #__community_fields_values ' .
			    		'SET user_id = ' . $data['user_id'] . ', ' .
		    			'field_id = ' . $rankid . ', ' .
		    			'value = "' . $data['rank_title'] . '" ';
	        $db->setQuery($query);
			if (!$db->query()) {
				$this->setError($db->getError());
				return false;
			}
		}
		return true;
    }

    function getProfileRank( $rnk )
    {
    	$row = $this->getTable('ccbranks', 'ccboardTable');
    	$db = &JFactory::getDBO();

		$row->load( $rnk );
		if( $row->rank_special ) {
			$retval = $rnk;
		} else {
    		$retval = 0;
		}
		return $retval;
    }

    function setRank( $rnk,  $uid )
    {
    	$row = $this->getTable('ccbranks', 'ccboardTable');
    	$db = &JFactory::getDBO();

    	$query = 'SELECT COUNT(id) FROM #__ccb_posts ' .
    				'WHERE post_user = ' . $uid . ' AND hold = 0';
    	$db->setQuery($query);
    	$tot = $db->loadResult();
    	$tot = isset( $tot) ? $tot : 0;
		$this->_post_count = $tot;

		if( $rnk > 0 ) {   // if special
			$row->load( $rnk );
			$this->_rank = $rnk;
			$this->_rank_title = $row->rank_title;
			$this->_rank_special = $row->rank_special;
			$this->_rank_image = $row->rank_image;
		} else {
			$query = 'SELECT id, rank_title, rank_image ' .
				'FROM #__ccb_ranks ' .
				'WHERE rank_min <= ' . $tot . ' AND rank_special = 0 ' .
				'ORDER BY rank_min DESC LIMIT 1';
			$db->setQuery($query);
	        $rankObj  = $db->loadObject();
	        $this->_rank = isset($rankObj->id) ? $rankObj->id : 0;
			$this->_rank_image = isset($rankObj->rank_image) ? $rankObj->rank_image : '';
			$this->_rank_title = isset($rankObj->rank_title) ? $rankObj->rank_title: '';
			$this->_rank_special = 0;
		}
    }

}
?>
