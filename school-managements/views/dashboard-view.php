<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management Dashboard - Portal</title>
    <!-- Modern Premium Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-secondary: rgba(17, 24, 39, 0.7);
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --accent-pink: #ec4899;
            --accent-emerald: #10b981;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-shadow: rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            transition: all 0.25s ease;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
            background-attachment: fixed;
        }

        /* App Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Glassmorphism */
        .sidebar {
            width: 260px;
            background: rgba(10, 15, 30, 0.8);
            backdrop-filter: blur(16px);
            border-right: 1px solid var(--glass-border);
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 20px;
            background: linear-gradient(135deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 40px;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border-left: 3px solid var(--accent-blue);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid var(--glass-border);
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
        }

        .user-info h4 {
            font-size: 14px;
            font-weight: 600;
        }

        .user-info p {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Main Content Panel */
        .main-panel {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .title-group h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
            background: linear-gradient(to right, #ffffff, #d1d5db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .title-group p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .badge-live {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--accent-emerald);
            color: var(--accent-emerald);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-emerald);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 8px 32px var(--glass-shadow);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--accent-blue), var(--accent-purple));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.4);
            border-color: rgba(255,255,255,0.15);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255,255,255,0.03);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--accent-blue);
        }

        .stat-card:nth-child(2) .card-icon { color: var(--accent-purple); }
        .stat-card:nth-child(3) .card-icon { color: var(--accent-pink); }
        .stat-card:nth-child(4) .card-icon { color: var(--accent-emerald); }

        .card-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .card-value {
            font-size: 26px;
            font-weight: 700;
        }

        /* Charts Section */
        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 35px;
        }

        .chart-box {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px var(--glass-shadow);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .chart-header h3 {
            font-size: 18px;
            font-weight: 600;
        }

        .chart-canvas {
            width: 100%;
            height: 250px;
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding-top: 20px;
        }

        /* Simulated SVG Charts */
        .svg-chart {
            width: 100%;
            height: 100%;
        }

        /* Notice Board List */
        .notice-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .notice-item {
            padding: 16px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            border-left: 4px solid var(--accent-purple);
            font-size: 14px;
        }

        .notice-item h5 {
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .notice-item p {
            color: var(--text-muted);
            line-height: 1.4;
        }

        .notice-date {
            font-size: 11px;
            color: var(--accent-purple);
            margin-top: 6px;
        }

        /* Quick Action Toolbar */
        .quick-actions {
            background: rgba(255,255,255,0.01);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .quick-actions h4 {
            font-size: 16px;
            font-weight: 600;
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.06);
            color: var(--text-main);
            border: 1px solid var(--glass-border);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.1);
        }

        @media (max-width: 992px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
            .sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div>
                <div class="brand">
                    <div class="brand-icon">S</div>
                    <span>Global School ERP</span>
                </div>
                <ul class="menu-list">
                    <li><a href="#" class="menu-item active">Dashboard</a></li>
                    <li><a href="#" class="menu-item">Students</a></li>
                    <li><a href="#" class="menu-item">Teachers</a></li>
                    <li><a href="#" class="menu-item">Attendance</a></li>
                    <li><a href="#" class="menu-item">Exams & Grades</a></li>
                    <li><a href="#" class="menu-item">Fees Module</a></li>
                    <li><a href="#" class="menu-item">Library</a></li>
                    <li><a href="#" class="menu-item">Transport</a></li>
                </ul>
            </div>
            
            <div class="user-profile">
                <div class="avatar">SA</div>
                <div class="user-info">
                    <h4>Admin Portal</h4>
                    <p>Super Administrator</p>
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="main-panel">
            <!-- Top Header -->
            <header class="header-section">
                <div class="title-group">
                    <h1>School Management Overview</h1>
                    <p>Live insights, statistical charts, and management workflows dashboard</p>
                </div>
                <div class="badge-live">
                    <span class="live-dot"></span> Live Analytics Ready
                </div>
            </header>

            <!-- Statistical Cards -->
            <section class="cards-grid">
                <div class="stat-card">
                    <div class="card-icon">🎓</div>
                    <div class="card-label">Total Active Students</div>
                    <div class="card-value">1,248</div>
                </div>
                <div class="stat-card">
                    <div class="card-icon">👨‍🏫</div>
                    <div class="card-label">Certified Teachers</div>
                    <div class="card-value">84</div>
                </div>
                <div class="stat-card">
                    <div class="card-icon">💵</div>
                    <div class="card-label">Monthly Fees Collected</div>
                    <div class="card-value">$42,560</div>
                </div>
                <div class="stat-card">
                    <div class="card-icon">📅</div>
                    <div class="card-label">Average Attendance Rate</div>
                    <div class="card-value">94.8%</div>
                </div>
            </section>

            <!-- Interactive Chart Rows -->
            <section class="charts-row">
                <!-- Admissions Trend Chart -->
                <div class="chart-box">
                    <div class="chart-header">
                        <h3>Student Admissions & Fees Collection Trends (Last 6 Months)</h3>
                        <span style="font-size: 12px; color: var(--text-muted);">Real-time aggregated stats</span>
                    </div>
                    <div class="chart-canvas">
                        <svg class="svg-chart" viewBox="0 0 500 200" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="blueGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.4"/>
                                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                                </linearGradient>
                                <linearGradient id="purpleGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.4"/>
                                    <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.0"/>
                                </linearGradient>
                            </defs>
                            <!-- Grid Lines -->
                            <line x1="0" y1="50" x2="500" y2="50" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                            <line x1="0" y1="100" x2="500" y2="100" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                            <line x1="0" y1="150" x2="500" y2="150" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                            
                            <!-- Area 1 (Admissions) -->
                            <path d="M 0 160 Q 100 120 200 130 T 400 80 L 500 60 L 500 200 L 0 200 Z" fill="url(#blueGrad)" />
                            <path d="M 0 160 Q 100 120 200 130 T 400 80 L 500 60" fill="none" stroke="var(--accent-blue)" stroke-width="3" />
                            
                            <!-- Area 2 (Fees Collection) -->
                            <path d="M 0 180 Q 100 140 200 150 T 400 100 L 500 90 L 500 200 L 0 200 Z" fill="url(#purpleGrad)" />
                            <path d="M 0 180 Q 100 140 200 150 T 400 100 L 500 90" fill="none" stroke="var(--accent-purple)" stroke-width="3" />
                            
                            <!-- Data Dots -->
                            <circle cx="200" cy="130" r="5" fill="#3b82f6" stroke="#fff" stroke-width="1.5" />
                            <circle cx="400" cy="80" r="5" fill="#3b82f6" stroke="#fff" stroke-width="1.5" />
                            <circle cx="200" cy="150" r="5" fill="#8b5cf6" stroke="#fff" stroke-width="1.5" />
                            <circle cx="400" cy="100" r="5" fill="#8b5cf6" stroke="#fff" stroke-width="1.5" />
                        </svg>
                    </div>
                </div>

                <!-- Notice Board -->
                <div class="chart-box">
                    <div class="chart-header">
                        <h3>Notice Board</h3>
                    </div>
                    <div class="notice-list">
                        <div class="notice-item">
                            <h5>Summer Holidays Announcement</h5>
                            <p>School campuses will remain closed for summer recess from June 20 to July 10, 2026.</p>
                            <div class="notice-date">10 mins ago</div>
                        </div>
                        <div class="notice-item" style="border-left-color: var(--accent-pink);">
                            <h5>Term-1 Marks Submission</h5>
                            <p>Teachers are requested to finalize and upload Grade 10 marksheets before June 25.</p>
                            <div class="notice-date">2 hours ago</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Action Panel -->
            <footer class="quick-actions">
                <div>
                    <h4>Interactive API & Portal Operations</h4>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">Inspect backend capabilities using interactive Swagger tool and Postman specs</p>
                </div>
                <div class="btn-group">
                    <a href="/school-management-api-docs" class="btn" target="_blank">Open Swagger API Docs</a>
                    <button class="btn btn-secondary" onclick="alert('Plugin configured successfully! Access endpoints using /wp-json/school-management/v1')">Test Connection</button>
                </div>
            </footer>
        </main>
    </div>
</body>
</html>
