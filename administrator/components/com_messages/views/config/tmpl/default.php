<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JFactory::getDocument()->addScriptDeclaration("
		Joomla.submitbutton = function(task)
		{
			if (task == 'config.cancel' || document.formvalidator.isValid(document.getElementById('config-form')))
			{
				Joomla.submitform(task, document.getElementById('config-form'));
			}
		};
");
?>
<form action="<?php echo JRoute::_('index.php?option=com_messages'); ?>" method="post" name="adminForm" id="message-form" class="form-validate form-horizontal">
	<fieldset>
		<div>
			<div style="display:inline-block; width:40%;">
				<button class="btn btn-success btn-block" type="submit" onclick="Joomla.submitform('config.save', this.form); window.parent.jQuery('#modal-cog').modal('hide');">
					<?php echo JText::_('JAPPLY'); ?></button>
			</div>

			<div style="display:inline-block; width:40%; float: right;">
			<button class="btn btn-default btn-block" type="submit" onclick="window.parent.jQuery('#modal-cog').modal('hide');">
				<?php echo JText::_('JCANCEL');?></button>
			</div>
		</div>
		<hr />
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('lock'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('lock'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('mail_on_new'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('mail_on_new'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('auto_purge'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('auto_purge'); ?>
			</div>
		</div>

		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
</form>
