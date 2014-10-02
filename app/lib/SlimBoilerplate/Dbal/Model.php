<?php

namespace SlimBoilerplate\Dbal;

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
	 * @param $table
	 * @param string $whereColumn
	 * @param mixed $whereColumnValue
	 * @param bool $first
	 * @return array
	 */
	public function read($table, $whereColumn = '', $whereColumnValue = null, $first = false)
	{
		if ($whereColumn && $whereColumnValue !== null) {
			return $this->db->query("SELECT *
								FROM $table
								WHERE $whereColumn = ?
								" . ($first ? " LIMIT 1" : ''), $whereColumnValue);
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
		$columns = array();
		$columnsKeys = array();
		$columnsUnescaped = array();

		foreach ($values as $c => $val) {
			$columns[] = "`$c`";
			$columnsKeys[":$c"] = $val;
		}

		foreach ($valuesUnescaped as $c => $val) {
			$columns[] = "`$c`";
			$columnsUnescaped[] = $val;
		}

		if (empty($columns))
			return false;

		$vals = array_merge(array_keys($columnsKeys), $columnsUnescaped);

		$sql = "INSERT INTO `$table` (" . implode(', ', $columns) . ")
				VALUES (" . implode(', ', $vals) . ")";

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
		$set = array();
		$valuesToEscape = array();

		foreach ($values as $c => $val) {
			$set[] = "`$c` = ?";
			$valuesToEscape[] = $val;
		}

		foreach ($valuesUnescaped as $c => $val) {
			$set[] = "`$c` = $val";
		}

		if (empty($set))
			return false;

		$sql = "UPDATE `$table` SET " . implode(', ', $set) . " WHERE `$column` = ?";
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

		$sql = "DELETE FROM `$table` WHERE `$column` = ?";
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
		if (!count($columnValues))
			return false;

		$sql = "DELETE FROM `$table` WHERE `$column` IN (" . implode(',', $columnValues) . ")";

		return $this->db->exec($sql);
	}

	/**
	 * Do a SHOW COLUMNS FROM $table
	 *
	 * @param $table
	 * @return array
	 */
	public function showColums($table)
	{
		return $this->db->query("SHOW COLUMNS FROM `$table`");
	}

	/**
	 * @return DB
	 */
	public function getDb()
	{
		return $this->db;
	}

	/**
	 * @param DB $db
	 */
	public function setDb($db)
	{
		$this->db = $db;
	}
}
