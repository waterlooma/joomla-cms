<?php

defined('JPATH_PLATFORM') or die;

extract($displayData);

JHtml::_('bootstrap.tooltip', '.modalTooltip', array('container' => '.modal-body'));

$optionNo = '';

if ($noUser)
{
	$optionNo = '<option value="0">' . JText::_('JLIB_HTML_BATCH_USER_NOUSER') . '</option>';
}

// Create the batch selector to select a user on a selection list.
?>
<label id="batch-user-lbl" for="batch-user" class="modalTooltip" title="<?php
echo JHtml::tooltipText('JLIB_HTML_BATCH_USER_LABEL', 'JLIB_HTML_BATCH_USER_LABEL_DESC'); ?>">
	<?php echo JText::_('JLIB_HTML_BATCH_USER_LABEL'); ?>
</label>
<select name="batch[user_id]" class="inputbox" id="batch-user-id">
	<option value=""><?php echo JText::_('JLIB_HTML_BATCH_USER_NOCHANGE'); ?></option>
	<?php echo $optionNo; ?>
	<?php echo JHtml::_('select.options', JHtml::_('user.userlist'), 'value', 'text'); ?>
</select>
