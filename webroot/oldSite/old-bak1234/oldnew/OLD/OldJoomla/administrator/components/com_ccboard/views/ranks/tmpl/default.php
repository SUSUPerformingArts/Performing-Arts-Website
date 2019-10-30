<?php
/**
 * @version		$Id: default.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/
	defined( '_JEXEC' ) or die( 'Restricted Access' );
?>

<fieldset>
	<p class="ccbsubheader">
		<?php echo JText::_('CCB_RANK_DESCRIPTION'); ?>
	</p>
</fieldset>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<div id="editcell">
    <table class="adminlist">
        <thead>
            <tr>
            	<th width="5">#</th>
                <th width="20">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
                </th>
                <th style="text-align:left;"><?php echo JHTML::_('grid.sort', JText::_('CCB_RANK_TITLE'), 'rank_title', $this->filter_orderDir, $this->filter_order == 'rank_title'); ?></th>
                <th style="width: 100px;"><?php echo JHTML::_('grid.sort', JText::_('CCB_RANK_MIN'), 'rank_min', $this->filter_orderDir, $this->filter_order == 'rank_min'); ?></th>
                <th style="width: 100px;"><?php echo JHTML::_('grid.sort', JText::_('CCB_RANK_SPECIAL'), 'rank_special', $this->filter_orderDir, $this->filter_order == 'rank_special'); ?></th>
                <th style="width: 150px;"><?php echo JText::_('CCB_RANK_IMAGE')?></th>
                <th width="5"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->filter_orderDir, $this->filter_order == 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        for( $i = 0, $n=count($this->items); $i < $n; $i++ ) {
            $row = $this->items[$i];
            $checked = JHTML::_('grid.id', $i, $row->id );
            $link = JRoute::_('index.php?option=com_ccboard&view=editrank&cid='.$row->id);
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td><?php echo $i+1; ?></td>
                <td><?php echo $checked; ?></td>
                <td><a href="<?php echo $link; ?>"><?php echo $row->rank_title; ?></a></td>
                <td style="text-align: center;"><?php echo $row->rank_min; ?></td>
                <td style="text-align: center;">
                	<?php
                		if( $row->rank_special ) {
							$img = 'tick.png';
							$alt = JText::_( 'CCB_RANK_SPECIAL' );
                		}
                		else {
                			$img = 'publish_x.png';
							$alt = JText::_( 'CCB_NORMAL_RANK' );
                		}
                	?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'CCB_RANK' ); ?> "><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','toggleRank_special')">
					<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></a></span>
                </td>
                <td style="text-align: center;">
                	<img src="<?php echo JURI::root(); ?>components/com_ccboard/assets/ranks/<?php echo $row->rank_image;?>" border="0" />
                </td>
                <td><?php echo $row->id; ?></td>
            </tr>
        <?php } ?>
        	<tr>
        		<td colspan=7 align=center><?php echo $this->pagination->getListFooter(); ?></td>
        	</tr>
        </tbody>
    </table>
</div>
<?php echo CCBOARD_COPYRIGHT; ?>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_orderDir; ?>" />
</form>
