<?php
require_once(__DIR__ . '/conn_db.php'); // include database connection file
require_once(__DIR__ . '/utils.php'); // include utility functions
require_once(__DIR__ . '/conf.php'); // include configuration file

$dbConnection = DBConnection::getInstance();
$PDO = $dbConnection->getConnection();

if ($PDO !== null) {
// this script will only get called when there are issues with the database
// -> recreate the database regardless if it exists or not

    if ($dbConnection->checkDBExists()) {
        // if the table "accounts" is not in array of missing tables -> backup content of "accounts" table
        $tablesComplete = $dbConnection->checkDBSchema(); // returns true if the database is complete, returns array of missing tables otherwise

        // if the table "accounts" is not in array of missing tables or the database is complete -> backup content of "accounts" table
        $tableExists = false;
        if ($tablesComplete === true) {
            // database exists and is complete -> stop the script and do nothing -> redirect to index.php
            // echo "Database is complete<br>";
            redirect(); // redirect to home page
        }
        if (is_array($tablesComplete) && !in_array("accounts", $tablesComplete)) {
            $tableExists = true;
        }

        if ($tableExists) {
            // echo "Backup content of 'accounts' table<br>";
            $PDO = $dbConnection->useDB();
            $stmt = $PDO->prepare("SELECT * FROM accounts");
            $stmt->execute();
            $result = $stmt->fetchAll();
            $accounts = array();
            foreach ($result as $row) {
                $accounts[] = $row;

                // echo $row['username'] . " " . $row['password'] . " " . $row['email'] . "<br>";
            }
        }

        try {
            // drop the database
            $dbname = $dbConnection->getDbname();
            $stmt = $PDO->prepare("DROP DATABASE $dbname");
            $stmt->execute();
        } catch (PDOException $e) {
            // echo "Error: " . $e->getMessage();
        }
    }

    try {
        // create the database
        $dbname = $dbConnection->getDbname();
        $stmt = $PDO->prepare("CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $stmt->execute();
    } catch (PDOException $e) {
        // echo "Error: " . $e->getMessage();
    }

    // use the database
    $PDO = $dbConnection->useDB();

    if ($PDO !== null) {
        // execute SQL scripts
        $sql = file_get_contents(dirname(__DIR__) . '/db/setup_db.sql'); // read the SQL file - returns false if file does not exist
        if ($sql !== false) {
            $PDO->exec($sql);
        }

        $sql = file_get_contents(dirname(__DIR__) . '/db/fill_basics_db.sql'); // read the SQL file - returns false if file does not exist
        if ($sql !== false) {
            $PDO->exec($sql);
        }

        // restore content of "accounts" table
        if (isset($accounts)) {
            $stmt = $PDO->prepare("INSERT INTO accounts (username, password, email) VALUES (:username, :password, :email)");
            foreach ($accounts as $account) {
                $stmt->bindParam(':username', $account['username']);
                $stmt->bindParam(':password', $account['password']);
                $stmt->bindParam(':email', $account['email']);
                $stmt->execute();
            }
        }
    }
}

redirect(); // redirect to home page

