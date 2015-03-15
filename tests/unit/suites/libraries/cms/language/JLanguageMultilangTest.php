<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Language
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JComponentHelper.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Language
 * @since       3.4
 */
class JLanguageMultiLangTest extends TestCaseDatabase
{
	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 *
	 * @since   3.4
	 */
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');

		return $dataSet;
	}

	/**
	 * Test JComponentHelper::isEnabled
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function testIsEnabledWithSiteApp()
	{
		JApplicationCms::getInstance('site');

		$this->assertFalse(
			JLanguageMultilang::isEnabled()
		);
	}

	/**
	 * Test JComponentHelper::isEnabled
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function testIsEnabledWithAdminApp()
	{
		JApplicationCms::getInstance('administrator');

		$this->assertFalse(
			JLanguageMultilang::isEnabled()
		);
	}
}
