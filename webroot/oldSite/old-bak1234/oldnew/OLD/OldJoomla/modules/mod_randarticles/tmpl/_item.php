<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div>
	<span class="contentheading" width="100%">
<?php if ($params->get('item_title')) : ?>
	<?php if ($params->get('link_titles') && $item->linkOn != '') : ?>
		<a href="<?php echo $item->linkOn;?>" class="contentpagetitle">
			<?php echo $item->title;?></a>
	<?php else : ?>
		<?php echo $item->title; ?>
	<?php endif; ?>
<?php endif; ?>
	</span>
	<span>
	<?php if ($item->image!='') { echo "<br />";
	if ($params->get('link_titles') && $item->linkOn != '') : ?>
		<a href="<?php echo $item->linkOn;?>" class="contentpagetitle">
			<?php echo $item->image;?></a>
	<?php else : ?>
		<?php echo $item->image; ?>
	<?php endif; }?>	
		<br />
		<?php echo $item->text; ?>
		<br />
		<?php if ($params->get('readmore') && $params->get('intro')) { ?>
		<a href="<?php echo $item->linkOn;?>">
			<?php echo $item->linkText;?></a>
		<?php } ?>
</span>
</div>
