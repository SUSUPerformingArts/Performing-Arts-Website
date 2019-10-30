<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<table class="moduletable<?php echo $params->get('moduleclass_sfx') ?>" cellspacing=15>
	<tr>
	<?php foreach ($list as $item) : ?>
		<td>
			<?php modRandArticlesHelper::renderItem($item, $params, $access); ?>
		</td>
	<?php endforeach; ?>
	</tr>
</table>