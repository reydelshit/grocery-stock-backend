<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        $sql = " SELECT * FROM stock_history";


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $stock = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($stock);
        }



        break;

    case "POST":
        $stock = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO stock_history (product_name, type, quantity, created_at, product_id) VALUES (:product_name, :type, :quantity, :created_at, :product_id)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':product_name', $stock->product_name);
        $stmt->bindParam(':type', $stock->type);
        $stmt->bindParam(':quantity', $stock->quantity);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':product_id', $stock->product_id);



        if ($stmt->execute()) {

            if ($stock->type == "Stock In") {
                $sql = "UPDATE product SET stocks = stocks + :quantity
                    WHERE product_id = :product_id";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':quantity', $stock->quantity);
                $stmt->bindParam(':product_id', $stock->product_id);
                $stmt->execute();
            } else {
                $sql = "UPDATE product SET stocks = stocks - :quantity
                    WHERE product_id = :product_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':quantity', $stock->quantity);
                $stmt->bindParam(':product_id', $stock->product_id);
                $stmt->execute();
            }

            $response = [
                "status" => "success",
                "message" => "stock successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "stock failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $supplier = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE medication SET status= :status
                    WHERE medication_id = :medication_id";
        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':medication_id', $supplier->medication_id);
        $stmt->bindParam(':status', $supplier->status);


        if ($stmt->execute()) {

            $response = [
                "status" => "success",
                "message" => "medication updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "medication update failed"
            ];
        }

        echo json_encode($response);
        break;
}
