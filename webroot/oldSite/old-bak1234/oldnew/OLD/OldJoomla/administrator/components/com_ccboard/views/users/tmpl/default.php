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
	 	<?php echo JText::_('CCB_PROFILE_HEADER_DESCRIPTION');  ?>
	</p>
</fieldset>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<div id="editcell" style="width:100%; margin-bottom: 15px;">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value=''; this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
		</tr>
	</table>

    <table class="adminlist">
        <thead>
            <tr>
				<th >#</th>
                <th width="20">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
                </th>
                <th style="text-align:left;">	<?php echo JHTML::_('grid.sort', JText::_('CCB_NAME'), 		'name', @$this->filter_order_Dir, $this->filter_order == 'name'); ?></th>
                <th style="text-align:left;">	<?php echo JHTML::_('grid.sort', JText::_('CCB_USERNAME'), 	'username', @$this->filter_order_Dir, $this->filter_order == 'username'); ?></th>
				<th width="1%" nowrap="nowrap">	<?php echo JHTML::_('grid.sort', JText::_('CCB_BLOCK'), 	'block', @$this->filter_order_Dir, $this->filter_order == 'block'); ?></th>
				<th >							<?php echo JHTML::_('grid.sort', JText::_('CCB_GROUP'), 	'usertype', $this->filter_order_Dir, $this->filter_order == 'usertype'); ?></th>
                <th width="1%" nowrap="nowrap">	<?php echo JHTML::_('grid.sort', JText::_('CCB_MODERATOR'),	'moderator', $this->filter_order_Dir, $this->filter_order == 'moderator'); ?></th>
				<th >							<?php echo JHTML::_('grid.sort', JText::_('CCB_EMAIL'), 	'email', $this->filter_order_Dir, $this->filter_order == 'email'); ?></th>
    			<th >							<?php echo JHTML::_('grid.sort', JText::_('CCB_POSTS'), 	'post_count', @$this->filter_order_Dir, @$this->filter_order == 'post_count'); ?></th>
    			<th >							<?php echo JHTML::_('grid.sort', JText::_('CCB_LAST_VISIT'),'lastvisitdate', $this->filter_order_Dir, $this->filter_order == 'lastvisitdate'); ?></th>
                <th width="5"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->filter_order_Dir, $this->filter_order == 'id'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        for( $i = 0, $n=count($this->items); $i < $n; $i++ ) {
            $row = $this->items[$i];
            $checked = JHTML::_('grid.id', $i, $row->id );
            $link = JRoute::_('index.php?option=com_ccboard&view=editprofile&cid='.$row->id);
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td align="center"><?php echo $i+1; ?></td>
                <td><?php echo $checked; ?></td>
                <td><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td>
                <td><?php echo $row->username; ?></td>
                <td nowrap="nowrap" align="center"><img src="images/<?php if( $row->block) echo 'tick.png'; else echo 'publish_x.png'; ?>" width="16" height="16" border="0"/>
                <td align="center"><?php echo $row->usertype; ?></td>
                <td nowrap="nowrap" align="center"><img src="images/<?php if( $row->moderator) echo 'tick.png'; else echo 'publish_x.png'; ?>" width="16" height="16" border="0"/>
                <td><?php echo $row->email; ?></td>
				<td align="center"><?php echo number_format( isset($row->post_count)?$row->post_count:0); ?></td>
				<td><?php echo JHTML::_('date', strtotime($row->lastvisitDate), '%Y-%m-%d %H:%M:%S'); ?></td>
                <td nowrap="nowrap" align="center"><?php echo $row->id; ?></td>
            </tr>
        <?php } ?>
        	<tr>
        		<td colspan=11 align=center><?php echo $this->pagination->getListFooter(); ?></td>
        	</tr>
        </tbody>
    </table>
</div>
<?php echo CCBOARD_COPYRIGHT; ?>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir; ?>" />
</form>
