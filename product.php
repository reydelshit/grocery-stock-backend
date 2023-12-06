<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['product_id'])) {
            $product_id_spe = $_GET['product_id'];
            $sql = "SELECT * FROM product WHERE product_id = :product_id";
        }


        if (!isset($_GET['product_id'])) {
            $sql = " SELECT * FROM product";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($product_id_spe)) {
                $stmt->bindParam(':product_id', $product_id_spe);
            }

            $stmt->execute();
            $product = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($product);
        }

        break;



    case "POST":
        $product = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO product (supplier_name, product_image, product_name, stocks) VALUES (:supplier_name, :product_image, :product_name,:stocks)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':supplier_name', $product->supplier_name);
        $stmt->bindParam(':product_image', $product->product_image);
        $stmt->bindParam(':product_name', $product->product_name);


        $stmt->bindParam(':stocks', $product->stocks);


        if ($stmt->execute()) {

            $last_id = $conn->lastInsertId();
            $type = "Stock In";
            $sql3 = "INSERT INTO stock_history (product_name, type, quantity, created_at, product_id) VALUES (:product_name, :type, :quantity, :created_at, :product_id)";
            $stmt3 = $conn->prepare($sql3);
            $created_at = date('Y-m-d');
            $stmt3->bindParam(':product_name', $product->product_name);
            $stmt3->bindParam(':type', $type);
            $stmt3->bindParam(':quantity', $product->stocks);
            $stmt3->bindParam(':created_at', $created_at);
            $stmt3->bindParam(':product_id', $last_id);

            $stmt3->execute();

            $response = [
                "status" => "success",
                "message" => "product successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "product failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $product = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE product SET product_name= :product_name, description = :description, expiration_date = :expiration_date, racks = :racks
                    WHERE product_id = :product_id";

        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':product_id', $product->product_id);
        $stmt->bindParam(':product_name', $product->product_name);
        $stmt->bindParam(':description', $product->description);
        $stmt->bindParam(':expiration_date', $product->expiration_date);
        $stmt->bindParam(':racks', $product->racks);


        if ($stmt->execute()) {

            $response = [
                "status" => "success",
                "message" => "product updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "product update failed"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $product = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM product WHERE product_id = :product_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_id', $product->product_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "product deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "product delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
