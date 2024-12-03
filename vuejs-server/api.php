<?php
// Cho phép truy cập từ mọi nguồn gốc (CORS)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Xử lý các yêu cầu preflight của phương thức OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// Import các file class
require_once 'Database.php';
require_once 'Product.php';
require_once 'Category.php';
require_once 'User.php';
require_once 'Cart.php';
require_once 'Order.php';


// Khởi tạo kết nối đến cơ sở dữ liệu
$database = new Database();
$db = $database->getConnection();

// Khởi tạo class Product và Category
$product = new Product($db);
$category = new Category($db);
$user = new User($db);
$cart = new Cart($db);
$order = new Order($db);


// Lấy phương thức HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Lấy phần path từ URL, để xử lý các route
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

// Kiểm tra _method để giả lập phương thức PUT hoặc DELETE
if (isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
} else {
    $method = $_SERVER['REQUEST_METHOD'];
}

// Kiểm tra route và xử lý API tương ứng
switch ($request[0]) {
    case 'products':
        switch ($method) {
            case 'GET':
                if (isset($request[1]) && is_numeric($request[1])) {
                    // Lấy một sản phẩm theo ID
                    $id = intval($request[1]);
                    $result = $product->getProductById($id);
                    echo json_encode($result);
                } else {
                    // Lấy tất cả sản phẩm
                    $result = $product->getProducts();
                    echo json_encode($result);
                }
                break;

            case 'POST':
                // Thêm sản phẩm mới
                $name = $_POST['name'] ?? null;
                $description = $_POST['description'] ?? null;
                $price = $_POST['price'] ?? null;
                $category_id = $_POST['category_id'] ?? null;

                // $image = $input['image'] ?? $_POST['image'] ?? null;

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $imageTmpPath = $_FILES['image']['tmp_name'];
                    $imageName = $_FILES['image']['name'];
                    $imageSize = $_FILES['image']['size'];
                    $imageType = $_FILES['image']['type'];

                    // Kiểm tra định dạng file
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($imageType, $allowedTypes)) {
                        // Tạo tên file duy nhất và di chuyển file
                        $uploadDir = __DIR__ . '/uploads/product/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $imagePath = $uploadDir . uniqid() . '_' . basename($imageName);

                        if (move_uploaded_file($imageTmpPath, $imagePath)) {
                            // Lưu đường dẫn vào DB
                            $image = basename($imagePath); // Lưu tên file để sử dụng sau
                        } else {
                            echo json_encode(['message' => 'Lỗi khi lưu ảnh']);
                            exit;
                        }
                    } else {
                        echo json_encode(['message' => 'Định dạng ảnh không hợp lệ']);
                        exit;
                    }
                } else {
                    $image = null;
                }


                if ($product->createProduct($name, $price, $image, $description, $category_id)) {
                    echo json_encode(['message' => 'Sản phẩm đã được thêm']);
                } else {
                    echo json_encode(['message' => 'Thêm sản phẩm thất bại']);
                }
                break;

            case 'PUT':
                // Cập nhật sản phẩm
                $id = intval($request[1]);

                $name = $_POST['name'] ?? null;
                $description = $_POST['description'] ?? null;
                $price = $_POST['price'] ?? null;
                $category_id = $_POST['category_id'] ?? null;

                $image = null;

                $currentProduct = $product->getProductById($id);
                if (!$currentProduct) {
                    echo json_encode(['message' => 'Sản phẩm không tồn tại']);
                    exit;
                }


                // Kiểm tra nếu có file ảnh được tải lên
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $imageTmpPath = $_FILES['image']['tmp_name'];
                    $imageName = $_FILES['image']['name'];
                    $imageType = $_FILES['image']['type'];

                    // Kiểm tra định dạng file
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($imageType, $allowedTypes)) {
                        $uploadDir = __DIR__ . '/uploads/product/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $imagePath = $uploadDir . uniqid() . '_' . basename($imageName);

                        // Xóa ảnh cũ nếu có
                        if (!empty($currentProduct['image'])) {
                            $oldImagePath = $uploadDir . $currentProduct['image'];
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }

                        if (move_uploaded_file($imageTmpPath, $imagePath)) {
                            $image = basename($imagePath); // Lưu tên file
                        } else {
                            echo json_encode(['message' => 'Lỗi khi lưu ảnh']);
                            exit;
                        }
                    } else {
                        echo json_encode(['message' => 'Định dạng ảnh không hợp lệ']);
                        exit;
                    }
                }

                // Gọi hàm cập nhật sản phẩm
                if ($product->updateProduct($id, $name, $price, $image, $description, $category_id)) {
                    echo json_encode(['message' => 'Sản phẩm đã được cập nhật']);
                } else {
                    echo json_encode(['message' => 'Cập nhật sản phẩm thất bại']);
                }

                break;

            case 'DELETE':
                // Xóa sản phẩm
                $id = intval($request[1]);

                $currentProduct = $product->getProductById($id);
                if (!$currentProduct) {
                    echo json_encode(['message' => 'Sản phẩm không tồn tại']);
                    exit;
                }

                // Xóa ảnh cũ nếu có
                $uploadDir = __DIR__ . '/uploads/product/';
                if (!empty($currentProduct['image'])) {
                    $oldImagePath = $uploadDir . $currentProduct['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                if ($product->deleteProduct($id)) {
                    echo json_encode(['message' => 'Sản phẩm đã được xóa']);
                } else {
                    echo json_encode(['message' => 'Xóa sản phẩm thất bại']);
                }
                break;
        }
        break;

    case 'categories':
        switch ($method) {
            case 'GET':
                if (isset($request[1]) && is_numeric($request[1])) {
                    // Lấy một danh mục theo ID
                    $id = intval($request[1]);
                    $result = $category->getCategoryById($id);
                    echo json_encode($result);
                } else {
                    // Lấy tất cả danh mục
                    $result = $category->getCategories();
                    echo json_encode($result);
                }
                break;

            case 'POST':
                // Thêm danh mục mới
                // Nhận dữ liệu từ form-data
                $input = json_decode(file_get_contents('php://input'), true);
                $name = $input['name'] ?? $_POST['name'] ?? null;
                $description = $input['description'] ?? $_POST['description'] ?? null;

                // Kiểm tra dữ liệu
                if (empty($name) || empty($description)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                // Gọi hàm thêm danh mục
                if ($category->createCategory($name, $description)) {
                    echo json_encode(['message' => 'Danh mục đã được thêm']);
                } else {
                    echo json_encode(['message' => 'Thêm danh mục thất bại']);
                }
                break;

            case 'PUT':
                // Lấy ID từ URL
                $id = intval($request[1]);

                // Lấy dữ liệu từ form-data
                $name = isset($_POST['name']) ? $_POST['name'] : null;
                $description = isset($_POST['description']) ? $_POST['description'] : null;

                // Kiểm tra dữ liệu đầu vào
                if (empty($name) || empty($description)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                // Cập nhật danh mục
                if ($category->updateCategory($id, $name, $description)) {
                    echo json_encode(['message' => 'Danh mục đã được cập nhật']);
                } else {
                    echo json_encode(['message' => 'Cập nhật danh mục thất bại']);
                }
                break;

            case 'DELETE':
                // Xóa danh mục
                $id = intval($request[1]);
                if ($category->deleteCategory($id)) {
                    echo json_encode(['message' => 'Danh mục đã được xóa']);
                } else {
                    echo json_encode(['message' => 'Xóa danh mục thất bại']);
                }
                break;
        }
        break;
    case 'users':
        switch ($method) {
            case 'GET':

                if (isset($request[1]) && is_numeric($request[1])) {
                    // Lấy một người dùng theo ID
                    $id = intval($request[1]);
                    $result = $user->getUserById($id);
                    echo json_encode($result);
                } else {
                    // Lấy tất cả người dùng
                    $result = $user->getUsers();
                    echo json_encode($result);
                }
                break;

            case 'POST':
                // Thêm người dùng mới
                $input = json_decode(file_get_contents('php://input'), true);
                $name = $input['name'] ?? $_POST['name'] ?? null;
                $email = $input['email'] ?? $_POST['email'] ?? null;
                $password = $input['password'] ?? $_POST['password'] ?? null;
                $role = $input['role'] ?? $_POST['role'] ?? null;
                $note = $input['note'] ?? $_POST['note'] ?? null;

                // Kiểm tra dữ liệu
                if (empty($name) || empty($email) || empty($password) || empty($role)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                if ($user->createUser($name, $email, $password, $role, $note)) {
                    echo json_encode(['message' => 'Người dùng đã được thêm']);
                } else {
                    echo json_encode(['message' => 'Thêm người dùng thất bại']);
                }
                break;

            case 'PUT':

                $input = json_decode(file_get_contents('php://input'), true);
                $id = intval($request[1]);
                $name = isset($_POST['name']) ? $_POST['name'] : null;
                $email =  isset($_POST['email']) ? $_POST['email'] : null;
                $password = isset($_POST['password']) ? $_POST['password'] : null;
                $role =  isset($_POST['role']) ? $_POST['role'] : null;
                $note =  isset($_POST['note']) ? $_POST['note'] : null;

                if (empty($name) || empty($email) || empty($role)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                if ($user->updateUser($id, $name, $email, $password, $role, $note)) {
                    echo json_encode(['message' => 'Người dùng đã được cập nhật']);
                } else {
                    echo json_encode(['message' => 'Cập nhật người dùng thất bại']);
                }
                break;

            case 'DELETE':
                // Xóa người dùng
                $id = intval($request[1]);
                if ($user->deleteUser($id)) {
                    echo json_encode(['message' => 'Người dùng đã được xóa']);
                } else {
                    echo json_encode(['message' => 'Xóa người dùng thất bại']);
                }
                break;
        }
        break;
    case 'login':
        switch ($method) {
            case 'POST':
                // echo json_encode(password_hash('123456', PASSWORD_BCRYPT));
                $input = json_decode(file_get_contents('php://input'), true);
                $email = $input['email'] ?? $_POST['email'] ?? null;
                $password = $input['password'] ?? $_POST['password'] ?? null;

                if (empty($email) || empty($password)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $userLogin = $user->checkLogin($email, $password);
                if ($userLogin) {
                    echo json_encode(
                        [
                            'status' => true,
                            'data' => $userLogin
                        ]
                    );
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => 'Tài khoản hoặc mật khẩu không đúng'
                    ]);
                }
                break;
        }
        break;

    case 'register':
        switch ($method) {
            case 'POST':
                // Thêm người dùng mới
                $input = json_decode(file_get_contents('php://input'), true);
                $name = $input['name'] ?? $_POST['name'] ?? null;
                $email = $input['email'] ?? $_POST['email'] ?? null;
                $password = $input['password'] ?? $_POST['password'] ?? null;
                $role = "user";
                $note = $input['note'] ?? $_POST['note'] ?? null;

                // Kiểm tra dữ liệu
                if (empty($name) || empty($email) || empty($password) || empty($role)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                if ($user->createUser($name, $email, $password, $role, $note)) {
                    echo json_encode(['message' => 'Người dùng đã được thêm']);
                } else {
                    echo json_encode(['message' => 'Thêm người dùng thất bại']);
                }
                break;
        }
        break;

    case 'cart':
        switch ($method) {
            case 'GET':
                // Thêm người dùng mới
                // $input = json_decode(file_get_contents('php://input'), true);
                $product_id = $_GET['product_id'];
                $quantity = $_GET['quantity'];
                $user_id = $_GET['user_id'];

                // Kiểm tra dữ liệu
                if (empty($product_id) || empty($quantity) || empty($user_id)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                if ($cart->addCart($product_id, $quantity, $user_id)) {
                    echo json_encode(['message' => 'Thêm giỏ hàng thành công']);
                } else {
                    echo json_encode(['message' => 'Thêm giỏ hàng không thành công']);
                }
                break;
        }
        break;
    case 'cart-detail':
        switch ($method) {
            case 'GET':
                // Thêm người dùng mới
                // $input = json_decode(file_get_contents('php://input'), true);
                $user_id = $_GET['user_id'];

                // Kiểm tra dữ liệu
                if (empty($user_id)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $data = $cart->cartDetail($user_id);
                echo json_encode($data);
                break;
        }
        break;
    case 'cart-update':
        switch ($method) {
            case 'GET':
                // Thêm người dùng mới
                // $input = json_decode(file_get_contents('php://input'), true);
                $cart_id = $_GET['cart_id'];
                $action = $_GET['action'];
                $product_id = $_GET['product_id'];

                // Kiểm tra dữ liệu
                if (empty($cart_id) || empty($action) || empty($product_id)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $data = $cart->updateCart($cart_id, $action, $product_id);
                echo json_encode($data);
                break;
        }
        break;
    case 'search-product':
        switch ($method) {
            case 'POST':
                // Thêm người dùng mới
                $input = json_decode(file_get_contents('php://input'), true);
                $product_name = $input['product_name'] ?? $_POST['product_name'] ?? null;
                // Kiểm tra dữ liệu
                if (empty($product_name)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $data = $product->searchProduct($product_name);
                echo json_encode($data);
                break;
        }
        break;
    case 'product-detail':
        switch ($method) {
            case 'GET':
                // Thêm người dùng mới
                // $input = json_decode(file_get_contents('php://input'), true);
                $product_id = $_GET['product_id'];
                // Kiểm tra dữ liệu
                if (empty($product_id)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $data = $product->productDetail($product_id);
                echo json_encode($data);
                break;
        }
        break;
    case 'check-out':
        switch ($method) {
            case 'POST':
                // Thêm người dùng mới
                $user_id = $_POST['user_id'] ?? null;

                $name = $_POST['name'] ?? null;
                $address = $_POST['address'] ?? null;
                $phone = $_POST['phone'] ?? null;
                $email = $_POST['email'] ?? null;


                // Kiểm tra dữ liệu
                if (empty($name) && empty($address) && empty($phone) && empty($email)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $data = $order->checkout($user_id, $name, $address, $phone, $email);
                echo json_encode($data);
                break;
        }
        break;
    case 'change-status':
        switch ($method) {
            case 'GET':
                // Thêm người dùng mới
                $order_id = $_GET['order_id'] ?? null;
                $status_code = $_GET['status_code'] ?? null;


                // Kiểm tra dữ liệu
                if (empty($order_id) && empty($status_code)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $data = $order->changeStatus($order_id, $status_code);
                echo json_encode($data);
                break;
        }
        break;
    case 'order_detail':
        switch ($method) {
            case 'GET':
                // Thêm người dùng mới
                $order_id = $_GET['order_id'] ?? null;

                // Kiểm tra dữ liệu
                if (empty($order_id)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $data = $order->orderDetail($order_id);
                echo json_encode($data);
                break;
        }
        break;
    case 'show_order':
        switch ($method) {
            case 'GET':
                // Thêm người dùng mới
                $user_id = $_GET['user_id'] ?? null;

                // Kiểm tra dữ liệu
                if (empty($user_id)) {
                    echo json_encode(['message' => 'Dữ liệu không hợp lệ']);
                    break;
                }

                $data = $order->showOrder($user_id);
                echo json_encode($data);
                break;
        }
        break;
    case 'show_order_admin':
        switch ($method) {
            case 'GET':
                $data = $order->showOrderAdmin();
                echo json_encode($data);
                break;
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'API không hợp lệ']);
        break;
}



// GET (Lấy tất cả sản phẩm): GET /api.php/products
// GET (Lấy sản phẩm theo ID): GET /api.php/products/{id}
// POST (Thêm sản phẩm mới): POST /api.php/products (Dữ liệu JSON)
// PUT (Cập nhật sản phẩm): PUT /api.php/products/{id} (Dữ liệu JSON)
// DELETE (Xóa sản phẩm): DELETE /api.php/products/{id}


// GET tất cả danh mục: GET /api.php/categories
// GET danh mục theo ID: GET /api.php/categories/{id}
// POST thêm danh mục mới: POST /api.php/categories
// PUT cập nhật danh mục: PUT /api.php/categories/{id}
// DELETE xóa danh mục: DELETE /api.php/categories/{id}