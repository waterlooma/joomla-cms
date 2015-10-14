<?php

defined('JPATH_PLATFORM') or die;

// Create the batch selector to change an access level on a selection list.
JHtml::_('bootstrap.tooltip', '.modalTooltip', array('container' => '.modal-body'));

?>

<label id="batch-access-lbl" for="batch-access" class="modalTooltip" title="<?php echo JHtml::tooltipText('JLIB_HTML_BATCH_ACCESS_LABEL', 'JLIB_HTML_BATCH_ACCESS_LABEL_DESC'); ?>">
	<?php echo JText::_('JLIB_HTML_BATCH_ACCESS_LABEL'); ?></label>
	<?php echo JHtml::_(
		'access.assetgrouplist',
		'batch[assetgroup_id]', '',
		'class="inputbox"',
		array(
			'title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),
			'id' => 'batch-access'
		)
	); ?>
