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
     * @param void
     * @return void
     * @throws Exception
     */
    public function __construct()
    {
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
}