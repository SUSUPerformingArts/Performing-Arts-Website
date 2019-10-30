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
<script language="javascript" type="text/javascript">
function setgood() {
	// TODO: Put setGood back
	return false;
}
function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'cancelCat') {
        submitform( pressbutton );
        return;
    }

    // do field validation
    if (form.cat_name.value.length == 0) {
        alert( "<?php echo JText::_( 'THE_CATEGORY_CAN_NOT_BE_BLANK', true ); ?>" );
    } else {
        submitform( pressbutton );
    }
}
</script>
<form action="" method="post" name="adminForm" id="adminForm" onSubmit="return setgood();">
<div style="width:100%;float:left;">
<fieldset class="adminform">
    <legend><?php echo JText::_( 'CCB_DETAILS' ); ?></legend>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="cat_name">
                    <?php echo JText::_( 'CCB_CATEGORY' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="cat_name" id="cat_name" size="100" maxlength="100" value="<?php echo isset($this->item->cat_name)?$this->item->cat_name:'';?>" />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" valign="top">
                <label for="ordering">
                    <?php echo JText::_('CCB_ORDER'); ?>:
                </label>
            </td>
            <td>
                <?php echo $this->ordering; ?>
            </td>
        </tr>
    </table>
</fieldset>
</div>
<?php echo CCBOARD_COPYRIGHT; ?>
<input type="hidden" name="option" value="com_ccboard" />
<input type="hidden" name="id" value="<?php echo isset($this->item->id)?$this->item->id:''; ?>" />
<input type="hidden" name="task" value="" />
</form>
