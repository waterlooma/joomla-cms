function initializeMedia(path, empty, previewWidth, previewHeight, source, fieldId) {
	// Initialize the preview image button
	if (source == empty) {
		imagePreview = empty;
	} else {
		var imagePreview = new Image(previewWidth, previewHeight);
		imagePreview.src = source;
	}
	jQuery("#media_preview_" + fieldId).popover({
		trigger  : "hover",
		placement: "right",
		content  : imagePreview,
		html     : true
	});

	// Initialize the tooltip
	jQuery("#" + fieldId).tooltip('destroy');
	var imgValue = jQuery("#" + fieldId).val();
	jQuery("#" + fieldId).tooltip({'placement': 'top', 'title': imgValue});

	// Save and close modal
	jQuery("#btn_" + fieldId).on("click", function () {
		jQuery("#" + fieldId).val(jQuery("#imageModal_" + fieldId + " iframe").contents().find("#f_url").val()).trigger("change");

		// Reset tooltip and preview
		var imgValue = jQuery("#" + fieldId).val();
		var popover = jQuery("#media_preview_" + fieldId).data("popover");
		var imgPreview = new Image(previewWidth, previewHeight);
		if (imgValue == "") {
			popover.options.content = empty;
			jQuery("#" + fieldId).tooltip("destroy");
		} else {
			imgPreview.src = path + imgValue;
			popover.options.content = imgPreview;
			jQuery("#" + fieldId).tooltip("destroy").tooltip({"placement": "top", "title": imgValue});
		}
	});
}

// Clear button
function clearMediaInput(fieldId, empty){
	jQuery("#" + fieldId).val("");
	jQuery("#media_preview_" + fieldId).data("popover").options.content = empty;
	jQuery("#" + fieldId).tooltip("destroy");
	return false;
}