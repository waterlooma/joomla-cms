<?php
/**
 * @package    Joomla.Form
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
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
?>
	function jInsertFieldValue(value, id) {
		var $ = jQuery.noConflict();
		var old_value = $("#" + id, parent.document).val();
		if (old_value != value) {
			var $elem = $("#" + id, parent.document);
			$elem.val(value);
			$elem.trigger("change");
			jMediaRefreshPopover(id);
		}
	}

	function jMediaRefreshPopover(id) {
		var $ = jQuery.noConflict();
		var some = $("#" + id, parent.document).val();
		var popover = jQuery("#media_preview_" + id, parent.document).data("popover");
		var imgPreview = new Image(<?php echo $previewWidth; ?>, <?php echo $previewHeight; ?>);
		if (some == "") {
			popover.options.content = "<?php echo JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY'); ?>";
			// Reset tooltip
			$("#" + id, parent.document).tooltip('destroy');
		} else {
			imgPreview.src = "<?php echo JUri::root(); ?>" + some ;
			popover.options.content = imgPreview;
			// Reset tooltip
			$("#" + id, parent.document).tooltip('destroy');
			$("#" + id, parent.document).tooltip({'placement':'top', 'title': some});
			$("#" + id, parent.document).tooltip('show');

		}
	}
