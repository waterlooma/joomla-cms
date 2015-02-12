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
				$img.attr("src", "<?php echo JUri::root(); ?>" + value);
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
		var $imgpath = $("#" + "<?php echo $id; ?>").val();
		$("#TipImgpath").html($imgpath);
		if ($imgpath.length) {
		 $tip.show();
		} else {
		 $tip.hide();
		}
	}
