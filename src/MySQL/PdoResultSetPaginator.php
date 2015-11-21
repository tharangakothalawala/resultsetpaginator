<?php

namespace TSK\ResultSetPaginator\MySQL;

use PDO;
use PDOStatement;

class PdoResultSetPaginator extends AbstractResultSetPaginator
{
	/**
	 * @var PDO $databaseConnection
	 */
	protected $databaseConnection = null;

	/**
	 * @var string $limitClause
	 */
	protected $limitClause = 'LIMIT %s, %s';

	/**
	 * Constructor
	 *
	 * @param PDO $databaseConnection
	 * @param integer $offset
	 * @param integer $limit
	 */
	public function __construct(PDO $databaseConnection, $offset, $limit)
	{
		$this->databaseConnection = $databaseConnection;
		$this->offset = $offset;
		$this->limit = $limit;

		$this->setLimitClause();
	}

	/**
	 * @param string $sql sql statement
	 *
	 * @return PDOStatement
	 */
	public function query($sql)
	{
		$sql = trim($sql);

		// inject LIMIT if not set explicitely
		if (strpos(strtoupper($sql), 'LIMIT') === false) {
			$sql .= ' ' . $this->getLimitClause();
		}

		// inject SQL_CALC_FOUND_ROWS if not set explicitely
		if (strpos(strtoupper($sql), 'SQL_CALC_FOUND_ROWS') === false) {
			$sql = 'SELECT SQL_CALC_FOUND_ROWS ' . substr($sql, 6, strlen($sql));
		}

		$stmt = $this->databaseConnection->query($sql);
		$this->setFoundRows();

		return $stmt;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setLimitClause()
	{
		$this->limitClause = sprintf($this->limitClause, $this->offset, $this->limit);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setFoundRows()
	{
		/** @var PDOStatement $stmt */
		$stmt = $this->databaseConnection->query('SELECT FOUND_ROWS() AS `found_rows`');
		$this->foundRows = (int) $stmt->fetch(\PDO::FETCH_COLUMN);
	}
}
