<?php

class Database {
	/**
	 * @var PDO
	 */
	private $connection;

	/**
	 * Creates a connection to a MySQL database
	 * 
	 * @param string $db      Name of the database to connect to
	 * @param string $user    Username
	 * @param string $passwd  Password
	 * @param string $host    Database host. Defaults to 127.0.0.1 (localhost)
	 * @param array  $options PDO Driver options {@see http://php.net/manual/en/ref.pdo-mysql.php}
	 */
	public function __construct($db, $user, $passwd, $host = '127.0.0.1', $options = [])
	{
		$this->connection = new \PDO("mysql:dbname={$db};host={$host}", $user, $passwd, $options);
	}

	public function prepareInsert($data, $table)
	{
		$columns      = array_keys($data);
		$values       = array_values($data);
		$placeholders = $this->generatePlaceholders($columns);
		
		$strColumns      = implode(',', $columns);
		$strPlaceholders = implode(',', $placeholders);

		$statement = $this->connection->prepare("INSERT INTO {$table} ({$strColumns}) VALUES ({$strPlaceholders})");

		foreach (array_combine($placeholders, $values) as $placeholder => $value)
		{
			$statement->bindValue($placeholder, $value);
		}

		return $statement;
	}

	public function prepareRead($table, $columns, $where, $limit)
	{
		if (is_array($columns))
		{
			$columns = implode(',', $columns);
		}

		$query = "SELECT {$columns} FROM {$table}";

		$placeholders = [];

		if (!empty($where))
		{
			$query .= is_array($where)
				? $this->processWhere($where, $placeholders)
				: " {$where}";
		}

		if ($limit > 0)
		{
			$query .= " LIMIT {$limit}";
		}

		$statement = $this->connection->prepare($query);

		if (!empty($placeholders))
		{
			foreach ($placeholders as $placeholder => $value)
			{
				$statement->bindValue($placeholder, $value);
			}
		}

		return $statement;
	}

	public function prepareUpdate($table, $data, $where)
	{
		$placeholders = $this->generatePlaceholders(array_keys($data));
		$pColumns     = array_combine($placeholders, array_keys($data));
		$pValues      = array_combine($placeholders, array_values($data));

		$set = '';

		foreach ($pColumns as $placeholder => $column)
		{
			$set .= "{$column}={$placeholder},";
		}

		$set = rtrim(',', $set);

		$query = "UPDATE {$table} SET {$set}";

		if (!empty($where))
		{
			$query .= is_array($where)
				? $this->processWhere($where, $pValues)
				: " {$where}";
		}

		$statement = $this->connection->prepare($query);

		unset($placeholder);

		foreach ($pValues as $placeholder => $value)
		{
			$statement->bindValue($placeholder, $value);
		}

		return $statement;
	}

	public function prepareDelete($table, $where)
	{
		$query = "DELETE FROM {$table}";
		$placeholders = [];

		if (!empty($where))
		{
			$query .= is_array($where)
				? $this->processWhere($where, $placeholders)
				: " {$where}";
		}

		$statement = $this->connection->prepare($query);

		if (!empty($placeholders))
		{
			foreach ($placeholders as $placeholder => $value)
			{
				$statement->bindValue($placeholder, $value);
			}
		}

		return $statement;
	}

	private function generatePlaceholders($columns)
	{
		foreach ($columns as &$column)
		{
			$column = ":{$column}";
		}

		return $columns;
	}

	private function processWhere($where, &$placeholders)
	{
		$string = '';

		foreach ($where as $clause)
		{	
			if (count($clause) !== 4)
			{
				throw new RuntimeException('All where clauses must contain 4 parameters.');
			}

			foreach ($clause as $key => $value)
			{
				if ($key < 3)
				{
					$string .= " {$value}";
				}
				elseif ($key === 3)
				{
					$string .= " :{$clause[$key-2]}";
				}

				if (1 === $key)
				{
					$placeholders[":{$value}"] = $clause[$key+2];
				}
			}
		}

		return $string;
	}
}
