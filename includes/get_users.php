<?php
include 'connect.php'; // Ensure $conn is properly initialized

$sql = "
    SELECT DISTINCT l.user_id, 
        u.first_name, u.last_name,
        COALESCE(
            (SELECT message FROM livechat WHERE livechat.user_id = l.user_id ORDER BY created_at DESC LIMIT 1), 
            'No messages yet'
        ) AS last_message,
        COALESCE(
            (SELECT DATE_FORMAT(created_at, '%h:%i %p') FROM livechat WHERE livechat.user_id = l.user_id ORDER BY created_at DESC LIMIT 1),
            ''
        ) AS last_time,
        (SELECT COUNT(*) FROM livechat WHERE livechat.user_id = l.user_id AND sender = 'user' AND is_read = 0) AS unread_count,
        COALESCE(
            (SELECT DATEDIFF(NOW(), created_at) FROM livechat WHERE livechat.user_id = l.user_id ORDER BY created_at DESC LIMIT 1),
            NULL
        ) AS days_since_last_message
    FROM livechat l
    JOIN users u ON u.id = l.user_id
    ORDER BY last_time DESC
";

$result = $conn->query($sql);
$users = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            "user_id" => (int) $row["user_id"], // Ensure integer
            "full_name" => trim($row["first_name"] . " " . $row["last_name"]),
            "last_message" => $row["last_message"],
            "last_time" => $row["last_time"],
            "unread_count" => (int) $row["unread_count"], // Ensure integer
            "days_since_last_message" => isset($row["days_since_last_message"]) ? (int) $row["days_since_last_message"] : null
        ];
    }
    echo json_encode($users);
} else {
    echo json_encode(["error" => "Database error: " . $conn->error]);
}

$conn->close();
?>
