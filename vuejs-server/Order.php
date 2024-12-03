<?php

class Order
{
    private $conn;
    private $table = 'cart';


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function checkout($user_id, $name, $address, $phone, $email)
    {

        // Truy vấn để lấy cart_id từ user_id
        $sql = "SELECT id AS cart_id 
        FROM cart 
        WHERE user_id = :user_id 
        LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cart_id = $result['cart_id'] ?? null;

        $sql = "SELECT SUM(price * quantity) AS total_price 
                FROM cart_detail 
                WHERE cart_id = :cart_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_price = $result['total_price'] ?? 0; // Nếu không có sản phẩm nào, tổng giá sẽ là 0


        $sql = "INSERT INTO `order_`(`user_id`, `date`, `total_price`, `name`, `address`, `phone`, `email`) VALUES (:user_id, :date_now, :total_price, :user_name,:user_address, :user_phone, :user_email )";

        $now = date('Y-m-d');
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':date_now', $now);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':user_name', $name);
        $stmt->bindParam(':user_address', $address);
        $stmt->bindParam(':user_phone', $phone);
        $stmt->bindParam(':user_email', $email);

        if ($stmt->execute()) {
            // Lấy order_id mới nhất
            $order_id = $this->conn->lastInsertId();

            $sql = "SELECT *
                FROM cart_detail 
                WHERE cart_id = :cart_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            $stmt->execute();

            $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($order_id) {
                foreach ($cart_items as $item) {
                    $sql = "INSERT INTO `order_detail`(`order_id`, `product_id`, `price`, `quantity`) 
                VALUES (:order_id, :product_id, :product_price, :quantity)";

                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $item['product_id']);
                    $stmt->bindParam(':product_price', $item['price']);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->execute();
                }

                $sql = "DELETE FROM cart_detail 
                    WHERE cart_id = :cart_id";

                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
                $stmt->execute();

                return true;
            } else {
                return false;
            }
        } else {
            return false; // Hoặc xử lý lỗi tại đây
        }
    }

    public function changeStatus($order_id, $status_code)
    {
        // Kiểm tra giá trị $status_code có hợp lệ hay không
        if (!in_array($status_code, [1, 2, 3, 4, 5])) {
            return false;
        }

        // Truy vấn cập nhật trạng thái đơn hàng
        $sql = "UPDATE `order_` 
                SET `status` = :status_code 
                WHERE `id` = :order_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status_code', $status_code, PDO::PARAM_INT);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function orderDetail($order_id)
    {
        // Truy vấn SQL để lấy chi tiết đơn hàng
        $sql = "SELECT od.id AS order_detail_id, 
                   od.product_id, 
                   od.price, 
                   od.quantity, 
                   p.name AS product_name, 
                   p.description AS product_description, 
                   p.image AS product_image
            FROM order_detail AS od
            INNER JOIN products AS p ON od.product_id = p.id
            WHERE od.order_id = :order_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);

        // Thực hiện truy vấn
        $stmt->execute();

        // Lấy dữ liệu dưới dạng mảng
        $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra nếu có dữ liệu
        if (!empty($order_details)) {
            return $order_details;
        } else {
            return false;
        }
    }

    public function showOrder($user_id)
    {
        // Truy vấn SQL để lấy danh sách đơn hàng của một người dùng
        $sql = "SELECT id AS order_id, 
                       date AS order_date, 
                       total_price, 
                       status, 
                       name AS customer_name, 
                       address, 
                       phone, 
                       email
                FROM order_
                WHERE user_id = :user_id
                ORDER BY date DESC"; // Sắp xếp theo ngày giảm dần

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Thực hiện truy vấn
        $stmt->execute();

        // Lấy dữ liệu dưới dạng mảng
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra nếu có dữ liệu
        if (!empty($orders)) {
            return $orders; // Trả về danh sách đơn hàng
        } else {
            throw new Exception("No orders found for User ID: " . $user_id);
        }
    }

    public function showOrderAdmin()
    {
        // Truy vấn SQL để lấy danh sách đơn hàng của một người dùng
        $sql = "SELECT id AS order_id, 
                       date AS order_date, 
                       total_price, 
                       status, 
                       name AS customer_name, 
                       address, 
                       phone, 
                       email
                FROM order_
                ORDER BY date DESC"; // Sắp xếp theo ngày giảm dần

        $stmt = $this->conn->prepare($sql);

        // Thực hiện truy vấn
        $stmt->execute();

        // Lấy dữ liệu dưới dạng mảng
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra nếu có dữ liệu
        if (!empty($orders)) {
            return $orders; // Trả về danh sách đơn hàng
        } else {
            return false;
        }
    }
}
