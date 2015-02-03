<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Media Manager Component Controller
 *
 * @since  1.5
 */
class MediaController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		JPluginHelper::importPlugin('content');
		$vName = $this->input->get('view', 'media');

		$document = JFactory::getDocument();
		$vType    = $document->getType();

		switch ($vName)
		{
			case 'images':
				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName = 'manager';
				// Get/Create the view
				$view = $this->getView($vName, $vType);
				if (JFactory::getApplication()->isSite() && !file_exists(JPATH_ROOT . '/templates/' . JFactory::getApplication()->getTemplate() .
					'/html/com_media/images/default.php'))
				{
					$view->addTemplatePath($this->basePath . '/views/' . strtolower($vName) . '/tmpl');
				}
				break;

			case 'imagesList':
				$mName   = 'list';
				$vLayout = $this->input->get('layout', 'default', 'string');
				// Get/Create the view
				$view = $this->getView($vName, $vType);
				$view->addTemplatePath($this->basePath . '/views/' . strtolower($vName) . '/tmpl');
				break;

			case 'mediaList':
				$app     = JFactory::getApplication();
				$mName   = 'list';
				$vLayout = $app->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');
				// Get/Create the view
				$view = $this->getView($vName, $vType);
				break;

			case 'media':
			default:
				$vName   = 'media';
				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName = 'manager';
				// Get/Create the view
				$view = $this->getView($vName, $vType);
				$view->addTemplatePath($this->basePath . '/views/' . strtolower($vName) . '/tmpl');
				break;
		}

		// Get/Create the model
		if ($model = $this->getModel($mName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();

		return $this;
	}

	/**
	 * Validate FTP credentials
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function ftpValidate()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
	}
}
