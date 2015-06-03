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
 * @var mixed            $value Value of the field
 * @var string           $class CSS class to apply
 * @var integer          $size Size for the input element
 * @var mixed            $groups filtering groups (null means no filtering)
 * @var mixed            $exclude users to exclude from the list of users
 *
 */

$html = array();

$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' . $id
	. (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
	. (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '');

// Initialize some field attributes.
$attr = $class ? ' class="' . (string) $class . '"' : '';
$attr .= $size ? ' size="' . (int) $size . '"' : '';

// Initialize JavaScript field attributes.
$onchange = (string) $element['onchange'];

// Build the script.
$script = "
	function jSelectUser_" . $id . "(id, title) {
		var old_id = document.getElementById('" . $id . "_id').value;
		if (old_id != id) {
			document.getElementById('" . $id . "_id').value = id;
			document.getElementById('" . $id . "_name').value = title;
			" . $onchange . "
		}
		jQuery('#userModal').modal('hide');
	}
";

// Add the script to the document head.
JFactory::getDocument()->addScriptDeclaration($script);

// Load the current username if available.
$table = JTable::getInstance('user');

if (is_numeric($value))
{
	$table->load($value);
}
// Handle the special case for "current".
elseif (strtoupper($value) == 'CURRENT')
{
	$table->load(JFactory::getUser()->id);
}
else
{
	$table->name = JText::_('JLIB_FORM_SELECT_USER');
}
?>
<?php // Create a dummy text field with the user name. ?>
<div class="input-append">
	<input
		type="text" id="<?php echo $id; ?>_name"
		value="<?php echo  htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8'); ?>"
		readonly
		disabled="disabled" <?php echo $attr; ?> />
	<?php if ($field->readonly === false) : ?>
		<a href="#userModal" role="button" class="btn btn-primary" data-toggle="modal" title="<?php echo JText::_('JLIB_FORM_CHANGE_USER') ?>"><i class="icon-user"></i></a>
		<?php echo JHtml::_(
			'bootstrap.renderModal',
			'userModal',
			array(
				'url' => $link,
				'title' => JText::_('JLIB_FORM_CHANGE_USER'),
				'height' => '300px',
				'width' => '800px',
				'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
					. JText::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
			)
		); ?>
	<?php endif; ?>
</div>

<?php // Create the real field, hidden, that stored the user id. ?>
<input type="hidden" id="<?php echo $id; ?>_id" name="<?php echo $name; ?>" value="<?php echo (int) $value; ?>" />