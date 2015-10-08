tinymce.PluginManager.add('jdragdrop', function(editor) {

	// Reset the drop area border
	tinyMCE.DOM.bind(document, 'dragleave', function(e) {
		e.stopPropagation();
		e.preventDefault();
		tinyMCE.activeEditor.contentAreaContainer.style.borderWidth='';

		return false;
	});

	// The upload logic
	function UploadFile(file) {
		var errorMsgs = [];
		var fd = new FormData();
		fd.append('Filedata', file);
		fd.append('folder', mediaUploadPath);

		jQuery.ajax({
			url: uploadUri,
			type: 'post',
			enctype: 'multipart/form-data',
			data: fd,
			cache: false,
			contentType: false,
			processData: false,
			xhr: function() {
				var myXhr = jQuery.ajaxSettings.xhr();
				return myXhr;
			},
			success: function(data, myXhr){
				if (data.status == 0) {
						tinyMCE.activeEditor.windowManager.alert(data.message + ': ' + setCustomDir + data.location);
				}
				if (data.status == 1) {
					// Create the image tag
					var newNode = tinyMCE.activeEditor.getDoc().createElement ('img');
					newNode.src= setCustomDir + data.location;
					tinyMCE.activeEditor.execCommand('mceInsertContent', false, newNode.outerHTML);
				}
				setTimeout(function(){ jQuery('#jloader').remove(); }, 200);
			},
			error: function(myXhr, errorThrown){
				setTimeout(function(){ jQuery('#jloader').remove(); }, 100);
			}
		});
	}

	// Listers for drag and drop
	if (typeof FormData != 'undefined'){

		// Fix for Chrome
		editor.on('dragenter', function(e) {
			e.stopPropagation();

			return false;
		});


		// Notify user when file is over the drop area
		editor.on('dragover', function(e) {
			e.preventDefault();
			tinyMCE.activeEditor.contentAreaContainer.style.borderColor = 'green'
			tinyMCE.activeEditor.contentAreaContainer.style.borderStyle = 'dashed'
			tinyMCE.activeEditor.contentAreaContainer.style.borderWidth = '5px';

			return false;
		});

		// Logic for the dropped file
		editor.on('drop', function(e) {

			// We override only for files
			if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
				for (var i = 0, f; f = e.dataTransfer.files[i]; i++) {

					if (f.name.match(/\.(jpg|jpeg|png|gif|bmp)$/)) {

						editor.contentAreaContainer.style.borderWidth = '';
						jQuery('<div id=\"jloader\" />').css({
							position  : 'absolute',
							width     : '100%',
							height    : '100%',
							left      : 0,
							top       : 0,
							opacity   : 0.55,
							zIndex    : 1000000,
							background: 'url(/media/jui/images/ajax-loader.gif) #fff no-repeat 50% 50%'
						}).appendTo(jQuery('.editor').css('position', 'relative'));

						editor.contentAreaContainer.style.borderWidth = '';

						// Upload the file(s)
						UploadFile(f);
						e.preventDefault();
					}
				}
			}
			editor.contentAreaContainer.style.borderWidth = '';
		});
	} else {
		Joomla.renderMessages({'error': [Joomla.JText._("PLG_TINY_ERR_UNSUPPORTEDBROWSER")]});
		editor.on('drop', function(e) {
			e.preventDefault();

			return false;
		});
	}
});