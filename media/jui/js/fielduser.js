/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
function jSelectUser(id, title, field) {
	var old_id = document.getElementById(field + '_id').value;
	if (old_id != id) {
		document.getElementById(field + '_id').value = id;
		document.getElementById(field + '_name').value = title;
		eval(document.getElementById(field + '_id').getAttribute('data-onchange'));
	}
	jQuery('#userModal').modal('hide');
}
