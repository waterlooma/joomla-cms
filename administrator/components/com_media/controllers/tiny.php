<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Joomla.Media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
/**
 * Base Upload Controller
 *
 * @since  3.5
 */
class MediaControllerTiny extends JControllerLegacy
{
	/**
	 * Prefix for the view and model classes
	 *
	 * @var    string
	 * @since  3.5
	 */
	public $prefix = 'Media';

	/**
	 * Execute the controller.
	 *
	 * @return  mixed  A rendered view or true
	 *
	 * @since   3.5
	 */
	public function upload()
	{
		$app = JFactory::getApplication();

		// Check for request forgeries
		if (!JSession::checkToken('request'))
		{
			echo new JResponseJson(new Exception(JText::_('JINVALID_TOKEN')));
			$app->close();
		}

		// Check for session id forgery
		// if ($this->input->get('sessionName') != $this->input->get('sessionId'))
		// {
			// $this->app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');
			// return;
		// }

		JFactory::getDocument()->setMimeEncoding('application/json');
		JFactory::getApplication()->setHeader('Content-Disposition', 'attachment;filename="progress-report-results.json"');

		$params = JComponentHelper::getParams('com_media');
		$user  = JFactory::getUser();

		// Get some data from the request
		$files        = $this->input->files->get('tinyFiles', '', 'array');
		$this->folder = $this->input->get('path', '', 'path');

		// Authorize the user
		if (!$user->authorise('core.create', 'com_media'))
		{
			// User is not authorised to create
			echo new JResponseJson(new Exception(JText::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED')));
			$app->close();
		}

		// Total length of post back data in bytes.
		$contentLength = (int) $_SERVER['CONTENT_LENGTH'];
		// Maximum allowed size of post back data in MB.
		$postMaxSize = (int) ini_get('post_max_size');
		// Maximum allowed size of script execution in MB.
		$memoryLimit = (int) ini_get('memory_limit');
		// Check for the total size of post back data.
		if (($postMaxSize > 0 && $contentLength > $postMaxSize * 1024 * 1024)
			|| ($memoryLimit != -1 && $contentLength > $memoryLimit * 1024 * 1024))
		{
			echo new JResponseJson(new Exception(JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE')));
			$app->close();
		}
		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
		// Perform basic checks on file info before attempting anything

		$files['name']     = JFile::makeSafe($files['name']);
		$files['filepath'] = implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $files['name']));

		if (($files['error'] == UPLOAD_ERR_INI_SIZE)
			|| ($uploadMaxSize > 0 && $files['size'] > $uploadMaxSize)
			|| ($uploadMaxFileSize > 0 && $files['size'] > $uploadMaxFileSize))
		{
			// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
			echo new JResponseJson(new Exception(JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE')));
			$app->close();
		}
		if (JFile::exists($files['filepath']))
		{
			if ($params->get('overwrite_files', 0) == 1 && $user->authorise('core.delete', 'com_media'))
			{
				/*
				 * A file with this name already exists,
				* the option to overwrite the file is set to yes and
				* the current user is authorised to delete files
				* so we delete it here and upload the new later.
				* Note that we can't restore the old file if the uplaod fails.
				*/
				JFile::delete($files['filepath']);
				echo new JResponseJson(new Exception(JText::_('COM_MEDIA_ERROR_FILE_EXISTS_OVERWRITE')));
				$app->close();
			}
			else
			{
				/*
				 * A file with this name already exists and
				* the option to overwrite the file is set to no
				* or the user is not authorised to delete files.
				*/
				echo new JResponseJson(new Exception(JText::_('COM_MEDIA_ERROR_FILE_EXISTS')));
				$app->close();
			}
		}
		if (!isset($files['name']))
		{
			// No filename (after the name was cleaned by JFile::makeSafe)
			echo new JResponseJson(new Exception(JText::_('COM_MEDIA_INVALID_REQUEST')));
			$app->close();
		}
		// Enable uploading filenames with alphanumeric and spaces
		$fileparts = pathinfo($files['filepath']);
		$files['original_name'] = $files['name'];
		// Transform filename to punycode
		$fileparts['filename'] = JStringPunycode::toPunycode($fileparts['filename']);
		// The relative path needed for the response
		$fileparts['relative'] = str_replace(JPATH_ROOT, '',  $fileparts['dirname']);
		// Transform filename to punycode, then neglect otherthan non-alphanumeric characters & underscores. Also transform extension to lowercase
		$safeFileName = preg_replace(array("/[\\s]/", "/[^a-zA-Z0-9_]/"), array("_", ""), $fileparts['filename']) . '.' . strtolower($fileparts['extension']);
		// Create filepath with safe-filename
		$files['final'] = $fileparts['dirname'] . DIRECTORY_SEPARATOR . $safeFileName;

		file_put_contents(
			$files['final'],
			file_get_contents($_FILES['tinyFiles']['tmp_name'])
		);

		$json = json_encode(
			array(
			'dataUrl' => $fileparts['relative'] . '/' . $files['name']
			)
		);

		echo $json;
		$app->close();
	}
}
