<?php
/**
 * @package     Joomla.Administrator
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
 * @var string           $id The DOM id of the element
 * @var string           $name The name of the field
 * @var boolean          $required The required attribute
 * @var mixed            $value The value of the field (user id)
 * @var string           $class The CSS class to apply
 * @var integer          $size The ize for the input element
 * @var mixed            $groups The filtering groups (null means no filtering)
 * @var mixed            $exclude The users to exclude from the list of users
 * @var string           $onchange The script for on change event
 * @var string           $userName The user name
 * @var boolean          $readOnly Check for field read only attribute
 */

// Set the link for the user selection page
$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' . $id
	. (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
	. (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '');

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
		disabled="disabled"
		<?php echo $class ? ' class="' . (string) $class . '"' : ''; ?>
		<?php echo $size ? ' size="' . (int) $size . '"' : ''; ?>/>
	<?php if (!$readOnly) : ?>
		<a href="#userModal" role="button" class="btn btn-primary" data-toggle="modal" title="<?php echo JText::_('JLIB_FORM_CHANGE_USER') ?>"><i class="icon-user"></i></a>
		<?php echo JHtml::_(
			'bootstrap.renderModal',
			'userModal',
			array(
				'url'    => $link,
				'title'  => JText::_('JLIB_FORM_CHANGE_USER'),
				'height' => '300px',
				'width'  => '800px',
				'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
					. JText::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
			)
		); ?>
	<?php endif; ?>
</div>

<?php // Create the real field, hidden, that stored the user id. ?>
<input type="hidden" id="<?php echo $id; ?>_id" name="<?php echo $name; ?>" value="<?php echo (int) $value; ?>" />