<?php
session_start();
require_once __DIR__ . '/../config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM `admin` WHERE `username` = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = $admin['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bengali Cultural Association</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --red: #D43F3A;
            --gold: #E5A93B;
            --dark: #211A17;
            --white: #FFFFFF;
            --cream: #FFFBF0;
            --sand: #FBF4E6;
            --gray: #7A726E;
            --border: rgba(33, 26, 23, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--cream);
            color: var(--dark);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background-color: var(--white);
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(33, 26, 23, 0.06);
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .login-header {
            background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
            color: var(--white);
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: var(--gold);
        }

        .login-logo {
            font-size: 2.2rem;
            color: var(--gold);
            margin-bottom: 0.5rem;
        }

        .login-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .login-subtitle {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.7);
            margin-top: 0.2rem;
        }

        .login-body {
            padding: 2.5rem 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.8rem;
            font-size: 0.95rem;
            font-family: 'Outfit', sans-serif;
            background-color: var(--sand);
            border: 1px solid transparent;
            border-radius: 8px;
            color: var(--dark);
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            background-color: var(--white);
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(229, 169, 59, 0.15);
        }

        .form-control:focus + .input-icon {
            color: var(--gold);
        }

        .error-alert {
            background-color: #FDF2F2;
            border: 1px solid #F5C6C6;
            color: #C81E1E;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .btn-login {
            width: 100%;
            background-color: var(--red);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 0.9rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-login:hover {
            background-color: #B9302B;
            box-shadow: 0 4px 15px rgba(212, 63, 58, 0.2);
            transform: translateY(-1px);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: var(--gray);
        }

        .login-footer a {
            color: var(--red);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo"><i class="fa-solid fa-dharmachakra"></i></div>
            <h1 class="login-title">Association Portal</h1>
            <span class="login-subtitle">Administrator Access</span>
        </div>

        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="error-alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter admin username" required autocomplete="username">
                        <i class="fa-solid fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
                        <i class="fa-solid fa-lock input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>Login securely</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="login-footer">
                <p>Return to <a href="../index.php">Website Homepage</a></p>
            </div>
        </div>
    </div>

</body>
</html>
