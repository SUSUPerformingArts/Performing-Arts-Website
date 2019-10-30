<?php
/**
 * @version		$Id: default.php 110 2009-04-26 07:59:21Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined('_JEXEC') or die('Restricted access');
?>
<table width="100%" border="0">
	<tr>
		<td width="55%" valign="top">
			<div id="cpanel">
				<?php echo $this->addIcon('config.png','general', JText::_('CCB_MENU_GENERAL'));?>
				<?php echo $this->addIcon('category.png','categories', JText::_('CCB_MENU_CATEGORIES'));?>
				<?php echo $this->addIcon('forums.png','forums', JText::_('CCB_MENU_FORUMS'));?>
				<?php echo $this->addIcon('users.png','users', JText::_('CCB_MENU_PROFILES'));?>
				<?php echo $this->addIcon('ranks.png','ranks', JText::_('CCB_MENU_RANKS'));?>
				<?php echo $this->addIcon('editcss.png','editcss', JText::_('CCB_MENU_CSS'));?>
				<?php echo $this->addIcon('tools.png','tools', JText::_('CCB_MENU_TOOLS'));?>
				<?php echo $this->addIcon('about.png','about', JText::_('CCB_MENU_ABOUT'));?>
			</div>
		</td>
		<td width="45%" valign="top">
			<?php
				echo $this->pane->startPane( 'stat-pane' );
				echo $this->pane->startPanel( JText::_('CCB_WELCOME_TO_CCBOARD'), 'welcome' );
			?>
			<table class="adminlist">
				<tr>
					<td>
						<p class="ccbaboutdesc">
							<strong><?php echo JText::_('CCB_WELCOME_NOTE');?></strong>
						</p>
						<p class="ccbaboutdesc">
							If you require professional support just head on to the forums at
							<a href="http://codeclassic.org/community.html" target="_blank">
								http://codeclassic.org/community.html
							</a>
							<br/><br/>For How-To, you can browse through the documentations at
							<a href="http://codeclassic.org/the-howto.html" target="_blank">
								http://codeclassic.org/the-howto.html
							</a>
						</p>
						<p class="ccbaboutdesc">
							If you found any bugs, please post at http://codeclassic.org/community.html
						</p>
					</td>
				</tr>
			</table>
			<?php
				echo $this->pane->endPanel();
				echo $this->pane->startPanel( JText::_('CCB_BOARD_STATISTICS') , 'board' );
			?>
				<table class="adminlist">
					<tr>
						<td class="ccbgeneraldesc">
							<?php echo JText::_( 'CCB_TOTAL_USERS' ).': '; ?>
						</td>
						<td align="center" class="ccbgeneraldesc">
							<strong><?php echo $this->ccbitems->totalusers; ?></strong>
						</td>
					</tr>
					<tr>
						<td class="ccbgeneraldesc">
							<?php echo JText::_( 'CCB_TOTAL_FORUMS' ).': '; ?>
						</td>
						<td align="center" class="ccbgeneraldesc">
							<strong><?php echo $this->ccbitems->totalforums; ?></strong>
						</td>
					</tr>
					<tr>
						<td class="ccbgeneraldesc">
							<?php echo JText::_( 'CCB_TOTAL_TOPICS' ).': '; ?>
						</td>
						<td align="center" class="ccbgeneraldesc">
							<strong><?php echo $this->ccbitems->totaltopics; ?></strong>
						</td>
					</tr>
					<tr>
						<td class="ccbgeneraldesc">
							<?php echo JText::_( 'CCB_TOTAL_POSTS' ).': '; ?>
						</td>
						<td align="center" class="ccbgeneraldesc">
							<strong><?php echo $this->ccbitems->totalposts; ?></strong>
						</td>
					</tr>
				</table>
			<?php
				echo $this->pane->endPanel();
				echo $this->pane->endPane();
			?>
		</td>
	</tr>
</table>
<?php echo CCBOARD_COPYRIGHT; ?>


