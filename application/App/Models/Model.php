<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use PDO;

class Model
{
    protected static string $dbHost = 'pgsql';
    protected static string $dbName = 'postgres';
    protected static string $dbUser = 'test_user';
    protected static string $dbPassword = 'test_password';
    protected static PDO $dbConnection;
    protected static \PDOStatement $statement;

    /**
     * __construct
     *
     * Creates a New Database Connection...
     *
     * @param void
     * @return void
     * @throws Exception
     */
    public function __construct()
    {
        // Create a DSN...
        $dsn = "pgsql:host=" . self::$dbHost . ";port=5432;dbname=" . self::$dbName;

        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            self::$dbConnection = new PDO($dsn, self::$dbUser, self::$dbPassword, $options);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * query
     *
     * Takes advantage of PDO prepare method to create a prepared statement.
     *
     * @param string $query Sql query from extending Models
     * @return bool Anonymos
     */
    protected static function query(string $query): bool
    {
        self::$statement = self::$dbConnection->prepare($query);
        return true;
    }

    /**
     * bindParams
     *
     * Binds the prepared statement using the bindValue method.
     *
     * @param mixed $param , $value, $type  The parameter to bind the value to and the data type which is by default null.
     * @return void Anonymos
     */
    protected static function bindParams(mixed $param, $value, $type = null): void
    {
        if ($type == null) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
        }

        self::$statement->bindValue($param, $value, $type);
    }

    /**
     * execute
     *
     * Executes the Sql statement and returns a boolean status
     *
     * @param void
     * @return boolean Anonymos
     */
    protected static function execute(): bool
    {
        self::$statement->execute();
        return true;
    }

    /**
     * fetch
     *
     * Executes the Sql statement and returns a single array from the resulting Sql query.
     *
     * @param void
     * @return array Anonymos
     */
    protected static function fetch(): array
    {
        self::execute();
        return self::$statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * fetchAll
     *
     * Executes the Sql statement and returns an array from the resulting Sql query.
     *
     * @param void
     * @return array Anonymos
     */
    protected static function fetchAll(): array
    {
        self::execute();
        return self::$statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * lastInsertedId
     *
     * Makes use of the database connection and returns the last inserted id in the database.
     *
     * @param void
     * @return string|false Anonymos
     */
    protected static function lastInsertedId(): bool|string
    {
        return self::$dbConnection->lastInsertId();
    }
}