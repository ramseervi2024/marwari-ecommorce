<?php
if (!defined('ABSPATH')) { exit; }
$swagger_json_url = plugin_dir_url(dirname(__FILE__)) . 'swagger/swagger.json';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HR & Payroll ERP – API Documentation</title>
    <meta name="description" content="Interactive Swagger UI for the HR & Payroll ERP REST API. Explore and test all endpoints live." />
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0f1117; color: #e2e8f0; min-height: 100vh; }
        .hr-swagger-header {
            background: linear-gradient(135deg, #1a1f35 0%, #162032 50%, #1a1f35 100%);
            border-bottom: 1px solid rgba(99,179,237,0.2);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .hr-swagger-header .logo {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        .hr-swagger-header h1 { font-size: 20px; font-weight: 700; color: #e2e8f0; }
        .hr-swagger-header span { font-size: 13px; color: #64748b; }
        .hr-badge {
            margin-left: auto;
            background: rgba(59,130,246,0.15);
            border: 1px solid rgba(59,130,246,0.3);
            color: #60a5fa;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        #swagger-ui { padding: 0; }
        .swagger-ui .topbar { display: none; }
        .swagger-ui .info { padding: 24px 32px; }
        .swagger-ui .info .title { color: #e2e8f0; }
        .swagger-ui .info .description { color: #94a3b8; }
        .swagger-ui .scheme-container { background: #1e2535; border-bottom: 1px solid rgba(255,255,255,0.05); padding: 12px 32px; }
        .swagger-ui .opblock-tag { color: #93c5fd; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .swagger-ui .opblock.opblock-get    .opblock-summary-method { background: #0e7490; }
        .swagger-ui .opblock.opblock-post   .opblock-summary-method { background: #1d4ed8; }
        .swagger-ui .opblock.opblock-put    .opblock-summary-method { background: #b45309; }
        .swagger-ui .opblock.opblock-delete .opblock-summary-method { background: #991b1b; }
        .swagger-ui .btn.authorize { background: #1d4ed8; border-color: #3b82f6; color: #fff; border-radius: 6px; }
        .swagger-ui .btn.authorize:hover { background: #2563eb; }
        .swagger-ui select, .swagger-ui input { background: #1e2535; color: #e2e8f0; border-color: rgba(255,255,255,0.1); border-radius: 6px; }
        .swagger-ui .responses-wrapper, .swagger-ui .response-col_status { color: #e2e8f0; }
        .swagger-ui section.models { background: #161b2e; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; margin: 0 32px 32px; }
        .swagger-ui .model-title { color: #7dd3fc; }
        .swagger-ui .model { color: #94a3b8; }
        .swagger-ui .opblock-body { background: #161b2e; }
        .swagger-ui .highlight-code { background: #0d1117; }
        .swagger-ui .microlight { background: #0d1117; color: #e2e8f0; }
        .swagger-ui table tbody tr td { border-color: rgba(255,255,255,0.06); color: #cbd5e1; }
        .swagger-ui .parameter__name { color: #93c5fd; }
        .swagger-ui .parameter__type  { color: #86efac; }
        .swagger-ui .opblock-summary-description { color: #94a3b8; }
        .swagger-ui .opblock { border-radius: 8px; margin-bottom: 8px; border-color: rgba(255,255,255,0.08); background: #1a2035; }
        .swagger-ui .opblock .opblock-summary { border-radius: 8px; }
        .swagger-ui .wrapper { padding: 0 32px; }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>
    <div class="hr-swagger-header">
        <div class="logo">🏢</div>
        <div>
            <h1>HR & Payroll ERP</h1>
            <span>REST API Interactive Documentation</span>
        </div>
        <div class="hr-badge">v1.0.0 · OpenAPI 3.0</div>
    </div>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function () {
            SwaggerUIBundle({
                url: "<?php echo esc_js($swagger_json_url); ?>",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset],
                plugins: [SwaggerUIBundle.plugins.DownloadUrl],
                layout: 'StandaloneLayout',
                persistAuthorization: true,
                tryItOutEnabled: true,
                displayRequestDuration: true,
                filter: true,
                syntaxHighlight: { theme: 'monokai' },
            });
        };
    </script>
</body>
</html>
