<?php

class Product
{
    private $conn;
    private $table = 'products';

    public $id;
    public $name;
    public $price;
    public $image;
    public $description;


    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function getProducts()
    {
        if (isset($_GET['idCategory'])) {
            $query = "SELECT * FROM " . $this->table . " where category_id = :category_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $_GET['idCategory']);
            $stmt->execute();
        } else {
            $query = "SELECT products.*,category.name AS categoryName FROM " . $this->table . " JOIN category on products.category_id = category.id;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getProductById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function createProduct($name, $price, $image, $description, $category_id)
    {
        $query = "INSERT INTO " . $this->table . " (name, price, image, description, category_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $price, $image, $description, $category_id]);
    }


    public function updateProduct($id, $name, $price, $image, $description, $category_id)
    {
        // Xây dựng câu truy vấn SQL
        $query = "UPDATE " . $this->table . " SET name = ?, price = ?, description = ?, category_id = ?";

        // Nếu có ảnh mới, thêm vào truy vấn
        if ($image) {
            $query .= ", image = ?";
        }
        $query .= " WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // Xây dựng mảng tham số
        $params = [$name, $price, $description, $category_id];
        if ($image) {
            $params[] = $image;
        }
        $params[] = $id;

        return $stmt->execute($params);
    }



    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function searchProduct($product_name)
    {
        // Tìm kiếm sản phẩm dựa trên tên
        $sql = "SELECT * FROM `products` WHERE `name` LIKE :product_name";
        $stmt = $this->conn->prepare($sql);
        $searchTerm = '%' . $product_name . '%'; // Thêm ký tự % để tìm kiếm gần đúng
        $stmt->bindParam(':product_name', $searchTerm);
        $stmt->execute();

        // Lấy tất cả các sản phẩm phù hợp
        $products = $stmt->fetchAll();

        if (!$products) {
            return [
                'status' => false,
                'message' => 'No products found.'
            ];
        }

        return [
            'status' => true,
            'products' => $products
        ];
    }

    public function productDetail($product_id)
    {
        $sql = "SELECT products.*, category.name as categoryName FROM `products` JOIN category on products.category_id = category.id WHERE products.id = :product_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $products = $stmt->fetch();
        if (!$products) {
            return [
                'status' => false,
                'message' => 'No products found.'
            ];
        }

        return [
            'status' => true,
            'products' => $products
        ];
    }
}
