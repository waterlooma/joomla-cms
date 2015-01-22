<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

JHtml::_('bootstrap.modal');

// The button.
if ($disabled != true)
{
	JHtml::_('bootstrap.tooltip');
}

$attr = '';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="input-small ' . $class . '"' : ' class="input-small"';
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
		. $form->getValue($authorField)) . '&amp;fieldid=' . $id . '&amp;folder=' . $folder) . '"';

// Render the modal
echo JHtmlBootstrap::renderModal(
						'imageModal_'. $id,
						array(
							'url' => $url,
							'title' => JText::_('JLIB_FORM_CHANGE_IMAGE'),
							'width' => '800px',
							'height' => '565px'
							)
						);

/*
 * Add javascript for:
 * the proxy jModalClose to close the modal
 * the image/text to replace the popover
 * initialize popover
 */
JFactory::getDocument()->addScriptDeclaration('
		if(typeof jModalClose == "function"){
			var fnCode = jModalClose.toString() ;
			fnCode = fnCode.replace(/\}$/, "jQuery(\"#imageModal_' . $id . '\").modal(\"hide\");\n}");
					window.eval(fnCode);
				} else {
			function jModalClose() {
				jQuery("#imageModal_' . $id . '").modal("hide");
			}
		}

		function jInsertFieldValue(value, id) {
			var $ = jQuery.noConflict();
			var old_value = $("#" + id, parent.document).val();
			if (old_value != value) {
				var $elem = $("#" + id, parent.document);
				$elem.val(value);
				$elem.trigger("change");
				if (typeof($elem.get(0).onchange) === "function") {
					$elem.get(0).onchange();
				}
				jMediaRefreshPopover(id);
			}
		}

		function jMediaRefreshPopover(id) {
			var $ = jQuery.noConflict();
			var some = $("#" + id, parent.document).val();
			var popover = jQuery("#media-preview", parent.document).data("popover");
			var imgPreview = new Image(' .$previewWidth .', ' .$previewHeight .');
			if (some == "' . JUri::root() . '" || some == "") {
				popover.options.content = "' . JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '";
			} else {
				imgPreview.src = "' . JUri::root() . '" + some ;
				popover.options.content = imgPreview;
			}
		}

		jQuery(document).ready(function(){
			var imagePreview = new Image(' .$previewWidth .', ' .$previewHeight .');
			imagePreview.src = "' . $src . '";
			console.log(imagePreview.src);
			jQuery("#media-preview").popover({trigger: "hover", placement: "right", content: imagePreview, html: true});
		});
');
?>
<?php if ($showPreview) : ?>
<div class="input-prepend input-append">
	<div class="media-preview add-on" style="padding: 0; border: 0;">
		<span id="media-preview" rel="popover" class="btn" title="<?php echo
		JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'); ?>" data-content="" data-original-title="<?php
		echo JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'); ?>" data-trigger="hover">
		<i class="icon-eye"></i>
		</span>
	</div>
<? endif; ?>
<?php if (!$showPreview) : ?>
<div class="input-append">
<? endif; ?>
	<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" readonly="readonly"<?php echo $attr; ?>/>

<?php if ($disabled != true) : ?>
	<a href="#imageModal_<?php echo $id; ?>" role="button" class="btn" data-toggle="modal"><?php echo JText::_("JLIB_FORM_BUTTON_SELECT"); ?></a>

	<a class="btn hasTooltip" title="<?php echo JText::_("JLIB_FORM_BUTTON_CLEAR"); ?>" href="#" onclick="jInsertFieldValue('', '<?php echo $id; ?>');">
		<i class="icon-remove"></i>
	</a>
<?php endif; ?>
</div>