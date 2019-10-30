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

<script type="text/javascript">
function submitbutton(pressbutton) {
    if( pressbutton == 'showHelp' ) {
        window.open('http://codeclassic.org', '_blank', '', false);
        return false;
    }

    submitform(pressbutton);
    return false;
}
</script>
<fieldset>
	<p class="ccbsubheader">
		<?php echo JText::_('CCB_CATEGORY_DESCRIPTION'); ?>
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
                <th style="text-align:left;"><?php echo JHTML::_('grid.sort', JText::_('CCB_CATEGORIES'), 'cat_name', $this->filter_orderDir, $this->filter_order == 'cat_name'); ?></th>
                <th width="100" ><?php echo JHTML::_('grid.sort', JText::_('CCB_ORDER'), 'ordering', $this->filter_orderDir, $this->filter_order == 'ordering'); ?><?php echo JHTML::_('grid.order', $this->items);?></th>
                <th width="5"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->filter_orderDir, $this->filter_order == 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        for( $i = 0, $n=count($this->items); $i < $n; $i++ ) {
            $row = $this->items[$i];
            $checked = JHTML::_('grid.id', $i, $row->id );
            $link = JRoute::_('index.php?option=com_ccboard&view=editcat&cid='.$row->id);
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td><?php echo $i+1; ?></td>
                <td><?php echo $checked; ?></td>
                <td><a href="<?php echo $link; ?>"><?php echo $row->cat_name; ?></a></td>
                <td class="order">
                    <span><?php echo $this->pagination->orderUpIcon($i); ?></span>
                    <span><?php echo $this->pagination->orderDownIcon($i, $n); ?></span>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"  class="text_area" style="text-align:center" />
                </td>
                <td><?php echo $row->id; ?></td>
            </tr>
        <?php } ?>
        	<tr>
        		<td colspan=5 align=center><?php echo $this->pagination->getListFooter(); ?></td>
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
