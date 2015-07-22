<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Filter
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Wrapper class for JFilterOutput
 *
 * @package     Joomla.Platform
 * @subpackage  Filter
 * @since       3.4
 */
class JFilterWrapperOutput
{
	/**
	 * Helper wrapper method for objectHTMLSafe
	 *
	 * @param   object   &$mixed        An object to be parsed.
	 * @param   integer  $quote_style   The optional quote style for the htmlspecialchars function.
	 * @param   mixed    $exclude_keys  An optional string single field name or array of field names not.
	 *
	 * @return void
	 *
	 * @see     JFilterOutput::objectHtmlSafe()
	 * @since   3.4
	 */
	public function objectHtmlSafe(&$mixed, $quote_style = 3, $exclude_keys = '')
	{
		return JFilterOutput::objectHtmlSafe($mixed, $quote_style, $exclude_keys);
	}

	/**
	 * Helper wrapper method for linkXHTMLSafe
	 *
	 * @param   string  $input  String to process.
	 *
	 * @return string  Processed string.
	 *
	 * @see     JFilterOutput::linkXhtmlSafe()
	 * @since   3.4
	 */
	public function linkXhtmlSafe($input)
	{
		return JFilterOutput::linkXhtmlSafe($input);
	}

	/**
	 * Helper wrapper method for stringURLSafe
	 *
	 * @param   string  $string  String to process.
	 *
	 * @return string  Processed string.
	 *
	 * @see     JFilterOutput::stringUrlSafe()
	 * @since   3.4
	 */
	public function stringUrlSafe($string)
	{
		return JFilterOutput::stringUrlSafe($string);
	}

	/**
	 * Helper wrapper method for stringUrlUnicodeSlug
	 *
	 * @param   string  $string  String to process.
	 *
	 * @return string  Processed string.
	 *
	 * @see     JFilterOutput::stringUrlUnicodeSlug()
	 * @since   3.4
	 */
	public function stringUrlUnicodeSlug($string)
	{
		return JFilterOutput::stringUrlUnicodeSlug($string);
	}

	/**
	 * Helper wrapper method for ampReplace
	 *
	 * @param   string  $text  Text to process.
	 *
	 * @return string  Processed string.
	 *
	 * @see     JFilterOutput::ampReplace()
	 * @since   3.4
	 */
	public function ampReplace($text)
	{
		return JFilterOutput::ampReplace($text);
	}

	/**
	 * Helper wrapper method for _ampReplaceCallback
	 *
	 * @param   string  $m  String to process.
	 *
	 * @return string  Replaced string.
	 *
	 * @see     JFilterOutput::_ampReplaceCallback()
	 * @since   3.4
	 */
	public function _ampReplaceCallback($m)
	{
		return JFilterOutput::_ampReplaceCallback($m);
	}

	/**
	 * Helper wrapper method for cleanText
	 *
	 * @param   string  &$text  Text to clean.
	 *
	 * @return string  Cleaned text.
	 *
	 * @see     JFilterOutput::cleanText()
	 * @since   3.4
	 */
	public function cleanText(&$text)
	{
		return JFilterOutput::cleanText($text);
	}

	/**
	 * Helper wrapper method for stripImages
	 *
	 * @param   string  $string  Sting to be cleaned.
	 *
	 * @return string  Cleaned string.
	 *
	 * @see     JFilterOutput::stripImages()
	 * @since   3.4
	 */
	public function stripImages($string)
	{
		return JFilterOutput::stripImages($string);
	}

	/**
	 * Helper wrapper method for stripIframes
	 *
	 * @param   string  $string  Sting to be cleaned.
	 *
	 * @return string  Cleaned string.
	 *
	 * @see     JFilterOutput::stripIframes()
	 * @since   3.4
	 */
	public function stripIframes($string)
	{
		return JFilterOutput::stripIframes($string);
	}
}
