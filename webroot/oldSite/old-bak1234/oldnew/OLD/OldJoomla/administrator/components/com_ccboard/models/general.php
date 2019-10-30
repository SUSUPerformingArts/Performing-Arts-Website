<?php

/**
 * @version		$Id: general.php 171 2009-09-21 14:36:52Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );
jimport('joomla.filesystem.file');

class ccboardModelGeneral extends JModel
{
   	function ccboardModelGeneral()
	{
		parent::__construct();
	}


    function getData()
    {
		require_once(JPATH_COMPONENT.DS.'ccboard-config.php');
		$configObj = new ccboardConfig();
	    return $configObj;
    }


 	function store($data)
    {
    	JRequest::checkToken() or jexit( 'Invalid Token' );

		$file = JPATH_COMPONENT.DS.'ccboard-config.php';
    	$registry = new JRegistry('config');

    	$properties = array();
		$properties['boardname']  		= $this->makeClean($data['boardname'], 'string' );
		$properties['boardlocked'] 		= $this->makeClean($data['boardlocked'], 'integer');
		$properties['lockedmsg']  		= $this->makeClean($data['lockedmsg'], 'string' );
		$properties['theme'] 			= $this->makeClean($data['theme'],'string');
		$properties['ccbeditor']		= $this->makeClean($data['ccbeditor'],'string');
		$properties['editorwidth'] 		= $this->makeClean($data['editorwidth'], 'integer');
		$properties['editorheight'] 	= $this->makeClean($data['editorheight'], 'integer');
		$properties['subjwidth'] 		= $this->makeClean($data['subjwidth'], 'integer');
		$properties['sigmax']	 		= $this->makeClean($data['sigmax'],'integer');
		$properties['timeoffset']	 	= $this->makeClean($data['timeoffset'],'integer');
		$properties['dateformat']	 	= $this->makeClean($data['dateformat'],'string');
		$properties['postlistorder']	= $this->makeClean($data['postlistorder'],'string');
        $properties['showquickreply']	= $this->makeClean($data['showquickreply'],'integer');

		$properties['userprofile']	 	= $this->makeClean($data['userprofile'],'string');
		$properties['showrealname']	 	= $this->makeClean($data['showrealname'],'integer');
		$properties['allowedit']	 	= $this->makeClean($data['allowedit'],'integer');
		$properties['editgracetime']	= $this->makeClean($data['editgracetime'],'integer');
		$properties['showeditmarkup']	= $this->makeClean($data['showeditmarkup'],'integer');
		$properties['emailsub']			= $this->makeClean($data['emailsub'],'integer');
        $properties['autosub']			= $this->makeClean($data['autosub'],'integer');
		$properties['showrank']			= $this->makeClean($data['showrank'],'integer');
		$properties['showfavourites']	= $this->makeClean($data['showfavourites'],'integer');
		$properties['showkarma']		= $this->makeClean($data['showkarma'],'integer');
        $properties['showtopicavatar']  = $this->makeClean($data['showtopicavatar'],'integer');
        $properties['showboardsummary'] = $this->makeClean($data['showboardsummary'],'integer');
        $properties['showreglink']      = $this->makeClean($data['showreglink'],'integer');
        $properties['showloginlink']    = $this->makeClean($data['showloginlink'],'integer');
		$properties['smallavatarheight']= $this->makeClean($data['smallavatarheight'],'integer');
		$properties['smallavatarwidth']	= $this->makeClean($data['smallavatarwidth'],'integer');
		$properties['avatarheight']		= $this->makeClean($data['avatarheight'],'integer');
		$properties['avatarwidth']		= $this->makeClean($data['avatarwidth'],'integer');

		$properties['showcaptcha']	 	= $this->makeClean($data['showcaptcha'],'integer');
		$properties['emailalert']	 	= $this->makeClean($data['emailalert'],'integer');
		$properties['logip']	 		= $this->makeClean($data['logip'],'integer');
		$properties['badwords'] 		= $this->makeClean($data['badwords'],'string');

		$properties['avatarupload']		= $this->makeClean($data['avatarupload'], 'integer');
		$properties['avataruploadsize']	= $this->makeClean($data['avataruploadsize'], 'integer');
		$properties['attachments']		= $this->makeClean($data['attachments'],'integer');
		$properties['fileuploadsize']	= $this->makeClean($data['fileuploadsize'], 'integer');
		$properties['extensions']		= $this->makeClean($data['extensions'], 'string');
		$properties['itemid']			= $this->getItemId('index.php?option=com_ccboard&view=forumlist');

		$properties = $this->validate( $properties);

        if( $properties['userprofile'] == 'jomsocial') {
			if( !$this->modifyJomFields()) {
				$this->setError(JText::_('CCB_ERROR_JOMSOCIAL_TABLES_MISSING'));
				return false;
			}
            $properties['jomsocialId'] = $this->getItemId('index.php?option=com_community&view=frontpage');
		} elseif ($properties['userprofile'] == 'combuilder') {
			if( !$this->modifyCBFields()) {
				$this->setError(JText::_('CCB_ERROR_COMMUNITY_BUILDER_TABLES_MISSING'));
				return false;
			}
            $properties['comprofilerId'] = $this->getItemId('index.php?option=com_comprofiler');
		}

		$registry->loadArray( $properties );
		if( !JFile::write($file,$registry->toString('PHP', 'config', array('class' => 'ccboardConfig'))) ) {
			return false;
		}

		return true;

    }

    function validate($properties)
    {
    	$properties['editorwidth']		= $properties['editorwidth'] < 10 ? 10 : $properties['editorwidth'];
		$properties['editorheight']		= $properties['editorheight'] < 10? 10 : $properties['editorheight'];
		$properties['subjwidth']		= $properties['subjwidth'] < 50 ? 50: $properties['subjwidth'];
		$properties['sigmax']			= $properties['sigmax'] < 50 ? 50 :$properties['sigmax'];
		$properties['smallavatarheight']= $properties['smallavatarheight'] < 10?10:$properties['smallavatarheight'];
		$properties['smallavatarwidth']	= $properties['smallavatarwidth'] < 10?10:$properties['smallavatarwidth'];
		$properties['avatarheight']		= $properties['avatarheight'] < 10?10:$properties['avatarheight'];
		$properties['avatarwidth']		= $properties['avatarwidth'] < 10?10:$properties['avatarwidth'];
		$properties['avataruploadsize']	= $properties['avataruploadsize'] < 1 ? 1 : $properties['avataruploadsize']	;
		$properties['fileuploadsize']	= $properties['fileuploadsize'] < 1 ? 1 : $properties['fileuploadsize'];
		$properties['extensions']		= strlen($properties['extensions']) < 1 ? 'zip,jpg,gif,png' : $properties['extensions'];

		return $properties;
    }

    function makeClean( $elem, $datatype )
    {
    	$filter = new JFilterInput(array(),array(),1,1);
		$elem = $filter->clean($elem, $datatype);
		$elem = trim($elem);
		if( $datatype == 'string') {
			$elem = JFilterOutput::ampReplace( $elem );
			$elem = str_replace( '"', '&quot;', $elem );
			$elem = str_replace( "'", '&#039;', $elem );
    	} elseif( $datatype == 'integer') {
    		$elem = (int) $elem;
    	}

    	return $elem;
    }

    function modifyCBFields()
    {
   		$query = 'SHOW COLUMNS FROM #__comprofiler';
	    $results = ($results = $this->_getList($query,0 )) ? $results : array();
		if (count($results) < 1) {
			// CB Tables Doesnt exists
			return false;
		}
		$fields = array();
		foreach ($results as $item) {
			$fields[] = $item->Field;
		}

		if( !in_array('ccbpostcount', $fields)) {
			$query = 'ALTER TABLE  #__comprofiler ADD COLUMN ccbpostcount int(11) NOT NULL  DEFAULT 0;';
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		if( !in_array('ccbrankname', $fields)) {
			$query = 'ALTER TABLE  #__comprofiler ADD COLUMN ccbrankname VARCHAR(255) NOT NULL  DEFAULT "";';
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		if( !in_array('ccbkarma', $fields)) {
			$query = 'ALTER TABLE  #__comprofiler ADD COLUMN ccbkarma int(11) NOT NULL DEFAULT 0;';
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		if( !in_array('ccblocation', $fields)) {
			$query = 'ALTER TABLE  #__comprofiler ADD COLUMN ccblocation VARCHAR(255) DEFAULT "";';
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		if( !in_array('ccbsignature', $fields)) {
			$query = 'ALTER TABLE  #__comprofiler ADD COLUMN ccbsignature mediumtext DEFAULT "";';
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		$query = "SELECT count(*) FROM #__comprofiler_fields where name like 'ccb%'";
	    $this->_db->setQuery($query);
        $rec = $this->_db->loadResult();

        $query = 'DELETE FROM #__comprofiler_fields where name like "ccb%"';
        $this->_db->setQuery($query);
        $this->_db->query();
        $query = 'INSERT INTO #__comprofiler_fields ' .
            '(`name`,`tablecolumns`,`table`,`title`,`description`,`type`,`maxlength`,`size`,`required`,`tabid`,`ordering`,`cols`,`rows`,`value`,`default`,`published`,`registration`,`profile`,`displaytitle`,`readonly`,`searchable`,`calculated`,`sys`,`pluginid`,`params`) ' .
            ' VALUES '.
            '("ccbpostcount","ccbpostcount","#__comprofiler","Total Posts","","integer",20,20,0,21,-13,0,0,"","0",1,0,1,1,1,0,0,0,1,"" )';
        $this->_db->setQuery($query);
        $this->_db->query();
        $query = 'INSERT INTO #__comprofiler_fields ' .
            '(`name`,`tablecolumns`,`table`,`title`,`description`,`type`,`maxlength`,`size`,`required`,`tabid`,`ordering`,`cols`,`rows`,`value`,`default`,`published`,`registration`,`profile`,`displaytitle`,`readonly`,`searchable`,`calculated`,`sys`,`pluginid`,`params`) ' .
            ' VALUES '.
            '("ccbrankname","ccbrankname","#__comprofiler","Rank","","textarea",255,255,0,21,-12,0,0,"","",1,0,1,1,1,0,0,0,1,"")';
        $this->_db->setQuery($query);
        $this->_db->query();
        $query = 'INSERT INTO #__comprofiler_fields ' .
            '(`name`,`tablecolumns`,`table`,`title`,`description`,`type`,`maxlength`,`size`,`required`,`tabid`,`ordering`,`cols`,`rows`,`value`,`default`,`published`,`registration`,`profile`,`displaytitle`,`readonly`,`searchable`,`calculated`,`sys`,`pluginid`,`params`) ' .
            ' VALUES '.
            '("ccbkarma","ccbkarma","#__comprofiler","Karma","","integer",20,20,0,21,-11,0,0,"","0",1,0,1,1,1,0,0,0,1,"")';
        $this->_db->setQuery($query);
        $this->_db->query();
        $query = 'INSERT INTO #__comprofiler_fields ' .
            '(`name`,`tablecolumns`,`table`,`title`,`description`,`type`,`maxlength`,`size`,`required`,`tabid`,`ordering`,`cols`,`rows`,`value`,`default`,`published`,`registration`,`profile`,`displaytitle`,`readonly`,`searchable`,`calculated`,`sys`,`pluginid`,`params`) ' .
            ' VALUES '.
            '("ccblocation","ccblocation","#__comprofiler","Location","","textarea",255,255,0,21,-10,0,0,"","",1,0,1,1,0,0,0,0,1,"")';
        $this->_db->setQuery($query);
        $this->_db->query();
        $query = 'INSERT INTO #__comprofiler_fields ' .
            '(`name`,`tablecolumns`,`table`,`title`,`description`,`type`,`maxlength`,`size`,`required`,`tabid`,`ordering`,`cols`,`rows`,`value`,`default`,`published`,`registration`,`profile`,`displaytitle`,`readonly`,`searchable`,`calculated`,`sys`,`pluginid`,`params`) ' .
            ' VALUES '.
            '("ccbsignature","ccbsignature","#__comprofiler","Signature","","textarea",300,300,0,21,-9,0,0,"","",1,0,1,1,0,0,0,0,1,"")';
        $this->_db->setQuery($query);
        $this->_db->query();

		return true;


    }

    function modifyJomFields()
    {
    	$recInserted = false;
		$query = "SELECT id FROM #__community_fields where fieldcode = 'FIELD_CCB_GROUP_CCBOARD'";
	    $this->_db->setQuery($query);
        $rec = $this->_db->loadResult();
		if( $rec < 1 ) {
			$query = 'INSERT INTO #__community_fields ' .
			'(`type`,`ordering`,`published`,`min`,`max`,`name`,`tips`,`visible`,`required`,`searchable`,`options`,`fieldcode`) ' .
			'VALUES ' .
			'("group",0,1,10,100,"ccBoard","ccBoard",1,1,1,"","FIELD_CCB_GROUP_CCBOARD")';
			$this->_db->setQuery($query);
			$this->_db->query();
			$recInserted = true;
		}

		$query = "SELECT id FROM #__community_fields where fieldcode = 'FIELD_CCB_POST_COUNT'";
	    $this->_db->setQuery($query);
        $rec = $this->_db->loadResult();
		if( $rec < 1 ) {
			$query = 'INSERT INTO #__community_fields ' .
			'(`type`,`ordering`,`published`,`min`,`max`,`name`,`tips`,`visible`,`required`,`searchable`,`options`,`fieldcode`) ' .
			'VALUES ' .
			'("text",0,1,1,100,"Total Posts","Forum Total Posts",1,1,1,"","FIELD_CCB_POST_COUNT")';
			$this->_db->setQuery($query);
			$this->_db->query();
			$recInserted = true;
		}

		$query = "SELECT id FROM #__community_fields where fieldcode = 'FIELD_CCB_RANK'";
	    $this->_db->setQuery($query);
        $rec = $this->_db->loadResult();
		if( $rec < 1 ) {
			$query = 'INSERT INTO #__community_fields ' .
			'(`type`,`ordering`,`published`,`min`,`max`,`name`,`tips`,`visible`,`required`,`searchable`,`options`,`fieldcode`) ' .
			'VALUES ' .
			'("text",0,1,1,100,"Rank","Forum Rank",1,1,1,"","FIELD_CCB_RANK")';
			$this->_db->setQuery($query);
			$this->_db->query();
			$recInserted = true;
		}
		$query = "SELECT id FROM #__community_fields where fieldcode = 'FIELD_CCB_LOCATION'";
	    $this->_db->setQuery($query);
        $rec = $this->_db->loadResult();
		if( $rec < 1 ) {
			$query = 'INSERT INTO #__community_fields ' .
			'(`type`,`ordering`,`published`,`min`,`max`,`name`,`tips`,`visible`,`required`,`searchable`,`options`,`fieldcode`) ' .
			'VALUES ' .
			'("text",0,1,1,255,"Location","Location",1,1,1,"","FIELD_CCB_LOCATION")';
			$this->_db->setQuery($query);
			$this->_db->query();
			$recInserted = true;
		}
		$query = "SELECT id FROM #__community_fields where fieldcode = 'FIELD_CCB_SIGNATURE'";
	    $this->_db->setQuery($query);
        $rec = $this->_db->loadResult();
		if( $rec < 1 ) {
			$query = 'INSERT INTO #__community_fields ' .
			'(`type`,`ordering`,`published`,`min`,`max`,`name`,`tips`,`visible`,`required`,`searchable`,`options`,`fieldcode`) ' .
			'VALUES ' .
			'("text",0,1,1,300,"Signature","Signature",1,1,1,"","FIELD_CCB_SIGNATURE")';
			$this->_db->setQuery($query);
			$this->_db->query();
			$recInserted = true;
		}

		if( $recInserted ) {
			$query = "UPDATE #__community_fields set ordering=id where fieldcode like 'FIELD_CCB%'";
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		return true;
    }

    function getItemId( $key )
	{
	    $db = &JFactory::getDBO();
        $query = 'SELECT id FROM #__menu WHERE link like "'. $key . '%" ';
        $db->setQuery($query);
        $obj = $db->loadObject();
    	return $obj->id;
	}

}
?>
