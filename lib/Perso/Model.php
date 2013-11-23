<?php

namespace Perso;

class Model
{

	/**
	 * @var DB
	 */
	protected $db;

	public function __construct(& $db = null)
	{
		$this->db = $db ? $db : DB::getInstance();
	}

	/**
	 * Do a SELECT * FROM $table WHERE $column = '$value' and return the first result
	 *
	 * @param $table
	 * @param $column
	 * @param $columnValue
	 * @return array
	 */
	public function readFirst($table, $column, $columnValue)
	{
		$res = $this->read($table, $column, $columnValue, true);

		if (count($res))
			return $res[0];

		return array();
	}

	/**
	 * Do a SELECT * FROM $table WHERE $column = '$value'
	 *
	 * @param $table
	 * @param $column
	 * @param $columnValue
	 * @param bool $first
	 * @return array
	 */
	public function read($table, $column = '', $columnValue = null, $first = false)
	{
		if ($column && $columnValue) {
			return $this->db->query("SELECT *
								FROM $table
								WHERE $column = ?
								" . ($first ? " LIMIT 1" : ''), $columnValue);
		} else {
			return $this->db->query("SELECT * FROM $table");
		}
	}

	/**
	 * Do an INSERT query
	 *
	 * @param $table
	 * @param array $values
	 * @param array $valuesUnescaped
	 * @return bool|int|string
	 */
	public function insert($table, $values = array(), $valuesUnescaped = array())
	{
		//TODO Verify if the columns exists in the db
		$columns          = array();
		$columnsKeys      = array();
		$columnsUnescaped = array();

		foreach ($values as $c => $val) {
			$columns[]          = "`$c`";
			$columnsKeys[":$c"] = $val;
		}

		foreach ($valuesUnescaped as $c => $val) {
			$columns[]          = "`$c`";
			$columnsUnescaped[] = $val;
		}

		if (empty($columns))
			return false;

		$vals = array_merge(array_keys($columnsKeys), $columnsUnescaped);

		$sql = 'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ')
				VALUES (' . implode(', ', $vals) . ')';

		return $this->db->executeWithBinaryFiles($sql, $columnsKeys);
	}

	/**
	 * Do an UPDATE query
	 *
	 * @param $table
	 * @param $column
	 * @param $columnValue
	 * @param array $values
	 * @param array $valuesUnescaped
	 * @return bool|int|string
	 */
	public function update($table, $column, $columnValue, $values = array(), $valuesUnescaped = array())
	{
		$set            = array();
		$valuesToEscape = array();

		foreach ($values as $c => $val) {
			$set[]            = "`$c` = ?";
			$valuesToEscape[] = $val;
		}

		foreach ($valuesUnescaped as $c => $val) {
			$set[] = "`$c` = $val";
		}

		if (empty($set))
			return false;

		$sql              = 'UPDATE `' . $table . '` SET ' . implode(', ', $set) . ' WHERE `' . $column . '` = ?';
		$valuesToEscape[] = $columnValue;

		return $this->db->exec($sql, $valuesToEscape);
	}

	/**
	 * Do an DELETE query
	 *
	 * @param $table
	 * @param $column
	 * @param $columnValue
	 * @return bool|int|string
	 */
	public function delete($table, $column, $columnValue)
	{
		$valuesToEscape = array();

		$sql              = 'DELETE FROM `' . $table . '` WHERE `' . $column . '` = ?';
		$valuesToEscape[] = $columnValue;

		return $this->db->exec($sql, $valuesToEscape);
	}

	/**
	 * Do an DELETE query WHERE column IN
	 *
	 * @param $table
	 * @param $column
	 * @param $columnValues
	 * @return bool|int|string
	 */
	public function deleteIn($table, $column, $columnValues)
	{
		if(! count($columnValues))
			return false;

		$sql = 'DELETE FROM `' . $table . '` WHERE `' . $column . '` IN (' . implode(',', $columnValues) . ')';

		return $this->db->exec($sql);
	}

	/**
	 * @return \Perso\DB
	 */
	public function getDb()
	{
		return $this->db;
	}

	/**
	 * @param \Perso\DB $db
	 */
	public function setDb($db)
	{
		$this->db = $db;
	}
}