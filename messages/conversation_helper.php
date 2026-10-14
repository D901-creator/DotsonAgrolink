<?php

/**
 * Get an existing conversation between two users
 * or create a new one.
 *
 * The smaller user ID is always stored as user_one_id
 * and the larger user ID is stored as user_two_id.
 *
 * This prevents duplicate conversations between
 * the same farmer and buyer.
 */

function getOrCreateConversation(
    PDO $pdo,
    int $user_one_id,
    int $user_two_id
): int {

    // Prevent a user from chatting with themselves.
    if ($user_one_id === $user_two_id) {
        throw new Exception(
            "A user cannot start a conversation with themselves."
        );
    }

    /*
     * Normalize the two user IDs.
     *
     * Example:
     *
     * Farmer ID = 15
     * Buyer ID  = 7
     *
     * They will always be stored as:
     *
     * user_one_id = 7
     * user_two_id = 15
     *
     * This means 7 + 15 and 15 + 7
     * are treated as the same conversation.
     */
    $user_one = min($user_one_id, $user_two_id);
    $user_two = max($user_one_id, $user_two_id);

    /*
     * Check whether the conversation already exists.
     */
    $stmt = $pdo->prepare("
        SELECT id
        FROM conversations
        WHERE user_one_id = ?
        AND user_two_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $user_one,
        $user_two
    ]);

    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

    /*
     * If it already exists, return its ID.
     */
    if ($conversation) {
        return (int) $conversation['id'];
    }

    /*
     * Otherwise create a new conversation.
     */
    $stmt = $pdo->prepare("
        INSERT INTO conversations
        (
            user_one_id,
            user_two_id
        )
        VALUES (?, ?)
    ");

    $stmt->execute([
        $user_one,
        $user_two
    ]);

    /*
     * Return the newly created conversation ID.
     */
    return (int) $pdo->lastInsertId();
}