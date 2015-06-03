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
 * @var  string   $itemId The type id number
 * @var  string   $title The link text
 * @var  string   $height The height of the iframe
 * @var  string   $width The width of the iframe
 * @var  string   $typeAlias The component type
 */
extract($displayData);

$link = 'index.php?option=com_contenthistory&amp;view=history&amp;layout=modal&amp;tmpl=component&amp;item_id='
	. (int) $itemId . '&amp;type_id=' . $typeId . '&amp;type_alias='
	. $typeAlias . '&amp;' . JSession::getFormToken() . '=1';

echo JHtml::_(
	'bootstrap.renderModal',
	'versionsModal',
	array(
		'url' => $link,
		'title' => JText::_('COM_CONTENTHISTORY_MODAL_TITLE'),
		'height' => '300px',
		'width' => '800px',
		'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
			. JText::_("JTOOLBAR_CLOSE") . '</button>'
	)
);
?>
<button onclick="jQuery('#versionsModal').modal('show')" class="btn btn-small" data-toggle="modal" title="<?php echo $title; ?>">
	<span class="icon-32-restore"></span><?php echo $title; ?>
</button>

