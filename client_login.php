<?php
session_start();
include "dbase.php";

// If already logged in, redirect to dashboard
if (isset($_SESSION['CLIENT_ISLOGIN']) && $_SESSION['CLIENT_ISLOGIN'] == "cLi3nt_s3cr3t_2026") {
    header("Location: client_dashboard.php");
    exit;
}

$error_msg = "";

if (isset($_POST['login'])) {
    $iduser = trim($_POST['iduser']);
    $passwd = $_POST['passwd'];
    
    if (empty($iduser) || empty($passwd)) {
        $error_msg = "Username dan Password harus diisi!";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id_user_client, iduser, nama, passwd, kodcustomer, stsaktif FROM r_user_client WHERE iduser = ?");
            $stmt->execute([$iduser]);
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Plaintext password comparison as requested in requirements for simple integration
                if ($user['passwd'] === $passwd) {
                    if ($user['stsaktif'] == 1) {
                        $_SESSION['CLIENT_ISLOGIN'] = "cLi3nt_s3cr3t_2026";
                        $_SESSION['CLIENT_ID_PK'] = $user['id_user_client'];
                        $_SESSION['CLIENT_IDUSER'] = $user['iduser'];
                        $_SESSION['CLIENT_NAMA'] = $user['nama'];
                        $_SESSION['CLIENT_KODCUSTOMER'] = $user['kodcustomer'];
                        
                        header("Location: client_dashboard.php");
                        exit;
                    } else {
                        $error_msg = "Akun Anda sudah dinonaktifkan. Silakan hubungi tim DSI.";
                    }
                } else {
                    $error_msg = "Password salah!";
                }
            } else {
                $error_msg = "Username tidak ditemukan!";
            }
        } catch(PDOException $e) {
            $error_msg = "Sistem sedang gangguan, silakan coba lagi nanti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSI Client Portal - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #0f172a;
            --accent: #38bdf8;
            --bg-color: #f1f5f9;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-main: #1e293b;
            --text-light: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--secondary);
            background-image: 
                radial-gradient(at 0% 0%, hsla(217,100%,50%,0.2) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(199,100%,50%,0.2) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            overflow: hidden;
        }

        /* Abstract shapes for background */
        .shape {
            position: absolute;
            filter: blur(60px);
            z-index: -1;
            opacity: 0.6;
        }
        .shape-1 {
            background: #3b82f6;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            top: -100px;
            left: -100px;
            animation: float 8s ease-in-out infinite;
        }
        .shape-2 {
            background: #0ea5e9;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(30px) scale(1.05); }
            100% { transform: translateY(0) scale(1); }
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            margin: 1rem;
            position: relative;
            z-index: 1;
            perspective: 1000px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform-style: preserve-3d;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { transform: translateY(40px) scale(0.95); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary);
            letter-spacing: -0.5px;
            margin-top: 10px;
        }

        .logo-container p {
            color: var(--text-light);
            font-size: 14px;
            margin-top: 5px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: white;
            font-size: 28px;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
            transform: rotate(-10deg);
            transition: transform 0.3s ease;
        }

        .glass-card:hover .logo-icon {
            transform: rotate(0deg) scale(1.05);
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--secondary);
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            color: var(--text-main);
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 40px;
            color: #94a3b8;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: #ffffff;
        }

        .input-group input:focus + i {
            color: var(--primary);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
            box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.39);
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px 15px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #fecaca;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: var(--text-light);
        }
        
        /* Mobile optimization */
        @media (max-width: 480px) {
            .glass-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="login-container">
        <div class="glass-card">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fa fa-briefcase"></i>
                </div>
                <h1>Client Portal</h1>
                <p>PT. DSI - Log & Progress Tracking</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="error-msg">
                    <i class="fa fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="client_login.php">
                <div class="input-group">
                    <label for="iduser">Username</label>
                    <input type="text" id="iduser" name="iduser" placeholder="Masukkan ID User Anda" required autocomplete="off">
                    <i class="fa fa-user"></i>
                </div>

                <div class="input-group">
                    <label for="passwd">Password</label>
                    <input type="password" id="passwd" name="passwd" placeholder="Masukkan Password Anda" required>
                    <i class="fa fa-lock"></i>
                </div>

                <button type="submit" name="login" class="btn-login">Masuk ke Portal</button>
            </form>

            <div class="footer">
                &copy; <?php echo date('Y'); ?> PT. DSI. Segala Hak Dilindungi.
            </div>
        </div>
    </div>

    <script>
        // Simple micro-interaction for input fields
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.style.transform = 'translateY(-2px)';
                input.parentElement.style.transition = 'transform 0.3s ease';
            });
            input.addEventListener('blur', () => {
                input.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
