<?php
header('Content-Type: text/html; charset=UTF-8');
include "db.php";

$termo = $_GET['q'] ?? '';

if($termo != ''){
    $stmt = $conn->prepare("SELECT id, title, content FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC");
    $like = "%$termo%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $posts = [];
    while($row = $result->fetch_assoc()){
        $posts[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'content' => strlen($row['content']) > 100 ? substr($row['content'], 0, 100).'...' : $row['content']
        ];
    }
    echo json_encode($posts);
}
?>