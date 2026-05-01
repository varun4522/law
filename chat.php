<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lexi AI - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            color: #0a0a0a;
            background: #fff;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        a { text-decoration: none; color: inherit; transition: all 0.3s ease; }

        /* Navigation */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #fff;
            border-bottom: 1px solid #e8e8e8;
            padding: 16px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            flex-shrink: 0;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: #0a0a0a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
        }

        .nav-links a {
            font-size: 14px;
            font-weight: 500;
            color: #555;
        }

        .nav-links a:hover {
            color: #0a0a0a;
        }

        .cta-buttons {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .btn-outline {
            padding: 10px 20px;
            border: 1.5px solid #0a0a0a;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: transparent;
            transition: all 0.3s;
        }

        .btn-outline:hover {
            background: #0a0a0a;
            color: #fff;
        }

        .btn-primary {
            padding: 10px 24px;
            background: #0a0a0a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #222;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Iframe Container */
        .chat-container {
            flex-grow: 1;
            width: 100%;
            background-color: #ffffff; /* Matches the new Chatbot white theme */
        }
        
        iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            nav {
                padding: 12px 0;
            }
            .navbar-container {
                padding: 0 16px;
            }
            .logo {
                font-size: 20px;
            }
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav>
    <div class="navbar-container">
        <div class="logo">
            <div class="logo-icon"><i class="fas fa-balance-scale"></i></div>
            <a href="index.php"><span>Law Connectors</span></a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php#features">Features</a></li>
            <li><a href="index.php#services">Services</a></li>
            <li><a href="chat.php" style="color: #0a0a0a; font-weight: 700;">Lexi AI</a></li>
            <li><a href="index.php#how">How It Works</a></li>
        </ul>
        <div class="cta-buttons">
            <a href="login.php" class="btn-primary">Sign In</a>
        </div>
    </div>
</nav>

<!-- Chatbot Iframe -->
<div class="chat-container">
    <iframe src="https://test.1xclube.org/api" title="Lexi AI Legal Assistant"></iframe>
</div>

</body>
</html>
