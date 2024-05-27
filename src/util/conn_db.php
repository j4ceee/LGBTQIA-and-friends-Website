<?php

class DBConnection {
    private static $instance = null;
    private ?PDO $conn;
    private string $dbname;
    private string $servername;
    private string $username;
    private string $password;

    private function __construct() {
        $this->dbname = getenv('DB_NAME');
        $this->servername = getenv('DB_HOST');
        $this->username = getenv('DB_USER');
        $this->password = trim(file_get_contents(getenv('PASSWORD_FILE_PATH')));
        $this->connect();
    }

    // Singleton pattern (ensures only one instance of DBConnection is created)
    public static function getInstance(): ?DBConnection
    {
        if (self::$instance == null)
        {
            self::$instance = new DBConnection();
        }

        return self::$instance;
    }

    private function connect(): void
    {
        $servername = $this->servername;
        $username = $this->username;
        $password = $this->password;

        try {
            if(!isset($this->conn)) {
                // Create connection (if not already created)
                $this->conn = new PDO("mysql:host=$servername", $username, $password);
            }
        } catch(PDOException) {
            $this->conn = null;
        }
    }

    public function getConnection(): PDO|null
    {
        return $this->conn;
    }

    public function getDbname(): string
    {
        return $this->dbname;
    }

    function checkDBExists(): bool
    {
        if ($this->conn === null) {
            return false;
        }
        $stmt = $this->conn->prepare("SHOW DATABASES LIKE :dbname"); // prepare statement to check if database exists, :dbname is a placeholder
        $stmt->bindParam(':dbname', $this->dbname); // bind parameter :dbname to $this->dbname
        $stmt->execute(); // execute statement
        $result = $stmt->fetchAll(); // fetch all results and store in $result
        return count($result) > 0; // return true if database exists, false otherwise
    }

    function checkDBSchema(): bool|array
    // returns true if all tables exist, otherwise returns an array of missing tables
    {
        $reqTables = array("event_types", "events", "accounts");

        if ($this->conn === null) {
            return false;
        }
        try {
            $stmt = $this->conn->prepare("SHOW TABLES FROM " . $this->dbname); // prepare statement to check if database schema exists
            $stmt->execute(); // execute statement
            $result = $stmt->fetchAll(); // fetch all results and store in $result
        } catch (PDOException) {
            return false;
        }

        // compare tables in database with required tables

        $tables = array();
        foreach ($result as $table) {
            $tables[] = $table[0]; // store table name in $tables
        }

        $missingTables = array_diff($reqTables, $tables); // get missing tables

        if (count($missingTables) > 0) {
            return $missingTables;
        }
        return true;
    }

    function useDB(): PDO|null
    {
        if ($this->conn === null) {
            return null;
        }
        try {
            $dbname = $this->dbname;
            $stmt = $this->conn->prepare("USE $dbname"); // use database
            $stmt->execute(); // execute statement
        } catch (PDOException) {
            return null;
        }

        return $this->conn;
    }
}
