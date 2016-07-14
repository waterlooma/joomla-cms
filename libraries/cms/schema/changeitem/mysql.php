<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Schema
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Checks the database schema against one MySQL DDL query to see if it has been run.
 *
 * @since  2.5
 */
class JSchemaChangeitemMysql extends JSchemaChangeitem
{
	/**
	 * Checks a DDL query to see if it is a known type
	 * If yes, build a check query to see if the DDL has been run on the database.
	 * If successful, the $msgElements, $queryType, $checkStatus and $checkQuery fields are populated.
	 * The $msgElements contains the text to create the user message.
	 * The $checkQuery contains the SQL query to check whether the schema change has
	 * been run against the current database. The $queryType contains the type of
	 * DDL query that was run (for example, CREATE_TABLE, ADD_COLUMN, CHANGE_COLUMN_TYPE, ADD_INDEX).
	 * The $checkStatus field is set to zero if the query is created
	 *
	 * If not successful, $checkQuery is empty and , and $checkStatus is -1.
	 * For example, this will happen if the current line is a non-DDL statement.
	 *
	 * @return void
	 *
	 * @since  2.5
	 */
	protected function buildCheckQuery()
	{
		// Initialize fields in case we can't create a check query
		$this->checkStatus = -1; // change status to skipped
		$result = null;

		// Remove any newlines
		$this->updateQuery = str_replace("\n", '', $this->updateQuery);

		// Fix up extra spaces around () and in general
		$find = array('#((\s*)\(\s*([^)\s]+)\s*)(\))#', '#(\s)(\s*)#');
		$replace = array('($3)', '$1');
		$updateQuery = preg_replace($find, $replace, $this->updateQuery);
		$wordArray = explode(' ', $updateQuery);

		// First, make sure we have an array of at least 6 elements
		// if not, we can't make a check query for this one
		if (count($wordArray) < 6)
		{
			// Done with method
			return;
		}

		// We can only make check queries for alter table and create table queries
		$command = strtoupper($wordArray[0] . ' ' . $wordArray[1]);

		if ($command === 'ALTER TABLE')
		{
			$alterCommand = strtoupper($wordArray[3] . ' ' . $wordArray[4]);

			if ($alterCommand == 'ADD COLUMN')
			{
				$colName = $this->fixQuote($wordArray[5]);
				$result = 'SHOW COLUMNS IN ' . $wordArray[2]
					. ' WHERE ' . $this->db->quoteName('Field') . ' = ' . $colName;
				$this->queryType = 'ADD_COLUMN';
				$this->msgElements = array($this->fixQuote($wordArray[2]), $colName);
			}
			elseif ($alterCommand == 'ADD INDEX' || $alterCommand == 'ADD KEY' || $alterCommand == 'ADD UNIQUE')
			{
				if ($pos = strpos($wordArray[5], '('))
				{
					$index = $this->fixQuote(substr($wordArray[5], 0, $pos));
				}
				else
				{
					$index = $this->fixQuote($wordArray[5]);
				}

				$result = 'SHOW INDEXES IN ' . $wordArray[2]
					. ' WHERE ' . $this->db->quoteName('Key_name') . ' = ' . $index;
				$this->queryType = 'ADD_INDEX';
				$this->msgElements = array($this->fixQuote($wordArray[2]), $index);
			}
			elseif ($alterCommand == 'DROP INDEX' || $alterCommand == 'DROP KEY')
			{
				$posIdx = 5;

				if (count($wordArray) > 8)
				{
					if (substr($wordArray[5], -4) == ',ADD')
					{
						$alterCommand2 = strtoupper($wordArray[6]);
						if ($alterCommand2 == 'INDEX' || $alterCommand2 == 'KEY' || $alterCommand2 == 'UNIQUE')
						{
							$posIdx = 7;
						}
					}
					elseif (substr($wordArray[5], -1) == ',')
					{
						$alterCommand2 = strtoupper($wordArray[6] . ' ' . $wordArray[7]);
						if ($alterCommand2 == 'ADD INDEX' || $alterCommand2 == 'ADD KEY' || $alterCommand2 == 'ADD UNIQUE')
						{
							$posIdx = 8;
						}
					}
					elseif (substr($wordArray[6], 0, 1) == ',')
					{
						$alterCommand2 = strtoupper($wordArray[6] . ' ' . $wordArray[7]);
						if ($alterCommand2 == ',ADD INDEX' || $alterCommand2 == ',ADD KEY' || $alterCommand2 == ',ADD UNIQUE')
						{
							$posIdx = 8;
						}
					}
					elseif ((count($wordArray) > 9) && ($wordArray[6] == ','))
					{
						$alterCommand2 = strtoupper($wordArray[7] . ' ' . $wordArray[8]);
						if ($alterCommand2 == 'ADD INDEX' || $alterCommand2 == 'ADD KEY' || $alterCommand2 == 'ADD UNIQUE')
						{
							$posIdx = 9;
						}
					}
				}

				if ($posIdx > 5)
				{
					if ($pos = strpos($wordArray[$posIdx], '('))
					{
						$index = $this->fixQuote(substr($wordArray[$posIdx], 0, $pos));
					}
					else
					{
						$index = $this->fixQuote($wordArray[$posIdx]);
					}

					$this->queryType = 'ADD_INDEX';
				}
				else
				{
					$index = $this->fixQuote($wordArray[5]);
					$this->queryType = 'DROP_INDEX';
					$this->checkQueryExpected = 0;
				}
				$result = 'SHOW INDEXES IN ' . $wordArray[2]
					. ' WHERE ' . $this->db->quoteName('Key_name') . ' = ' . $index;
				$this->msgElements = array($this->fixQuote($wordArray[2]), $index);
			}
			elseif ($alterCommand == 'DROP COLUMN')
			{
				$colName = $this->fixQuote($wordArray[5]);
				$result = 'SHOW COLUMNS IN ' . $wordArray[2]
					. ' WHERE ' . $this->db->quoteName('Field') . ' = ' . $colName;
				$this->queryType = 'DROP_COLUMN';
				$this->checkQueryExpected = 0;
				$this->msgElements = array($this->fixQuote($wordArray[2]), $colName);
			}
			elseif (($alterCommand == 'CONVERT TO') && (count($wordArray) > 9))
			{
				if ((strtoupper($wordArray[5] . $wordArray[6]) == 'CHARACTERSET')
					&& ($wordArray[8] == 'COLLATE'))
				{
					$table = $wordArray[2];
					$collat = $this->fixQuote(strtolower($wordArray[9]));

					if (!$this->db->hasUTF8mb4Support())
					{
						$collat = str_replace('utf8mb4', 'utf8', $collat);
					}

					$result = 'SHOW TABLE STATUS WHERE ' . $this->db->quoteName('Name')
						. ' = ' . $this->fixQuote($table)
						. ' AND ' . $this->db->quoteName('Collation') . ' = ' . $collat;
					$this->queryType = 'CREATE_TABLE';
					$this->msgElements = array($this->fixQuote($table) . ' (COLLATION ' . $collat . ')');
				}
			}
			elseif (strtoupper($wordArray[3]) == 'MODIFY')
			{
				// Kludge to fix problem with "integer unsigned"
				$type = $this->fixQuote($wordArray[5]);

				if (isset($wordArray[6]))
				{
					$type = $this->fixQuote($this->fixInteger($wordArray[5], $wordArray[6]));
				}

				$result = 'SHOW COLUMNS IN ' . $wordArray[2] . ' WHERE field = ' . $this->fixQuote($wordArray[4]) . ' AND type = ' . $type;
				$this->queryType = 'CHANGE_COLUMN_TYPE';
				$this->msgElements = array($this->fixQuote($wordArray[2]), $this->fixQuote($wordArray[4]), $type);
			}
			elseif (strtoupper($wordArray[3]) == 'CHANGE')
			{
				// Kludge to fix problem with "integer unsigned"
				$type = $this->fixQuote($this->fixInteger($wordArray[6], $wordArray[7]));
				$result = 'SHOW COLUMNS IN ' . $wordArray[2] . ' WHERE field = ' . $this->fixQuote($wordArray[4]) . ' AND type = ' . $type;
				$this->queryType = 'CHANGE_COLUMN_TYPE';
				$this->msgElements = array($this->fixQuote($wordArray[2]), $this->fixQuote($wordArray[4]), $type);
			}
		}

		if ($command == 'CREATE TABLE')
		{
			if (strtoupper($wordArray[2] . $wordArray[3] . $wordArray[4]) == 'IFNOTEXISTS')
			{
				$table = $wordArray[5];
			}
			else
			{
				$table = $wordArray[2];
			}

			$result = 'SHOW TABLES LIKE ' . $this->fixQuote($table);
			$this->queryType = 'CREATE_TABLE';
			$this->msgElements = array($this->fixQuote($table));
		}

		// Set fields based on results
		if ($this->checkQuery = $result)
		{
			// Unchecked status
			$this->checkStatus = 0;
		}
		else
		{
			// Skipped
			$this->checkStatus = -1;
		}
	}

	/**
	 * Fix up integer. Fixes problem with MySQL integer descriptions.
	 * If you change a column to "integer unsigned" it shows
	 * as "int(10) unsigned" in the check query.
	 *
	 * @param   string  $type1  the column type
	 * @param   string  $type2  the column attributes
	 *
	 * @return  string  The original or changed column type.
	 *
	 * @since   2.5
	 */
	private function fixInteger($type1, $type2)
	{
		$result = $type1;

		if (strtolower($type1) == "integer" && strtolower(substr($type2, 0, 8)) == 'unsigned')
		{
			$result = 'int(10) unsigned';
		}

		return $result;
	}

	/**
	 * Fixes up a string for inclusion in a query.
	 * Replaces name quote character with normal quote for literal.
	 * Drops trailing semi-colon. Injects the database prefix.
	 *
	 * @param   string  $string  The input string to be cleaned up.
	 *
	 * @return  string  The modified string.
	 *
	 * @since   2.5
	 */
	private function fixQuote($string)
	{
		$string = str_replace('`', '', $string);
		$string = str_replace(';', '', $string);
		$string = str_replace('#__', $this->db->getPrefix(), $string);

		return $this->db->quote($string);
	}
}
