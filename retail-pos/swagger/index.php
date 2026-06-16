<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retail POS ERP API - Swagger UI Sandbox</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.9.0/swagger-ui.css" />
    <style>
      html { box-sizing: border-box; overflow: -moz-scrollbars-vertical; overflow-y: scroll; }
      *, *:before, *:after { box-sizing: inherit; }
      body { margin: 0; background: #111827; }
      /* Dark Theme customization for premium look in docs too */
      .swagger-ui { filter: invert(0.9) hue-rotate(180deg); }
      .swagger-ui .info .title { color: #ffffff; }
      .swagger-ui .scheme-container { background: #e0e0e0; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.9.0/swagger-ui-bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.9.0/swagger-ui-standalone-preset.js"></script>
    <script>
    window.onload = function() {
      const ui = SwaggerUIBundle({
        url: "<?php echo esc_url( plugins_url( 'swagger.json', __FILE__ ) ); ?>",
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],
        plugins: [
          SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "BaseLayout",
        requestInterceptor: function(request) {
          // Allow users to easily intercept request or append token
          return request;
        }
      });
      window.ui = ui;
    };
    </script>
</body>
</html>
