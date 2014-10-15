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
		<?php echo $this->form->getField('lock')->getControlGroup();
			echo $this->form->getField('mail_on_new')->getControlGroup();
			echo $this->form->getField('auto_purge')->getControlGroup(); ?>
		</fieldset>
	<hr/>
	<div class="clearfix">
		<div class="btn-group pull-right">
			<button class="btn btn-success" type="submit" onclick="Joomla.submitform('config.save', this.form); window.parent.jQuery('#modal-cog').modal('hide');"><?php echo JText::_('JAPPLY'); ?></button>
		</div>
		<div class="btn-group pull-left">
			<button class="btn btn-default" type="submit" onclick="window.parent.jQuery('#modal-cog').modal('hide');"><?php echo JText::_('JCANCEL');?></button>
		</div>
	</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
</form>
