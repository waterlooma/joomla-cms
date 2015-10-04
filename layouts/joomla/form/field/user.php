<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

/**
 * Layout variables
 * ------------------
 *
 * @var string           $id DOM id of the element
 * @var SimpleXMLElement $element The object of the <field /> XML element that describes the form field.
 * @var JFormField       $field Object to access to the field properties
 * @var string           $name Name of the field to display
 * @var boolean          $required Is this field required?
 * @var mixed            $value Value of the field (user id)
 * @var string           $class CSS class to apply
 * @var integer          $size Size for the input element
 * @var mixed            $groups filtering groups (null means no filtering)
 * @var mixed            $exclude users to exclude from the list of users
 * @var string           $onchange The script for on change event
 * @var string           $userName The user name
 * @var boolean          $readOnly Check for field read only attribute
 */

$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' . $id
	. (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
	. (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '');

// Load the modal behavior script.
JHtml::_('behavior.modal', 'a.modal_' . $id);

// Add the script to the document head.
JFactory::getDocument()->addScriptDeclaration(
	"
	function jSelectUser_" . $id . "(id, title) {
		var old_id = document.getElementById('" . $id . "_id').value;
		if (old_id != id) {
			document.getElementById('" . $id . "_id').value = id;
			document.getElementById('" . $id . "_name').value = title;
			" . $onchange . "
		}
		jQuery('#userModal').modal('hide');
	}
	"
);
?>
<?php // Create a dummy text field with the user name. ?>
<div class="input-append">
	<input
		type="text" id="<?php echo $id; ?>_name"
		value="<?php echo  htmlspecialchars($userName, ENT_COMPAT, 'UTF-8'); ?>"
		readonly
		disabled="disabled" <?php echo $attr; ?> />
	<?php if (!$readOnly) : ?>
		<a class="btn btn-primary modal_<?php echo $id; ?>" title="<?php echo JText::_('JLIB_FORM_CHANGE_USER'); ?>" href="<?php echo $link; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
			<span class="icon-user"></span>
		</a>
	<?php endif; ?>
</div>

<?php // Create the real field, hidden, that stored the user id. ?>
<input type="hidden" id="<?php echo $id; ?>_id" name="<?php echo $name; ?>" value="<?php echo (int) $value; ?>" />