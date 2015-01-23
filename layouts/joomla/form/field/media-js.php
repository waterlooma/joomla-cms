<?php
/**
 * @package    Joomla.Form
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

extract($displayData);
?>
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
		var popover = jQuery("#media_preview_" + id, parent.document).data("popover");
		var imgPreview = new Image(<?php echo $previewWidth; ?>, <?php echo $previewHeight; ?>);
		if (some == "") {
			popover.options.content = "<?php echo JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY'); ?>";
		} else {
			imgPreview.src = "<?php echo JUri::root(); ?>" + some ;
			popover.options.content = imgPreview;
		}
	}
