<?php
/**
 * @version		$Id: default.php 162 2009-08-22 18:33:26Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/
	defined( '_JEXEC' ) or die( 'Restricted Access' );
	$dispclr = $this->theme;
	global $ccbConfig;
	$userArray = array();
?>


<script language="javascript" type="text/javascript">

function submitbutton()
{
	var form = document.FrmPostlist;
	form.submit();
	return;
}
</script>
<form action="<?php echo $this->action; ?>" method="post" name="FrmPostlist" id="FrmPostlist">
<div class="ccbmaindiv">
	<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $ccbConfig->boardname;?> </td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
	 	</tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td class="ccbheaderrow">
				<table class="ccbnormaltable" cellpadding="0" cellspacing="5">
					<tr>
						<td class="ccbheaderlink"><?php echo $this->userprofile['home']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['latest']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['link']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['mylist']; ?></td>
						<td class="ccbheaderusername"><?php echo JText::_('WELCOME') . ' ' . $this->userprofile['boardusername']; ?></td>
						<td class="ccbheaderavatar"><?php echo $this->userprofile['thumb']; ?></td>
					</tr>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<?php foreach($this->sticky as $rec) { ?>
		<tr>
			<td class="ccbleftbody"></td>
			<td class="ccbheaderrow">
				<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
					<tr>
						<td class="ccbtopicicon"><img src="<?php echo CCBOARD_ASSETS_URL; ?>/sticky<?php echo $rec->topic_type; ?>.gif" class="ccbiconimg" /></td>
						<td class="ccblinetop">
							<a href="<?php echo JRoute::_('index.php?option=com_ccboard&view=postlist&forum=' . (int)$rec->forum_id . '&topic=' . (int)$rec->id.'&Itemid=' . $ccbConfig->itemid ); ?>">
								<b><?php echo $this->escape($rec->post_subject); ?></b>
							</a>&nbsp;
						</td>
						<td class="ccbfrmlast" >
                        <?php
                            if( $rec->post_time != 0 ) {
                                if( $rec->post_user > 0 ) {
                                    if( isset( $userArray[$rec->post_user])) {
                                        $uname = $userArray[$rec->post_user];
                                    } else {
                                        $uname = ccboardHelperConfig::getUserName( $rec->post_user );
                                        $userArray[$rec->post_user] = $uname;
                                    }
                                } else {
                                    $uname = $rec->post_username;
                                }
                            }

                            echo JText::_('POST_LIST_BY') . '&nbsp;<b>'. $uname . '</b>&nbsp;' . JText::_('POST_LIST_ON') . '&nbsp;' .
								JHTML::_('date', $rec->post_time + $ccbConfig->timeoffset, $ccbConfig->dateformat);
                         ?>
						</td>
					</tr>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<?php }	?>
		<tr class="ccbtablefooter">
 			 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
 		</tr>
		<tr><td colspan="3" class="ccbcolspacer"></td></tr>
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo JText::_('BUTTON_POST_MOVE'); ?></td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
	 	</tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td>
				<table class="ccbnormaltable" cellpadding="0" cellspacing="2">
					<tr><td class="ccbmovcap"><br /><td></td></tr>
					<tr>
						<td class="ccbmovcap"><?php echo JText::_('FORUM'); ?>:</td>
						<td><?php echo $this->forumname; ?></td>
					</tr>
					<tr>
						<td class="ccbmovcap"><?php echo JText::_('TOPIC_P'); ?></td>
						<td><?php echo $this->items[0]->post_subject; ?></td>
					</tr>
					<tr><td class="ccbmovcap"><br /><td></td></tr>
					<tr>
						<td class="ccbmovcap"><?php echo JText::_('MOVE_TO_FORUM'); ?>:</td>
						<td><?php echo $this->fcombo; ?></td>
					</tr>
					<tr><td class="ccbmovcap"><br /><td></td></tr>
					<tr>
						<td class="ccbmovcap"></td>
						<td>
							<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="#" onclick='submitbutton(); return false;'>
								<span><?php echo JText::_('MOVE'); ?></span>
							</a>
							<a rel="nofollow" class="ovalbutton<?php echo $dispclr; ?>" href="<?php echo JRoute::_($this->posturl . 'list&forum=' . ((int)$this->items[0]->forum_id) . '&topic=' . ((int)$this->items[0]->topic_id) .'&Itemid=' . $ccbConfig->itemid); ?>">
								<span><?php echo JText::_('CANCEL'); ?></span>
							</a>
						</td>
					</tr>
					<tr><td class="ccbmovcap"><br /><td></td></tr>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<tr class="ccbtablefooter">
 			 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
 		</tr>
		<tr><td colspan="3" class="ccbcolspacer"></td></tr>
	</table>
</div>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" name="task" value="postmove" />
<input type="hidden" name="forum" value="<?php echo $this->items[0]->forum_id; ?>" />
<input type="hidden" name="topic" value="<?php echo $this->items[0]->topic_id; ?>" />
<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
</form>
<br/><br/>
<?php echo CCBOARD_COPYRIGHT; ?>
<br/><br/>
