<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

JHtml::_('bootstrap.modal');

echo JHtmlBootstrap::renderModal(
		'versionsModal',
		array(
			'url' => $link,
			'title' => $displayData['label'],
			'height' => '300px',
			'width' => '800px',
			'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
				. JText::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
		)
	);

?>
<button onclick="jQuery('#versionsModal').modal('show')" class="btn" data-toggle="modal" title="<?php echo $displayData['label']; ?>">
	<span class="icon-archive"></span><?php echo $displayData['label']; ?>
</button>