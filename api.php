<?php
$dbHost = "localhost";
$dbName = "notes";
$dbUser = "postgres";
$dbPass = "postgres";

if (!isset($_POST['type'])) {
    echo json_encode(["status" => false, "message" => "Not found!"]);
    return;
}

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
    case "get":
        $db = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $stmt = $db->query("SELECT * FROM notes");
        $fileNames = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($fileNames);
}
