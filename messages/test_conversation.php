<?php

session_start();

require_once "../config/database.php";
require_once "conversation_helper.php";

echo "<h2>Conversation Engine Test</h2>";

try {

    /*
     * TEST USER IDs
     *
     * Change these two numbers to two existing
     * users in your users table.
     */
    $user_one_id = 1;
    $user_two_id = 2;

    $conversation_id = getOrCreateConversation(
        $pdo,
        $user_one_id,
        $user_two_id
    );

    echo "<p>";
    echo "Conversation created/found successfully.";
    echo "</p>";

    echo "<p>";
    echo "Conversation ID: ";
    echo "<strong>" . htmlspecialchars($conversation_id) . "</strong>";
    echo "</p>";

} catch (Exception $e) {

    echo "<p style='color:red;'>";
    echo "Error: ";
    echo htmlspecialchars($e->getMessage());
    echo "</p>";
}