<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors.codemirror
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * CodeMirror Editor Plugin.
 *
 * @since  1.6
 */
class PlgEditorCodemirror extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  12.3
	 */
	protected $autoloadLanguage = true;

	/**
	 * Mapping of syntax to CodeMirror modes.
	 *
	 * @var array
	 */
	protected $modeAlias = array(
			'html' => 'htmlmixed',
			'ini'  => 'properties'
		);

	/**
	 * Initialises the Editor.
	 *
	 * @return  void
	 */
	public function onInit()
	{
		static $done = false;

		// Do this only once.
		if ($done)
		{
			return;
		}

		$done = true;

		// Most likely need this later
		$doc = JFactory::getDocument();

		// Codemirror shall have its own group of plugins to modify and extend its behavior
		$result = JPluginHelper::importPlugin('editors_codemirror');
		$dispatcher	= JEventDispatcher::getInstance();

		// At this point, params can be modified by a plugin before going to the layout renderer.
		$dispatcher->trigger('onCodeMirrorBeforeInit', array(&$this->params));

		$basePath = $this->params->get('basePath', 'media/editors/codemirror/');
		$modePath = $this->params->get('modePath', 'media/editors/codemirror/mode/%N/%N');

		JHtml::_('script', $basePath . 'lib/codemirror.min.js', false, false, false, false, true);
		JHtml::_('script', $basePath . 'lib/addons.min.js', false, false, false, false, true);
		JHtml::_('stylesheet', $basePath . 'lib/codemirror.min.css', false, false, false, false, true);
		JHtml::_('stylesheet', $basePath . 'lib/addons.min.css', false, false, false, false, true);

		$fskeys          = $this->params->get('fullScreenMod', array());
		$fskeys[]        = $this->params->get('fullScreen', 'F10');
		$fullScreenCombo = implode('-', $fskeys);

		JFactory::getDocument()->addScriptDeclaration(
		"
		fsCombo = " . json_encode($fullScreenCombo) . ";
		modPath = " . json_encode(JUri::root(true) . '/' . $modePath . (JDEBUG ? '.js' : '.min.js')) .";
		"
		);

		$font = $this->params->get('fontFamily', 0);
		$fontInfo = $this->getFontInfo($font);

		if (isset($fontInfo))
		{
			if (isset($fontInfo->url))
			{
				$doc->addStylesheet($fontInfo->url);
			}

			if (isset($fontInfo->css))
			{
				$fontFamily = $fontInfo->css . '!important';
			}
		}

		$fontFamily = isset($fontFamily) ? $fontFamily : 'monospace';
		$fontSize   = $this->params->get('fontSize', 13) . 'px;';
		$lineHeight = $this->params->get('lineHeight', 1.2) . 'em;';

		// Set the active line color.
		$color           = $this->params->get('activeLineColor', '#a4c2eb');
		$r               = hexdec($color{1} . $color{2});
		$g               = hexdec($color{3} . $color{4});
		$b               = hexdec($color{5} . $color{6});
		$activeLineColor = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', .5)';

		// Set the color for matched tags.
		$color               = $this->params->get('highlightMatchColor', '#fa542f');
		$r                   = hexdec($color{1} . $color{2});
		$g                   = hexdec($color{3} . $color{4});
		$b                   = hexdec($color{5} . $color{6});
		$highlightMatchColor = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', .5)';

		JFactory::getDocument()->addStyleDeclaration(
		"
		.CodeMirror
		{
			font-family: " . $fontFamily . ";
			font-size: " . $fontSize . ";
			line-height: " . $lineHeight . ";
			border: 1px solid #ccc;
		}
		/* In order to hide the Joomla menu */
		.CodeMirror-fullscreen
		{
			z-index: 1040;
		}
		/* Make the fold marker a little more visible/nice */
		.CodeMirror-foldmarker
		{
			background: rgb(255, 128, 0);
			background: rgba(255, 128, 0, .5);
			box-shadow: inset 0 0 2px rgba(255, 255, 255, .5);
			font-family: serif;
			font-size: 90%;
			border-radius: 1em;
			padding: 0 1em;
			vertical-align: middle;
			color: white;
			text-shadow: none;
		}
		.CodeMirror-foldgutter, .CodeMirror-markergutter { width: 1.2em; text-align: center; }
		.CodeMirror-markergutter { cursor: pointer; }
		.CodeMirror-markergutter-mark { cursor: pointer; text-align: center; }
		.CodeMirror-markergutter-mark:after { content: \"\25CF\"; }
		.CodeMirror-activeline-background { background: " . $activeLineColor . "; }
		.CodeMirror-matchingtag { background: " . $highlightMatchColor . "; }
		"
		);

		$dispatcher->trigger('onCodeMirrorAfterInit', array(&$this->params));
	}

	/**
	 * Copy editor content to form field.
	 *
	 * @param   string  $id  The id of the editor field.
	 *
	 * @return  string  Javascript
	 */
	public function onSave($id)
	{
		return sprintf('document.getElementById(%1$s).value = Joomla.editors.instances[%1$s].getValue();', json_encode((string) $id));
	}

	/**
	 * Get the editor content.
	 *
	 * @param   string  $id  The id of the editor field.
	 *
	 * @return  string  Javascript
	 */
	public function onGetContent($id)
	{
		return sprintf('Joomla.editors.instances[%1$s].getValue();', json_encode((string) $id));
	}

	/**
	 * Set the editor content.
	 *
	 * @param   string  $id       The id of the editor field.
	 * @param   string  $content  The content to set.
	 *
	 * @return  string  Javascript
	 */
	public function onSetContent($id, $content)
	{
		return sprintf('Joomla.editors.instances[%1$s].setValue(%2$s);', json_encode((string) $id), json_encode((string) $content));
	}

	/**
	 * Adds the editor specific insert method.
	 *
	 * @param   string  $id  The id of the editor field.
	 *
	 * @return  boolean
	 */
	public function onGetInsertMethod()
	{
		static $done = false;

		// Do this only once.
		if ($done)
		{
			return true;
		}

		$done = true;

		JFactory::getDocument()->addScriptDeclaration("
		;function jInsertEditorText(text, editor) { Joomla.editors.instances[editor].replaceSelection(text); };
		");

		return true;
	}

	/**
	 * Display the editor area.
	 *
	 * @param   string   $name     The control name.
	 * @param   string   $content  The contents of the text area.
	 * @param   string   $width    The width of the text area (px or %).
	 * @param   string   $height   The height of the text area (px or %).
	 * @param   int      $col      The number of columns for the textarea.
	 * @param   int      $row      The number of rows for the textarea.
	 * @param   boolean  $buttons  True and the editor buttons will be displayed.
	 * @param   string   $id       An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   string   $asset    Not used.
	 * @param   object   $author   Not used.
	 * @param   array    $params   Associative array of editor parameters.
	 *
	 * @return  string  HTML
	 */
	public function onDisplay(
		$name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		$id = empty($id) ? $name : $id;

		// Must pass the field id to the buttons in this editor.
		$buttons = $this->displayButtons($id, $buttons, $asset, $author);

		// Only add "px" to width and height if they are not given as a percentage.
		$width .= is_numeric($width) ? 'px' : '';
		$height .= is_numeric($height) ? 'px' : '';

		// Options for the CodeMirror constructor.
		$options = new stdClass;

		// Should we focus on the editor on load?
		$options->autofocus = (boolean) $this->params->get('autoFocus', true);

		// Until there's a fix for the overflow problem, always wrap lines.
		$options->lineWrapping = true;

		// Add styling to the active line.
		$options->styleActiveLine = (boolean) $this->params->get('activeLine', true);

		// Do we use line numbering?
		if ($options->lineNumbers = (boolean) $this->params->get('lineNumbers', 0))
		{
			$options->gutters[] = 'CodeMirror-linenumbers';
		}

		// Do we use code folding?
		if ($options->foldGutter = (boolean) $this->params->get('codeFolding', 1))
		{
			$options->gutters[] = 'CodeMirror-foldgutter';
		}

		// Do we use a marker gutter?
		if ($options->markerGutter = (boolean) $this->params->get('markerGutter', $this->params->get('marker-gutter', 0)))
		{
			$options->gutters[] = 'CodeMirror-markergutter';
		}

		// Load the syntax mode.
		$syntax = $this->params->get('syntax', 'html');
		$options->mode = isset($this->modeAlias[$syntax]) ? $this->modeAlias[$syntax] : $syntax;

		// Load the theme if specified.
		if ($theme = $this->params->get('theme'))
		{
			$options->theme = $theme;
			JHtml::_('stylesheet', $this->params->get('basePath', 'media/editors/codemirror/') . 'theme/' . $theme . '.css');
		}

		// Special options for tagged modes (xml/html).
		if (in_array($options->mode, array('xml', 'htmlmixed', 'htmlembedded', 'php')))
		{
			// Autogenerate closing tags (html/xml only).
			$options->autoCloseTags = (boolean) $this->params->get('autoCloseTags', true);

			// Highlight the matching tag when the cursor is in a tag (html/xml only).
			$options->matchTags = (boolean) $this->params->get('matchTags', true);
		}

		// Special options for non-tagged modes.
		if (!in_array($options->mode, array('xml', 'htmlmixed', 'htmlembedded')))
		{
			// Autogenerate closing brackets.
			$options->autoCloseBrackets = (boolean) $this->params->get('autoCloseBrackets', true);

			// Highlight the matching bracket.
			$options->matchBrackets = (boolean) $this->params->get('matchBrackets', true);
		}

		$options->scrollbarStyle = $this->params->get('scrollbarStyle', 'native');

		// Vim Keybindings.
		$options->vimMode = (boolean) $this->params->get('vimKeyBinding', 0);

		$displayData = (object) array(
				'options' => $options,
				'params'  => $this->params,
				'name'    => $name,
				'id'      => $id,
				'cols'    => $col,
				'rows'    => $row,
				'content' => $content,
				'buttons' => $buttons
			);

		$dispatcher = JEventDispatcher::getInstance();

		// At this point, displayData can be modified by a plugin before going to the layout renderer.
		$results = $dispatcher->trigger('onCodeMirrorBeforeDisplay', array(&$displayData));

		$results[] = JLayoutHelper::render('editors.codemirror.element', $displayData, __DIR__ . '/layouts', array('debug' => JDEBUG));

		foreach ($dispatcher->trigger('onCodeMirrorAfterDisplay', array(&$displayData)) as $result)
		{
			$results[] = $result;
		}

		return implode("\n", $results);
	}

	/**
	 * Displays the editor buttons.
	 *
	 * @param   string  $name     Button name.
	 * @param   mixed   $buttons  [array with button objects | boolean true to display buttons]
	 * @param   mixed   $asset    Unused.
	 * @param   mixed   $author   Unused.
	 *
	 * @return  string  HTML
	 */
	protected function displayButtons($name, $buttons, $asset, $author)
	{
		$return = '';

		$args = array(
			'name'  => $name,
			'event' => 'onGetInsertMethod'
		);

		$results = (array) $this->update($args);

		if ($results)
		{
			foreach ($results as $result)
			{
				if (is_string($result) && trim($result))
				{
					$return .= $result;
				}
			}
		}

		if (is_array($buttons) || (is_bool($buttons) && $buttons))
		{
			$buttons = $this->_subject->getButtons($name, $buttons, $asset, $author);

			$return .= JLayoutHelper::render('joomla.editors.buttons', $buttons);
		}

		return $return;
	}

	/**
	 * Gets font info from the json data file
	 *
	 * @param   string  $font  A key from the $fonts array.
	 *
	 * @return  object
	 */
	protected function getFontInfo($font)
	{
		static $fonts;

		if (!$fonts)
		{
			$fonts = json_decode(JFile::read(__DIR__ . '/fonts.json'), true);
		}

		return isset($fonts[$font]) ? (object) $fonts[$font] : null;
	}
}
