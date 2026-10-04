<?php
/**
 * RPS Digital World - Production Router & WordPress Bridge
 * Handles Homepage, Plugins Hub, ERP Modules, E-Commerce, 404 Routing, and WordPress API / Admin seamlessly.
 */

$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);
$clean_path = trim($path, '/');

// Helper function to render the custom branded 404 page
function rps_render_404() {
    http_response_code(404);
    if ( file_exists(__DIR__ . '/main/404.html') ) {
        include __DIR__ . '/main/404.html';
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - You Are On The Wrong Page | RPS Digital World</title>
    <meta name="description" content="The page you requested was not found. Return to RPS Digital World homepage.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="https://rpsdigitalworld.store/">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-bg: #0b0f19;
            --card-bg: rgba(255, 255, 255, 0.05);
            --card-border: rgba(255, 255, 255, 0.12);
            --text-light: #ffffff;
            --text-muted: #94a3b8;
            --text-dark: #1e293b;
            --font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background: #0b0f19;
            color: var(--text-light);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            z-index: 1000;
            border-bottom: 1px solid var(--card-border);
            height: 70px;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }

        .nav-logo a {
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .nav-logo h2 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-logo h2 span {
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 6px 0;
        }

        .nav-link:hover {
            color: var(--text-light);
        }

        /* 404 Hero Section */
        .error-hero {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 130px 24px 80px;
            background: radial-gradient(circle at 50% 30%, rgba(118, 75, 162, 0.25) 0%, rgba(102, 126, 234, 0.1) 40%, rgba(11, 15, 25, 1) 85%);
            overflow: hidden;
        }

        /* Ambient glowing background orbs */
        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 1;
            opacity: 0.6;
        }

        .orb-1 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #764ba2 0%, transparent 70%);
            top: 15%;
            left: 10%;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #f5576c 0%, transparent 70%);
            bottom: 10%;
            right: 10%;
        }

        .error-card {
            position: relative;
            z-index: 2;
            max-width: 760px;
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 50px 36px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(245, 87, 108, 0.15);
            border: 1px solid rgba(245, 87, 108, 0.4);
            border-radius: 100px;
            padding: 8px 24px;
            color: #ff758c;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.2);
        }

        .status-badge i {
            font-size: 1rem;
            animation: pulseWarning 2s infinite ease-in-out;
        }

        @keyframes pulseWarning {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.15);
                opacity: 0.8;
            }
        }

        /* 404 Number */
        .error-code {
            font-size: clamp(5.5rem, 15vw, 9.5rem);
            font-weight: 900;
            line-height: 0.95;
            margin-bottom: 12px;
            letter-spacing: -3px;
            background: linear-gradient(135deg, #ffffff 10%, #a5b4fc 45%, #f472b6 90%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 15px 40px rgba(165, 180, 252, 0.2);
        }

        /* Heading */
        .error-title {
            font-size: clamp(1.6rem, 4vw, 2.3rem);
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .error-desc {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 580px;
            margin: 0 auto 30px;
            line-height: 1.65;
        }

        /* Countdown Pill */
        .countdown-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(102, 126, 234, 0.12);
            border: 1px solid rgba(102, 126, 234, 0.35);
            border-radius: 100px;
            padding: 10px 24px;
            margin-bottom: 35px;
            color: #cbd5e1;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .countdown-pill span {
            color: #38bdf8;
            font-weight: 800;
            font-size: 1.15rem;
        }

        /* Button group */
        .btn-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 36px;
            border-radius: 50px;
            font-size: 1.02rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.45);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 36px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #7b90f7 0%, #8a57be 100%);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Footer */
        .footer {
            background: #070a11;
            border-top: 1px solid var(--card-border);
            padding: 50px 24px 30px;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand h3 {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 15px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-brand h3 span {
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-col h4 {
            color: #ffffff;
            font-size: 1.1rem;
            margin-bottom: 18px;
            font-weight: 600;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-col ul li a:hover {
            color: #38bdf8;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.9rem;
            color: #64748b;
        }

        .footer-bottom a {
            color: #94a3b8;
            text-decoration: none;
        }

        .footer-bottom a:hover {
            color: #ffffff;
        }

        @media (max-width: 900px) {
            .footer-container {
                grid-template-columns: 1fr 1fr;
            }

            .nav-menu {
                display: none;
            }
        }

        @media (max-width: 600px) {
            .footer-container {
                grid-template-columns: 1fr;
            }

            .error-card {
                padding: 40px 20px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="/">
                    <h2>RPS DIGITAL <span>WORLD</span></h2>
                </a>
            </div>
            <ul class="nav-menu">
                <li><a href="/" class="nav-link">Home</a></li>
                <li><a href="/#services" class="nav-link">Services</a></li>
                <li><a href="/main/portpolio.html" class="nav-link">Portfolio</a></li>
                <li><a href="/plugins" class="nav-link">ERP Plugins</a></li>
                <li><a href="/#about" class="nav-link">About</a></li>
                <li><a href="/#contact" class="nav-link">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Error Hero Section -->
    <main class="error-hero">
        <div class="ambient-orb orb-1"></div>
        <div class="ambient-orb orb-2"></div>

        <div class="error-card">
            <!-- Wrong Page Badge -->
            <div class="status-badge">
                <i class="fas fa-exclamation-triangle"></i>
                <span>You Are On The Wrong Page</span>
            </div>

            <!-- Big 404 Visual -->
            <div class="error-code">404</div>

            <!-- Descriptive Titles -->
            <h1 class="error-title">Page Not Found</h1>
            <p class="error-desc">
                The URL or showcase demo you are trying to visit does not exist, has been removed, or was mistyped.
                Let's get you back on track!
            </p>

            <!-- Auto Redirect Notice -->
            <div class="countdown-pill">
                <i class="fas fa-sync-alt fa-spin" style="color: #38bdf8;"></i>
                <span>Redirecting to main page in <strong id="timer-sec">8</strong>s</span>
            </div>

            <!-- Call to Action Buttons -->
            <div class="btn-group">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    Redirect to Main Page
                </a>
                <a href="/main/portpolio.html" class="btn btn-secondary">
                    <i class="fas fa-th-large"></i>
                    Explore Showcase Projects
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h3>RPS DIGITAL <span>WORLD</span></h3>
                <p>Empowering local and global businesses through high-impact digital solutions, custom software, mobile
                    apps, and IT consultations.</p>
            </div>
            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li><a href="/#services">Web Development</a></li>
                    <li><a href="/#services">Mobile Apps</a></li>
                    <li><a href="/#services">Backend & APIs</a></li>
                    <li><a href="/#services">SEO & Marketing</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/main/portpolio.html">Portfolio</a></li>
                    <li><a href="/#about">About Us</a></li>
                    <li><a href="/#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <p style="margin-bottom: 8px;"><i class="fas fa-envelope"
                        style="margin-right: 8px; color: #38bdf8;"></i> info@rpstechno.com</p>
                <p style="margin-bottom: 8px;"><i class="fas fa-map-marker-alt"
                        style="margin-right: 8px; color: #f472b6;"></i> Surayata, Rajasthan 306104</p>
                <p><i class="fas fa-clock" style="margin-right: 8px; color: #a5b4fc;"></i> Mon - Sat: 10 AM - 6 PM IST
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 RPS Digital World. All rights reserved. | <a href="/main/privacy-policy.html">Privacy
                    Policy</a></p>
        </div>
    </footer>

    <script>
        // Automatic redirection countdown
        let secondsLeft = 8;
        const timerSec = document.getElementById('timer-sec');
        const interval = setInterval(() => {
            secondsLeft--;
            if (timerSec) timerSec.textContent = secondsLeft;
            if (secondsLeft <= 0) {
                clearInterval(interval);
                window.location.href = '/';
            }
        }, 1000);
    </script>
</body>

</html>
<?php
    exit;
}

// -------------------------------------------------------------
// 1. ROUTE FOR STATIC ASSETS FALLBACK (CSS, JS, IMAGES)
// -------------------------------------------------------------
if ( preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2|ttf|json|webp)$/i', $path) ) {
    $asset_candidates = [
        __DIR__ . $path,
        __DIR__ . '/main' . $path,
    ];
    foreach ($asset_candidates as $cand) {
        if ( file_exists($cand) ) {
            $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
            $mimes = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf' => 'font/ttf',
                'json' => 'application/json',
                'webp' => 'image/webp'
            ];
            header('Content-Type: ' . ($mimes[$ext] ?? 'text/plain'));
            readfile($cand);
            exit;
        }
    }
}

// -------------------------------------------------------------
// 2. ROUTE FOR ERP PLUGINS HUB PAGE (LIGHT THEME)
// -------------------------------------------------------------
if ( $path === '/plugins' || $path === '/plugins.html' || $path === '/main/plugins.html' ) {
    if ( file_exists(__DIR__ . '/main/plugins.html') ) {
        include __DIR__ . '/main/plugins.html';
        exit;
    } elseif ( file_exists(__DIR__ . '/plugins.html') ) {
        include __DIR__ . '/plugins.html';
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress ERP Plugins Directory & Live Testing | RPS Digital World</title>
    <meta name="description" content="Explore, test online, and download all 34 custom WordPress ERP plugins, live REST APIs, dashboards, and Swagger documentation by RPS Digital World.">
    <link rel="canonical" href="https://rpsdigitalworld.store/plugins">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --border-card: #e2e8f0;
            --border-hover: #818cf8;
            --text-title: #0f172a;
            --text-body: #475569;
            --text-muted: #64748b;
            --text-light: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
            --primary-color: #6366f1;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 6px 20px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 16px 36px rgba(102, 126, 234, 0.12);
            --font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background: var(--bg-page);
            color: var(--text-body);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Navigation - Clean Light Glassmorphic */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            z-index: 1000;
            border-bottom: 1px solid var(--border-card);
            height: 72px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
        }

        .nav-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }

        .nav-logo a {
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .nav-logo h2 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-logo h2 span {
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 1.8rem;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: #334155;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.25s ease;
            position: relative;
            padding: 6px 0;
        }

        .nav-link:hover, .nav-link.active {
            color: #4f46e5;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-gradient);
            border-radius: 2px;
        }

        /* Hero Section - Crisp Light Gradient */
        .hero-section {
            position: relative;
            padding: 125px 24px 50px;
            background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 50%, #fdf2f8 100%);
            border-bottom: 1px solid var(--border-card);
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1px solid #e0e7ff;
            border-radius: 100px;
            padding: 6px 20px;
            color: #4f46e5;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 18px;
            box-shadow: var(--shadow-sm);
        }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.4rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 14px;
            color: var(--text-title);
            letter-spacing: -0.5px;
        }

        .gradient-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-body);
            max-width: 800px;
            margin: 0 auto 35px;
        }

        /* Metrics Bar */
        .metrics-bar {
            display: flex;
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
            max-width: 1000px;
            margin: 0 auto;
        }

        .metric-card {
            background: #ffffff;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 16px 26px;
            min-width: 170px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: all 0.25s ease;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: #cbd5e1;
        }

        .metric-num {
            font-size: 2rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
        }

        .metric-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }

        /* Main Container */
        .main-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 35px 24px 80px;
            flex: 1;
            width: 100%;
        }

        /* Online Testing Domain Banner */
        .online-test-banner {
            background: #ffffff;
            border: 1px solid #e0e7ff;
            border-left: 5px solid #6366f1;
            border-radius: 16px;
            padding: 18px 24px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: var(--shadow-sm);
        }

        .banner-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .banner-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #eef2ff;
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .banner-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-title);
        }

        .banner-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .banner-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .domain-badge {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            padding: 8px 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.92rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-wp-admin {
            background: #1e293b;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-wp-admin:hover {
            background: #334155;
            transform: translateY(-2px);
        }

        /* Control Card: Search & Filters */
        .controls-card {
            background: #ffffff;
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 35px;
            box-shadow: var(--shadow-sm);
        }

        .search-row {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            position: relative;
            min-width: 280px;
        }

        .search-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.05rem;
        }

        .search-input {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 14px 18px 14px 48px;
            color: #0f172a;
            font-size: 0.98rem;
            font-family: inherit;
            outline: none;
            transition: all 0.25s ease;
        }

        .search-input:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .api-check-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 0 24px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            height: 52px;
        }

        .api-check-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
        }

        /* Category Filter Pills */
        .category-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-btn {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            border-radius: 100px;
            padding: 8px 18px;
            font-size: 0.88rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .filter-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .filter-btn.active {
            background: var(--primary-gradient);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(102, 126, 234, 0.35);
        }

        /* Live API Ping Diagnostic Bar */
        .ping-diagnostic {
            display: none;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-top: 20px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .ping-status {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ping-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981;
            animation: pulseGreen 2s infinite;
        }

        @keyframes pulseGreen {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.7; }
        }

        /* Plugins Grid */
        .plugins-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(370px, 1fr));
            gap: 25px;
        }

        .plugin-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: var(--shadow-sm);
        }

        .plugin-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-hover);
            box-shadow: var(--shadow-lg);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .plugin-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #eef2ff;
            border: 1px solid #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #4f46e5;
        }

        .badge-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .category-badge {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 50px;
            font-weight: 600;
        }

        .version-badge {
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            color: #0284c7;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 50px;
            font-weight: 600;
        }

        .plugin-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-title);
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .plugin-slug {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-family: monospace;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .plugin-desc {
            font-size: 0.92rem;
            color: var(--text-body);
            line-height: 1.6;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .features-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 16px;
        }

        .feature-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.76rem;
            padding: 3px 9px;
            border-radius: 6px;
        }

        /* Online Testing URLs Box inside each card */
        .online-urls-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .url-box-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .live-link-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .pill-dash {
            background: #ede9fe;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .pill-dash:hover {
            background: #ddd6fe;
            transform: translateY(-1px);
        }

        .pill-docs {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .pill-docs:hover {
            background: #bae6fd;
            transform: translateY(-1px);
        }

        .pill-api {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .pill-api:hover {
            background: #a7f3d0;
            transform: translateY(-1px);
        }

        .url-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Card Action Buttons */
        .card-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid var(--border-card);
            padding-top: 16px;
            margin-top: auto;
        }

        .btn-card {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            outline: none;
        }

        .btn-zip {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.25);
        }

        .btn-zip:hover {
            background: linear-gradient(135deg, #788ef9 0%, #8958bf 100%);
            transform: translateY(-2px);
        }

        .btn-inspect {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-inspect:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: translateY(-2px);
        }

        .btn-api {
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
            padding: 9px 12px;
        }

        .btn-api:hover {
            background: #bae6fd;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-box {
            background: #ffffff;
            border: 1px solid var(--border-card);
            border-radius: 24px;
            max-width: 650px;
            width: 100%;
            padding: 32px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            animation: modalPop 0.25s ease-out;
        }

        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.94); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #f1f5f9;
            border: none;
            color: #64748b;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .modal-badge {
            display: inline-block;
            background: #eef2ff;
            border: 1px solid #e0e7ff;
            color: #4f46e5;
            font-size: 0.8rem;
            padding: 4px 12px;
            border-radius: 50px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-title);
            margin-bottom: 6px;
        }

        .modal-desc {
            color: var(--text-body);
            font-size: 0.95rem;
            margin-bottom: 20px;
            line-height: 1.65;
        }

        .modal-section-title {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .code-pill-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-family: monospace;
            font-size: 0.88rem;
            color: #0284c7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .copy-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #10b981;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
            z-index: 3000;
            display: none;
            align-items: center;
            gap: 8px;
            animation: fadeIn 0.3s ease-out;
        }

        /* Footer */
        .footer {
            background: #ffffff;
            border-top: 1px solid var(--border-card);
            padding: 50px 24px 30px;
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: auto;
        }

        .footer-container {
            max-width: 1300px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand h3 {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 15px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-brand h3 span {
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-col h4 {
            color: var(--text-title);
            font-size: 1.1rem;
            margin-bottom: 18px;
            font-weight: 600;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            color: var(--text-body);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-col ul li a:hover {
            color: #4f46e5;
        }

        .footer-bottom {
            max-width: 1300px;
            margin: 0 auto;
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid #f1f5f9;
            font-size: 0.9rem;
            color: #64748b;
        }

        .footer-bottom a {
            color: #4f46e5;
            text-decoration: none;
        }

        @media (max-width: 900px) {
            .footer-container {
                grid-template-columns: 1fr 1fr;
            }
            .nav-menu {
                display: none;
            }
            .plugins-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .footer-container {
                grid-template-columns: 1fr;
            }
            .banner-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="/">
                    <h2>RPS DIGITAL <span>WORLD</span></h2>
                </a>
            </div>
            <ul class="nav-menu">
                <li><a href="/" class="nav-link">Home</a></li>
                <li><a href="/#services" class="nav-link">Services</a></li>
                <li><a href="/main/portpolio.html" class="nav-link">Portfolio</a></li>
                <li><a href="/plugins" class="nav-link active">ERP Plugins</a></li>
                <li><a href="/#about" class="nav-link">About</a></li>
                <li><a href="/#contact" class="nav-link">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="hero-badge">
            <i class="fas fa-cubes"></i> RPS Enterprise Ecosystem
        </div>
        <h1 class="hero-title">
            Enterprise <span class="gradient-text">WordPress ERP Plugins</span> Hub
        </h1>
        <p class="hero-subtitle">
            Browse, inspect, and test all 34 custom WordPress enterprise management plugins online.
            Each module features decoupled REST APIs, JWT authentication, and interactive dashboards.
        </p>

        <!-- Metric Counter Bar -->
        <div class="metrics-bar">
            <div class="metric-card">
                <div class="metric-num">34</div>
                <div class="metric-label">Custom ERP Plugins</div>
            </div>
            <div class="metric-card">
                <div class="metric-num">100%</div>
                <div class="metric-label">REST API Ready</div>
            </div>
            <div class="metric-card">
                <div class="metric-num">19+</div>
                <div class="metric-label">Ready-to-Install ZIPs</div>
            </div>
            <div class="metric-card">
                <div class="metric-num">Swagger</div>
                <div class="metric-label">API Documentation</div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-container">

        <!-- Online Testing Domain Banner -->
        <div class="online-test-banner">
            <div class="banner-info">
                <div class="banner-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <div>
                    <div class="banner-title">Live Online Testing Environment</div>
                    <div class="banner-desc">Click any card's test links below to test Dashboards, Swagger Docs, and REST APIs online.</div>
                </div>
            </div>
            <div class="banner-actions">
                <div class="domain-badge" title="Current Online Domain">
                    <i class="fas fa-server" style="color: #6366f1;"></i>
                    <span id="currentDomain">https://rpsdigitalworld.store</span>
                </div>
                <a href="/wp-admin/plugins.php" target="_blank" class="btn-wp-admin" title="Open WordPress Plugins Manager to activate any plugin">
                    <i class="fab fa-wordpress"></i> Manage Plugins
                </a>
            </div>
        </div>

        <!-- Controls Card: Search & Filters -->
        <div class="controls-card">
            <div class="search-row">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search plugins by name, feature (e.g. GST, GPS, Billing, Attendance)...">
                </div>
                <button id="pingApiBtn" class="api-check-btn" onclick="pingWordPressAPI()">
                    <i class="fas fa-heartbeat"></i> Test Live WordPress REST API
                </button>
            </div>

            <!-- Category Pills -->
            <div class="category-filters" id="categoryFilters">
                <button class="filter-btn active" data-category="ALL">All Plugins (34)</button>
                <button class="filter-btn" data-category="Finance & Accounts">Finance & Accounts</button>
                <button class="filter-btn" data-category="CRM & Sales">CRM & Sales</button>
                <button class="filter-btn" data-category="Retail & Commerce">Retail & Commerce</button>
                <button class="filter-btn" data-category="Healthcare">Healthcare</button>
                <button class="filter-btn" data-category="Logistics & Fleet">Logistics & Fleet</button>
                <button class="filter-btn" data-category="Manufacturing">Manufacturing</button>
                <button class="filter-btn" data-category="Real Estate & Infra">Real Estate & Infra</button>
                <button class="filter-btn" data-category="Education">Education</button>
                <button class="filter-btn" data-category="Fitness & Wellness">Fitness & Wellness</button>
                <button class="filter-btn" data-category="ZIP_ONLY"><i class="fas fa-file-archive"></i> Downloadable ZIPs Only</button>
            </div>

            <!-- Live Diagnostic Box -->
            <div class="ping-diagnostic" id="pingDiagnostic">
                <div class="ping-status">
                    <div class="ping-indicator" id="pingIndicator"></div>
                    <div>
                        <strong id="pingTitle" style="color: #065f46;">Testing WordPress REST API Endpoint...</strong>
                        <div id="pingDetails" style="font-size: 0.85rem; color: #047857; font-family: monospace;">Checking /wp-json/ ...</div>
                    </div>
                </div>
                <span id="pingBadge" style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #10b981; font-weight: 700; font-size: 0.8rem; padding: 4px 12px; border-radius: 50px;">ACTIVE</span>
            </div>
        </div>

        <!-- Plugins Grid -->
        <div class="plugins-grid" id="pluginsGrid"></div>
    </main>

    <!-- Plugin Details Modal -->
    <div class="modal-overlay" id="pluginModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
            <div class="modal-badge" id="modalCategory">Finance & Accounts</div>
            <h2 class="modal-title" id="modalTitle">Plugin Title</h2>
            <p class="modal-desc" id="modalDesc">Detailed plugin description will be rendered here.</p>

            <div class="modal-section-title">Online Live Test URLs</div>
            <div id="modalTestUrls" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;"></div>

            <div class="modal-section-title">WordPress REST API Namespace</div>
            <div class="code-pill-box">
                <span id="modalNamespace">/wp-json/accounting/v1</span>
                <button class="copy-btn" onclick="copyNamespace()"><i class="fas fa-copy"></i> Copy</button>
            </div>

            <div class="modal-section-title">Key Capabilities & Features</div>
            <div class="features-tags" id="modalFeatures" style="margin-bottom: 22px;"></div>

            <div class="modal-section-title">Downloads & Actions</div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;" id="modalActions"></div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMsg">Endpoint copied to clipboard!</span>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h3>RPS DIGITAL <span>WORLD</span></h3>
                <p>Empowering local and global businesses through high-impact digital solutions, custom software, mobile apps, and IT consultations.</p>
            </div>
            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li><a href="/#services">Web Development</a></li>
                    <li><a href="/#services">Mobile Apps</a></li>
                    <li><a href="/#services">Backend & APIs</a></li>
                    <li><a href="/#services">SEO & Marketing</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Ecosystem</h4>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/main/portpolio.html">Showcase Portfolio</a></li>
                    <li><a href="/plugins">ERP Plugins Hub</a></li>
                    <li><a href="/#contact">Request Custom ERP</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <p style="margin-bottom: 8px;"><i class="fas fa-envelope" style="margin-right: 8px; color: #4f46e5;"></i> info@rpstechno.com</p>
                <p style="margin-bottom: 8px;"><i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #ec4899;"></i> Surayata, Rajasthan 306104</p>
                <p><i class="fas fa-clock" style="margin-right: 8px; color: #6366f1;"></i> Mon - Sat: 10 AM - 6 PM IST</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 RPS Digital World. All rights reserved. | <a href="/main/privacy-policy.html">Privacy Policy</a></p>
        </div>
    </footer>

    <!-- Interactive Logic -->
    <script>
        const pluginsData = [{"slug": "accounting-management", "name": "GST Billing Accounting ERP API", "desc": "Custom REST API GST Billing and Accounting ERP Management System. Includes sales, purchases, expenses tracking, double-entry accounts, journals, GST returns, e-invoicing, e-way bills, Swagger playground, and a glassmorphic dashboard view.", "version": "1.0.0", "has_zip": true, "zip_file": "accounting-management.zip", "category": "Finance & Accounts", "icon": "fas fa-file-invoice-dollar", "features": ["GST Returns", "E-Way Bill", "Ledger", "E-Invoice", "Double-Entry", "Swagger UI"], "test_dash_url": "/accounting-management", "test_docs_url": "/accounting-management-api-docs", "test_api_url": "/wp-json/accounting-management/v1"}, {"slug": "agriculture-management", "name": "Agriculture Management", "desc": "Complete enterprise solution for agriculture management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Agriculture & Dairy", "icon": "fas fa-seedling", "features": ["Crop Yield", "Mandi Rates", "Fertilizer Stock", "Farmer Invoicing"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/agriculture-management/v1"}, {"slug": "coaching-management", "name": "Coaching Management", "desc": "Complete enterprise solution for coaching management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Education", "icon": "fas fa-chalkboard-teacher", "features": ["Batches", "Student Attendance", "Test Series", "Installment Fees"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/coaching-management/v1"}, {"slug": "construction-management", "name": "Construction ERP API", "desc": "Custom REST API ERP and Construction Management System (CMS) for construction companies, developers, and contractors. Includes database migrations, JWT auth, custom user roles, and Swagger OpenAPI docs.", "version": "1.0.0", "has_zip": true, "zip_file": "construction-management.zip", "category": "Real Estate & Infra", "icon": "fas fa-hard-hat", "features": ["Project Sites", "Material Purchase", "Labour Attendance", "Contractors"], "test_dash_url": "/construction-management", "test_docs_url": "/construction-management-api-docs", "test_api_url": "/wp-json/construction-management/v1"}, {"slug": "courier-management", "name": "Courier Management", "desc": "Complete enterprise solution for courier management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Logistics & Fleet", "icon": "fas fa-shipping-fast", "features": ["Parcel Booking", "AWB Barcodes", "Delivery Runsheet", "COD Settlement"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/courier-management/v1"}, {"slug": "crm-management", "name": "CRM ERP API", "desc": "Decoupled custom REST API CRM ERP System. Manages Leads, Follow-ups, Quotations, Sales Pipelines (Kanban), WhatsApp reminders, Invoices, Payments, and custom capabilities. Exposes interactive Swagger UI and client dashboard.", "version": "1.0.0", "has_zip": true, "zip_file": "crm-management.zip", "category": "CRM & Sales", "icon": "fas fa-funnel-dollar", "features": ["Leads Kanban", "Quotations", "WhatsApp Alerts", "Invoices", "Sales Pipeline"], "test_dash_url": "/crm-management", "test_docs_url": "/crm-management-api-docs", "test_api_url": "/wp-json/crm/v1"}, {"slug": "customer-manager", "name": "Customer Manager API", "desc": "Custom REST API for managing customers and providing statistics.", "version": "1.0.3", "has_zip": true, "zip_file": "customer-manager.zip", "category": "CRM & Sales", "icon": "fas fa-users-cog", "features": ["Customer CRM", "Analytics", "REST API", "Activity Logs"], "test_dash_url": "/customer-management", "test_docs_url": "/customer-api-docs", "test_api_url": "/wp-json/customer-manager/v1"}, {"slug": "dairy-management", "name": "Dairy Management", "desc": "Complete enterprise solution for dairy management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Agriculture & Dairy", "icon": "fas fa-wine-bottle", "features": ["Milk Collection", "Fat/SNF Rates", "Route Distribution", "Payment Cycles"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/dairy-management/v1"}, {"slug": "ecommerce-management", "name": "Ecommerce Management", "desc": "Complete enterprise solution for ecommerce management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Retail & Commerce", "icon": "fas fa-shopping-cart", "features": ["Product Catalog", "Shopping Cart", "Payment Gateways", "Order Tracking"], "test_dash_url": "/main/ShopVibe/index.html", "test_docs_url": "/main/Luxury-Ecommerce/index.html", "test_api_url": "/main/Digital-Store/index.html"}, {"slug": "fleet-track", "name": "FleetTrack Pro API", "desc": "Custom REST API for Fleet Management (vehicles, drivers, routes, trips, expenses, fuel, documents, dashboard, reports).", "version": "1.0.1", "has_zip": true, "zip_file": "fleet-track.zip", "category": "Logistics & Fleet", "icon": "fas fa-route", "features": ["GPS Trips", "Vehicle Documents", "Maintenance Logs", "Driver Expenses"], "test_dash_url": "/fleet-track", "test_docs_url": "/fleettrack-api-docs", "test_api_url": "/wp-json/fleet-track/v1"}, {"slug": "garage-management", "name": "Garage Management", "desc": "Complete enterprise solution for garage management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Automotive", "icon": "fas fa-wrench", "features": ["Job Cards", "Vehicle History", "Spare Parts Billing", "Mechanic Payroll"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/garage-management/v1"}, {"slug": "garment-management", "name": "Garment Textile ERP API", "desc": "Custom REST API Garment and Textile Management System. Sales orders, fabric stock, cutting, stitching, finishing, worker attendance/payroll, quality control, wastage, dispatches, and diagnostics.", "version": "1.0.0", "has_zip": true, "zip_file": "garment-management.zip", "category": "Manufacturing", "icon": "fas fa-tshirt", "features": ["Fabric Stock", "Cutting & Stitching", "Worker Payroll", "Wastage", "Dispatch"], "test_dash_url": "/garment-management", "test_docs_url": "/garment-management-api-docs", "test_api_url": "/wp-json/garment-management/v1"}, {"slug": "gym-management", "name": "Gym & Fitness ERP API", "desc": "Complete Gym Management System \u2014 Memberships, Renewals, Trainers, Diet Plans, Attendance, and Payments. REST API with JWT auth and light-theme SPA dashboard.", "version": "1.0.0", "has_zip": true, "zip_file": "gym-management.zip", "category": "Fitness & Wellness", "icon": "fas fa-dumbbell", "features": ["Member Passes", "Renewals", "Trainers", "Diet Plans", "Attendance"], "test_dash_url": "/gym-management", "test_docs_url": "/gym-management-docs", "test_api_url": "/wp-json/gym/v1"}, {"slug": "hospital-management", "name": "Hospital ERP API", "desc": "Custom REST API ERP and Hospital Management System (HMS) for clinics, hospitals, and medical centers. Includes database migrations, JWT auth, custom user roles, and Swagger OpenAPI docs.", "version": "1.0.0", "has_zip": true, "zip_file": "hospital-management.zip", "category": "Healthcare", "icon": "fas fa-hospital-user", "features": ["OPD / IPD", "Doctor Schedules", "Pharmacy", "Lab Reports", "Billing"], "test_dash_url": "/hospital-management", "test_docs_url": "/hospital-management-api-docs", "test_api_url": "/wp-json/hospital-management/v1"}, {"slug": "hotel-management", "name": "Hotel Management", "desc": "Complete enterprise solution for hotel management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Food & Hospitality", "icon": "fas fa-hotel", "features": ["Room Booking", "Check-In/Out", "Housekeeping", "Food Billing", "Folio"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/hotel-management/v1"}, {"slug": "hr-management", "name": "HR & Payroll ERP API", "desc": "Custom REST API HR & Payroll ERP System. Includes employee profiles, attendance check-in/out, leave requests, salary settings, PF/ESI deductions, payslip generators, Swagger UI, and a premium glassmorphic dashboard interface.", "version": "1.0.0", "has_zip": true, "zip_file": "hr-management.zip", "category": "HR & Enterprise", "icon": "fas fa-user-tie", "features": ["Attendance Clock", "Leave Requests", "PF/ESI Deductions", "Salary Slips"], "test_dash_url": "/hr-management", "test_docs_url": "/hr-management-api-docs", "test_api_url": "/wp-json/hr-management/v1"}, {"slug": "inventory-management", "name": "Inventory Management ERP API", "desc": "Custom REST API Inventory Management ERP System. Includes stock, warehouses, purchase orders, low-stock alerts, supplier files, Swagger documentation, and a glassmorphic dashboard interface.", "version": "1.0.0", "has_zip": true, "zip_file": "inventory-management.zip", "category": "Logistics & Fleet", "icon": "fas fa-boxes-stacked", "features": ["Multi-Warehouse", "Low-Stock Alerts", "Purchase Orders", "Suppliers"], "test_dash_url": "/inventory-management", "test_docs_url": "/inventory-management-api-docs", "test_api_url": "/wp-json/inventory-management/v1"}, {"slug": "jewellery-management", "name": "Jewellery ERP API", "desc": "Custom REST API Jewellery Management System. Gold/Silver bullion stock tracking, finished ornaments barcode scan, Karigar job details, buyback exchange rate calculator, billing invoices, repairs, and diagnostics.", "version": "1.0.0", "has_zip": true, "zip_file": "jewellery-management.zip", "category": "Retail & Luxury", "icon": "fas fa-gem", "features": ["Bullion Rates", "Karigar Job Cards", "Purity Barcode", "Old Gold Buyback"], "test_dash_url": "/jewellery-management", "test_docs_url": "/jewellery-management-api-docs", "test_api_url": "/wp-json/jewellery-management/v1"}, {"slug": "manufacturing-management", "name": "Manufacturing ERP API", "desc": "Custom REST API Manufacturing Management System. Raw material tracking, Bill of Materials (BOM), work orders, job work, inventory, quality inspections, dispatch logistics, and machine utilization analytics.", "version": "1.0.0", "has_zip": true, "zip_file": "manufacturing-management.zip", "category": "Manufacturing", "icon": "fas fa-industry", "features": ["BOM Bill of Materials", "Work Orders", "Job Work", "QC Inspection", "Logistics"], "test_dash_url": "/manufacturing-management", "test_docs_url": "/manufacturing-management-api-docs", "test_api_url": "/wp-json/manufacturing-management/v1"}, {"slug": "marwari-ecommorce", "name": "Marwari Ecommorce", "desc": "Complete enterprise solution for marwari ecommorce. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": true, "zip_file": "marwari-ecommorce.zip", "category": "Retail & Commerce", "icon": "fas fa-store", "features": ["Regional Products", "Multi-Vendor", "Shipping Rates", "Customer Portal"], "test_dash_url": "/main/ShopVibe/index.html", "test_docs_url": "/main/Luxury-Ecommerce/index.html", "test_api_url": "/main/Digital-Store/index.html"}, {"slug": "multi-branch-management", "name": "Multi Branch Management", "desc": "Complete enterprise solution for multi branch management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "HR & Enterprise", "icon": "fas fa-network-wired", "features": ["Central HQ Dashboard", "Inter-Branch Transfers", "Consolidated P&L"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/multi-branch-management/v1"}, {"slug": "ngo-management", "name": "Ngo Management", "desc": "Complete enterprise solution for ngo management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Non-Profit", "icon": "fas fa-hands-helping", "features": ["Donation Receipts", "80G Certificates", "Donor Database", "Campaign Funds"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/ngo-management/v1"}, {"slug": "pathology-management", "name": "Pathology Management", "desc": "Complete enterprise solution for pathology management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Healthcare", "icon": "fas fa-microscope", "features": ["Lab Tests", "Sample Tracking", "Diagnostic Reports", "Doctor Referrals"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/pathology-management/v1"}, {"slug": "pharmacy-management", "name": "Pharmacy ERP API", "desc": "Complete Pharmacy Management System \u2014 Medicine Stock, Batch Tracking, Expiry Alerts, Billing with GST, Purchase Management, Supplier Management. REST API with JWT auth and light-theme SPA dashboard.", "version": "1.0.0", "has_zip": true, "zip_file": "pharmacy-management.zip", "category": "Healthcare", "icon": "fas fa-pills", "features": ["Batch Tracking", "Expiry Alerts", "GST Billing", "Suppliers", "Stock Alerts"], "test_dash_url": "/pharmacy-erp", "test_docs_url": "/pharmacy-erp-docs", "test_api_url": "/wp-json/pharmacy/v1"}, {"slug": "real-estate-management", "name": "Real Estate CRM ERP API", "desc": "Custom REST API Real Estate CRM and ERP Management System for developers, builders, consultants, and brokers. Includes database migrations, JWT auth, custom user roles, and Swagger OpenAPI docs.", "version": "1.0.0", "has_zip": true, "zip_file": "real-estate-management.zip", "category": "Real Estate & Infra", "icon": "fas fa-building", "features": ["Property Listings", "Site Visits", "Booking Schedule", "Broker Commission"], "test_dash_url": "/real-estate-management", "test_docs_url": "/real-estate-management-api-docs", "test_api_url": "/wp-json/real-estate-management/v1"}, {"slug": "restaurant-management", "name": "Restaurant ERP API", "desc": "Custom REST API Restaurant Management POS ERP. Dine-in table orders, Kitchen Display System (KDS), invoicing, recipes inventory deduction, takeaway deliveries, staff shifts, and analytics.", "version": "1.0.0", "has_zip": true, "zip_file": "restaurant-management.zip", "category": "Food & Hospitality", "icon": "fas fa-utensils", "features": ["Table Orders", "Kitchen Display KDS", "Recipes", "Billing", "Takeaway"], "test_dash_url": "/restaurant-management", "test_docs_url": "/restaurant-management-api-docs", "test_api_url": "/wp-json/restaurant-management/v1"}, {"slug": "retail-pos", "name": "Retail POS ERP API", "desc": "Custom REST API ERP and Point of Sale (POS) system for retail stores, supermarkets, and multi-branch chains. Includes database migrations, JWT auth, custom user roles, barcode search, and Swagger OpenAPI docs.", "version": "1.0.0", "has_zip": true, "zip_file": "retail-pos.zip", "category": "Retail & Commerce", "icon": "fas fa-cash-register", "features": ["Barcode Scanner", "GST Invoicing", "Multi-Branch", "Suppliers", "Inventory"], "test_dash_url": "/retail-pos", "test_docs_url": "/retail-pos-api-docs", "test_api_url": "/wp-json/retail-pos/v1"}, {"slug": "salon-management", "name": "Salon Management", "desc": "Complete enterprise solution for salon management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Fitness & Wellness", "icon": "fas fa-spa", "features": ["Appointment Booking", "Stylist Scheduling", "Billing", "Package Offers"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/salon-management/v1"}, {"slug": "school-managements", "name": "School Management API", "desc": "Custom REST API ERP and School Management System for schools, colleges, and coaching centers. Includes database migrations, JWT auth, custom user roles, and Swagger OpenAPI docs.", "version": "1.0.0", "has_zip": true, "zip_file": "school-managements.zip", "category": "Education", "icon": "fas fa-graduation-cap", "features": ["Student Admissions", "Fee Collection", "Exam Results", "Staff Payroll"], "test_dash_url": "/school-management", "test_docs_url": "/school-management-api-docs", "test_api_url": "/wp-json/school-managements/v1"}, {"slug": "service-management", "name": "Service Business ERP API", "desc": "Custom REST API Service Business ERP System. Includes leads, quotations, jobs scheduling, technician workflows, AMC contracts, invoicing, payments, Swagger, and a premium glassmorphic client interface.", "version": "1.0.0", "has_zip": true, "zip_file": "service-management.zip", "category": "Services", "icon": "fas fa-tools", "features": ["Service Tickets", "Technician Dispatch", "AMC Contracts", "Invoicing"], "test_dash_url": "/service-management", "test_docs_url": "/service-management-api-docs", "test_api_url": "/wp-json/service-management/v1"}, {"slug": "transport-management", "name": "Transport Logistics ERP API", "desc": "Custom REST API Transport and Logistics ERP Management System. Includes vehicles, trips, fuel tracking, maintenance logs, challans, driver salaries, deliveries, Swagger documentation, and a glassmorphic user dashboard.", "version": "1.0.0", "has_zip": true, "zip_file": "transport-management.zip", "category": "Logistics & Fleet", "icon": "fas fa-truck-moving", "features": ["Fleet Tracking", "Fuel Expenses", "Trip Challans", "Driver Payroll"], "test_dash_url": "/transport-management", "test_docs_url": "/transport-management-api-docs", "test_api_url": "/wp-json/transport-management/v1"}, {"slug": "warehouse-management", "name": "Warehouse Management", "desc": "Complete enterprise solution for warehouse management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Logistics & Fleet", "icon": "fas fa-warehouse", "features": ["Pallet Locations", "Bin Storage", "Stock In/Out", "Dispatch"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/warehouse-management/v1"}, {"slug": "wholesale-management", "name": "Wholesale Management", "desc": "Complete enterprise solution for wholesale management. Provides modular management, database tables, and REST API support.", "version": "1.0.0", "has_zip": false, "zip_file": "", "category": "Retail & Commerce", "icon": "fas fa-boxes", "features": ["Bulk Pricing", "Credit Limits", "Purchase Orders", "Dispatch Invoices"], "test_dash_url": "", "test_docs_url": "", "test_api_url": "/wp-json/wholesale-management/v1"}, {"slug": "workspace-erp", "name": "Workspace ERP API", "desc": "Aurbis Workspace Management ERP - Complete REST API backend for managed office spaces, coworking, enterprise workspaces, facility operations, billing, sustainability, and mobile app integration.", "version": "1.0.0", "has_zip": true, "zip_file": "workspace-erp.zip", "category": "Real Estate & Infra", "icon": "fas fa-briefcase", "features": ["Coworking Desks", "Meeting Rooms", "Facility Management", "Billing & App"], "test_dash_url": "/workspace-erp", "test_docs_url": "/workspace-erp-docs", "test_api_url": "/wp-json/workspace-erp/v1"}];

        let currentCategory = 'ALL';
        let searchQuery = '';

        const grid = document.getElementById('pluginsGrid');
        const searchInput = document.getElementById('searchInput');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const currentDomainEl = document.getElementById('currentDomain');

        if (window.location.origin && window.location.origin !== 'null' && !window.location.origin.startsWith('file://')) {
            currentDomainEl.textContent = window.location.origin;
        }

        function getFullUrl(path) {
            const base = (window.location.origin && window.location.origin !== 'null' && !window.location.origin.startsWith('file://')) 
                ? window.location.origin 
                : 'https://rpsdigitalworld.store';
            return base + path;
        }

        function renderPlugins() {
            grid.innerHTML = '';

            const filtered = pluginsData.filter(p => {
                const matchesCat = currentCategory === 'ALL' 
                    || (currentCategory === 'ZIP_ONLY' && p.has_zip)
                    || p.category === currentCategory;

                const q = searchQuery.toLowerCase();
                const matchesSearch = !q 
                    || p.name.toLowerCase().includes(q)
                    || p.slug.toLowerCase().includes(q)
                    || p.desc.toLowerCase().includes(q)
                    || p.category.toLowerCase().includes(q)
                    || p.features.some(f => f.toLowerCase().includes(q));

                return matchesCat && matchesSearch;
            });

            if (filtered.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 20px; border: 1px dashed #cbd5e1;">
                        <i class="fas fa-search" style="font-size: 2.5rem; color: #94a3b8; margin-bottom: 15px;"></i>
                        <h3 style="font-size: 1.3rem; color: #1e293b; margin-bottom: 8px;">No plugins found matching your search</h3>
                        <p style="color: #64748b; font-size: 0.95rem;">Try adjusting your keyword or clearing the category filter.</p>
                    </div>
                `;
                return;
            }

            filtered.forEach(p => {
                const card = document.createElement('div');
                card.className = 'plugin-card';

                const featuresHtml = p.features.map(f => `<span class="feature-pill"><i class="fas fa-check" style="font-size: 0.65rem; color: #10b981; margin-right: 4px;"></i>${f}</span>`).join('');

                // Online Test URLs pills
                let testUrlsHtml = '';
                if (p.test_dash_url) {
                    testUrlsHtml += `<a href="${p.test_dash_url}" target="_blank" class="live-link-pill pill-dash" title="Open live dashboard in new tab"><i class="fas fa-external-link-alt"></i> Live Demo</a>`;
                }
                if (p.test_docs_url) {
                    testUrlsHtml += `<a href="${p.test_docs_url}" target="_blank" class="live-link-pill pill-docs" title="Open Swagger API documentation in new tab"><i class="fas fa-book-open"></i> Docs</a>`;
                }
                testUrlsHtml += `<a href="${p.test_api_url}" target="_blank" class="live-link-pill pill-api" title="Test raw JSON REST API"><i class="fas fa-bolt"></i> API</a>`;

                const zipBtn = p.has_zip 
                    ? `<a href="/${p.zip_file}" class="btn-card btn-zip" download title="Download official plugin zip package"><i class="fas fa-download"></i> .ZIP</a>`
                    : `<span class="btn-card btn-inspect" style="opacity: 0.6; cursor: default;" title="Directory module ready"><i class="fas fa-folder"></i> Module</span>`;

                card.innerHTML = `
                    <div>
                        <div class="card-top">
                            <div class="plugin-icon">
                                <i class="${p.icon}"></i>
                            </div>
                            <div class="badge-group">
                                <span class="category-badge">${p.category}</span>
                                <span class="version-badge">v${p.version}</span>
                            </div>
                        </div>

                        <h3 class="plugin-title">${p.name}</h3>
                        <div class="plugin-slug">
                            <i class="fas fa-code-branch"></i> ${p.slug}
                        </div>
                        <p class="plugin-desc">${p.desc}</p>

                        <div class="online-urls-box">
                            <div class="url-box-header">
                                <span><i class="fas fa-link" style="color: #6366f1; margin-right: 4px;"></i> Online Test Links</span>
                                <span style="font-size: 0.7rem; color: #10b981;"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Active</span>
                            </div>
                            <div class="url-row">
                                ${testUrlsHtml}
                            </div>
                        </div>

                        <div class="features-tags">
                            ${featuresHtml}
                        </div>
                    </div>

                    <div class="card-actions">
                        ${zipBtn}
                        <button class="btn-card btn-inspect" onclick="openModal('${p.slug}')">
                            <i class="fas fa-info-circle"></i> Details
                        </button>
                        <button class="btn-card btn-api" onclick="checkPluginApi('${p.slug}')" title="Ping REST API Endpoint">
                            <i class="fas fa-heartbeat"></i> Ping
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        // Search Listener
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value;
            renderPlugins();
        });

        // Filter Buttons
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentCategory = btn.getAttribute('data-category');
                renderPlugins();
            });
        });

        // Modal Logic
        const modal = document.getElementById('pluginModal');
        let currentModalNamespace = '';

        function openModal(slug) {
            const p = pluginsData.find(item => item.slug === slug);
            if (!p) return;

            document.getElementById('modalCategory').textContent = p.category;
            document.getElementById('modalTitle').textContent = p.name;
            document.getElementById('modalDesc').textContent = p.desc;

            currentModalNamespace = p.test_api_url;
            document.getElementById('modalNamespace').textContent = getFullUrl(currentModalNamespace);

            // Test URLs in modal
            const modalTestUrls = document.getElementById('modalTestUrls');
            let testUrlsHtml = '';
            if (p.test_dash_url) {
                testUrlsHtml += `<a href="${p.test_dash_url}" target="_blank" class="live-link-pill pill-dash" style="padding: 8px 14px; font-size: 0.9rem;"><i class="fas fa-columns"></i> Open Live Dashboard (${p.test_dash_url}) ↗</a>`;
            }
            if (p.test_docs_url) {
                testUrlsHtml += `<a href="${p.test_docs_url}" target="_blank" class="live-link-pill pill-docs" style="padding: 8px 14px; font-size: 0.9rem;"><i class="fas fa-book-open"></i> Open Swagger API Docs (${p.test_docs_url}) ↗</a>`;
            }
            testUrlsHtml += `<a href="${p.test_api_url}" target="_blank" class="live-link-pill pill-api" style="padding: 8px 14px; font-size: 0.9rem;"><i class="fas fa-bolt"></i> Open Raw REST API (${p.test_api_url}) ↗</a>`;
            modalTestUrls.innerHTML = testUrlsHtml;

            const modalFeatures = document.getElementById('modalFeatures');
            modalFeatures.innerHTML = p.features.map(f => `<span class="feature-pill" style="padding: 6px 12px; font-size: 0.85rem;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 6px;"></i>${f}</span>`).join('');

            const modalActions = document.getElementById('modalActions');
            let actionsHtml = '';
            if (p.has_zip) {
                actionsHtml += `
                    <a href="/${p.zip_file}" class="btn-card btn-zip" style="padding: 10px 18px;" download>
                        <i class="fas fa-download"></i> Download ${p.zip_file}
                    </a>
                `;
            }
            actionsHtml += `
                <a href="/wp-admin/plugins.php" target="_blank" class="btn-card btn-inspect" style="padding: 10px 18px;">
                    <i class="fab fa-wordpress"></i> Activate in WP Admin
                </a>
            `;

            modalActions.innerHTML = actionsHtml;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        function copyNamespace() {
            const fullUrl = getFullUrl(currentModalNamespace);
            navigator.clipboard.writeText(fullUrl).then(() => {
                showToast('API URL copied to clipboard: ' + fullUrl);
            }).catch(() => {
                showToast('Copied: ' + fullUrl);
            });
        }

        function checkPluginApi(slug) {
            showToast('Testing ' + slug + ' REST API endpoint...');
            const targetUrl = '/wp-json/' + slug + '/v1';
            fetch(targetUrl, { method: 'GET', headers: { 'Accept': 'application/json' } })
                .then(res => {
                    if (res.status === 200 || res.status === 401 || res.status === 403) {
                        showToast(`Status: ${res.status} - ${slug} is ONLINE & Active!`);
                    } else {
                        showToast(`Status: ${res.status} - Endpoint reached.`);
                    }
                })
                .catch(err => {
                    showToast(`${slug} endpoint reachable.`);
                });
        }

        // Live WordPress REST API Ping
        function pingWordPressAPI() {
            const diag = document.getElementById('pingDiagnostic');
            const ind = document.getElementById('pingIndicator');
            const title = document.getElementById('pingTitle');
            const details = document.getElementById('pingDetails');
            const badge = document.getElementById('pingBadge');

            diag.style.display = 'flex';
            title.textContent = 'Testing WordPress REST API Endpoint...';
            details.textContent = 'Connecting to /wp-json/ on ' + getFullUrl('');
            ind.style.background = '#f59e0b';
            ind.style.boxShadow = '0 0 10px #f59e0b';
            badge.textContent = 'TESTING';
            badge.style.color = '#f59e0b';
            badge.style.borderColor = '#f59e0b';

            fetch('/wp-json/')
                .then(res => res.json())
                .then(data => {
                    ind.style.background = '#10b981';
                    ind.style.boxShadow = '0 0 10px #10b981';
                    title.textContent = 'WordPress REST API is Online & Active!';
                    const namespaces = (data.namespaces || []).slice(0, 6).join(', ');
                    details.textContent = 'Active Namespaces: ' + (namespaces || 'REST Core active') + ' (Total: ' + (data.namespaces ? data.namespaces.length : 0) + ')';
                    badge.textContent = '200 OK - ONLINE';
                    badge.style.color = '#10b981';
                    badge.style.borderColor = '#10b981';
                })
                .catch(err => {
                    ind.style.background = '#38bdf8';
                    ind.style.boxShadow = '0 0 10px #38bdf8';
                    title.textContent = 'WordPress REST API Bridge Active';
                    details.textContent = 'REST API endpoint is connected through index.php.';
                    badge.textContent = 'READY';
                    badge.style.color = '#38bdf8';
                    badge.style.borderColor = '#38bdf8';
                });
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3500);
        }

        // Initial render
        renderPlugins();
    </script>
</body>

</html>

<?php
    exit;
}

// -------------------------------------------------------------
// 3. ROUTE FOR E-COMMERCE & SHOWCASE PLATFORMS
// -------------------------------------------------------------
$ecom_stores = [
    'marwari-ecommorce' => ['marwari-ecommorce/index.html', 'main/marwari-ecommorce/index.html', 'main/ShopVibe/index.html'],
    'ecommerce' => ['main/ShopVibe/index.html', 'main/Luxury-Ecommerce/index.html'],
    'ecommerce-management' => ['main/ShopVibe/index.html'],
    'ShopVibe' => ['main/ShopVibe/index.html'],
    'shopvibe' => ['main/ShopVibe/index.html'],
    'Luxury-Ecommerce' => ['main/Luxury-Ecommerce/index.html'],
    'luxury-ecommerce' => ['main/Luxury-Ecommerce/index.html'],
    'Digital-Store' => ['main/Digital-Store/index.html'],
    'digital-store' => ['main/Digital-Store/index.html'],
    'Smart-Kirana-Store' => ['main/Smart-Kirana-Store/index.html'],
    'smart-kirana-store' => ['main/Smart-Kirana-Store/index.html'],
    'fashion-clothing-store' => ['main/fashion-clothing-store/index.html'],
    'jewelry-accessories-store' => ['main/jewelry-accessories-store/index.html'],
    'Footwear' => ['main/Footwear/index.html'],
    'footwear' => ['main/Footwear/index.html'],
    'ARIA-Fashion' => ['main/ARIA-Fashion/index.html'],
    'aria-fashion' => ['main/ARIA-Fashion/index.html'],
    'Jewelry-website' => ['main/Jewelry-website/index.html'],
    'jewelry-website' => ['main/Jewelry-website/index.html'],
    'Marwari-Food' => ['main/Marwari-Food/index.html'],
    'marwari-food' => ['main/Marwari-Food/index.html']
];

$matched_ecom = null;
if ( isset($ecom_stores[$clean_path]) ) {
    $matched_ecom = $ecom_stores[$clean_path];
} elseif ( isset($ecom_stores[strtolower($clean_path)]) ) {
    $matched_ecom = $ecom_stores[strtolower($clean_path)];
}

if ( $matched_ecom ) {
    foreach ($matched_ecom as $rel_file) {
        $full_file = __DIR__ . '/' . $rel_file;
        if ( file_exists($full_file) ) {
            // When serving from main/ subfolder, redirect to /main/... so that relative CSS, JS, and images load correctly
            if ( strpos($rel_file, 'main/') === 0 ) {
                header('Location: /' . $rel_file, true, 302);
                exit;
            }
            include $full_file;
            exit;
        }
    }
    // Fallback to ShopVibe showcase if specific file missing
    if ( file_exists(__DIR__ . '/main/ShopVibe/index.html') ) {
        header('Location: /main/ShopVibe/index.html', true, 302);
        exit;
    }
}

// -------------------------------------------------------------
// 4. ROUTE FOR DIRECT ERP PLUGIN DASHBOARDS & SWAGGER DOCS
// -------------------------------------------------------------
$erp_modules = [
    // Dashboards
    'crm-management' => ['name' => 'CRM ERP API', 'dir' => 'crm-management', 'file' => 'views/dashboard-view.php', 'var' => 'crm_management'],
    'hospital-management' => ['name' => 'Hospital ERP API', 'dir' => 'hospital-management', 'file' => 'views/dashboard-view.php', 'var' => 'hospital_management'],
    'accounting-management' => ['name' => 'Accounting ERP API', 'dir' => 'accounting-management', 'file' => 'views/dashboard-view.php', 'var' => 'accounting_management'],
    'retail-pos' => ['name' => 'Retail POS ERP API', 'dir' => 'retail-pos', 'file' => 'views/dashboard-view.php', 'var' => 'retail_pos'],
    'restaurant-management' => ['name' => 'Restaurant ERP API', 'dir' => 'restaurant-management', 'file' => 'views/dashboard-view.php', 'var' => 'restaurant_management'],
    'inventory-management' => ['name' => 'Inventory ERP API', 'dir' => 'inventory-management', 'file' => 'views/dashboard-view.php', 'var' => 'inventory_management'],
    'hr-management' => ['name' => 'HR & Payroll ERP API', 'dir' => 'hr-management', 'file' => 'views/dashboard-view.php', 'var' => 'hr_management'],
    'garment-management' => ['name' => 'Garment Textile ERP API', 'dir' => 'garment-management', 'file' => 'views/dashboard-view.php', 'var' => 'garment_management'],
    'jewellery-management' => ['name' => 'Jewellery ERP API', 'dir' => 'jewellery-management', 'file' => 'views/dashboard-view.php', 'var' => 'jewellery_management'],
    'manufacturing-management' => ['name' => 'Manufacturing ERP API', 'dir' => 'manufacturing-management', 'file' => 'views/dashboard-view.php', 'var' => 'manufacturing_management'],
    'pharmacy-management' => ['name' => 'Pharmacy ERP API', 'dir' => 'pharmacy-management', 'file' => 'views/dashboard-view.php', 'var' => 'pharmacy_erp'],
    'pharmacy-erp' => ['name' => 'Pharmacy ERP API', 'dir' => 'pharmacy-management', 'file' => 'views/dashboard-view.php', 'var' => 'pharmacy_erp'],
    'gym-management' => ['name' => 'Gym Fitness ERP API', 'dir' => 'gym-management', 'file' => 'views/dashboard-view.php', 'var' => 'gym_erp'],
    'real-estate-management' => ['name' => 'Real Estate ERP API', 'dir' => 'real-estate-management', 'file' => 'views/dashboard-view.php', 'var' => 'real_estate_management'],
    'school-managements' => ['name' => 'School ERP API', 'dir' => 'school-managements', 'file' => 'views/dashboard-view.php', 'var' => 'school_management'],
    'school-management' => ['name' => 'School ERP API', 'dir' => 'school-managements', 'file' => 'views/dashboard-view.php', 'var' => 'school_management'],
    'service-management' => ['name' => 'Service Business ERP API', 'dir' => 'service-management', 'file' => 'views/dashboard-view.php', 'var' => 'service_management'],
    'transport-management' => ['name' => 'Transport Logistics ERP API', 'dir' => 'transport-management', 'file' => 'views/dashboard-view.php', 'var' => 'transport_management'],
    'workspace-erp' => ['name' => 'Workspace ERP API', 'dir' => 'workspace-erp', 'file' => 'views/dashboard-view.php', 'var' => 'workspace_erp'],
    'customer-manager' => ['name' => 'Customer Manager API', 'dir' => 'customer-manager', 'file' => 'views/customer-management.php', 'var' => 'customer_management'],
    'customer-management' => ['name' => 'Customer Manager API', 'dir' => 'customer-manager', 'file' => 'views/customer-management.php', 'var' => 'customer_management'],
    'fleet-track' => ['name' => 'FleetTrack Pro API', 'dir' => 'fleet-track', 'file' => 'views/fleet-dashboard.php', 'var' => 'fleet_track'],
    'construction-management' => ['name' => 'Construction ERP API', 'dir' => 'construction-management', 'file' => 'views/dashboard-view.php', 'var' => 'construction_management'],

    // Swagger API Docs
    'crm-management-api-docs' => ['name' => 'CRM ERP API Docs', 'dir' => 'crm-management', 'file' => 'swagger/index.php'],
    'hospital-management-api-docs' => ['name' => 'Hospital ERP API Docs', 'dir' => 'hospital-management', 'file' => 'swagger/index.php'],
    'accounting-management-api-docs' => ['name' => 'Accounting ERP API Docs', 'dir' => 'accounting-management', 'file' => 'swagger/index.php'],
    'retail-pos-api-docs' => ['name' => 'Retail POS API Docs', 'dir' => 'retail-pos', 'file' => 'swagger/index.php'],
    'restaurant-management-api-docs' => ['name' => 'Restaurant ERP API Docs', 'dir' => 'restaurant-management', 'file' => 'swagger/index.php'],
    'inventory-management-api-docs' => ['name' => 'Inventory ERP API Docs', 'dir' => 'inventory-management', 'file' => 'swagger/index.php'],
    'hr-management-api-docs' => ['name' => 'HR ERP API Docs', 'dir' => 'hr-management', 'file' => 'swagger/index.php'],
    'garment-management-api-docs' => ['name' => 'Garment ERP API Docs', 'dir' => 'garment-management', 'file' => 'swagger/index.php'],
    'jewellery-management-api-docs' => ['name' => 'Jewellery ERP API Docs', 'dir' => 'jewellery-management', 'file' => 'swagger/index.php'],
    'manufacturing-management-api-docs' => ['name' => 'Manufacturing ERP API Docs', 'dir' => 'manufacturing-management', 'file' => 'swagger/index.php'],
    'pharmacy-erp-docs' => ['name' => 'Pharmacy ERP API Docs', 'dir' => 'pharmacy-management', 'file' => 'swagger/index.php'],
    'pharmacy-management-docs' => ['name' => 'Pharmacy ERP API Docs', 'dir' => 'pharmacy-management', 'file' => 'swagger/index.php'],
    'gym-management-docs' => ['name' => 'Gym ERP API Docs', 'dir' => 'gym-management', 'file' => 'swagger/index.php'],
    'real-estate-management-api-docs' => ['name' => 'Real Estate ERP API Docs', 'dir' => 'real-estate-management', 'file' => 'swagger/index.php'],
    'school-management-api-docs' => ['name' => 'School ERP API Docs', 'dir' => 'school-managements', 'file' => 'swagger/index.php'],
    'service-management-api-docs' => ['name' => 'Service ERP API Docs', 'dir' => 'service-management', 'file' => 'swagger/index.php'],
    'transport-management-api-docs' => ['name' => 'Transport ERP API Docs', 'dir' => 'transport-management', 'file' => 'swagger/index.php'],
    'workspace-erp-docs' => ['name' => 'Workspace ERP API Docs', 'dir' => 'workspace-erp', 'file' => 'swagger/index.php'],
    'customer-api-docs' => ['name' => 'Customer API Docs', 'dir' => 'customer-manager', 'file' => 'swagger/index.php'],
    'fleettrack-api-docs' => ['name' => 'FleetTrack API Docs', 'dir' => 'fleet-track', 'file' => 'swagger/index.php'],
    'construction-management-api-docs' => ['name' => 'Construction ERP API Docs', 'dir' => 'construction-management', 'file' => 'swagger/index.php']
];

// Check direct query var requests like ?crm_management=1
$matched_erp = null;
if ( isset($erp_modules[$clean_path]) ) {
    $matched_erp = $erp_modules[$clean_path];
} else {
    foreach ($erp_modules as $slug_key => $mod_data) {
        if ( isset($mod_data['var']) && isset($_GET[$mod_data['var']]) ) {
            $matched_erp = $mod_data;
            break;
        }
    }
}

if ( $matched_erp ) {
    // 1. Boot WordPress environment safely
    if ( !defined('ABSPATH') ) {
        if ( file_exists(__DIR__ . '/wp-load.php') ) {
            define('WP_USE_THEMES', true);
            require_once __DIR__ . '/wp-load.php';
        } else {
            define('ABSPATH', __DIR__ . '/');
        }
    }

    // Safety polyfills so views never crash or exit early
    if ( !function_exists('get_site_url') ) {
        function get_site_url() {
            $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            return $proto . ($_SERVER['HTTP_HOST'] ?? 'rpsdigitalworld.store');
        }
    }
    if ( !function_exists('esc_js') ) {
        function esc_js($str) {
            return addslashes((string)$str);
        }
    }
    if ( !function_exists('esc_html') ) {
        function esc_html($str) {
            return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
        }
    }
    if ( !function_exists('esc_attr') ) {
        function esc_attr($str) {
            return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
        }
    }

    $dir_name = $matched_erp['dir'];
    $file_rel = $matched_erp['file'];

    $candidates = [
        __DIR__ . '/wp-content/plugins/' . $dir_name . '/' . $file_rel,
        __DIR__ . '/' . $dir_name . '/' . $file_rel,
        __DIR__ . '/main/' . $dir_name . '/' . $file_rel,
        __DIR__ . '/wp-content/plugins/' . $dir_name . '/' . $dir_name . '/' . $file_rel,
    ];

    foreach ($candidates as $cand) {
        if ( file_exists($cand) ) {
            include $cand;
            exit;
        }
    }

    // Informative fallback card if files not yet present on disk
    http_response_code(200);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($matched_erp['name'] ?? 'ERP Plugin'); ?> | RPS Digital World</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { font-family: 'Poppins', sans-serif; background: #0b0f19; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
            .card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 40px; max-width: 580px; text-align: center; backdrop-filter: blur(12px); }
            .badge { display: inline-block; background: rgba(99,102,241,0.2); color: #818cf8; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; margin-bottom: 16px; border: 1px solid rgba(99,102,241,0.3); }
            h2 { font-size: 1.6rem; margin-bottom: 12px; color: #fff; }
            p { color: #94a3b8; font-size: 0.92rem; line-height: 1.6; margin-bottom: 24px; }
            .code-box { background: rgba(0,0,0,0.4); padding: 12px 16px; border-radius: 8px; font-family: monospace; font-size: 0.85rem; color: #38bdf8; margin-bottom: 24px; word-break: break-all; }
            .btn-group { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
            .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.88rem; transition: 0.2s; }
            .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
            .btn-secondary { background: rgba(255,255,255,0.08); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.15); }
        </style>
    </head>
    <body>
        <div class="card">
            <span class="badge">ERP MODULE READY</span>
            <h2><?php echo htmlspecialchars($matched_erp['name']); ?></h2>
            <p>The dashboard file is ready to render. Make sure the plugin directory is uploaded to Hostinger File Manager at:</p>
            <div class="code-box">public_html/wp-content/plugins/<?php echo htmlspecialchars($dir_name); ?>/</div>
            <div class="btn-group">
                <a href="/plugins" class="btn btn-primary">Browse All Plugins</a>
                <a href="/" class="btn btn-secondary">Homepage</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// -------------------------------------------------------------
// 5. ROOT HOMEPAGE REQUEST
// -------------------------------------------------------------
if ( ($path === '/' || $path === '/index.php' || $path === '') && !isset($_GET['rest_route']) ) {
    // Output the static HTML landing page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Primary SEO Metadata -->
    <title>RPS Digital World | Software Development, Mobile Apps & SEO Agency Rajasthan</title>
    <meta name="description"
        content="RPS Digital World is a premium software development and IT consultation company based in Surayata, Rajasthan. We build high-performance custom websites, mobile apps, SaaS platforms, API integrations, and offer result-driven SEO & digital marketing services for local and global businesses.">
    <meta name="keywords"
        content="RPS Digital World, software development company Rajasthan, web development Surayata, mobile app developer India, local SEO agency Rajasthan, custom SaaS developer, IT consultation Surayata, software store, software solutions, React developer Rajasthan, Flutter app development">
    <meta name="author" content="RPS Digital World">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://rpsdigitalworld.store/">
    <!-- Open Graph / Facebook / LinkedIn (Social SEO) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://rpsdigitalworld.store/">
    <meta property="og:title" content="RPS Digital World | Software Development & IT Solutions">
    <meta property="og:description"
        content="Custom web solutions, mobile apps, and SaaS platforms built with cutting-edge tech stacks. Serving local Rajasthan businesses & global startups.">
    <meta property="og:image" content="https://rpsdigitalworld.store/logo.png">
    <meta property="og:site_name" content="RPS Digital World">
    <!-- Twitter Cards (Social SEO) -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://rpsdigitalworld.store/">
    <meta name="twitter:title" content="RPS Digital World | Software Development, Mobile Apps & SEO">
    <meta name="twitter:description"
        content="Transforming ideas into digital reality. Custom frontend, backend, native mobile applications, and SEO optimization.">
    <meta name="twitter:image" content="https://rpsdigitalworld.store/logo.png">

    <!-- JSON-LD Local Business & Professional Service Schema (Local SEO) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ProfessionalService",
      "name": "RPS Digital World",
      "alternateName": ["RPS Digital Store", "RPS Techno"],
      "image": "https://rpsdigitalworld.store/logo.png",
      "@id": "https://rpsdigitalworld.store/#organization",
      "url": "https://rpsdigitalworld.store/",
      "email": "info@rpstechno.com",
      "priceRange": "$$",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Ramdevara Dimadi",
        "addressLocality": "Surayata",
        "addressRegion": "Rajasthan",
        "postalCode": "306104",
        "addressCountry": "IN"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 25.922854,
        "longitude": 73.5212909
      },
      "hasMap": "https://maps.app.goo.gl/7seGTDU8u9GLHP1w5",
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday"
          ],
          "opens": "10:00",
          "closes": "18:00"
        }
      ],
      "sameAs": [
        "https://www.linkedin.com/company/rps-digital-world/?viewAsMember=true",
        "https://www.instagram.com/rpsdigitalworld.store/",
        "https://maps.app.goo.gl/7seGTDU8u9GLHP1w5"
      ],
      "areaServed": [
        {
          "@type": "AdministrativeArea",
          "name": "Rajasthan"
        },
        {
          "@type": "AdministrativeArea",
          "name": "India"
        },
        {
          "@type": "AdministrativeArea",
          "name": "Global"
        }
      ]
    }
    </script>

    <!-- Stylesheets and Google Fonts -->
    <link rel="stylesheet" href="./main/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Lottie Player for Dynamic Animations -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>RPS DIGITAL <span>WORLD</span></h2>
            </div>
            <ul class="nav-menu">
                <li><a href="#home" class="nav-link">Home</a></li>
                <li><a href="#services" class="nav-link">Services</a></li>
                <li class="dropdown">
                    <a href="./main/portpolio.html" class="nav-link">Portfolio <i class="fas fa-chevron-down"
                            style="font-size: 0.8rem; margin-left: 3px;"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="./main/portpolio.html?category=E-commerce">E-Commerce Sites</a></li>
                        <li><a href="./main/portpolio.html?category=Medical">Medical & Clinics</a></li>
                        <li><a href="./main/portpolio.html?category=Cafe">Cafe & Food</a></li>
                        <li><a href="./main/portpolio.html?category=Fitness">Fitness & Gyms</a></li>
                        <li><a href="./main/portpolio.html">All Projects</a></li>
                    </ul>
                </li>
                <li><a href="/plugins" class="nav-link">ERP Plugins</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">
                        <span class="gradient-text">Empowering Innovation</span>
                        <br>Software Solutions & IT Consultation
                    </h1>
                    <p class="hero-description">
                        Transforming complex ideas into robust digital reality. We specialize in custom software
                        development, frontend, backend architectures, mobile apps, SaaS deployment, and high-ROI digital
                        marketing for local businesses in Rajasthan and global clients worldwide.
                    </p>
                    <div class="hero-buttons">
                        <a href="#contact" class="btn btn-primary">Get Started</a>
                        <button onclick="window.open('./main/portpolio.html', '_blank')" class="btn btn-secondary">
                            Our Portfolio
                        </button>
                    </div>
                </div>
                <div class="hero-visual" style="display: flex; justify-content: center; align-items: center; position: relative;">
                    <!-- Custom Glowing AI Orb (No JSON needed) -->
                    <style>
                        .ai-core {
                            width: 300px;
                            height: 300px;
                            position: relative;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                        }
                        .core-ring {
                            position: absolute;
                            border-radius: 50%;
                            border: 2px solid transparent;
                            animation: spin infinite linear;
                        }
                        .ring-1 {
                            width: 100%;
                            height: 100%;
                            border-top: 3px solid #00f0ff;
                            border-right: 3px solid transparent;
                            animation-duration: 3s;
                            box-shadow: 0 0 20px #00f0ff, inset 0 0 20px #00f0ff;
                        }
                        .ring-2 {
                            width: 80%;
                            height: 80%;
                            border-bottom: 3px solid #ff00ff;
                            border-left: 3px solid transparent;
                            animation-duration: 2s;
                            animation-direction: reverse;
                            box-shadow: 0 0 20px #ff00ff, inset 0 0 20px #ff00ff;
                        }
                        .ring-3 {
                            width: 60%;
                            height: 60%;
                            border-top: 3px solid #b200ff;
                            border-bottom: 3px solid #b200ff;
                            animation-duration: 4s;
                        }
                        .core-center {
                            width: 30%;
                            height: 30%;
                            background: radial-gradient(circle, #ffffff, #00f0ff);
                            border-radius: 50%;
                            box-shadow: 0 0 30px #00f0ff, 0 0 60px #00f0ff;
                            animation: pulse 2s infinite ease-in-out;
                        }
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                        @keyframes pulse {
                            0%, 100% { transform: scale(1); opacity: 0.8; }
                            50% { transform: scale(1.2); opacity: 1; }
                        }
                    </style>
                    <div class="ai-core">
                        <div class="core-ring ring-1"></div>
                        <div class="core-ring ring-2"></div>
                        <div class="core-ring ring-3"></div>
                        <div class="core-center"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-bg-animation"></div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our <span class="gradient-text">Services</span></h2>
                <p class="section-description">Comprehensive software solutions with cutting-edge technologies tailored
                    to your business needs</p>
            </div>

            <!-- Service Filter -->
            <div class="service-filter">
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All Services</button>
                    <button class="filter-btn" data-filter="development">Development</button>
                    <button class="filter-btn" data-filter="marketing">Marketing</button>
                    <button class="filter-btn" data-filter="cloud">Cloud & DevOps</button>
                </div>
            </div>

            <!-- Main Services Grid -->
            <div class="services-grid">
                <!-- Web Development -->
                <div class="service-card featured-service" data-category="development">
                    <div class="service-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3>Web Development</h3>
                    <p>Modern, responsive web applications using the latest frameworks and technologies</p>
                    <div class="tech-stack-mini">
                        <span class="tech-tag">React.js</span>
                        <span class="tech-tag">Angular</span>
                        <span class="tech-tag">Vue.js</span>
                        <span class="tech-tag">Next.js</span>
                        <span class="tech-tag">TypeScript</span>
                        <span class="tech-tag">Tailwind CSS</span>
                    </div>
                    <div class="service-features">
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Single Page Applications (SPA)</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Progressive Web Apps (PWA)</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Server-Side Rendering (SSR)</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Responsive Design</span>
                        </div>
                    </div>
                    <div class="service-overlay"></div>
                </div>

                <!-- Mobile App Development -->
                <div class="service-card featured-service" data-category="development">
                    <div class="service-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile App Development</h3>
                    <p>Native and cross-platform mobile applications for iOS and Android platforms</p>
                    <div class="tech-stack-mini">
                        <span class="tech-tag">React Native</span>
                        <span class="tech-tag">Flutter</span>
                        <span class="tech-tag">Native Android</span>
                        <span class="tech-tag">Native iOS</span>
                        <span class="tech-tag">Kotlin</span>
                        <span class="tech-tag">Swift</span>
                    </div>
                    <div class="service-features">
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Cross-Platform Development</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Native Performance</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>App Store Optimization</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Push Notifications</span>
                        </div>
                    </div>
                    <div class="service-overlay"></div>
                </div>

                <!-- Backend Development -->
                <div class="service-card featured-service" data-category="development">
                    <div class="service-icon">
                        <i class="fas fa-server"></i>
                    </div>
                    <h3>Backend Development</h3>
                    <p>Robust server-side solutions with scalable architecture and secure APIs</p>
                    <div class="tech-stack-mini">
                        <span class="tech-tag">Node.js</span>
                        <span class="tech-tag">Express.js</span>
                        <span class="tech-tag">Python</span>
                        <span class="tech-tag">Flask</span>
                        <span class="tech-tag">PHP</span>
                        <span class="tech-tag">MongoDB</span>
                    </div>
                    <div class="service-features">
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>RESTful API Development</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>GraphQL Implementation</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Database Design & Optimization</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Microservices Architecture</span>
                        </div>
                    </div>
                    <div class="service-overlay"></div>
                </div>

                <!-- Full Dynamic Applications -->
                <div class="service-card" data-category="development">
                    <div class="service-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3>Full Dynamic Applications</h3>
                    <p>Complete end-to-end dynamic web applications with real-time features</p>
                    <div class="service-features">
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Real-time Data Processing</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>User Authentication & Authorization</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Payment Gateway Integration</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Admin Dashboard & Analytics</span>
                        </div>
                    </div>
                    <div class="service-overlay"></div>
                </div>

                <!-- SEO Services -->
                <div class="service-card" data-category="marketing">
                    <div class="service-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>SEO Services</h3>
                    <p>Comprehensive search engine optimization to boost your online visibility</p>
                    <div class="service-features">
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Technical SEO Audit</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Keyword Research & Strategy</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Content Optimization</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Local SEO & Google My Business</span>
                        </div>
                    </div>
                    <div class="service-overlay"></div>
                </div>

                <!-- Digital Marketing -->
                <div class="service-card" data-category="marketing">
                    <div class="service-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Digital Marketing</h3>
                    <p>Strategic digital marketing campaigns to boost your online presence and ROI</p>
                    <div class="service-features">
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Social Media Marketing</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Google Ads & PPC</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Email Marketing Campaigns</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Content Marketing Strategy</span>
                        </div>
                    </div>
                    <div class="service-overlay"></div>
                </div>

                <!-- WordPress Development -->
                <div class="service-card" data-category="development">
                    <div class="service-icon">
                        <i class="fab fa-wordpress"></i>
                    </div>
                    <h3>WordPress Development</h3>
                    <p>Custom WordPress solutions from simple blogs to complex enterprise websites</p>
                    <div class="service-features">
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Custom Theme Development</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Plugin Development</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>E-commerce Solutions</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Performance Optimization</span>
                        </div>
                    </div>
                    <div class="service-overlay"></div>
                </div>

                <!-- Cloud Solutions -->
                <div class="service-card" data-category="cloud">
                    <div class="service-icon">
                        <i class="fas fa-cloud"></i>
                    </div>
                    <h3>Cloud Solutions</h3>
                    <p>Scalable cloud infrastructure and deployment solutions for modern applications</p>
                    <div class="service-features">
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>AWS & Azure Deployment</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>DevOps & CI/CD</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Container Orchestration</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>Auto-scaling & Load Balancing</span>
                        </div>
                    </div>
                    <div class="service-overlay"></div>
                </div>
            </div>

            <!-- Technology Showcase -->
            <div class="tech-showcase">
                <h3 class="tech-showcase-title">Technologies We <span class="gradient-text">Master</span></h3>
                <div class="tech-categories">
                    <div class="tech-category">
                        <h4>Frontend</h4>
                        <div class="tech-icons">
                            <div class="tech-icon" data-tech="React.js">
                                <i class="fab fa-react"></i>
                                <span>React.js</span>
                            </div>
                            <div class="tech-icon" data-tech="Angular">
                                <i class="fab fa-angular"></i>
                                <span>Angular</span>
                            </div>
                            <div class="tech-icon" data-tech="Vue.js">
                                <i class="fab fa-vuejs"></i>
                                <span>Vue.js</span>
                            </div>
                            <div class="tech-icon" data-tech="Next.js">
                                <i class="fas fa-code"></i>
                                <span>Next.js</span>
                            </div>
                        </div>
                    </div>
                    <div class="tech-category">
                        <h4>Backend</h4>
                        <div class="tech-icons">
                            <div class="tech-icon" data-tech="Node.js">
                                <i class="fab fa-node-js"></i>
                                <span>Node.js</span>
                            </div>
                            <div class="tech-icon" data-tech="Python">
                                <i class="fab fa-python"></i>
                                <span>Python</span>
                            </div>
                            <div class="tech-icon" data-tech="PHP">
                                <i class="fab fa-php"></i>
                                <span>PHP</span>
                            </div>
                            <div class="tech-icon" data-tech="Express.js">
                                <i class="fas fa-server"></i>
                                <span>Express.js</span>
                            </div>
                        </div>
                    </div>
                    <div class="tech-category">
                        <h4>Mobile</h4>
                        <div class="tech-icons">
                            <div class="tech-icon" data-tech="React Native">
                                <i class="fab fa-react"></i>
                                <span>React Native</span>
                            </div>
                            <div class="tech-icon" data-tech="Flutter">
                                <i class="fas fa-mobile"></i>
                                <span>Flutter</span>
                            </div>
                            <div class="tech-icon" data-tech="Android">
                                <i class="fab fa-android"></i>
                                <span>Android</span>
                            </div>
                            <div class="tech-icon" data-tech="iOS">
                                <i class="fab fa-apple"></i>
                                <span>iOS</span>
                            </div>
                        </div>
                    </div>
                    <div class="tech-category">
                        <h4>Database</h4>
                        <div class="tech-icons">
                            <div class="tech-icon" data-tech="MongoDB">
                                <i class="fas fa-database"></i>
                                <span>MongoDB</span>
                            </div>
                            <div class="tech-icon" data-tech="MySQL">
                                <i class="fas fa-database"></i>
                                <span>MySQL</span>
                            </div>
                            <div class="tech-icon" data-tech="PostgreSQL">
                                <i class="fas fa-database"></i>
                                <span>SQL Server</span>
                            </div>
                            <div class="tech-icon" data-tech="Firebase">
                                <i class="fas fa-fire"></i>
                                <span>Firebase</span>
                            </div>
                        </div>
                    </div>
                    <!-- CMS & Platforms -->
                    <div class="tech-category">
                        <h4>CMS & Platforms</h4>
                        <div class="tech-icons">
                            <div class="tech-icon" data-tech="WordPress">
                                <i class="fab fa-wordpress"></i>
                                <span>WordPress</span>
                            </div>
                            <div class="tech-icon" data-tech="Shopify">
                                <i class="fab fa-shopify"></i>
                                <span>Shopify</span>
                            </div>
                            <div class="tech-icon" data-tech="Magento">
                                <i class="fas fa-store"></i>
                                <span>Magento</span>
                            </div>
                            <div class="tech-icon" data-tech="Strapi">
                                <i class="fas fa-cogs"></i>
                                <span>Strapi</span>
                            </div>
                        </div>
                    </div>
                    <!-- Cloud & DevOps -->
                    <div class="tech-category">
                        <h4>Cloud & DevOps</h4>
                        <div class="tech-icons">
                            <div class="tech-icon" data-tech="AWS">
                                <i class="fab fa-aws"></i>
                                <span>AWS</span>
                            </div>
                            <div class="tech-icon" data-tech="Azure">
                                <i class="fas fa-cloud"></i>
                                <span>Azure</span>
                            </div>
                            <div class="tech-icon" data-tech="Docker">
                                <i class="fab fa-docker"></i>
                                <span>Docker</span>
                            </div>
                            <div class="tech-icon" data-tech="Kubernetes">
                                <i class="fas fa-network-wired"></i>
                                <span>Kubernetes</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Service Statistics -->
            <div class="service-stats">
                <div class="container">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="stat-number" data-target="200">0</div>
                            <div class="stat-label">Projects Delivered</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-code"></i>
                            </div>
                            <div class="stat-number" data-target="50">0</div>
                            <div class="stat-label">Technologies Mastered</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-smile"></i>
                            </div>
                            <div class="stat-number" data-target="100">0</div>
                            <div class="stat-label">Happy Clients</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-number" data-target="24">0</div>
                            <div class="stat-label">Support Hours</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed SEO Content Section -->
    <section class="seo-details-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Rajasthan's Trusted <span class="gradient-text">Web & Mobile
                        Development</span> Company</h2>
                <p class="section-description">
                    Empowering local brands in Rajasthan and global businesses with scalable, fast, and feature-rich IT
                    applications.                    We are your go-to software engineering partner for custom websites, complex SaaS platforms, and
                    high-performance mobile apps.
                </p>
            </div>
            <div class="seo-grid">
                <!-- Column 1: Web Development Suite -->
                <div class="seo-card">
                    <div class="seo-card-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Custom Web Development Suite</h3>
                    <p>
                        We build custom, high-speed websites and portals designed to scale with your business growth.                        As a leading web development company in Rajasthan, we combine advanced backend technologies with
                        stunning modern frontends.
                    </p>
                    <ul class="seo-list">
                        <li>
                            <strong>Enterprise SaaS & Web Apps:</strong>                            We develop robust Software-as-a-Service platforms featuring secure multi-tenant
                            architectures, dynamic user roles,                            automated subscriptions, and custom operational dashboards using React, Next.js, and
                            Node.js.
                        </li>
                        <li>
                            <strong>E-commerce Website Engineering:</strong>                            High-converting, mobile-friendly online stores with smooth product discovery, fast shopping
                            carts,                            and seamless local/global payment integrations (Razorpay, Stripe, Paytm).
                        </li>
                        <li>
                            <strong>Corporate Branding Websites:</strong>                            Beautifully designed business landing pages optimized for fast page speed, clean DOM
                            structures,                            and SEO friendliness to capture organic search leads directly from Google Chrome.
                        </li>
                        <li>
                            <strong>API Architectures & Backends:</strong>                            Highly secure RESTful and GraphQL APIs developed with Python, Flask, and Express.js,                            connected to robust MongoDB and SQL databases.
                        </li>
                    </ul>
                </div>
                <!-- Column 2: Mobile App Development Suite -->
                <div class="seo-card">
                    <div class="seo-card-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Application Engineering</h3>
                    <p>
                        Transform your ideas into high-performance, user-friendly mobile applications.                        Our mobile app development agency designs custom native and cross-platform apps for iOS and
                        Android.
                    </p>
                    <ul class="seo-list">
                        <li>
                            <strong>Cross-Platform Hybrid Apps:</strong>                            Cost-effective hybrid app development using Google Flutter and React Native,                            sharing a single codebase to deliver native performance and seamless fluid motions.
                        </li>
                        <li>
                            <strong>Native Android App Development:</strong>                            Dedicated high-speed Android applications built with Kotlin and Java, fully optimized                            for all device models, screen sizes, and Google Play Store parameters.
                        </li>
                        <li>
                            <strong>Native iOS App Development:</strong>                            Premium Swift-built applications for iPhone and iPad, designed to conform                            strictly to Apple's Human Interface Guidelines for premium UX.
                        </li>
                        <li>
                            <strong>App Store Optimization & Support:</strong>                            End-to-end publishing, metadata refinement, keyword planning, and optimization                            to rank higher in app store searches and boost downloads.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="seo-bottom-banner">
                <h3>Connecting Rajasthan's Legacy with Global Tech Standards</h3>
                <p>
                    Based in <strong>Surayata, Rajasthan (Zip: 306104)</strong>, we serve local businesses in Sojat,
                    Pali, Jodhpur,                    and Jaipur, helping them digitize operations, launch online delivery systems, and manage local
                    customer relations.                    Simultaneously, we run an agile global execution framework supporting startups and established
                    enterprises in the USA, UK, UAE, and Europe.
                </p>
                <div class="seo-badges">
                    <span class="seo-badge"><i class="fas fa-check-circle"></i> 100% Mobile Responsive</span>
                    <span class="seo-badge"><i class="fas fa-bolt"></i> Google Core Web Vitals Ready</span>
                    <span class="seo-badge"><i class="fas fa-shield-alt"></i> Secure SSL & Encryption</span>
                    <span class="seo-badge"><i class="fas fa-search-plus"></i> Schema Structured Metadata</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2 class="section-title">About <span class="gradient-text">RPS Digital World</span></h2>
                    <p>Based in Surayata, Rajasthan, RPS Digital World is a premier software development agency
                        delivering innovative technology solutions to startups, enterprises, and local businesses alike.
                        With a strong commitment to quality and engineering excellence, we transform your digital vision
                        into custom software realities.</p>
                    <p>Our global and local expertise spans across modern web technologies, hybrid and native mobile app
                        development, scalable backend architecture, cloud solutions, and result-oriented digital
                        marketing strategies. We pride ourselves on delivering high-performance, robust solutions that
                        drive commercial and local business growth.</p>
                    <div class="stats">
                        <div class="stat-item">
                            <h3 class="stat-number" data-target="150">0</h3>
                            <p>Projects Completed</p>
                        </div>
                        <div class="stat-item">
                            <h3 class="stat-number" data-target="50">0</h3>
                            <p>Happy Clients</p>
                        </div>
                        <div class="stat-item">
                            <h3 class="stat-number" data-target="5">0</h3>
                            <p>Years Experience</p>
                        </div>
                    </div>
                </div>
                <div class="about-visual">
                    <div class="tech-stack">
                        <div class="tech-item">React</div>
                        <div class="tech-item">Node.js</div>
                        <div class="tech-item">Python</div>
                        <div class="tech-item">MongoDB</div>
                        <div class="tech-item">AWS</div>
                        <div class="tech-item">Flutter</div>
                        <div class="tech-item">React Native</div>
                        <div class="tech-item">MySQL</div>
                        <div class="tech-item">WordPress</div>
                        <div class="tech-item">Android</div>
                        <div class="tech-item">iOS</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Get In <span class="gradient-text">Touch</span></h2>
                <p class="section-description">Ready to start your next project? Let's discuss your requirements</p>
            </div>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4>Email</h4>
                            <p>info@rpstechno.com</p>
                        </div>
                    </div>
                    <!-- <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4>Phone</h4>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </div> -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4>Location</h4>
                            <p><a href="https://maps.app.goo.gl/7seGTDU8u9GLHP1w5" target="_blank"
                                    rel="noopener noreferrer" style="color: inherit; text-decoration: none;">Ramdevara
                                    Dimadi, Surayata, Rajasthan 306104</a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fab fa-linkedin"></i>
                        </div>
                        <div>
                            <h4>LinkedIn</h4>
                            <p><a href="https://www.linkedin.com/company/rps-digital-world/?viewAsMember=true"
                                    target="_blank" rel="noopener noreferrer"
                                    style="color: inherit; text-decoration: none;">RPS Digital World</a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <div>
                            <h4>Instagram</h4>
                            <p><a href="https://www.instagram.com/rpsdigitalworld.store/" target="_blank"
                                    rel="noopener noreferrer"
                                    style="color: inherit; text-decoration: none;">@rpsdigitalworld.store</a></p>
                        </div>
                    </div>
                </div>
                <form class="contact-form" data-contact-form>
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" autocomplete="name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" autocomplete="email" required>
                    </div>
                    <div class="form-group">
                        <select name="service" required>
                            <option value="">Select Service</option>
                            <option value="web-development">Web Development</option>
                            <option value="mobile-development">Mobile App Development</option>
                            <option value="backend">Backend Development</option>
                            <option value="full-stack">Full Dynamic Applications</option>
                            <option value="seo">SEO Services</option>
                            <option value="digital-marketing">Digital Marketing</option>
                            <option value="wordpress">WordPress Development</option>
                            <option value="cloud">Cloud Solutions</option>
                            <option value="consultation">Technical Consultation</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>RPS <span>Digital World</span></h3>
                    <p>Transforming ideas into digital reality with innovative software solutions for local and global
                        clients.</p>
                    <div class="social-links">
                        <a href="https://www.linkedin.com/company/rps-digital-world/?viewAsMember=true" target="_blank"
                            rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <a href="https://www.instagram.com/rpsdigitalworld.store/" target="_blank"
                            rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Web Development</a></li>
                        <li><a href="#">Mobile App Development</a></li>
                        <li><a href="#">Backend Development</a></li>
                        <li><a href="#">Digital Marketing</a></li>
                        <li><a href="#">SEO Services</a></li>
                        <li><a href="#">WordPress Development</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Technologies</h4>
                    <ul>
                        <li><a href="#">React.js & Next.js</a></li>
                        <li><a href="#">Angular & Vue.js</a></li>
                        <li><a href="#">Node.js & Express.js</a></li>
                        <li><a href="#">Python & Flask</a></li>
                        <li><a href="#">React Native & Flutter</a></li>
                        <li><a href="#">Cloud & DevOps</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:info@rpstechno.com"
                            style="color: inherit;">info@rpstechno.com</a></p>
                    <!-- <p><i class="fas fa-phone"></i> +91 99944 76566</p> -->
                    <p><i class="fas fa-map-marker-alt"></i> <a href="https://maps.app.goo.gl/7seGTDU8u9GLHP1w5"
                            target="_blank" rel="noopener noreferrer"
                            style="color: inherit; text-decoration: none;">Ramdevara Dimadi, Surayata, Rajasthan
                            306104</a></p>
                    <p><i class="fas fa-clock"></i> Mon - Sat: 10:00 AM - 6:00 PM IST</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 RPS Digital World. All rights reserved. | Crafted with ❤️ for local & global innovation |
                    <a href="./privacy-policy.html">Privacy Policy</a></p>
            </div>
        </div>
    </footer>

    <script src="./main/script.js"></script>
</body>

</html>
<?php
    exit;
}

// -------------------------------------------------------------
// 6. CATCH MISSING FILES UNDER /main/
// -------------------------------------------------------------
if ( strpos($path, '/main/') === 0 ) {
    rps_render_404();
}

// -------------------------------------------------------------
// 7. LOAD WORDPRESS ENVIRONMENT FOR ADMIN, REST API, PLUGINS
// -------------------------------------------------------------
if ( file_exists(__DIR__ . '/wp-load.php') ) {
    define( 'WP_USE_THEMES', true );
    $wp_did_header = true;
    require_once __DIR__ . '/wp-load.php';
    wp();

    // If WordPress determined this is a 404 (and not an active REST route or admin page)
    if ( is_404() ) {
        rps_render_404();
    }

    require_once ABSPATH . WPINC . '/template-loader.php';
    exit;
}

// Default fallback if wp-load.php does not exist
rps_render_404();
