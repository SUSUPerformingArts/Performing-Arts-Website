<?php
/**
 * @version		$Id: view.html.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

class ccboardViewccboard extends JView
{
	function display( $tpl = null )
	{
		JHTML::_('behavior.tooltip', '.hasTip');
		jimport('joomla.html.pane');
		$pane	=& JPane::getInstance('sliders');
		$this->assignRef( 'pane'		, $pane );

		$model = &$this->getModel();
		$item = $model->getData();

		$this->assignRef('ccbitems', $item);
		parent::display( $tpl );
	}


	function addIcon( $image , $view, $text )
	{
		$link = 'index.php?option=com_ccboard&view=' . $view;
?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image', 'administrator/components/com_ccboard/assets/' . $image , NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
<?php
	}
}

