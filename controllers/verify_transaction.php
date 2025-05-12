<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['user'], $data['action'], $data['description'], $data['timestamp'], $data['tx_hash'])) {
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

$user = $data['user'];
$action = $data['action'];
$description = $data['description'];
$timestamp = $data['timestamp'];
$stored_hash = $data['tx_hash'];

// Match the log format exactly
$data_to_hash = "user=$user;action=$action;description=$description;timestamp=$timestamp;";
$recomputed_hash = hash('sha256', $data_to_hash);
$match = $recomputed_hash === $stored_hash;

echo json_encode([
    'match' => $match,
    'recomputed_hash' => $recomputed_hash,
    'stored_hash' => $stored_hash
]);
