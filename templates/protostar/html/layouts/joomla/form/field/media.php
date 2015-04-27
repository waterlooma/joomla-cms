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

// Load the modal behavior script.
JHtml::_('behavior.modal');

// Include jQuery
JHtml::_('jquery.framework');

JFactory::getDocument()->addStyleDeclaration(
	'
		function jInsertFieldValue(value, id) {
		var $ = jQuery.noConflict();
		var old_value = $("#" + id).val();
		if (old_value != value) {
			var $elem = $("#" + id);
			$elem.val(value);
			$elem.trigger("change");
			if (typeof($elem.get(0).onchange) === "function") {
				$elem.get(0).onchange();
			}
			jMediaRefreshPreview(id);
		}
	}

	function jMediaRefreshPreview(id) {
		var $ = jQuery.noConflict();
		var value = $("#" + id).val();
		var $img = $("#" + id + "_preview");
		if ($img.length) {
			if (value) {
				$img.attr("src", "' . JUri::root() . '" + value);
				$("#" + id + "_preview_empty").hide();
				$("#" + id + "_preview_img").show()
			} else {
				$img.attr("src", "")
				$("#" + id + "_preview_empty").show();
				$("#" + id + "_preview_img").hide();
			}
		}
	}

	function jMediaRefreshPreviewTip(tip)
	{
		var $ = jQuery.noConflict();
		var $tip = $(tip);
		var $img = $tip.find("img.media-preview");
		$tip.find("div.tip").css("max-width", "none");
		var id = $img.attr("id");
		id = id.substring(0, id.length - "_preview".length);
		jMediaRefreshPreview(id);
		$tip.show();
	}

	// JQuery for tooltip for INPUT showing whole image path
	function jMediaRefreshImgpathTip(tip)
	{
		var $ = jQuery.noConflict();
		var $tip = $(tip);
		$tip.css("max-width", "none");
		var $imgpath = $("#" + "' . $id . '").val();
		$("#TipImgpath").html($imgpath);
		if ($imgpath.length) {
		 $tip.show();
		} else {
		 $tip.hide();
		}
	}
	'
);
// Tooltip for INPUT showing whole image path
$options = array(
	'onShow' => 'jMediaRefreshImgpathTip',
);

JHtml::_('behavior.tooltip', '.hasTipImgpath', $options);

if (!empty($class))
{
	$class .= ' hasTipImgpath';
}
else
{
	$class = 'hasTipImgpath';
}

$attr = '';

$attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="input-small ' . $class . '"' : ' class="input-small"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';

// The text field.
echo '<div class="input-prepend input-append">';

// The Preview.
$showPreview = true;
$showAsTooltip = false;

switch ($preview)
{
	case 'no': // Deprecated parameter value
	case 'false':
	case 'none':
		$showPreview = false;
		break;

	case 'yes': // Deprecated parameter value
	case 'true':
	case 'show':
		break;
	case 'tooltip':
	default:
		$showAsTooltip = true;
		$options = array(
				'onShow' => 'jMediaRefreshPreviewTip',
		);
		JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
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
		$src = '';
	}

	$width = $previewWidth;
	$height = $previewHeight;
	$style = '';
	$style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
	$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';

	$imgattr = array(
		'id' => $id . '_preview',
		'class' => 'media-preview',
		'style' => $style,
	);

	$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
	$previewImg = '<div id="' . $id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
	$previewImgEmpty = '<div id="' . $id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
		. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

	if ($showAsTooltip)
	{
		echo '<div class="media-preview add-on">';
		$tooltip = $previewImgEmpty . $previewImg;
		$options = array(
			'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
					'text' => '<i class="icon-eye"></i>',
					'class' => 'hasTipPreview'
					);

		echo JHtml::tooltip($tooltip, $options);
		echo '</div>';
	}
	else
	{
		echo '<div class="media-preview add-on" style="height:auto">';
		echo ' ' . $previewImgEmpty;
		echo ' ' . $previewImg;
		echo '</div>';
	}
}

echo '	<input type="text" name="' . $name . '" id="' . $id . '" value="'
	. htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly"' . $attr . ' />';

?>
<a class="modal btn" title="<?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?>" href="
<?php echo ($readonly ? ''
		: ($link ? $link
		: 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author='
	. $authorField) . '&amp;fieldid=' . $id . '&amp;folder=' . $folder) . '"'
	. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}"'; ?>>
 <?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?></a><a class="btn hasTooltip" title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>" href="#" onclick="jInsertFieldValue('', '<?php echo $id; ?>'); return false;">
	<i class="icon-remove"></i></a>


</div>