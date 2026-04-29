<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Law Connectors - AI Legal Services & Advocate Network</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #0a0a0a;
            background: #fff;
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

        /* Hero Section */
        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 40px;
            text-align: center;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(36px, 6vw, 64px);
            font-weight: 700;
            margin-bottom: 24px;
            line-height: 1.15;
        }

        .hero-subtitle {
            font-size: 18px;
            color: #555;
            margin-bottom: 32px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 300;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 16px 32px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-large-primary {
            background: #0a0a0a;
            color: #fff;
            border: none;
        }

        .btn-large-primary:hover {
            background: #222;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .btn-large-secondary {
            background: #f0f0f0;
            color: #0a0a0a;
            border: 2px solid #0a0a0a;
        }

        .btn-large-secondary:hover {
            background: #0a0a0a;
            color: #fff;
        }

        /* Features Section */
        .features {
            background: #f8f8f8;
            padding: 80px 40px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 5vw, 48px);
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            font-size: 16px;
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: #fff;
            padding: 40px 32px;
            border-radius: 12px;
            border: 1px solid #e8e8e8;
            transition: all 0.3s;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            border-color: #0a0a0a;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: #f0f0f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 28px;
            color: #0a0a0a;
        }

        .feature-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #666;
            font-size: 15px;
            line-height: 1.8;
            font-weight: 300;
        }

        /* How It Works */
        .how-it-works {
            padding: 80px 40px;
            background: #fff;
        }

        .process-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }

        .step {
            position: relative;
            padding-left: 60px;
        }

        .step-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 48px;
            height: 48px;
            background: #0a0a0a;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
        }

        .step h4 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .step p {
            color: #666;
            font-size: 14px;
            line-height: 1.8;
            font-weight: 300;
        }

        /* Stats Section */
        .stats {
            background: #0a0a0a;
            color: #fff;
            padding: 80px 40px;
        }

        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 60px;
            text-align: center;
        }

        .stat-item h3 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-item p {
            font-size: 14px;
            color: #b0b0b0;
            font-weight: 500;
        }

        /* Services Detail */
        .services {
            padding: 80px 40px;
            background: #f8f8f8;
        }

        .services-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-item {
            background: #fff;
            padding: 50px;
            margin-bottom: 30px;
            border-radius: 12px;
            border-left: 4px solid #0a0a0a;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 40px;
            align-items: center;
        }

        .service-item:nth-child(even) {
            grid-template-columns: 1fr auto;
        }

        .service-icon {
            width: 100px;
            height: 100px;
            background: #f0f0f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #0a0a0a;
        }

        .service-content h3 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .service-content p {
            color: #666;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 16px;
            font-weight: 300;
        }

        .service-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 20px;
        }

        .service-feature {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #555;
        }

        .service-feature i {
            color: #0a0a0a;
            font-weight: 700;
        }

        /* CTA Section */
        .cta-section {
            background: #0a0a0a;
            color: #fff;
            padding: 80px 40px;
            text-align: center;
        }

        .cta-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 5vw, 48px);
            font-weight: 700;
            margin-bottom: 24px;
        }

        .cta-section p {
            font-size: 18px;
            color: #c0c0c0;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 300;
        }

        .cta-buttons-section {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-white {
            padding: 14px 32px;
            background: #fff;
            color: #0a0a0a;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-white:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: #1a1a1a;
            color: #999;
            padding: 60px 40px;
            font-size: 14px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h4 {
            color: #fff;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #999;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: #fff;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .social-links {
            display: flex;
            gap: 20px;
        }

        .social-links a {
            color: #999;
            font-size: 18px;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #fff;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                background: #fff;
            }

            nav {
                padding: 12px 0;
                background: #fff;
            }

            .navbar-container {
                padding: 0 16px;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                gap: 0;
            }

            .logo {
                gap: 8px;
                font-size: 20px;
            }

            .logo-icon {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }

            .nav-links {
                display: none;
            }

            .cta-buttons {
                display: flex;
                gap: 8px;
                flex-direction: row;
            }

            .btn-outline, .btn-primary {
                padding: 8px 14px;
                font-size: 12px;
            }

            .hero {
                padding: 40px 16px;
                background: #fff;
            }

            .hero h1 {
                font-size: 32px;
                margin-bottom: 16px;
            }

            .hero-subtitle {
                font-size: 15px;
                margin-bottom: 24px;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 12px;
            }

            .btn-large {
                padding: 14px 24px;
                font-size: 14px;
                width: 100%;
            }

            .features {
                padding: 40px 16px;
                background: #f5f5f5;
            }

            .section-title {
                font-size: 26px;
                margin-bottom: 12px;
            }

            .section-subtitle {
                font-size: 14px;
                margin-bottom: 32px;
                max-width: 100%;
            }

            .features-container {
                grid-template-columns: 1fr;
                gap: 16px;
                max-width: 100%;
            }

            .feature-card {
                padding: 24px 16px;
                border-radius: 8px;
                background: #fff;
            }

            .feature-icon {
                width: 48px;
                height: 48px;
                font-size: 24px;
            }

            .feature-card h3 {
                font-size: 18px;
                margin-bottom: 8px;
            }

            .feature-card p {
                font-size: 13px;
            }

            .how-it-works {
                padding: 40px 16px;
                background: #fff;
            }

            .process-steps {
                gap: 20px;
                margin-top: 32px;
            }

            .step {
                padding-left: 50px;
            }

            .step-number {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .step h4 {
                font-size: 16px;
                margin-bottom: 6px;
            }

            .step p {
                font-size: 13px;
            }

            .services {
                padding: 40px 16px;
                background: #f5f5f5;
            }

            .services-container {
                max-width: 100%;
            }

            .service-item {
                padding: 24px 16px;
                grid-template-columns: 1fr !important;
                gap: 16px;
                margin-bottom: 20px;
                background: #fff;
                border-radius: 8px;
            }

            .service-icon {
                width: 80px;
                height: 80px;
                font-size: 36px;
                margin: 0 auto;
            }

            .service-content h3 {
                font-size: 20px;
                margin-bottom: 12px;
            }

            .service-content p {
                font-size: 14px;
                margin-bottom: 12px;
            }

            .service-features {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .service-feature {
                font-size: 13px;
            }

            .stats {
                padding: 40px 16px;
                background: #0a0a0a;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }

            .stat-item h3 {
                font-size: 32px;
                margin-bottom: 4px;
            }

            .stat-item p {
                font-size: 12px;
            }

            .cta-section {
                padding: 40px 16px;
                background: #0a0a0a;
            }

            .cta-section h2 {
                font-size: 28px;
                margin-bottom: 16px;
            }

            .cta-section p {
                font-size: 16px;
                margin-bottom: 24px;
            }

            .cta-buttons-section {
                flex-direction: column;
                gap: 12px;
            }

            .btn-white {
                padding: 12px 24px;
                font-size: 14px;
                width: 100%;
            }

            footer {
                padding: 32px 16px;
                background: #1a1a1a;
            }

            .footer-container {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .footer-section {
                font-size: 13px;
            }

            .footer-section h4 {
                font-size: 14px;
                margin-bottom: 12px;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 12px;
                font-size: 12px;
                border-top: 1px solid #333;
                padding-top: 24px;
            }

            .social-links {
                gap: 16px;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .navbar-container {
                padding: 0 12px;
            }

            .logo {
                font-size: 18px;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .btn-outline, .btn-primary {
                padding: 6px 12px;
                font-size: 11px;
            }

            .hero {
                padding: 32px 12px;
            }

            .hero h1 {
                font-size: 26px;
                margin-bottom: 12px;
            }

            .hero-subtitle {
                font-size: 14px;
            }

            .btn-large {
                padding: 12px 20px;
                font-size: 13px;
            }

            .features {
                padding: 32px 12px;
            }

            .section-title {
                font-size: 22px;
            }

            .section-subtitle {
                font-size: 13px;
                margin-bottom: 24px;
            }

            .features-container {
                gap: 12px;
            }

            .feature-card {
                padding: 20px 12px;
            }

            .feature-card h3 {
                font-size: 16px;
            }

            .feature-card p {
                font-size: 12px;
            }

            .how-it-works {
                padding: 32px 12px;
            }

            .service-item {
                padding: 20px 12px;
                margin-bottom: 16px;
            }

            .service-icon {
                width: 70px;
                height: 70px;
                font-size: 32px;
            }

            .service-content h3 {
                font-size: 18px;
            }

            .service-content p {
                font-size: 13px;
            }

            .stat-item h3 {
                font-size: 28px;
            }

            .stats-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .cta-section {
                padding: 32px 12px;
            }

            .cta-section h2 {
                font-size: 24px;
            }

            .cta-section p {
                font-size: 14px;
            }

            footer {
                padding: 24px 12px;
            }

            .footer-container {
                gap: 16px;
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
            <span>Law Connectors</span>
        </div>
        <ul class="nav-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="chat.php">Lexi AI</a></li>
            <li><a href="#how">How It Works</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="cta-buttons">
            <a href="signup.php" class="btn-outline">Sign Up</a>
            <a href="login.php" class="btn-primary">Get Started</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <h1>Legal Expertise at Your Fingertips</h1>
    <p class="hero-subtitle">Connect with verified advocates, access AI-powered legal guidance, and manage all your consultations in one secure platform.</p>
    <div class="hero-buttons">
        <a href="login.php" class="btn-large btn-large-primary">
            <i class="fas fa-arrow-right" style="margin-right: 8px;"></i> Explore Services
        </a>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="features">
    <div class="section-title">Why Choose Law Connectors?</div>
    <p class="section-subtitle">Everything you need for affordable and accessible legal services</p>
    
    <div class="features-container">
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-video"></i></div>
            <h3>One-on-One Advocate Sessions</h3>
            <p>Book personal consultation sessions directly with experienced advocates. Get personalized legal advice tailored to your specific needs.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-brain"></i></div>
            <h3>AI Legal Assistant</h3>
            <p>Get instant legal guidance 24/7 from our advanced AI assistant, powered by legal expertise and real-world cases.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-comments"></i></div>
            <h3>Community Forum</h3>
            <p>Ask questions anonymously, connect with other users, and learn from community discussions and expert answers.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-lock"></i></div>
            <h3>Secure & Private</h3>
            <p>Your conversations are end-to-end encrypted and completely confidential. We prioritize your privacy always.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-wallet"></i></div>
            <h3>Integrated Wallet</h3>
            <p>Manage payments securely with our integrated wallet system. Flexible payment options and instant refunds.</p>
        </div>

    </div>
</section>

<!-- Services Detail -->
<section class="services" id="services">
    <div class="services-container">
        <div class="section-title" style="margin-bottom: 60px;">Our Services</div>

        <div class="service-item">
            <div class="service-icon"><i class="fas fa-robot"></i></div>
            <div class="service-content">
                <h3>Law AI - Instant Legal Guidance</h3>
                <p>Our AI-powered legal assistant provides instant answers to your legal questions, available 24/7 without any waiting time.</p>
                <div class="service-features">
                    <div class="service-feature"><i class="fas fa-check"></i> Instant responses</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Legal accuracy</div>
                    <div class="service-feature"><i class="fas fa-check"></i> 24/7 availability</div>
                </div>
                <a href="chat.php" class="btn-primary" style="display:inline-block; margin-top:20px;">Try Lexi AI Now</a>
            </div>
        </div>

        <div class="service-item">
            <div class="service-content">
                <h3>Connect with Advocates</h3>
                <p>Book consultations directly with verified legal professionals. Choose based on expertise, experience, and client ratings.</p>
                <div class="service-features">
                    <div class="service-feature"><i class="fas fa-check"></i> Video consultations</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Phone sessions</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Email support</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Document review</div>
                </div>
            </div>
            <div class="service-icon"><i class="fas fa-handshake"></i></div>
        </div>

        <div class="service-item">
            <div class="service-icon"><i class="fas fa-comments-dollar"></i></div>
            <div class="service-content">
                <h3>Community & Forum</h3>
                <p>Join our anonymous community forum to ask questions freely and learn from others' experiences and expert advice.</p>
                <div class="service-features">
                    <div class="service-feature"><i class="fas fa-check"></i> Anonymous posting</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Expert responses</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Case discussions</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Legal resources</div>
                </div>
            </div>
        </div>

        <div class="service-item">
            <div class="service-content">
                <h3>Document Management</h3>
                <p>Securely upload and manage your legal documents. Get review and guidance from expert advocates on your documents.</p>
                <div class="service-features">
                    <div class="service-feature"><i class="fas fa-check"></i> Secure cloud storage</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Version control</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Expert review</div>
                    <div class="service-feature"><i class="fas fa-check"></i> Downloadable copies</div>
                </div>
            </div>
            <div class="service-icon"><i class="fas fa-file-contract"></i></div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works" id="how">
    <div class="process-container">
        <div class="section-title" style="margin-bottom: 20px;">How It Works</div>
        <p class="section-subtitle">Simple, fast, and secure - get legal help in just a few steps</p>

        <div class="process-steps">
            <div class="step">
                <div class="step-number">1</div>
                <h4>Create Account</h4>
                <p>Sign up in seconds with your email. Complete your profile to get personalized recommendations.</p>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <h4>Choose Service</h4>
                <p>Select between AI assistance, advocate consultation, or community forum based on your needs.</p>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <h4>Get Help</h4>
                <p>Receive instant guidance or book a consultation with a verified advocate of your choice.</p>
            </div>

            <div class="step">
                <div class="step-number">4</div>
                <h4>Secure Payment</h4>
                <p>Pay securely through our integrated wallet system with multiple payment options available.</p>
            </div>

            <div class="step">
                <div class="step-number">5</div>
                <h4>Follow Up</h4>
                <p>Access your consultation history, documents, and continue support whenever you need it.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="stats-container">
        <div class="stat-item">
            <h3>5,000+</h3>
            <p>Happy Clients</p>
        </div>
        <div class="stat-item">
            <h3>24/7</h3>
            <p>AI Support</p>
        </div>
        <div class="stat-item">
            <h3>99%</h3>
            <p>Satisfaction Rate</p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <h2>Ready to Get Legal Help?</h2>
    <p>Join thousands of satisfied clients who trust Law Connectors for their legal needs.</p>
    <div class="cta-buttons-section">
        <a href="signup.php" class="btn-white">Sign Up Free</a>
        <a href="#features" class="btn-white" style="background: transparent; border: 2px solid #fff; color: #fff;">Learn More</a>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h4>Law Connectors</h4>
            <p>Connecting you with legal expertise when you need it most.</p>
            <div class="social-links" style="margin-top: 16px;">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <div class="footer-section">
            <h4>Services</h4>
            <ul>
                <li><a href="chat.php">AI Legal Assistant</a></li>
                <li><a href="#">Find Advocates</a></li>
                <li><a href="#">Community Forum</a></li>
                <li><a href="#">Document Review</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Company</h4>
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Careers</a></li>
                <li><a href="#">Press</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Legal</h4>
            <ul>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Support</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 Law Connectors. All rights reserved.</p>
        <div class="social-links">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Cookies</a>
        </div>
    </div>
</footer>

</body>
</html>
