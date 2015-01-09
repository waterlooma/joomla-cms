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

// Add the proxy jModalClose for closing the modal
JFactory::getDocument()->addScriptDeclaration('
if(typeof jModalClose == "function"){
	var fnCode = jModalClose.toString() ;
	fnCode = fnCode.replace(/\}$/, "jQuery(\"#imageModal' . $id . '\").modal(\"hide\");\n}");
			window.eval(fnCode);
		} else {
	function jModalClose() {
		jQuery("#imageModal' . $id . '").modal("hide");
	}
}
');

$attr = '';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="input-small ' . $class . '"' : ' class="input-small"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';


// The Preview.
$showPreview = true;
$showAsTooltip = false;

$url = ($readonly ? ''
		: ($link ? $link
		: 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author='
		. $form->getValue($authorField)) . '&amp;fieldid=' . $id . '&amp;folder=' . $folder) . '"';

echo JHtmlBootstrap::renderModal(
						'imageModal'. $id,
						array(
							'url' => $url,
							'title' => JText::_('JLIB_FORM_CHANGE_IMAGE'),
							'width' => '800px',
							'height' => '565px'
							)
						);

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

	echo '<div class="input-prepend input-append">';
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

?>

	<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" readonly="readonly"<?php echo $attr; ?>/>

<?php if ($disabled != true) : ?>
	<a href="#imageModal<?php echo $id; ?>" role="button" class="btn" data-toggle="modal"><?php echo JText::_("JLIB_FORM_BUTTON_SELECT"); ?></a>

	<a class="btn hasTooltip" title="<?php echo JText::_("JLIB_FORM_BUTTON_CLEAR"); ?>" href="#" onclick="jInsertFieldValue('', '<?php echo $id; ?>'); return false; ">
		<i class="icon-remove"></i>
	</a>
<?php endif; ?>
</div>