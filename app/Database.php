<?php
// PDO connection and prepared-statement helpers. SQL text never includes user strings.

class Database
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function connect(array $config)
    {
        $host = (string) $config['db_host'];
        $port = (string) ($config['db_port'] ?? '3306');
        $name = (string) $config['db_name'];
        $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $name . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        if (defined('PDO::MYSQL_ATTR_MULTI_STATEMENTS')) {
            $options[PDO::MYSQL_ATTR_MULTI_STATEMENTS] = false;
        }
        $pdo = new PDO($dsn, (string) $config['db_user'], (string) $config['db_pass'], $options);
        return new self($pdo);
    }

    public function pdo()
    {
        return $this->pdo;
    }

    public function all($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function one($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function value($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn();
        return $value === false ? null : $value;
    }

    public function run($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function insert($sql, array $params = [])
    {
        $this->run($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }
}
