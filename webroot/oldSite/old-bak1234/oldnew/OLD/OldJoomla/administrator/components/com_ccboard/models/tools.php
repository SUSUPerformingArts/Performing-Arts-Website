<?php
/**
 * @version		$Id: tools.php 152 2009-06-20 17:26:28Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.application.component.model' );

require_once(JPATH_COMPONENT.DS.'ccboard-config.php');
require_once(JPATH_COMPONENT . DS .'models' . DS .'general.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_ccboard' . DS . 'models' . DS . 'postlist.php');

class ccboardModelTools extends JModel
{

	function ccboardModelTools()
	{
		parent::__construct();
	}

	function syncBoard()
	{
		$ccbConfig = new ccboardConfig();
		$db = &JFactory::getDBO();

		// (1) Rank Update
		if( !$this->rankMasterUpdate() ) {
			return false;
		}

		// (2) Update jos_users Sync with jos_ccb_users
		if( !$this->updateUsersToccBoard() ) {
			return false;
		}

		// (3) Update All Counters
		$plist = new ccboardModelPostList();
		$plist->_reCalcUser();				// Recalculate Post Count and Rank
		$plist->_reCalcTopic();				// Recalculate Reply Count
		$plist->_reCalcForum();				// Recalculate Topic count and Post Count

		// (4) Update External User Profiles

		if( $ccbConfig->userprofile == 'combuilder') {
			$query = 'UPDATE #__comprofiler AS c SET ' .
	        		'c.ccbrankname  = (SELECT r.rank_title 	FROM #__ccb_users as u INNER JOIN #__ccb_ranks AS r ON u.rank = r.id WHERE u.user_id = c.user_id ), ' .
	        		'c.ccbpostcount = (SELECT u.post_count 	FROM #__ccb_users AS u  where  u.user_id = c.user_id) ' ;
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

			$query = 'DELETE FROM #__community_fields_values WHERE field_id in ' .
    			'(' . $postfield . ', ' . $rankfield . ')  ';
        	$db->setQuery($query);
			$db->query();
			if( $postfield > 0 ) {
		    	$query = 'INSERT INTO #__community_fields_values (user_id, field_id, value) ' .
		    				'(SELECT user_id, '. $postfield .', post_count FROM #__ccb_users )';
		        $db->setQuery($query);
				$db->query();
			}
			if( $rankfield  > 0 ) {
		    	$query = 'INSERT INTO #__community_fields_values (user_id, field_id, value) ' .
						'(SELECT u.user_id,' . $rankfield . ', r.rank_title FROM #__ccb_users AS u INNER JOIN #__ccb_ranks AS r ON u.rank = r.id ) ';
		        $db->setQuery($query);
				$db->query();
			}
		}
		return true;
	}

	function upgradeDB()
	{

		$ccbConfig = new ccboardConfig();
		$model = new ccboardModelGeneral();

		if( !$this->checkTables() ) {
			return false;
		}

		if( $ccbConfig->userprofile == 'jomsocial') {
			if( !$model->modifyJomFields()) {
				$this->setError(JText::_('CCB_ERROR_JOMSOCIAL_TABLES_MISSING'));
				return false;
			}
		} elseif ($ccbConfig->userprofile == 'combuilder') {
			if( !$model->modifyCBFields()) {
				$this->setError(JText::_('CCB_ERROR_COMMUNITY_BUILDER_TABLES_MISSING'));
				return false;
			}
		}
		return true;
	}

	function migrateBoard($data)
	{
		if(!$this->fromFireboard()) return false;
		$this->syncBoard();
		return true;
	}



	function updateUsersToccBoard()
	{
		$db = &JFactory::getDBO();
		$query = 'SELECT id FROM #__ccb_ranks ORDER BY rank_min ASC LIMIT 1';
		$db->setQuery($query);
		$rank = ($rank = $db->loadResult())? $rank : 1 ;

		$query = 'INSERT INTO #__ccb_users (user_id, avatar, rank) (SELECT id, "avatar1.png", ' . $rank . ' FROM #__users WHERE id NOT IN (SELECT user_id FROM #__ccb_users) ) ';
		$db->setQuery($query);
		if( !$db->query() ) {
			$this->setError($db->getError());
			return false;
		}
		return true;
	}

	function rankMasterUpdate()
	{
		$db = &JFactory::getDBO();
		$query = 'SELECT count(id) FROM #__ccb_ranks';
		$db->setQuery($query);
		$tot = ($tot = $db->loadResult())? $tot : 0 ;
		if( $tot < 1 ) {
			$query = 'INSERT INTO #__ccb_ranks (id, rank_title, rank_min, rank_special, rank_image ) VALUES (1, "Fresher", 0, 0, "star00.png")';
			$db->setQuery($query);
			if( !$db->query() ) {
				$this->setError($db->getError());
				return false;
			}
		}
		return true;
	}


	function checkTables()
	{
		$_modes = array('CREATE', 'ALTER');
		$_table	= array();
		$_table['#__ccb_attachments'] = array(
				array('id', 'int(10) unsigned', 'NO', 'PRI', '', 'auto_increment', 'A'),
				array('post_id', 'int(10) unsigned', 'NO', '', '0', '', 'A'),
				array('ccb_name', 'varchar(255)', 'NO', '', ' ', '', 'A'),
				array('real_name', 'varchar(255)', 'NO', '', ' ', '', 'A'),
				array('filesize', 'int(10) unsigned', 'NO', '', '0', '', 'A'),
				array('hits', 'int(10) unsigned', 'NO', '', '0', '', 'A'),
				array('comment', 'mediumtext', 'NO', '', '', '', 'A'),
				array('filetime', 'int(10) unsigned', 'NO', '', '0', '', 'A'),
				array('extension', 'varchar(100)', 'YES', '', '', '', 'A'),
				array('mimetype', 'varchar(100)', 'YES', '', '', '', 'A')
				);
		$_table['#__ccb_category'] = array(
					array('id', 'int(10) unsigned', 'NO', 'PRI', '', 'auto_increment', 'A'),
					array('cat_name', 'varchar(255)', 'YES', '', '.', '', 'A'),
					array('ordering', 'int(10) unsigned', 'YES', '', '0', '', 'A')
				);
		$_table['#__ccb_forums'] = array(
				array('id', 'int(10) unsigned', 'NO', 'PRI', '', 'auto_increment', 'A'),
				array('forum_name', 'varchar(255)', 'YES', '', '.', '', 'A'),
				array('forum_desc', 'mediumtext', 'YES', '', '', '', 'A'),
				array('cat_id', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('topic_count', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('post_count', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('last_post_user', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('last_post_time', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('last_post_id', 'int(10) unsigned', 'YES', '','0', '', 'A'),
				array('published', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('locked', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('view_for', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('post_for', 'int(10) unsigned', 'YES', '', '18', '', 'A'),
				array('moderate_for', 'int(10) unsigned', 'YES', '', '23', '', 'A'),
				array('forum_image', 'varchar(100)', 'YES', '', '', '', 'A'),
				array('ordering', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('moderated', 'tinyint(3) unsigned', 'NO', '', '0', '', 'A'),
				array('review', 'tinyint(3) unsigned', 'NO', '', '0', '', 'A'),
				array('last_post_username', 'varchar(255)', 'YES', '', '', '', 'A')
				);
		$_table['#__ccb_moderators'] = array(
				array('id', 'int(10) unsigned', 'NO', 'PRI', '', 'auto_increment', 'A'),
				array('user_id', 'int(10) unsigned', 'NO', '', '', '', 'A'),
				array('forum_id', 'int(10) unsigned', 'NO', '', '', '', 'A')
				);
		$_table['#__ccb_posts'] = array(
				array('id', 'int(10) unsigned', 'NO', 'PRI', '', 'auto_increment', 'A'),
				array('topic_id', 'int(10) unsigned', 'YES', 'MUL', '0', '', 'A'),
				array('forum_id', 'int(10) unsigned', 'YES', 'MUL', '0', '', 'A'),
				array('post_subject', 'varchar(255)', 'YES', '', '.', '', 'A'),
				array('post_text', 'mediumtext', 'NO', '', '', '', 'A'),
				array('post_user', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('post_time', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('ip', 'varchar(20)', 'YES', '', '', '', 'A'),
				array('hold', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('modified_by', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('modified_time', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('modified_reason', 'varchar(255)', 'YES', '', '', '', 'A'),
				array('post_username', 'varchar(255)', 'YES', '', '', '', 'A')
				);
		$_table['#__ccb_ranks'] = array(
				array('id', 'int(10) unsigned', 'NO', 'PRI', '', 'auto_increment', 'A'),
				array('rank_title', 'varchar(255)', 'NO', '', '', '', 'A'),
				array('rank_min', 'int(10) unsigned', 'NO', '', '0', '', 'A'),
				array('rank_special', 'tinyint(3) unsigned', 'NO', '', '0', '', 'A'),
				array('rank_image', 'varchar(255)', 'NO', '', '', '', 'A')
				);
		$_table['#__ccb_topics'] = array(
				array('id', 'int(10) unsigned', 'NO', 'PRI', '', 'auto_increment', 'A'),
				array('forum_id', 'int(10) unsigned', 'YES', 'MUL', '0', '', 'A'),
				array('post_subject', 'varchar(255)', 'YES', '', '.', '', 'A'),
				array('reply_count', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('hits', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('post_time', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('post_user', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('last_post_time', 'int(10) unsigned', 'YES', 'MUL', '0', '', 'A'),
				array('last_post_id', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('last_post_user', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('start_post_id', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('topic_type', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('locked', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('topic_email', 'mediumtext', 'YES', '', '', '', 'A'),
				array('hold', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('topic_emoticon', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('post_username', 'varchar(255)', 'YES', '', '', '', 'A'),
				array('last_post_username', 'varchar(255)', 'YES', '', '', '', 'A'),
				array('topic_favourite', 'mediumtext', 'YES', '', '', '', 'A')
				);
		$_table['#__ccb_users'] = array(
				array('id', 'int(10) unsigned', 'NO', 'PRI', '', 'auto_increment', 'A'),
				array('user_id', 'int(10) unsigned', 'NO', '', '', '', 'A'),
				array('dob', 'int(11)', 'YES', '', '0', '', 'A'),
				array('location', 'varchar(45)', 'YES', '', '', '', 'A'),
				array('signature', 'mediumtext', 'YES', '', '', '', 'A'),
				array('avatar', 'varchar(100)', 'YES', '', '', '', 'A'),
				array('rank', 'int(10) unsigned', 'NO', '', '0', '', 'A'),
				array('post_count', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('gender', 'char(10)', 'YES', '', 'Male', '', 'A'),
				array('www', 'varchar(45)', 'YES', '', '', '', 'A'),
				array('icq', 'varchar(45)', 'YES', '', '', '', 'A'),
				array('aol', 'varchar(45)', 'YES', '', '', '', 'A'),
				array('msn', 'varchar(45)', 'YES', '', '', '', 'A'),
				array('yahoo', 'varchar(45)', 'YES', '', '', '', 'A'),
				array('jabber', 'varchar(45)', 'YES', '', '', '', 'A'),
				array('skype', 'varchar(45)', 'YES', '', '', '', 'A'),
				array('thumb', 'varchar(100)', 'YES', '', '', '', 'A'),
				array('showemail', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('moderator', 'tinyint(3) unsigned', 'YES', '', '0', '', 'A'),
				array('karma', 'int(10) signed', 'YES', '', '0', '', 'A'),
				array('karma_time', 'int(10) unsigned', 'YES', '', '0', '', 'A'),
				array('hits', 'int(10) unsigned', 'YES', '', '0', '', 'A')
				);

        $_index = array();
//        $_index[] = 'DROP INDEX ccb_posts_topic_id ON #__ccb_posts ';
//        $_index[] = 'DROP INDEX ccb_posts_forum_id ON #__ccb_posts ';
//        $_index[] = 'DROP INDEX ccb_topics_forum_id ON #__ccb_topics ';
//        $_index[] = 'DROP INDEX ccb_topics_last_post_time ON #__ccb_topics ';
        $_index[] = 'CREATE INDEX ccb_posts_topic_id ON #__ccb_posts (topic_id) ';
//        $_index[] = 'CREATE INDEX ccb_posts_forum_id ON #__ccb_posts (forum_id) ';
        $_index[] = 'CREATE INDEX ccb_topics_forum_id ON #__ccb_topics (forum_id) ';
        $_index[] = 'CREATE INDEX ccb_topics_last_post_time ON #__ccb_topics (last_post_time) ';
        
		$db = &JFactory::getDBO();
		$retval = true;

		foreach($_modes as $mode){
			foreach($_table as $key => $val){
				$tablename = $key;
				$altersql 	= '';

				if($mode == 'ALTER'){
					$rows = $this->getTableMetaData($tablename);
					foreach($_table[$tablename] as $fields){
						$isFound = false;
						foreach($rows as $row){
							if ($fields[0] == $row['Field']){
								$isFound = true;
								break;
							}
						}
						if($fields[6] == 'D' && $isFound){
							$altersql .= ($altersql <> ''?', ':'' ) . 'DROP COLUMN '.$fields[0];
						}
						elseif($fields[6] == 'A' ){
							if($isFound){
								$script = $this->getModifyScript($row, $fields, 'MODIFY');
								if($script <> ''){
									$altersql .= ($altersql <> ''?', ':'' ) . $script;
								}
							}
							else{
								$script = $this->getModifyScript($row, $fields, 'ADD');
								if($script <> ''){
									$altersql .= ($altersql <> ''?', ':'' ) . $script;
								}
							}
						}
					}

					if($altersql <> ''){
						$altersql = 'ALTER TABLE ' . $tablename . ' ' . $altersql;
					}
				}
				elseif($mode == 'CREATE'){
					foreach($_table[$tablename] as $fields){
						if($fields[6] == 'A' ){
							$script = $this->getModifyScript('', $fields, 'NEW');
							if($script <> ''){
								$altersql .= ($altersql <> ''?', ':'' ) . $script;
							}
						}
					}

					if($altersql <> ''){
						$altersql = 'CREATE TABLE IF NOT EXISTS ' . $tablename . ' ( ' . $altersql . ')';
					}
				}

				if($altersql <> ''){
					$db->setQuery( $altersql);
                    //print_r( $altersql);
					if( !$db->query() ) {
						$this->setError( $db->getError());
						$retval = false;
						break;
					}
				}
			}
			if(!$retval ){
				break;
			}
		}

		foreach($_index as $altersql){
            //print_r( $altersql);
            $db->setQuery( $altersql);
            $db->query();
       }
		return $retval;
	}

	function getTableMetaData($tablename)
	{
		$db = &JFactory::getDBO();
		$db->setQuery('SHOW FIELDS FROM '.$tablename);
		$items = ($items = $db->loadAssocList())?$items :array() ;
		return $items;
	}

	function getModifyScript($row, $fields, $action)
	{
		$script = '';

		if($action == 'MODIFY'){
			if($row['Type'] <> $fields[1] || $row['Null'] <> $fields[2] ||	$row['Default'] <> $fields[4] || $row['Extra'] <> $fields[5]){
				$script = $action.' COLUMN '. $row['Field'].' '.$fields[1] .' '. ($fields[2] == 'NO'?'NOT NULL ':'NULL ') . ' ' . ($fields[4] <> ''?'DEFAULT \''.$fields[4].'\'':'') .' ' . $fields[5];
			}
		}
		elseif($action == 'ADD'){
			$script = $action.' COLUMN '. $fields[0].' '.$fields[1] .' '. ($fields[2] == 'NO'?'NOT NULL ':'NULL ') . ' ' . ($fields[4] <> ''?'DEFAULT \''.$fields[4].'\'':'') .' ' . $fields[5];
		}
		elseif($action == 'NEW'){
			$script = $fields[0].' '.$fields[1] .' '. ($fields[2] == 'NO'?'NOT NULL ':'NULL ') . ' '. ($fields[3] == 'PRI'?'PRIMARY KEY':' ') . ' ' . ($fields[4] <> ''?'DEFAULT \''.$fields[4].'\'':'') .' '. ($fields[5] == 'auto_increment'?'auto_increment':'  ') . ' ';
		}

		return $script;
	}

	function fromFireboard()
	{
		require_once(JPATH_COMPONENT.DS.'elements'.DS.'permissions.php');
		jimport('joomla.filesystem.file');

		$db = &JFactory::getDBO();
		$pfor = JElementPermissions::getRegistered();

        $query = 'SELECT id FROM #__components WHERE `option` = "com_fireboard"';
        $db->setQuery($query);
        $obj = $db->loadResult();
    	if(!isset($obj)) {
    		$this->setError(JText::_('CCB_FIREBOARD_COMPONENT_MISSING'));
    		return false;
    	}


		//copy avatar folders
		$this->copydir(JPATH_SITE . DS . 'images' .DS. 'fbfiles' . DS . 'avatars', JPATH_COMPONENT_SITE . DS . 'assets' . DS. 'avatar' );
		$this->copydir(JPATH_SITE . DS . 'components'. DS. 'com_fireboard' .DS. 'template' .DS. 'default' .DS. 'images' . DS. 'english' . DS . 'ranks', JPATH_COMPONENT_SITE . DS . 'assets' . DS. 'ranks' );
		$this->copydir(JPATH_SITE . DS . 'images' .DS. 'fbfiles' . DS . 'files', JPATH_COMPONENT_SITE . DS . 'assets' . DS. 'uploads' );

		$query = array();
		$query[] = 'INSERT INTO #__ccb_ranks (id, rank_title, rank_min, rank_special, rank_image) '.
					'(SELECT rank_id, rank_title, rank_min, rank_special, rank_image FROM #__fb_ranks) ';

		$query[] = 'INSERT INTO #__ccb_users (user_id, avatar, thumb, dob, location, signature, moderator, post_count, rank, karma, karma_time, hits, www, icq, aol, yahoo, msn, skype ) '.
					'(SELECT userid, avatar, avatar, UNIX_TIMESTAMP(birthdate), location, signature, moderator, posts, rank, karma, karma_time, uhits, websiteurl, ICQ, AIM, YIM, MSN, SKYPE FROM #__fb_users) ';

		$query[] = 'INSERT INTO #__ccb_forums (id, forum_name, forum_desc, cat_id, published, locked, ordering, view_for, post_for, moderated, review) ' .
			'(SELECT f.id, f.name, f.description, f.parent, f.published, f.locked, f.ordering, 0, '. $pfor .', f.moderated, f.review FROM #__fb_categories AS f WHERE f.id IN ' .
			'(SELECT DISTINCT fb.catid FROM #__fb_messages AS fb WHERE fb.parent=0))';

		$query[] = 'INSERT INTO #__ccb_category (id, cat_name, ordering ) ' .
			'(SELECT f.id, f.name, f.ordering FROM #__fb_categories AS f WHERE f.id IN (SELECT c.cat_id FROM #__ccb_forums AS c))';

		$query[] = 'INSERT INTO #__ccb_moderators (user_id, forum_id) (SELECT userid, catid FROM #__fb_moderation) ';

		$query[] = 'INSERT INTO #__ccb_topics (id, forum_id, post_subject, hits, post_time, post_user, post_username, start_post_id, topic_type, locked, hold, topic_emoticon, topic_email, topic_favourite, last_post_id ) '.
					'(SELECT thread, catid, subject, hits, time, userid, name, id, 0, locked, hold, topic_emoticon, "", "", MIN(parent) '.
					'FROM #__fb_messages group by thread)';

		$query[] = 'INSERT INTO #__ccb_posts (id, topic_id, forum_id, post_subject, post_text, post_user, post_time, ip, hold, modified_by, modified_time, modified_reason, post_username) '.
					'(SELECT t.id, t.thread, t.catid, t.subject, p.message, t.userid, t.time, t.ip, t.hold, t.modified_by, t.modified_time, t.modified_reason, t.name ' .
					'FROM #__fb_messages AS t INNER JOIN #__fb_messages_text AS p ON t.id=p.mesid) ';

		// Import Data using SQLs
		$this->clearDB();
		foreach( $query as $qq ) {
			$db->setQuery( $qq );
			if( !$db->query()) {
				$this->setError( $db->getError());
				return false;
			}
		}

		// -- Attachements
		$query = 'SELECT * FROM #__fb_attachments';
		$db->setQuery($query);
		$data = $db->loadObjectList();
		foreach($data as $item ) {
			$orgf = trim($item->filelocation);
			$fname = basename($orgf);
			if(file_exists(JPATH_SITE .DS.'components'.DS.'com_ccboard'.DS.'assets'.DS.'uploads'.DS.$fname) ) {
				$ext = JFile::getExt($fname);
				$fsize = @filesize( JPATH_SITE .DS.'components'.DS.'com_ccboard'.DS.'assets'.DS.'uploads'.DS.$fname);
				$ftime = time();
				$query = 'INSERT INTO #__ccb_attachments (post_id, ccb_name, real_name, filesize, hits, comment, filetime, extension, mimetype) ' .
						'VALUES (' . $item->mesid . ',"' . $fname . '", "' . $fname . '", ' . $fsize . ', 0, "",' . $ftime .', "' . $ext . '", "" ) ' ;
				$db->setQuery($query);
				if( !$db->query()) {
					die($query);
					$this->setError( $db->getError());
					return false;
				}
			}
		}

		// - Email Subscriptions
		$query = 'SELECT * FROM #__fb_subscriptions';
		$db->setQuery($query);
		$data = $db->loadObjectList();
		foreach($data as $item ) {
			$query = 'UPDATE #__ccb_topics SET topic_email = CONCAT(topic_email,"' . $item->userid . '-") WHERE id = ' . $item->thread;
			$db->setQuery($query);
			if( !$db->query()) {

				$this->setError( $db->getError());
				return false;
			}
		}

		// - Favourites
		$query = 'SELECT * FROM #__fb_favorites';
		$db->setQuery($query);
		$data = $db->loadObjectList();
		foreach($data as $item ) {
			$query = 'UPDATE #__ccb_topics SET topic_favourite = CONCAT(topic_favourite, "' . $item->userid . '-") WHERE id = ' . $item->thread;
			$db->setQuery($query);
			if( !$db->query()) {
				$this->setError( $db->getError());
				return false;
			}
		}
		return true;
	}

	function clearDB()
	{
		$db = &JFactory::getDBO();

		$query = array();
		$query[] = 'DELETE FROM #__ccb_ranks';
		$query[] = 'DELETE FROM #__ccb_users';
		$query[] = 'DELETE FROM #__ccb_category';
		$query[] = 'DELETE FROM #__ccb_forums';
		$query[] = 'DELETE FROM #__ccb_topics';
		$query[] = 'DELETE FROM #__ccb_posts';
		$query[] = 'DELETE FROM #__ccb_moderators';
		$query[] = 'DELETE FROM #__ccb_attachments';

		foreach( $query as $qq ) {
			$db->setQuery( $qq );
			$db->query();
		}
	}

	function copydir($source,$destination)
	{
		if(!is_dir($destination)){
			$oldumask = umask(0);
			mkdir($destination, 01777); // so you get the sticky bit set
			umask($oldumask);
		}
		$dir_handle = @opendir($source) or die("Unable to open " . $source);
		while ($file = readdir($dir_handle)) {
			if($file!="." && $file!=".." && !is_dir($source.DS.$file)) {
				copy($source.DS.$file,$destination.DS.$file);
			}elseif($file!="." && $file!=".." && is_dir($source.DS.$file)) {
				$this->copydir($source.DS.$file,$destination.DS.$file);
			}
		}
		closedir($dir_handle);
	}


}
?>
