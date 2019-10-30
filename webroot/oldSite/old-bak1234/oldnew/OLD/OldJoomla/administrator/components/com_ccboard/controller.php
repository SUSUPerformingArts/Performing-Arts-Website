<?php
/**
 * @version		$Id: controller.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport('joomla.application.component.controller');

class ccboardController extends JController
{
    function ccboardController()
    {
       	parent::__construct();
    }

    function display($tmpl = null)
    {
    	$view = JRequest::getVar('view');
    	if($view == 'editcat' || $view == 'editforum' || $view == 'editprofile' || $view == 'editrank' || $view == 'editcss') {
    		JRequest::setVar('hidemainmenu', 1);
    	} else
    		JRequest::setVar('hidemainmenu', 0);

        parent::display($tmpl);
    }


    function addCat()
    {
	    $link = JRoute::_('index.php?option=com_ccboard&view=editcat&cid=0', false);
	    $this->setRedirect($link);
    }

    function editCat()
    {
    	$cid = JRequest::getVar('cid');
	    $link = JRoute::_('index.php?option=com_ccboard&view=editcat&cid='. $cid[0], false);
	    $this->setRedirect($link);
    }

    function delCat()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('categories');

        if ($model->delete($cid)) {
            $message = JText::_('CCB_CATEGORY_DELETED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }

    	$link = JRoute::_('index.php?option=com_ccboard&view=categories', false);
	    $this->setRedirect($link, $message);
    }

    function cancelCat()
    {
	    $link = JRoute::_('index.php?option=com_ccboard&view=categories', false);
	    $this->setRedirect($link);
    }

    function saveCat()
    {
  		JRequest::setVar('view', 'categories');
        $data = JRequest::get('post');
        $model = $this->getModel('editcat');

        if ($model->store($data)) {
            $message = JText::_('CCB_CATEGORY_SAVED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }

        $this->setRedirect(JRoute::_('index.php?option=com_ccboard&view=categories', false), $message);
    }


    function saveGeneral()
    {
  		JRequest::setVar('view', 'general');
        $data = JRequest::get('post');
        $model = $this->getModel('general');

        if ($model->store($data)) {
            $message = JText::_('CCB_GLOBAL_CONFIG_SAVED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }

        $this->setRedirect(JRoute::_('index.php?option=com_ccboard&view=general', false), $message);
     }


    function addForum()
    {
	    $link = JRoute::_('index.php?option=com_ccboard&view=editforum&cid=0', false);
	    $this->setRedirect($link);
    }

    function editForum()
    {
    	$cid = JRequest::getVar('cid');
	    $link = JRoute::_('index.php?option=com_ccboard&view=editforum&cid=' . $cid[0] , false);
	    $this->setRedirect($link);
    }

    function delForum()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('forums');

        if ($model->delete($cid)) {
            $message = JText::_('CCB_FORUM_DELETED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }

	    $link = JRoute::_('index.php?option=com_ccboard&view=forums', false);
	    $this->setRedirect($link, $message);
    }

    function cancelForum()
    {
	    $link = JRoute::_('index.php?option=com_ccboard&view=forums', false);
	    $this->setRedirect($link);
    }


    function saveForum()
    {
        $data = JRequest::get('post');
        $model = $this->getModel('editforum');

        if ($model->store($data)) {
            $message = JText::_('CCB_FORUM_SAVED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }
		$this->setRedirect(JRoute::_('index.php?option=com_ccboard&view=forums', false), $message);
    }

	function publishForum()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('forums');

        if ($model->publish($cid)) {
            $message = JText::_('CCB_FORUM_PUBLISHED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }

	    $link = JRoute::_('index.php?option=com_ccboard&view=forums', false);
	    $this->setRedirect($link, $message);
    }

	function unpublishForum()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('forums');

        if ($model->unpublish($cid)) {
            $message = JText::_('CCB_FORUM_UNPUBLISHED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }

	    $link = JRoute::_('index.php?option=com_ccboard&view=forums', false);
	    $this->setRedirect($link, $message);
    }

	function toggleForum_lock()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('forums');

        if ($model->toggle_lock($cid)) {
            $message = JText::_('CCB_FORUM_LOCK_UNLOCK_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }
	    $link = JRoute::_('index.php?option=com_ccboard&view=forums', false);
	    $this->setRedirect($link, $message);
    }

    function toggleForum_moderate()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('forums');

        if ($model->toggle_moderate($cid)) {
            $message = JText::_('CCB_FORUM_MODERATE_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }
	    $link = JRoute::_('index.php?option=com_ccboard&view=forums', false);
	    $this->setRedirect($link, $message);
    }

    function toggleForum_review()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('forums');

        if ($model->toggle_review($cid)) {
            $message = JText::_('CCB_FORUM_REVIEW_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }
	    $link = JRoute::_('index.php?option=com_ccboard&view=forums', false);
	    $this->setRedirect($link, $message);
    }

	function saveOrder()
    {
    	$view = JRequest::getVar('view');
    	if( $view == 'categories') {
	        $cid = JRequest::getVar('cid');
	        $orders = JRequest::getVar('order');
	        $model = $this->getModel('categories');

	        if ($model->saveOrder($cid, $orders)) {
	            $message = JText::_('CCB_ORDER_SAVED_SUCCESSFULLY');
	        } else {
	            $message = $model->getError();
	        }
	        $this->setRedirect(JRoute::_('index.php?option=com_ccboard&view=categories', false), $message);
    	} else if( $view == 'forums') {
	        $cid = JRequest::getVar('cid');
	        $orders = JRequest::getVar('order');
	        $model = $this->getModel('forums');

	        if ($model->saveOrder($cid, $orders)) {
	            $message = JText::_('CCB_ORDER_SAVED_SUCCESSFULLY');
	        } else {
	            $message = $model->getError();
	        }
	        $this->setRedirect(JRoute::_('index.php?option=com_ccboard&view=forums', false), $message);
    	}

    } //end saveOrder

    function orderDown()
    {
	   	$this->move(1);
    }


    function orderUp()
    {
		$this->move(-1);
    }

    function move($direction)
    {
    	$view = JRequest::getVar('view');
    	if( $view == 'categories') {
	        $cid = JRequest::getVar('cid');
	        $model = $this->getModel('categories');
	        if ($model->move($cid[0], $direction)) {
	            $message = JText::_('CCB_MOVED_SUCCESSFULLY');
	        } else  {
	            $message = $model->getError();
	        }
        	$this->setRedirect(JRoute::_('index.php?option=com_ccboard&view=categories', false), $message);
    	} else if ($view == 'forums') {
	        $cid = JRequest::getVar('cid');
	        $model = $this->getModel('forums');
	        if ($model->move($cid[0], $direction)) {
	            $message = JText::_('CCB_MOVED_SUCCESSFULLY');
	        } else  {
	            $message = $model->getError();
	        }
        	$this->setRedirect(JRoute::_('index.php?option=com_ccboard&view=forums', false), $message);
    	}

    } //end move

    function editProfile()
    {
    	$cid = JRequest::getVar('cid');
	    $link = JRoute::_('index.php?option=com_ccboard&view=editprofile&cid='. $cid[0], false);
	    $this->setRedirect($link);
    }

	function saveProfile()
	{
        $data = JRequest::get('post');
        $model = $this->getModel('editprofile');

        if ($model->store($data)) {
            $message = JText::_('CCB_PROFILE_SAVED_SUCCESSFULLY');
            $link = JRoute::_('index.php?option=com_ccboard&view=users', false);
        } else {
            $message = $model->getError();
            $uri =& JFactory::getURI();
            $link =  $uri->toString();
        }
		$this->setRedirect($link, $message);

	}

	function cancelProfile()
    {
	    $link = JRoute::_('index.php?option=com_ccboard&view=users', false);
	    $this->setRedirect($link);
    }

	function saveCSS()
	{
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );
		$file = JPATH_COMPONENT_SITE . DS . 'assets' . DS . 'ccboard.css';
		$filecontent	= JRequest::getVar('filecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);

		jimport('joomla.filesystem.file');
		if( !JFile::write($file, $filecontent)) {
			$message = JText::_('CCB_UNABLE_TO_SAVE_CSS_FILE');
		} else {
			$message = JText::_('CCB_CSS_SAVED_SUCCESSFULLY');
		}
	    $link = JRoute::_('index.php?option=com_ccboard&view=ccboard', false);
	    $this->setRedirect($link, $message);

	}

    function cancelCSS()
    {
	    $link = JRoute::_('index.php?option=com_ccboard&view=ccboard', false);
	    $this->setRedirect($link);
    }

	function toggleRank_special()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('ranks');

        if ($model->toggleRank_special($cid)) {
            $message = JText::_('CCB_RANK_SPECIAL_SAVED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }
	    $link = JRoute::_('index.php?option=com_ccboard&view=ranks', false);
	    $this->setRedirect($link, $message);
    }

    function delRank()
    {
     	$cid = JRequest::getVar('cid');
        $model = $this->getModel('ranks');

        if ($model->delete($cid)) {
            $message = JText::_('CCB_RANK_DELETED_SUCCESSFULLY');
        } else {
            $message = $model->getError();
        }

	    $link = JRoute::_('index.php?option=com_ccboard&view=ranks', false);
	    $this->setRedirect($link, $message);
    }

    function addRank()
    {
	    $link = JRoute::_('index.php?option=com_ccboard&view=editrank&cid=0', false);
	    $this->setRedirect($link);
    }

    function editRank()
    {
    	$cid = JRequest::getVar('cid');
	    $link = JRoute::_('index.php?option=com_ccboard&view=editrank&cid=' .$cid[0], false);
	    $this->setRedirect($link);
    }

    function cancelRank()
    {
	    $link = JRoute::_('index.php?option=com_ccboard&view=ranks', false);
	    $this->setRedirect($link);
    }

    function saveRank()
    {
        $data = JRequest::get('post');
        $model = $this->getModel('editrank');

        if ($model->store($data)) {
            $message = JText::_('CCB_RANK_SAVED_SUCCESSFULLY');
			$link= JRoute::_('index.php?option=com_ccboard&view=ranks', false);
        } else {
           $message = $model->getError();
           $uri =& JFactory::getURI();
           $link =  $uri->toString();
        }

		$this->setRedirect( $link, $message);
    }

    function syncBoard()
    {
        $model = $this->getModel('tools');
        if( $model->syncBoard() ) {
        	$message = JText::_('CCB_BOARD_SYNCED_SUCCESSFULLY');
        } else {
        	$message = $model->getError();
        }
    	$link= JRoute::_('index.php?option=com_ccboard&view=tools', false);
    	$this->setRedirect( $link, $message);
    }

    function upgradeDB()
    {
        $model = $this->getModel('tools');
        if( $model->upgradeDB() ) {
        	$message = JText::_('CCB_BOARD_UPGRADED_SUCCESSFULLY');
        } else {
        	$message = $model->getError();
        }
    	$link= JRoute::_('index.php?option=com_ccboard&view=tools', false);
    	$this->setRedirect( $link, $message);
    }

    function migrateBoard()
    {
        $data = JRequest::get('post');
    	$model = $this->getModel('tools');
        if( $model->migrateBoard($data) ) {
        	$message = JText::_('CCB_DATA_MIGRATED_SUCCESSFULLY');
        } else {
        	$message = $model->getError();
        }
    	$link= JRoute::_('index.php?option=com_ccboard&view=tools', false);
    	$this->setRedirect( $link, $message);
    }

}
?>
