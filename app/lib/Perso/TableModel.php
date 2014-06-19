<?php

namespace Perso;

class TableModel
{
	/**
	 * @var string
	 */
	protected $table = '';

	/**
	 * @var string
	 */
	protected $idColum = 'id';

	/**
	 * @var Model
	 */
	protected $model;

	/**
	 * @var DB
	 */
	protected $db;

	/**
	 * @param string $table
	 * @throws \ErrorException
	 */
	public function __construct($table = '')
	{
		if (!$table)
			throw new \ErrorException('TableModel table required');

		$this->table = $table;
		$this->model = new Model();
		$this->db = $this->model->getDB();
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
	 * @throws \ErrorException
	 * @return array
	 */
	public function readById($id)
	{
		if (!intval($id))
			throw new \ErrorException('Id required');

		return $this->model->readFirst($this->table, $this->idColum, intval($id), true);
	}

	/**
	 * @param array $values
	 * @param array $valuesUnescaped
	 * @return bool|int|string
	 */
	public function create($values = array(), $valuesUnescaped = array())
	{
		return $this->model->insert($this->table, $values, $valuesUnescaped);
	}

	/**
	 * @param $id
	 * @param array $values
	 * @param array $valuesUnescaped
	 * @return bool|int|string
	 * @throws \ErrorException
	 */
	public function updateById($id, $values = array(), $valuesUnescaped = array())
	{
		if (!intval($id))
			throw new \ErrorException('Id required');

		unset($values[$this->idColum]);
		unset($valuesUnescaped[$this->idColum]);

		return $this->model->update($this->table, $this->idColum, intval($id), $values, $valuesUnescaped);
	}

	/**
	 * @param int $id
	 * @throws \ErrorException
	 * @return array
	 */
	public function deleteById($id)
	{
		if (!intval($id))
			throw new \ErrorException('Id required');

		return $this->model->delete($this->table, $this->idColum, intval($id));
	}

	/**
	 * @param string $idColum
	 */
	public function setIdColum($idColum)
	{
		$this->idColum = $idColum;
	}

}