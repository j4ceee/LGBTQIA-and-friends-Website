<?php
require_once(__DIR__ . '/utils.php'); // include utility functions
require_once(__DIR__ . '/conf.php'); // include configuration file

echo "Hello, World!";

// redirect to home page
redirect();


/*
 * This script adds users to the database.
 * It should only be run once to add the initial users.
 * After that, it should be commented out.
 */
// TODO: only run this script once & comment out everything for release
/*
require_once(__DIR__ . '/conn_db.php'); // include database connection file

$dbConnection = new DBConnection();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    exit("Database connection failed or schema is incorrect.");
}

// Define the users
$users = [
    // [ // Example user
    //    'username' => 'user1',
    //    'email' => 'user1@example.com',
    //    'password' => 'password1'
    // ],
];
/*
foreach ($users as $user) {
    // Hash the password
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $PDO->prepare("INSERT INTO accounts (username, email, password) VALUES (:username, :email, :password)");

    // Bind the parameters
    $stmt->bindParam(':username', $user['username']);
    $stmt->bindParam(':email', $user['email']);
    $stmt->bindParam(':password', $hashedPassword);

    // Execute the statement
    $stmt->execute();
}

echo "Users have been added successfully: \n";
echo "<pre>";
print_r($users);
echo "</pre>";
*/