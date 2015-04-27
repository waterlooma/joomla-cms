<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Layout variables
 * ---------------------
 *
 * @var  string   $asset The asset text
 * @var  string   $authorField The label text
 * @var  integer  $authorId The author id
 * @var  string   $class The class text
 * @var  boolean  $disabled True if field is disabled
 * @var  string   $folder The folder text
 * @var  string   $id The label text
 * @var  string   $link The link text
 * @var  string   $name The name text
 * @var  string   $preview The preview image relative path
 * @var  integer  $previewHeight The image preview height
 * @var  integer  $previewWidth The image preview width
 * @var  string   $onchange  The onchange text
 * @var  boolean  $readonly True if field is readonly
 * @var  integer  $size The size text
 * @var  string   $value The value text
 */
extract($displayData);

// The button.
if ($disabled != true)
{
	JHtml::_('bootstrap.tooltip');
}

$attr = '';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="input-small hasTooltip ' . $class . '"' : ' class="input-small hasTooltip"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';

switch ($preview)
{
	case 'no': // Deprecated parameter value
	case 'false':
	case 'none':
		$showPreview = false;
		$showAsTooltip = false;
		break;
	case 'yes': // Deprecated parameter value
	case 'true':
	case 'show':
		break;
	case 'tooltip':
	default:
		$showPreview = true;
		$showAsTooltip = true;
		break;
}

// Pre fill the contents of the popover
if ($showPreview)
{
	if ($value && file_exists(JPATH_ROOT . '/' . $value))
	{
		$src = JUri::root() . $value;
	}
	else
	{
		$src = JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY');
	}
}

// The url for the modal
$url = ($readonly ? ''
		: ($link ? $link
		: 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author='
		. $authorId) . '&amp;fieldid=' . $id . '&amp;folder=' . $folder) . '"';

// Render the modal
echo JHtmlBootstrap::renderModal(
						'imageModal_'. $id, array(
							'url' => $url,
							'title' => JText::_('JLIB_FORM_CHANGE_IMAGE'),
							'width' => '800px',
							'height' => '565px',
							'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">' . JText::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>
    <button id="btn_' . $id . '" class="btn btn-success" data-dismiss="modal" aria-hidden="true">' . JText::_("JLIB_FORM_CHANGE_IMAGE") . '</button>')
						);

/*
 * Add javascript for:
 * proxy jModalClose to close the modal
 * initialize popover
 */
JFactory::getDocument()->addScriptDeclaration('
	jQuery(document).ready(function(){
		// Initialize the preview image button
		if ("' . $src . '" === "' . JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '") {
			imagePreview = "' . JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '";
		} else {
			var imagePreview = new Image(' . $previewWidth . ', ' .$previewHeight . ');
			imagePreview.src = "' . $src . '";
		}
		jQuery("#media_preview_' . $id . '").popover({trigger: "hover", placement: "right", content: imagePreview, html: true});

		// Initialize the tooltip
		jQuery("#' . $id . '").tooltip(\'destroy\');
		var imgValue = jQuery("#' . $id . '").val();
		jQuery("#' . $id . '").tooltip({\'placement\':\'top\', \'title\': imgValue});

		// Save and close modal
		jQuery("#btn_' . $id . '").on("click", function() {
			value_' . $id . ' = jQuery("#imageModal_' . $id . ' iframe").contents().find("#f_url").val();
			jQuery("#' . $id . '").val(value_' . $id . ').trigger("change");

			// Reset tooltip and preview
			var imgValue = jQuery("#' . $id . '").val();
			var popover = jQuery("#media_preview_' . $id . '").data("popover");
			var imgPreview = new Image(' . $previewWidth . ', ' . $previewHeight . ');
			if (imgValue == "") {
				popover.options.content = "' . JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '";
				jQuery("#' . $id . '").tooltip("destroy");
			} else {
				imgPreview.src = "' . JUri::root() . '" + imgValue ;
				popover.options.content = imgPreview;
				jQuery("#' . $id . '").tooltip("destroy").tooltip({"placement":"top", "title": imgValue});
			}
		});
	});

	// Clear button
	function clear_' . $id . '(){
		jQuery("#' . $id .'").val("");
		jQuery("#media_preview_' . $id . '").data("popover").options.content = "' . JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '";
		jQuery("#' . $id . '").tooltip("destroy");
		return false;
	};
');
?>
<?php if ($showPreview) : ?>
<div class="input-prepend input-append" id="media_field_<?php echo $id; ?>">
	<span id="media_preview_<?php echo $id; ?>" rel="popover" class="add-on" title="<?php echo
	JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'); ?>" data-content="" data-original-title="<?php
	echo JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'); ?>" data-trigger="hover">
	<i class="icon-eye"></i>
	</span>
<?php endif; ?>
<?php if (!$showPreview) : ?>
<div class="input-append" id="media_field_<?php echo $id; ?>">
<?php endif; ?>
	<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" readonly="readonly"<?php echo $attr; ?>/>

<?php if ($disabled != true) : ?>
	<a href="#imageModal_<?php echo $id; ?>" role="button" class="btn add-on" data-toggle="modal"><?php echo JText::_("JLIB_FORM_BUTTON_SELECT"); ?></a>
	<a class="btn icon-remove hasTooltip add-on" title="<?php echo JText::_("JLIB_FORM_BUTTON_CLEAR"); ?>" href="#" onclick="clear_<?php echo $id; ?>();"></a>
<?php endif; ?>
</div>
