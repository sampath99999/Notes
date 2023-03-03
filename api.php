<?php
$dbHost = "localhost";
$dbName = "php_server";
$dbUser = "postgres";
$dbPass = "postgres";

if (!isset($_POST['type'])) {
    echo json_encode(["status" => false, "message" => "Not found!"]);
    return;
}

try {
    switch ($_POST['type']) {
        case "create": {
                if (!isset($_POST['name'])) {
                    echo json_encode(["status" => false, "message" => "Name is required!"]);
                    return;
                }
                if (empty($_FILES)) {
                    echo json_encode(["status" => false, "message" => "File is required!"]);
                    return;
                }
                $name = $_POST['name'];
                $fileName = $_FILES["file"]["name"];
                $fileType = $_FILES["file"]["type"];
                $fileSize = $_FILES["file"]["size"];
                $fileTemp = $_FILES["file"]["tmp_name"];
                $uploadDir = "./tones/";
                $newFileName = $uploadDir . uniqid() . $fileName;
                if (is_uploaded_file($fileTemp)) {
                    $uploadFile = $newFileName;
                    move_uploaded_file($fileTemp, $uploadFile);
                    $db = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                    $stmt = $db->prepare("INSERT INTO notes (name, path) VALUES (?, ?)");
                    $stmt->execute([$name, $newFileName]);

                    $response = array("status" => true, "message" => "File uploaded successfully");
                    echo json_encode($response);
                } else {
                    $response = array("status" => false, "message" => "File upload failed");
                    echo json_encode($response);
                }
                break;
            }
        case "get": {
                $db = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                $stmt = $db->query("SELECT * FROM notes");
                $fileNames = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($fileNames);
                break;
            }
        case "delete": {
                if (!isset($_POST["id"])) {
                    echo json_encode(["status" => false, "message" => "ID is required"]);
                    return false;
                }
                $id = $_POST["id"];
                $pdo = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() > 0) {
                    echo json_encode(["status" => true, "message" => "Successfully delete"]);
                } else {
                    echo json_encode(["status" => false, "message" => "Something went wrong"]);
                }
                break;
            }
        case "update": {
                if (!isset($_POST["id"])) {
                    echo json_encode(["status" => false, "message" => "ID is required"]);
                    return false;
                }
                if (!isset($_POST["name"])) {
                    echo json_encode(["status" => false, "message" => "Name is required"]);
                    return false;
                }
                $id = $_POST["id"];
                $name = $_POST["name"];
                $pdo = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                $stmt = $pdo->prepare("UPDATE notes SET name = ? WHERE id = ?");
                $stmt->execute([$name, $id]);
                if ($stmt->rowCount() > 0) {
                    echo json_encode(["status" => true, "message" => "Successfully Updated"]);
                } else {
                    echo json_encode(["status" => false, "message" => "Something went wrong"]);
                }
                break;
            }
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => false,
        "message" => "Something went wrong",
        "data" => $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "message" => "Something went wrong",
        "data" => $e->getMessage()
    ]);
}
