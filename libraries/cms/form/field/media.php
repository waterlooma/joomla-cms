<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla CMS.
 * Provides a modal media selector including upload mechanism
 *
 * @since  1.6
 */
class JFormFieldMedia extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'Media';

	/**
	 * The initialised state of the document object.
	 *
	 * @var    boolean
	 * @since  1.6
	 */
	protected static $initialised = false;

	/**
	 * The authorField.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $authorField;

	/**
	 * The asset.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $asset;

	/**
	 * The link.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $link;

	/**
	 * Modal width.
	 *
	 * @var    integer
	 * @since  3.4.5
	 */
	protected $width;

	/**
	 * Modal height.
	 *
	 * @var    integer
	 * @since  3.4.5
	 */
	protected $height;

	/**
	 * The authorField.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $preview;

	/**
	 * The preview.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $directory;

	/**
	 * The previewWidth.
	 *
	 * @var    int
	 * @since  3.2
	 */
	protected $previewWidth;

	/**
	 * The previewHeight.
	 *
	 * @var    int
	 * @since  3.2
	 */
	protected $previewHeight;

	/**
	 * Layout to render
	 *
	 * @var  string
	 */
	protected $layout = 'joomla.form.field.media';
	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'authorField':
			case 'asset':
			case 'link':
			case 'width':
			case 'height':
			case 'preview':
			case 'directory':
			case 'previewWidth':
			case 'previewHeight':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'authorField':
			case 'asset':
			case 'link':
			case 'width':
			case 'height':
			case 'preview':
			case 'directory':
				$this->$name = (string) $value;
				break;

			case 'previewWidth':
			case 'previewHeight':
				$this->$name = (int) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see 	JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result == true)
		{
			$assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';

			$this->authorField   = $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
			$this->asset         = $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'];
			$this->link          = (string) $this->element['link'];
			$this->width  	     = isset($this->element['width']) ? (int) $this->element['width'] : 800;
			$this->height 	     = isset($this->element['height']) ? (int) $this->element['height'] : 500;
			$this->preview       = (string) $this->element['preview'];
			$this->directory     = (string) $this->element['directory'];
			$this->previewWidth  = isset($this->element['preview_width']) ? (int) $this->element['preview_width'] : 200;
			$this->previewHeight = isset($this->element['preview_height']) ? (int) $this->element['preview_height'] : 200;
		}

		return $result;
	}

	/**
	 * Method to get the field input markup for a media selector.
	 * Use attributes to identify specific created_by and asset_id fields
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		$asset = $this->asset;

		if ($asset == '')
		{
			$asset = JFactory::getApplication()->input->get('option');
		}

		if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
		{
			$this->folder = explode('/', $this->value);
			$this->folder = array_diff_assoc($this->folder, explode('/', JComponentHelper::getParams('com_media')->get('image_path', 'images')));
			array_pop($this->folder);
			$this->folder = implode('/', $this->folder);
		}
		elseif (file_exists(JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $this->directory))
		{
			$this->folder = $this->directory;
		}
		else
		{
			$this->folder = '';
		}

		$attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		// The text field.
		$html[] = '<div class="input-prepend input-append">';

		// The Preview.
		$showPreview = true;
		$showAsTooltip = false;

		switch ($this->preview)
		{
			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration('
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

		$html[] = '	<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly"' . $attr . ' data-basepath="'
			. JUri::root() . '"/>';

		if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
		{
			$folder = explode('/', $this->value);
			$folder = array_diff_assoc($folder, explode('/', JComponentHelper::getParams('com_media')->get('image_path', 'images')));
			array_pop($folder);
			$folder = implode('/', $folder);
		}
		');

		// The button.
		if ($this->disabled != true)
		{
			JHtml::_('bootstrap.tooltip');

			$html[] = '<a class="modal btn" title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '" href="'
				. ($this->readonly ? ''
				: ($this->link ? $this->link
					: 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author='
					. $this->form->getValue($this->authorField)) . '&amp;fieldid=' . $this->id . '&amp;folder=' . $folder) . '"'
				. ' rel="{handler: \'iframe\', size: {x: ' . $this->width . ', y: ' . $this->height . '}}">';
			$html[] = JText::_('JLIB_FORM_BUTTON_SELECT') . '</a><a class="btn hasTooltip" title="'
				. JText::_('JLIB_FORM_BUTTON_CLEAR') . '" href="#" onclick="';
			$html[] = 'jInsertFieldValue(\'\', \'' . $this->id . '\');';
			$html[] = 'return false;';
			$html[] = '">';
			$html[] = '<span class="icon-remove"></span></a>';
		}

			return
				JLayoutHelper::render($this->layout,
				array(

					'id'            => $this->id,
					'initialised'   => $initialised,
					'class'         => $this->class,
					'size'          => $this->size,
					'onchange'      => $this->onchange,
					'readonly'      => $this->readonly,
					'link'          => $this->link,
					'asset'         => $asset,
					'form'          => $this->form,
					'field'         => $this,
					'authorField'   => $this->authorField,
					'preview'       => $this->preview,
					'value'         => $this->value,
					'previewWidth'  => $this->previewWidth,
					'previewHeight' => $this->previewHeight,
					'folder'        => $this->folder,
					'disabled'      => $this->disabled,
					'name'          => $this->name
				)
			);
	}
}
