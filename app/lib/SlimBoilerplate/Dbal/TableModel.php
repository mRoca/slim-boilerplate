<?php

namespace SlimBoilerplate\Dbal;

use SlimBoilerplate\Exception\DbalException;

class TableModel
{
	/** @var string */
	protected $table = '';

	/** @var string */
	protected $idColum = 'id';

	/** @var Model */
	protected $model;

	/** @var DB */
	protected $db;

	/** @var array */
	protected $tableColumns;

	/**
	 * @param string $table
	 * @throws DbalException
	 */
	public function __construct($table = '')
	{
		if (!$table)
			throw new DbalException('TableModel table required');

		$this->table = $table;
		$this->model = new Model();
		$this->db = $this->model->getDB();
	}

	/**
	 * @param string $whereColumn
	 * @param mixed $whereColumnValue
	 * @param bool $first
	 * @throws DbalException
	 * @return array
	 */
	public function read($whereColumn = '', $whereColumnValue = null, $first = false)
	{
		$allowedColumns = $this->listColums();
		if (!isset($allowedColumns[$whereColumn])) {
			throw new DbalException("Column '$whereColumn' not found in table '$this->table'.");
		}

		return $this->model->read($this->table, $whereColumn, $whereColumnValue, $first);
	}

	/**
	 * @return array
	 */
	public function readAll()
	{
		return $this->model->read($this->table);
	}

	/**
	 * @param int $id
	 * @throws DbalException
	 * @return array
	 */
	public function readById($id)
	{
		if (!intval($id))
			throw new DbalException('Id required');

		return $this->model->readFirst($this->table, $this->idColum, intval($id), true);
	}

	/**
	 * @param array $values
	 * @param array $valuesUnescaped
	 * @throws DbalException
	 * @return bool|int|string
	 */
	public function create($values = array(), $valuesUnescaped = array())
	{
		$this->deleteNonExistingColumns($values);
		$this->deleteNonExistingColumns($valuesUnescaped);

		if (empty($values) && empty($valuesUnescaped)) {
			throw new DbalException('No allowed values detected.');
		}

		return $this->model->insert($this->table, $values, $valuesUnescaped);
	}

	/**
	 * @param $id
	 * @param array $values
	 * @param array $valuesUnescaped
	 * @throws DbalException
	 * @return bool|int|string
	 */
	public function updateById($id, $values = array(), $valuesUnescaped = array())
	{
		if (!intval($id))
			throw new DbalException('Id required');

		$this->deleteNonExistingColumns($values);
		$this->deleteNonExistingColumns($valuesUnescaped);

		return $this->model->update($this->table, $this->idColum, intval($id), $values, $valuesUnescaped);
	}

	/**
	 * @param int $id
	 * @throws DbalException
	 * @return array
	 */
	public function deleteById($id)
	{
		if (!intval($id))
			throw new DbalException('Id required');

		return $this->model->delete($this->table, $this->idColum, intval($id));
	}

	/**
	 * @param string $idColum
	 */
	public function setIdColum($idColum)
	{
		$this->idColum = $idColum;
	}

	/**
	 * @return array
	 */
	public function listColums()
	{
		if (!is_array($this->tableColumns)) {
			$this->tableColumns = array();

			try {
				$columns = $this->model->showColums($this->table);
			} catch (\Exception $e) {
				throw new DbalException('Unable to list table columns.');
			}

			foreach ($columns as $column) {
				$this->tableColumns[$column['Field']] = $column;
			}
		}

		return $this->tableColumns;
	}

	public function deleteNonExistingColumns(&$values)
	{
		$allowedColumns = $this->listColums();
		unset($allowedColumns[$this->idColum]);

		foreach ($values as $key => $value) {
			if (!isset($allowedColumns[$key])) {
				unset($values[$key]);
			}
		}

		return $values;
	}
}
