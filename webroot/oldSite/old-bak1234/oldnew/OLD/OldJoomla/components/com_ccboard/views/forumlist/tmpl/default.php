<?php
/**
 * @version		$Id: default.php 186 2009-09-26 05:33:43Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

	defined( '_JEXEC' ) or die( 'Restricted Access' );
	global $ccbConfig;
	$dispclr = $this->theme;
	$userArray = array();
?>
<div class="ccbmaindiv">
	<table class="ccbnormaltable" cellpadding="0" cellspacing="0" >
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<?php echo $ccbConfig->boardname;?> </td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
	 	</tr>
		<tr>
			<td class="ccbleftbody"></td>
			<td class="ccbheaderrow">
				<table class="ccbnormaltable" cellpadding="5" cellspacing="5">
					<tr>
						<td class="ccbheaderlink"><?php echo $this->userprofile['home']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['latest']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['link']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['mylist']; ?></td>
						<td class="ccbheaderlink"><?php echo $this->userprofile['approval']; ?></td>
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
							<a href="<?php echo JRoute::_($this->posturl .'&forum=' . (int)$rec->forum_id . '&topic=' . (int)$rec->id . '&Itemid=' . $ccbConfig->itemid) ; ?>">
								<b><?php echo $this->escape($rec->post_subject); ?></b>
							</a>&nbsp;
						</td>
						<td class="ccbfrmlast">
							<?php
								if( $rec->post_time != 0 ) {
									if( $rec->post_user > 0 ) {
										if( isset( $userArray[$rec->post_user])) {
											$uname = $userArray[$rec->post_user];
										} else {
											$uname = ccboardHelperConfig::getUserName( $rec->post_user );
											$userArray[$rec->post_user] = $uname;
										}
                                        $uname = '<a href="'. JRoute::_('index.php?option=com_ccboard&view=myprofile&cid=' . $rec->post_user . '&Itemid=' . $ccbConfig->itemid ) . '">' . $uname . '</a>';
									} else {
										$uname = $rec->post_username;
									}
									echo JText::_('POST_LIST_BY') . '&nbsp;<b>'. $uname . '</b>&nbsp;' . JText::_('POST_LIST_ON') . '&nbsp;' .
									JHTML::_('date', $rec->post_time + $ccbConfig->timeoffset, $ccbConfig->dateformat);
								}
							?>
						</td>
					</tr>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<?php } ?>
		<tr class="ccbtablefooter">
 			 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
 		</tr>
		<tr>
 			 <td class="ccbcolspacer"></td>
			 <td></td>
			 <td></td>
 		</tr>
	    <?php
	    	$cat_id=0;
	    	$i = 0;
	    	$n = count($this->items);
	    	while($i < $n && $n > 0) {
	    		$rec = $this->items[$i];
				$cat_id = $rec->cat_id;
		?>
 		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
			 <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;
                 <a href="<?php echo JRoute::_('index.php?option=com_ccboard&view=forumlist&cat='. $cat_id . '&Itemid=' . $ccbConfig->itemid); ?>">
                    <span class="textwhite"><?php echo $this->escape($rec->cat_name); ?></span>
                </a>
             </td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
 		</tr>
 		<tr>
			<td class="ccbleftbody"></td>
 			<td class="ccbdatacolumn">
 				<table class="ccbnormaltable" cellpadding="0" cellspacing="0">
					<tr class="ccbfrmrowheader">
						<td class="ccbfrmrowcol1" ></td>
						<td class="ccbfrmrowcol2"><b><?php echo $this->labels['forums'] ?></b></td>
			            <td class="ccbfrmrowcol3"><b><?php echo $this->labels['topics'] ?></b></td>
			            <td class="ccbfrmrowcol4" ><b><?php echo $this->labels['posts'] ?></b></td>
			            <td class="ccbfrmrowcol5"><b><?php echo $this->labels['lastpost'] ?></b></td>
					</tr>
					<?php
						while($i < $n) {
							$rec = $this->items[$i];
							if( $rec->cat_id != $cat_id) break;
					?>
					    	<tr class="ccbfrmrowheader">
			        			<td class="ccbtopicicon">
			        			<img src="<?php echo CCBOARD_ASSETS_URL; ?>/24-forum-<?php echo $dispclr;  if($rec->locked == 1) { echo '-locked'; } elseif($rec->review == 1) echo '-review'; ?>.png" />
			        			</td>
			            		<td class="ccblinetop" align="left">
			            			<a href="<?php echo JRoute::_($this->topicurl . '&forum='. (int) $rec->id .'&Itemid=' . $ccbConfig->itemid); ?>">
			            				<b><?php echo htmlentities($rec->forum_name, ENT_COMPAT, 'UTF-8'); ?></b>
			            			</a><br />
			            			<?php echo htmlentities($rec->forum_desc, ENT_COMPAT, 'UTF-8'); ?>
			            		</td>
			            		<td class="ccblinetop" align="center"><?php echo number_format((int)$rec->topic_count); ?></td>
			            		<td class="ccblinetop" align="center"><?php echo number_format((int)$rec->post_count); ?></td>
			            		<td class="ccblinetop" align="left">
			            			<?php
			            				if( $rec->last_post_time != 0 && $rec->topic_count > 0 ) {
                                            //echo '<a href="' . JRoute::_($this->posturl . '&forum='. $rec->id .'&topic='. (int) $rec->topic_id . '&Itemid=' . $ccbConfig->itemid . ccboardHelperConfig::getPageNumber($rec->topic_id, $rec->last_post_id)) . '">'. $this->escape($rec->post_subject) . '</a>&nbsp;';
                                            echo $this->escape($rec->post_subject) .'&nbsp;';
                                            echo '<a href="' . JRoute::_($this->posturl . '&forum='. $rec->id .'&topic='. (int) $rec->topic_id . '&Itemid=' . $ccbConfig->itemid . ccboardHelperConfig::getPageNumber($rec->topic_id, $rec->last_post_id)) .  '">';
											echo '<img src="' . CCBOARD_ASSETS_URL . '/lastpost.gif"/>';
											echo '</a><br/>';
			            					if( $rec->last_post_user > 0 ) {
												if( isset( $userArray[$rec->last_post_user])) {
													$uname = $userArray[$rec->last_post_user];
												} else {
													$uname = ccboardHelperConfig::getUserName( $rec->last_post_user );
													$userArray[$rec->last_post_user] = $uname;
												}
                                                $uname = '<a href="'. JRoute::_('index.php?option=com_ccboard&view=myprofile&cid=' . $rec->last_post_user . '&Itemid=' . $ccbConfig->itemid ) . '">' . $uname . '</a>';
			            					} else { $uname = $rec->last_post_username; }
											echo JText::_('POST_LIST_BY'). '&nbsp;<b>' . $uname . '</b><br />';
				            				echo JText::_('POST_LIST_ON') . '&nbsp;' . JHTML::_('date', $rec->last_post_time + $ccbConfig->timeoffset , $ccbConfig->dateformat) ;
			            				}
			            			?>
			            		</td>
						 	</tr>
				    <?php
					    	$i++;
						}
					?>
				</table>
			</td>
			<td class="ccbrightbody"></td>
		</tr>
		<tr class="ccbtablefooter">
	 		 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
	 	</tr>
		<tr>
 			 <td class="ccbcolspacer"></td>
			 <td></td>
			 <td></td>
 		</tr>
    <?php
        }  /* Main Loop */
       if( $ccbConfig->showboardsummary == 1) {
    ?>
		<tr>
	 		 <td class="ccbtopleft<?php echo $dispclr; ?>"></td>
             <td class="ccbtopmiddle<?php echo $dispclr; ?>">&nbsp;<b><?php echo JText::_('BOARD_STATISTICS'); ?></b></td>
			 <td class="ccbtopright<?php echo $dispclr; ?>"></td>
	 	</tr>
		<tr>
			<td class="ccbleftbody"></td>
            <td class="ccbboardsummary">
                <div class="graphleft">
                    <img src="<?php echo CCBOARD_ASSETS_URL; ?>/graph.png" />
                </div>
                <div class="graphright">
                    <?php
                        echo JText::_('CCB_TOTAL_CATEGORIES') . ':&nbsp;<b>' . $this->boardsummary->catcount . '</b>&nbsp;';
                        echo JText::_('CCB_TOTAL_FORUMS')  . ':&nbsp;<b>' . $this->boardsummary->forumcount . '</b>&nbsp;';

                        echo JText::_('CCB_TOTAL_THREADS') . ':&nbsp;<b>' . $this->boardsummary->topiccount . '</b>&nbsp;';
                        echo JText::_('CCB_TOTAL_POSTS') . ':&nbsp;<b>' . $this->boardsummary->postcount . '</b><br/>';

                        echo JText::_('CCB_TOTAL_USERS') . ':&nbsp;<b>' . $this->boardsummary->usercount . '</b>&nbsp;';
                        echo JText::_('CCB_LATEST_USERS') . ':&nbsp;';
                        foreach( $this->boardsummary->latestusers as $usr) {
                            echo '<a href="' . JRoute::_('index.php?option=com_ccboard&view=myprofile&cid=' . $usr->id . '&Itemid=' . $ccbConfig->itemid)  .  '">' . $usr->username . '</a>&nbsp;&nbsp;';
                        }
                    ?>
                </div>
            </td>
			<td class="ccbrightbody"></td>
		</tr>
		<tr class="ccbtablefooter">
	 		 <td class="ccbbotleft"></td>
			 <td class="ccbbotmiddle"></td>
			 <td class="ccbbotright"></td>
	 	</tr>
    <?php } ?>
	</table>
<?php
    if (!count($this->items)) {
			echo '<tr><td><p>'. JText::_('FORUMS_NOT_FOUND') .'</p></td></tr>';
	}
 ?>
</div>
<br/>
<?php echo CCBOARD_COPYRIGHT; ?>
<br clear="left" />
