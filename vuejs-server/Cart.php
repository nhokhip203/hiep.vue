<?php

class Cart
{
    private $conn;
    private $table = 'cart';


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findCart($user_id)
    {
        $sql = "SELECT * FROM `cart` WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $cart = $stmt->fetch();
        if (!$cart) {
            $sql = "INSERT INTO `cart`(`user_id`) VALUES (:user_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $sql = "SELECT * FROM `cart` WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $cart = $stmt->fetch();
        }
        return  $cart;
    }

    public function findProduct($product_id)
    {
        $sql = "SELECT * FROM `products` WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch();
        return $product;
    }

    public function addCart($product_id, $quantity, $user_id)
    {
        $cart = $this->findCart($user_id);
        if (!$cart) {
            return false; // Không tìm thấy giỏ hàng, trả về false
        }
        $product =  $this->findProduct($product_id);
        if (!$product) {
            return false; // Không tìm thấy sản phẩm, trả về false
        }
        // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
        $sql = "SELECT * FROM `cart_detail` WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cart_id', $cart['id']);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $cartDetail = $stmt->fetch();

        if ($cartDetail) {
            // Nếu sản phẩm đã tồn tại, tăng quantity
            $newQuantity = $cartDetail['quantity'] + $quantity;
            $sql = "UPDATE `cart_detail` SET `quantity` = :quantity WHERE `cart_id` = :cart_id AND `product_id` = :product_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':quantity', $newQuantity);
            $stmt->bindParam(':cart_id', $cart['id']);
            $stmt->bindParam(':product_id', $product_id);
            return $stmt->execute();
        } else {
            // Nếu sản phẩm chưa tồn tại, thêm mới vào cart_detail
            $sql = "INSERT INTO `cart_detail`(`cart_id`, `product_id`, `quantity`, `price`) VALUES (:cart_id, :product_id, :quantity, :price)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cart_id', $cart['id']);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':price', $product['price']);
            return $stmt->execute();
        }
    }

    public function cartDetail($user_id)
    {

        // Tìm giỏ hàng của người dùng
        $sql = "SELECT * FROM `cart` WHERE `user_id` = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $cart = $stmt->fetch();

        if (!$cart) {
            // Nếu không có giỏ hàng, trả về thông báo trống
            return [
                'status' => false,
                'message' => 'Giỏ hàng trống.'
            ];
        }

        // Lấy chi tiết các sản phẩm trong giỏ hàng
        $sql = "SELECT cd.*, p.name AS product_name, p.image AS product_image 
                FROM `cart_detail` cd
                INNER JOIN `products` p ON cd.product_id = p.id
                WHERE cd.cart_id = :cart_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cart_id', $cart['id']);
        $stmt->execute();
        $cartDetails = $stmt->fetchAll();

        return [
            'status' => true,
            'cart' => [
                'id' => $cart['id'],
                'user_id' => $cart['user_id'],
            ],
            'cart_details' => $cartDetails
        ];
    }

    public function updateCart($cart_id, $action, $product_id)
    {
        $sql = "select * from cart where id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $cart_id);
        $stmt->execute();
        $cart = $stmt->fetch();
        if (!$cart) {
            // Nếu không có giỏ hàng, trả về thông báo trống
            return [
                'status' => false,
                'message' => 'Giỏ hàng trống.'
            ];
        }

        $sql = "select * from cart_detail where cart_id = :cart_id and product_id=:product_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cart_id', $cart['id']);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $cart_detail = $stmt->fetch();
        switch ($action) {
            case "increase": {
                    $newQuantity = intval($cart_detail['quantity']) + 1;
                    $sql = "UPDATE `cart_detail` SET `quantity`=:quantity WHERE id=:id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':quantity', $newQuantity);
                    $stmt->bindParam(':id', $cart_detail['id']);
                    $stmt->execute();
                    break;
                }
            case "decrease": {
                    if (intval($cart_detail['quantity']) > 0) {
                        $newQuantity = intval($cart_detail['quantity']) - 1;
                        $sql = "UPDATE `cart_detail` SET `quantity`=:quantity WHERE id=:id";
                        $stmt = $this->conn->prepare($sql);
                        $stmt->bindParam(':quantity', $newQuantity);
                        $stmt->bindParam(':id', $cart_detail['id']);
                        $stmt->execute();
                    }
                    break;
                }
            case "delete": {
                    $sql = "DELETE FROM `cart_detail` WHERE id = :id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':id', $cart_detail['id']);
                    $stmt->execute();
                    break;
                }
        }
        return true;
    }
}
