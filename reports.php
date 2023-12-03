<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        $sql = "SELECT stock_history.type, stock_history.quantity, stock_history.product_name, stock_history.product_id FROM stock_history INNER JOIN product ON product.product_id = stock_history.product_id";


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($reports);
        }



        break;
}
