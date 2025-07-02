<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;
if (isset($_SESSION['user_id']) && file_exists('db_connect.php')) {
    require_once 'db_connect.php';
    
    $sql = "SELECT COUNT(ci.cart_item_id) as count
            FROM CART c
            JOIN CartItem ci ON c.cart_id = ci.cart_id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $cartCount = $row['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLITCH Game Store</title>
    <link rel="stylesheet" href="CSS/header-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Header Styles */
        header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 40px;
            background: rgb(26, 26, 26);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 30px rgb(0, 0, 0);
            border-bottom: 1px solid rgb(58, 98, 93);
            height: 60px;
        }

        /* Logo Styling */
        .logo-container {
            position: absolute;
            left: 40px;
            display: flex;
            align-items: center;
        }

        .header-logo {
            width: 60px;
            height: auto;
            transition: transform 0.3s ease;
        }

        .header-logo:hover {
            transform: rotate(10deg);
        }

        /* Navigation */
        nav {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 40px;
            padding: 0;
            margin: 0;
            justify-content: center;
        }

        nav ul li {
            display: inline;
            position: relative;
        }

        nav ul li a {
            text-decoration: none;
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            padding: 6px 0;
        }

        .login-btn {
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .login-btn {
            background-color: transparent;
            border: 1px solid #846A33;
            color: #846A33;
        }

        .login-btn:hover {
            background-color: rgb(170, 146, 93);
        }

        /* Dropdown Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            header {
                padding: 10px;
            }

            .header-logo {
                width: 50px;
            }

            nav ul {
                gap: 10px;
            }

            nav ul li a {
                font-size: 12px;
            }

            .acc-container {
                right: 60px;
            }

            .login-btn, .signup-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="logo-container">
        <a href=""><img src="Logo.png" alt="Logo" class="header-logo"></a>
    </div>
    <div class="nav-auth-wrapper">
        <nav>
            <ul>
                <li><a href="">الرئيسية</a></li>
                <li><a href="">العقود</a></li>
                <li><a href="">الإجازات</a></li>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <!-- Not Logged In State - Show Login/Signup -->
                    <li><a href="" class="login-btn">تسجيل الدخول</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
</body>
</html>