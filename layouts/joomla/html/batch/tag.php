<?php

defined('JPATH_PLATFORM') or die;


JHtml::_('bootstrap.tooltip', '.modalTooltip', array('container' => '.modal-body'));

// Create the batch selector to tag items on a selection list.
?>
<label id="batch-tag-lbl" for="batch-tag-id" class="modalTooltip" title="<?php
echo JHtml::tooltipText('JLIB_HTML_BATCH_TAG_LABEL', 'JLIB_HTML_BATCH_TAG_LABEL_DESC'); ?>">
<?php echo JText::_('JLIB_HTML_BATCH_TAG_LABEL'); ?>
</label>
<select name="batch[tag]" class="inputbox" id="batch-tag-id">
	<option value=""><?php echo JText::_('JLIB_HTML_BATCH_TAG_NOCHANGE'); ?></option>
	<?php echo JHtml::_('select.options', JHtml::_('tag.tags', array('filter.published' => array(1))), 'value', 'text'); ?>
</select>
