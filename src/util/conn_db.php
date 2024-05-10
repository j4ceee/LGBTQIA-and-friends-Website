<?php

class DBConnection {
    private ?PDO $connection;
    private string $dbname;
    private string $servername;
    private string $username;
    private string $password;

    public function __construct() {
        $this->dbname = getenv('DB_NAME');
        $this->servername = getenv('DB_HOST');
        $this->username = getenv('DB_USER');
        $this->password = trim(file_get_contents(getenv('PASSWORD_FILE_PATH')));
        $this->connect();
    }

    private function connect(): void
    {
        $servername = $this->servername;
        $username = $this->username;
        $password = $this->password;

        try {
            $this->connection = new PDO("mysql:host=$servername", $username, $password);
        } catch(PDOException) {
            $this->connection = null;
        }
    }

    public function getConnection(): PDO|null
    {
        return $this->connection;
    }

    public function getDbname(): string
    {
        return $this->dbname;
    }

    public function getServername(): string
    {
        return $this->servername;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    function checkDBExists(): bool
    {
        if ($this->connection === null) {
            return false;
        }
        $stmt = $this->connection->prepare("SHOW DATABASES LIKE :dbname"); // prepare statement to check if database exists, :dbname is a placeholder
        $stmt->bindParam(':dbname', $this->dbname); // bind parameter :dbname to $this->dbname
        $stmt->execute(); // execute statement
        $result = $stmt->fetchAll(); // fetch all results and store in $result
        return count($result) > 0; // return true if database exists, false otherwise
    }

    function checkDBSchema(): bool|array
    // returns true if all tables exist, otherwise returns an array of missing tables
    {
        $reqTables = array("event_types", "events", "accounts");

        if ($this->connection === null) {
            return false;
        }
        try {
            $stmt = $this->connection->prepare("SHOW TABLES FROM " . $this->dbname); // prepare statement to check if database schema exists
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

    function useDB(): ?PDO
    {
        if ($this->connection === null) {
            return null;
        }
        try {
            $dbname = $this->dbname;
            $stmt = $this->connection->prepare("USE $dbname"); // use database
            $stmt->execute(); // execute statement
        } catch (PDOException) {
            return null;
        }
        return $this->connection;
    }
}