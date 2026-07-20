<?php

include "dbase.php"; 
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$apiKey = $_SERVER['HTTP_X_API_KEY'];
if (empty($apiKey) || $apiKey !== $env['SECRET_KEY']) {
    http_response_code(401);
    echo json_encode([
        'status' => false,
        'message' => "Unauthorize"
    ]);
    exit;
}
$act = $_GET['act'] ?? '';

if ($method === 'GET') 
{
    if ($act === 'getUser')
    {
        try {
            $result = $conn->query("SELECT iduser, nama, fingerprint_id FROM ruser");
            
            http_response_code(200);
            echo json_encode([
                'status' => true,
                'message' => 'Success',
                'data' => $result->rowCount() > 0 ? $result->fetchAll(PDO::FETCH_ASSOC) : []
            ]);
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
    else 
    {
        http_response_code(404);
        echo json_encode([
            'status' => false,
            'message' => "Action Not Found"
        ]);
        exit;
    }
}

else if ($method === 'POST') 
{
    if ($act === 'attandance')
    {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!isset($data['data']) || !is_array($data['data'])) {
                http_response_code(400);
                echo json_encode([
                    'status' => false,
                    'message' => "Invalid payload"
                ]);
                exit;
            }

            $attendanc = $data['data'];
            // BULK INSERT
            $values = [];
            $params = [];
            foreach ($attendanc as $row) {
                $values[] = "(?, ?, ?, ?)";
                $params[] = $row['userid'];
                $params[] = $row['tanggal'];
                $params[] = $row['hadir'];
                $params[] = $row['pulang'];
            } 
            $sql = "INSERT INTO tkehadiran (iduser, tanggal, hadir, pulang) VALUES " . implode(',',$values)
                    . " ON DUPLICATE KEY UPDATE
                        hadir = IF(hadir='' OR VALUES(hadir) < hadir, VALUES(hadir), hadir),
                        pulang = IF(pulang='' OR VALUES(pulang) > pulang, VALUES(pulang), pulang)";
            $stmt = $conn->prepare($sql);
            if (!$stmt->execute($params)) {
                $error = $stmt->errorInfo();
                error_log('ERROR INSERT: '.var_export($error));
                http_response_code(500);
                echo json_encode([
                    'status' => false,
                    'message' => [
                        'SQLSTATE' => $error[0],
                        'Error Code' => $error[1],
                        'Message' => $error[2]
                    ]
                ]);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }   
    }
}

else 
{
    http_response_code(405);
    echo json_encode([
        'status' => false,
        'message' => "Method Not Allowed"
    ]);
    exit;
}