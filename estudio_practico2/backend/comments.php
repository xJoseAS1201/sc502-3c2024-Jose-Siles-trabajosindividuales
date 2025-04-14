<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit();
}


header("Content-Type: application/json");


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        require_once 'db.php';

        if (!isset($_GET['task_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Falta el ID de la tarea']);
            exit;
        }

        $task_id = $_GET['task_id'];

        $sql = "SELECT c.id, c.comment, c.created_at, u.username 
                FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.task_id = ?
                ORDER BY c.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $task_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $comments = [];

        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        echo json_encode(['status' => 'success', 'comments' => $comments]);

        $stmt->close();
        $conn->close();
        break;
    case 'POST':
        require_once 'db.php';

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['task_id']) || !isset($data['comment'])) {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
            exit;
        }

        $task_id = $data['task_id'];
        $comment = $data['comment'];
        $user_id = $_SESSION['user_id'];

        $sql = "INSERT INTO comments (task_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $task_id, $user_id, $comment);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Comentario agregado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al agregar el comentario']);
        }

        $stmt->close();
        $conn->close();
        break;
    case 'PUT':
        require_once 'db.php';

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id']) || !isset($data['comment'])) {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
            exit;
        }

        $comment_id = $data['id'];
        $new_comment = $data['comment'];
        $user_id = $_SESSION['user_id'];

        $check = $conn->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
        $check->bind_param("ii", $comment_id, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'No autorizado para editar este comentario']);
            $check->close();
            $conn->close();
            exit;
        }

        $check->close();

        $update = $conn->prepare("UPDATE comments SET comment = ?, updated_at = NOW() WHERE id = ?");
        $update->bind_param("si", $new_comment, $comment_id);

        if ($update->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Comentario actualizado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el comentario']);
        }

        $update->close();
        $conn->close();
        break;
    case 'DELETE':
        require_once 'db.php';

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID del comentario requerido']);
            exit;
        }

        $comment_id = $data['id'];
        $user_id = $_SESSION['user_id'];

        $check = $conn->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
        $check->bind_param("ii", $comment_id, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'No autorizado o comentario no existe']);
            $check->close();
            $conn->close();
            exit;
        }

        $check->close();

        $delete = $conn->prepare("DELETE FROM comments WHERE id = ?");
        $delete->bind_param("i", $comment_id);

        if ($delete->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Comentario eliminado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el comentario']);
        }

        $delete->close();
        $conn->close();
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>