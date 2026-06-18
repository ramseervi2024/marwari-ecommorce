<?php
if (!defined('ABSPATH')) {
    exit;
}
$site_url = get_site_url();
$api_base = $site_url . '/wp-json/crm/v1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM ERP API - Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body { margin: 0; background: #0f1117; }
        .swagger-ui .topbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .swagger-ui .topbar .download-url-wrapper { display: none; }
        .swagger-ui .topbar-wrapper img { display: none; }
        .swagger-ui .topbar-wrapper::after { 
            content: '🚀 CRM ERP REST API — Interactive Documentation'; 
            color: white; font-size: 1.2rem; font-weight: 700; letter-spacing: 0.5px;
        }
        .swagger-ui .info { background: #1a1d2e; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .swagger-ui .info .title { color: #a78bfa; }
    </style>
</head>
<body>
<div id="swagger-ui"></div>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
const spec = {
  openapi: '3.0.0',
  info: {
    title: 'CRM ERP API',
    version: '1.0.0',
    description: `## CRM ERP System REST API\n\nA full-featured Customer Relationship Management system built as a WordPress plugin.\n\n### Demo Credentials\n| Username | Password | Role |\n|---|---|---|\n| csuperadmin | 123456 | Super Admin |\n| cmanager | 123456 | Sales Manager |\n| cexecutive | 123456 | Sales Executive |\n| ctelecaller | 123456 | Telecaller |\n| ccustomer | 123456 | Customer |\n\n**Step 1**: Call \`POST /auth/login\` with username & password\n\n**Step 2**: Copy the \`token\` from the response\n\n**Step 3**: Click **Authorize** above → paste \`Bearer {token}\``,
    contact: { name: 'Ramesh Seervi', email: 'admin@crm-erp.com' }
  },
  servers: [{ url: '<?php echo esc_js($api_base); ?>', description: 'WordPress REST API' }],
  components: {
    securitySchemes: {
      BearerAuth: { type: 'http', scheme: 'bearer', bearerFormat: 'JWT', description: 'JWT Token from /auth/login' }
    },
    schemas: {
      Success: { type: 'object', properties: { success: { type: 'boolean', example: true }, message: { type: 'string' }, data: { type: 'object' } } },
      Error:   { type: 'object', properties: { success: { type: 'boolean', example: false }, message: { type: 'string' }, data: { type: 'object' } } },
      Lead: {
        type: 'object',
        properties: {
          id: { type: 'integer' }, lead_number: { type: 'string' }, first_name: { type: 'string' },
          last_name: { type: 'string' }, company_name: { type: 'string' }, mobile: { type: 'string' },
          email: { type: 'string', format: 'email' }, website: { type: 'string' }, lead_source: { type: 'string',
            enum: ['Website','Facebook','Google Ads','LinkedIn','Referral','WhatsApp','Walk-In','Cold Calling'] },
          industry: { type: 'string' }, city: { type: 'string' }, state: { type: 'string' },
          assigned_to: { type: 'integer' }, lead_status: { type: 'string',
            enum: ['New','Contacted','Interested','Follow-Up','Quotation Sent','Negotiation','Won','Lost'] },
          remarks: { type: 'string' }, created_at: { type: 'string', format: 'date-time' }
        }
      },
      Followup: {
        type: 'object',
        properties: {
          id: { type: 'integer' }, lead_id: { type: 'integer' }, followup_date: { type: 'string', format: 'date' },
          followup_time: { type: 'string' }, communication_type: { type: 'string', enum: ['Call','WhatsApp','Email','Meeting','SMS'] },
          remarks: { type: 'string' }, next_followup_date: { type: 'string', format: 'date' },
          status: { type: 'string', enum: ['Pending','Completed','Cancelled'] }
        }
      },
      Task: {
        type: 'object',
        properties: {
          id: { type: 'integer' }, title: { type: 'string' }, description: { type: 'string' },
          due_date: { type: 'string', format: 'date' },
          status: { type: 'string', enum: ['Pending','In Progress','Completed','Cancelled'] },
          priority: { type: 'string', enum: ['Low','Medium','High','Urgent'] },
          assigned_to: { type: 'integer' }, lead_id: { type: 'integer' }
        }
      },
      Deal: {
        type: 'object',
        properties: {
          id: { type: 'integer' }, deal_number: { type: 'string' }, lead_id: { type: 'integer' },
          customer_id: { type: 'integer' }, deal_value: { type: 'number' },
          expected_close_date: { type: 'string', format: 'date' },
          deal_stage: { type: 'string', enum: ['Prospecting','Qualification','Proposal','Negotiation','Won','Lost'] },
          probability: { type: 'integer', minimum: 0, maximum: 100 }, assigned_to: { type: 'integer' }
        }
      },
      Invoice: {
        type: 'object',
        properties: {
          id: { type: 'integer' }, invoice_number: { type: 'string' }, deal_id: { type: 'integer' },
          customer_id: { type: 'integer' }, invoice_date: { type: 'string', format: 'date' },
          due_date: { type: 'string', format: 'date' }, subtotal: { type: 'number' },
          tax_amount: { type: 'number' }, grand_total: { type: 'number' },
          status: { type: 'string', enum: ['Unpaid','Partial','Paid','Overdue'] }, items: { type: 'array', items: { type: 'object' } }
        }
      }
    }
  },
  security: [{ BearerAuth: [] }],
  paths: {
    '/auth/register': {
      post: {
        tags: ['Authentication'], summary: 'Register new user (sends OTP)',
        security: [],
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['username','email','name'],
          properties: { username: { type: 'string', example: 'john_doe' }, email: { type: 'string', example: 'john@example.com' },
            name: { type: 'string', example: 'John Doe' }, role: { type: 'string', example: 'crm_sales_executive',
              enum: ['crm_super_admin','crm_sales_manager','crm_sales_executive','crm_telecaller','crm_customer'] } } } } } },
        responses: { 200: { description: 'OTP sent to email' } }
      }
    },
    '/auth/register/verify': {
      post: {
        tags: ['Authentication'], summary: 'Verify OTP and complete registration',
        security: [],
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['email','otp'],
          properties: { email: { type: 'string' }, otp: { type: 'string', example: '123456' } } } } } },
        responses: { 201: { description: 'Registration completed' } }
      }
    },
    '/auth/login': {
      post: {
        tags: ['Authentication'], summary: 'Login with username & password (or OTP)',
        security: [],
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['username'],
          properties: { username: { type: 'string', example: 'csuperadmin' }, password: { type: 'string', example: '123456' },
            otp: { type: 'string', description: 'Use either password or otp' } } } } } },
        responses: { 200: { description: 'Login successful, returns JWT token', content: { 'application/json': { schema: {
          type: 'object', properties: { success: { type: 'boolean' }, data: { type: 'object',
            properties: { token: { type: 'string', description: 'JWT access token (24h)' },
              refresh_token: { type: 'string', description: 'Refresh token (7d)' },
              user: { type: 'object', properties: { id: { type: 'integer' }, username: { type: 'string' }, email: { type: 'string' }, role: { type: 'string' } } } } } } } } } } }
      }
    },
    '/auth/login/initiate': {
      post: {
        tags: ['Authentication'], summary: 'Initiate passwordless login (sends OTP)',
        security: [],
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['username_or_email'],
          properties: { username_or_email: { type: 'string' } } } } } },
        responses: { 200: { description: 'OTP sent' } }
      }
    },
    '/auth/logout': {
      post: { tags: ['Authentication'], summary: 'Logout (invalidates refresh token)', responses: { 200: { description: 'Logged out' } } }
    },
    '/auth/refresh-token': {
      post: {
        tags: ['Authentication'], summary: 'Refresh JWT token', security: [],
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['refresh_token'],
          properties: { refresh_token: { type: 'string' } } } } } },
        responses: { 200: { description: 'New tokens issued' } }
      }
    },
    '/auth/me': { get: { tags: ['Authentication'], summary: 'Get current user info', responses: { 200: { description: 'User details' } } } },
    '/auth/users': { get: { tags: ['Authentication'], summary: 'List all CRM users (admin only)', responses: { 200: { description: 'Users list' } } } },
    '/auth/users/status': {
      post: {
        tags: ['Authentication'], summary: 'Update user approval status',
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['user_id','status'],
          properties: { user_id: { type: 'integer' }, status: { type: 'string', enum: ['APPROVED','PENDING','BLOCKED','HOLD'] } } } } } },
        responses: { 200: { description: 'Status updated' } }
      }
    },
    '/auth/smtp': {
      get: { tags: ['Settings'], summary: 'Get SMTP settings', responses: { 200: { description: 'SMTP config' } } },
      post: {
        tags: ['Settings'], summary: 'Save SMTP settings',
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object',
          properties: { smtp_enabled: { type: 'string', enum: ['yes','no'] }, smtp_host: { type: 'string', example: 'smtp.gmail.com' },
            smtp_port: { type: 'string', example: '587' }, smtp_username: { type: 'string' }, smtp_password: { type: 'string' },
            smtp_encryption: { type: 'string', enum: ['tls','ssl','none'] }, smtp_from_email: { type: 'string' }, smtp_from_name: { type: 'string', example: 'CRM ERP' } } } } } },
        responses: { 200: { description: 'Settings saved' } }
      }
    },
    '/auth/smtp/test': {
      post: {
        tags: ['Settings'], summary: 'Send test email',
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['test_email'],
          properties: { test_email: { type: 'string', format: 'email' } } } } } },
        responses: { 200: { description: 'Test email sent' } }
      }
    },
    '/dashboard/stats': { get: { tags: ['Dashboard'], summary: 'Get dashboard KPI stats, funnel, recent follow-ups', responses: { 200: { description: 'Dashboard stats' } } } },
    '/dashboard/activity-logs': {
      get: { tags: ['Dashboard'], summary: 'Get system activity logs',
        parameters: [{ name: 'page', in: 'query', schema: { type: 'integer', default: 1 } }, { name: 'limit', in: 'query', schema: { type: 'integer', default: 50 } }],
        responses: { 200: { description: 'Activity logs' } } }
    },
    '/leads': {
      get: {
        tags: ['Leads'], summary: 'List leads (with filters, search, pagination)',
        parameters: [
          { name: 'page', in: 'query', schema: { type: 'integer', default: 1 } },
          { name: 'limit', in: 'query', schema: { type: 'integer', default: 10 } },
          { name: 'search', in: 'query', schema: { type: 'string' }, description: 'Search by name, email, mobile, company' },
          { name: 'lead_status', in: 'query', schema: { type: 'string' } },
          { name: 'lead_source', in: 'query', schema: { type: 'string' } },
          { name: 'assigned_to', in: 'query', schema: { type: 'integer' } },
          { name: 'sort', in: 'query', schema: { type: 'string', default: 'id' } },
          { name: 'order', in: 'query', schema: { type: 'string', enum: ['ASC','DESC'], default: 'ASC' } }
        ],
        responses: { 200: { description: 'Paginated leads list' } }
      },
      post: {
        tags: ['Leads'], summary: 'Create new lead',
        requestBody: { required: true, content: { 'application/json': { schema: {
          type: 'object', required: ['first_name','email','mobile'],
          properties: { first_name: { type: 'string', example: 'John' }, last_name: { type: 'string', example: 'Doe' },
            company_name: { type: 'string', example: 'Acme Corp' }, mobile: { type: 'string', example: '+919876543210' },
            email: { type: 'string', example: 'john@acme.com' }, website: { type: 'string' },
            lead_source: { type: 'string', default: 'Website' }, industry: { type: 'string', example: 'Technology' },
            city: { type: 'string', example: 'Mumbai' }, state: { type: 'string', example: 'Maharashtra' },
            assigned_to: { type: 'integer' }, lead_status: { type: 'string', default: 'New' }, remarks: { type: 'string' } } } } } },
        responses: { 201: { description: 'Lead created' } }
      }
    },
    '/leads/{id}': {
      get: { tags: ['Leads'], summary: 'Get single lead', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Lead details' } } },
      put: {
        tags: ['Leads'], summary: 'Update lead',
        parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }],
        requestBody: { required: true, content: { 'application/json': { schema: { '$ref': '#/components/schemas/Lead' } } } },
        responses: { 200: { description: 'Lead updated' } }
      },
      delete: { tags: ['Leads'], summary: 'Delete lead (soft delete)', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Lead deleted' } } }
    },
    '/followups': {
      get: { tags: ['Follow-Ups'], summary: 'List follow-ups',
        parameters: [
          { name: 'page', in: 'query', schema: { type: 'integer' } }, { name: 'limit', in: 'query', schema: { type: 'integer' } },
          { name: 'lead_id', in: 'query', schema: { type: 'integer' } },
          { name: 'status', in: 'query', schema: { type: 'string' } },
          { name: 'communication_type', in: 'query', schema: { type: 'string' } }
        ],
        responses: { 200: { description: 'Follow-ups list' } }
      },
      post: {
        tags: ['Follow-Ups'], summary: 'Create follow-up',
        requestBody: { required: true, content: { 'application/json': { schema: {
          type: 'object', required: ['lead_id','followup_date'],
          properties: { lead_id: { type: 'integer' }, followup_date: { type: 'string', format: 'date' },
            followup_time: { type: 'string', example: '14:30:00' },
            communication_type: { type: 'string', enum: ['Call','WhatsApp','Email','Meeting','SMS'], default: 'Call' },
            remarks: { type: 'string' }, next_followup_date: { type: 'string', format: 'date' },
            status: { type: 'string', enum: ['Pending','Completed','Cancelled'], default: 'Pending' } } } } } },
        responses: { 201: { description: 'Follow-up created' } }
      }
    },
    '/followups/{id}': {
      get: { tags: ['Follow-Ups'], summary: 'Get single follow-up', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Follow-up details' } } },
      put: { tags: ['Follow-Ups'], summary: 'Update follow-up', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { '$ref': '#/components/schemas/Followup' } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Follow-Ups'], summary: 'Delete follow-up', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/tasks': {
      get: { tags: ['Tasks'], summary: 'List tasks', parameters: [{ name: 'page', in: 'query', schema: { type: 'integer' } }, { name: 'limit', in: 'query', schema: { type: 'integer' } }, { name: 'status', in: 'query', schema: { type: 'string' } }, { name: 'priority', in: 'query', schema: { type: 'string' } }, { name: 'assigned_to', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Tasks list' } } },
      post: { tags: ['Tasks'], summary: 'Create task', requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['title','due_date'], properties: { title: { type: 'string' }, description: { type: 'string' }, due_date: { type: 'string', format: 'date' }, status: { type: 'string', default: 'Pending' }, priority: { type: 'string', default: 'Medium' }, assigned_to: { type: 'integer' }, lead_id: { type: 'integer' } } } } } }, responses: { 201: { description: 'Task created' } } }
    },
    '/tasks/{id}': {
      get: { tags: ['Tasks'], summary: 'Get task', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Task details' } } },
      put: { tags: ['Tasks'], summary: 'Update task', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { '$ref': '#/components/schemas/Task' } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Tasks'], summary: 'Delete task', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/quotations': {
      get: { tags: ['Quotations'], summary: 'List quotations', parameters: [{ name: 'page', in: 'query', schema: { type: 'integer' } }, { name: 'limit', in: 'query', schema: { type: 'integer' } }, { name: 'status', in: 'query', schema: { type: 'string' } }, { name: 'lead_id', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Quotations list' } } },
      post: {
        tags: ['Quotations'], summary: 'Create quotation',
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['lead_id','quotation_date','valid_until'],
          properties: { lead_id: { type: 'integer' }, quotation_date: { type: 'string', format: 'date' },
            valid_until: { type: 'string', format: 'date' }, subtotal: { type: 'number' }, discount: { type: 'number', default: 0 },
            tax_amount: { type: 'number', default: 0 }, status: { type: 'string', enum: ['Draft','Sent','Accepted','Rejected','Expired'], default: 'Draft' },
            items: { type: 'array', items: { type: 'object', properties: { name: { type: 'string' }, qty: { type: 'integer' }, price: { type: 'number' } } } } } } } } },
        responses: { 201: { description: 'Quotation created' } }
      }
    },
    '/quotations/{id}': {
      get: { tags: ['Quotations'], summary: 'Get quotation details', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Quotation with items' } } },
      put: { tags: ['Quotations'], summary: 'Update quotation / Customer approve-reject', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', properties: { status: { type: 'string' }, subtotal: { type: 'number' }, discount: { type: 'number' }, tax_amount: { type: 'number' }, items: { type: 'array', items: { type: 'object' } } } } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Quotations'], summary: 'Delete quotation', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/customers': {
      get: { tags: ['Customers'], summary: 'List customers', parameters: [{ name: 'page', in: 'query', schema: { type: 'integer' } }, { name: 'search', in: 'query', schema: { type: 'string' } }, { name: 'status', in: 'query', schema: { type: 'string' } }], responses: { 200: { description: 'Customers list' } } },
      post: { tags: ['Customers'], summary: 'Create customer', requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['company_name','email','mobile'], properties: { company_name: { type: 'string' }, contact_person: { type: 'string' }, mobile: { type: 'string' }, email: { type: 'string' }, gst_number: { type: 'string' }, address: { type: 'string' }, city: { type: 'string' }, state: { type: 'string' }, status: { type: 'string', default: 'Active' } } } } } }, responses: { 201: { description: 'Customer created' } } }
    },
    '/customers/{id}': {
      get: { tags: ['Customers'], summary: 'Get customer', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Customer details' } } },
      put: { tags: ['Customers'], summary: 'Update customer', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { type: 'object' } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Customers'], summary: 'Delete customer', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/deals': {
      get: { tags: ['Deals & Pipeline'], summary: 'List deals', parameters: [{ name: 'page', in: 'query', schema: { type: 'integer' } }, { name: 'deal_stage', in: 'query', schema: { type: 'string' } }, { name: 'lead_id', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Deals list' } } },
      post: { tags: ['Deals & Pipeline'], summary: 'Create deal', requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['lead_id','deal_value'], properties: { lead_id: { type: 'integer' }, customer_id: { type: 'integer' }, deal_value: { type: 'number', example: 50000 }, expected_close_date: { type: 'string', format: 'date' }, deal_stage: { type: 'string', default: 'Prospecting' }, probability: { type: 'integer', default: 10 }, assigned_to: { type: 'integer' } } } } } }, responses: { 201: { description: 'Deal created' } } }
    },
    '/deals/{id}': {
      get: { tags: ['Deals & Pipeline'], summary: 'Get deal', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deal details' } } },
      put: { tags: ['Deals & Pipeline'], summary: 'Update deal / Move pipeline stage', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { '$ref': '#/components/schemas/Deal' } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Deals & Pipeline'], summary: 'Delete deal', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/pipeline': { get: { tags: ['Deals & Pipeline'], summary: 'Get Kanban pipeline (deals grouped by stage)', responses: { 200: { description: 'Kanban data' } } } },
    '/pipeline/{id}': { put: { tags: ['Deals & Pipeline'], summary: 'Move deal to new stage (drag & drop)', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', properties: { deal_stage: { type: 'string' }, probability: { type: 'integer' } } } } } }, responses: { 200: { description: 'Stage updated' } } } },
    '/invoices': {
      get: { tags: ['Invoices'], summary: 'List invoices', parameters: [{ name: 'page', in: 'query', schema: { type: 'integer' } }, { name: 'status', in: 'query', schema: { type: 'string' } }, { name: 'customer_id', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Invoices list' } } },
      post: { tags: ['Invoices'], summary: 'Create invoice', requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['customer_id','invoice_date','due_date'], properties: { customer_id: { type: 'integer' }, deal_id: { type: 'integer' }, invoice_date: { type: 'string', format: 'date' }, due_date: { type: 'string', format: 'date' }, subtotal: { type: 'number' }, tax_amount: { type: 'number' }, status: { type: 'string', default: 'Unpaid' }, items: { type: 'array', items: { type: 'object' } } } } } } }, responses: { 201: { description: 'Invoice created' } } }
    },
    '/invoices/{id}': {
      get: { tags: ['Invoices'], summary: 'Get invoice', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Invoice details' } } },
      put: { tags: ['Invoices'], summary: 'Update invoice', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { type: 'object' } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Invoices'], summary: 'Delete invoice', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/payments': {
      get: { tags: ['Payments'], summary: 'List payments', parameters: [{ name: 'invoice_id', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Payments list' } } },
      post: { tags: ['Payments'], summary: 'Record payment', requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['invoice_id','amount','payment_date'], properties: { invoice_id: { type: 'integer' }, payment_date: { type: 'string', format: 'date' }, amount: { type: 'number' }, payment_mode: { type: 'string', default: 'Bank Transfer', enum: ['Bank Transfer','Cash','Cheque','UPI','Credit Card','Online'] }, transaction_reference: { type: 'string' } } } } } }, responses: { 201: { description: 'Payment recorded, invoice auto-reconciled' } } }
    },
    '/payments/{id}': {
      put: { tags: ['Payments'], summary: 'Update payment', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { type: 'object' } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Payments'], summary: 'Delete payment', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/call-logs': {
      get: { tags: ['Communications'], summary: 'List call logs', parameters: [{ name: 'lead_id', in: 'query', schema: { type: 'integer' } }, { name: 'page', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Call logs' } } },
      post: { tags: ['Communications'], summary: 'Log a call', requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['lead_id'], properties: { lead_id: { type: 'integer' }, call_date: { type: 'string', format: 'date-time' }, duration: { type: 'integer', description: 'Duration in seconds' }, notes: { type: 'string' }, recording_url: { type: 'string' } } } } } }, responses: { 201: { description: 'Call logged' } } }
    },
    '/call-logs/{id}': {
      put: { tags: ['Communications'], summary: 'Update call log', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { type: 'object' } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Communications'], summary: 'Delete call log', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/meetings': {
      get: { tags: ['Communications'], summary: 'List meetings', parameters: [{ name: 'lead_id', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Meetings list' } } },
      post: { tags: ['Communications'], summary: 'Schedule meeting', requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['lead_id','title','meeting_date'], properties: { lead_id: { type: 'integer' }, title: { type: 'string' }, meeting_date: { type: 'string', format: 'date' }, meeting_time: { type: 'string', example: '10:00:00' }, notes: { type: 'string' }, status: { type: 'string', default: 'Scheduled' } } } } } }, responses: { 201: { description: 'Meeting scheduled' } } }
    },
    '/meetings/{id}': {
      put: { tags: ['Communications'], summary: 'Update meeting', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], requestBody: { required: true, content: { 'application/json': { schema: { type: 'object' } } } }, responses: { 200: { description: 'Updated' } } },
      delete: { tags: ['Communications'], summary: 'Delete meeting', parameters: [{ name: 'id', in: 'path', required: true, schema: { type: 'integer' } }], responses: { 200: { description: 'Deleted' } } }
    },
    '/whatsapp/send': {
      post: {
        tags: ['Communications'], summary: 'Send WhatsApp message (logs + returns wa.me link)',
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['recipient_number','message'], properties: { lead_id: { type: 'integer' }, recipient_number: { type: 'string', example: '+919876543210' }, message: { type: 'string', example: 'Hello, following up on your inquiry.' } } } } } },
        responses: { 201: { description: 'WhatsApp message logged with wa.me deeplink' } }
      }
    },
    '/whatsapp/history': { get: { tags: ['Communications'], summary: 'WhatsApp message history', parameters: [{ name: 'lead_id', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'WhatsApp logs' } } } },
    '/email/send': {
      post: {
        tags: ['Communications'], summary: 'Send email via WordPress SMTP',
        requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['recipient_email','subject','message'], properties: { lead_id: { type: 'integer' }, recipient_email: { type: 'string', format: 'email' }, subject: { type: 'string' }, message: { type: 'string' } } } } } },
        responses: { 201: { description: 'Email sent and logged' } }
      }
    },
    '/email/history': { get: { tags: ['Communications'], summary: 'Email send history', parameters: [{ name: 'lead_id', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Email logs' } } } },
    '/documents': {
      get: { tags: ['Documents'], summary: 'List documents', parameters: [{ name: 'lead_id', in: 'query', schema: { type: 'integer' } }, { name: 'customer_id', in: 'query', schema: { type: 'integer' } }], responses: { 200: { description: 'Documents list' } } },
      post: { tags: ['Documents'], summary: 'Register document (by URL)', requestBody: { required: true, content: { 'application/json': { schema: { type: 'object', required: ['document_name','file_url'], properties: { lead_id: { type: 'integer' }, customer_id: { type: 'integer' }, document_name: { type: 'string' }, file_url: { type: 'string' } } } } } }, responses: { 201: { description: 'Document registered' } } }
    },
    '/media/upload': {
      post: {
        tags: ['Documents'], summary: 'Upload file to WordPress media library (multipart/form-data)',
        requestBody: { required: true, content: { 'multipart/form-data': { schema: { type: 'object', required: ['file'], properties: { file: { type: 'string', format: 'binary' }, lead_id: { type: 'integer' }, customer_id: { type: 'integer' }, document_name: { type: 'string' } } } } } },
        responses: { 201: { description: 'File uploaded, returns URL and attachment_id' } }
      }
    },
    '/reports/leads': { get: { tags: ['Reports'], summary: 'Leads report by status/source/daily trend', parameters: [{ name: 'date_from', in: 'query', schema: { type: 'string', format: 'date' } }, { name: 'date_to', in: 'query', schema: { type: 'string', format: 'date' } }], responses: { 200: { description: 'Lead report data' } } } },
    '/reports/followups': { get: { tags: ['Reports'], summary: 'Follow-up activity report', parameters: [{ name: 'date_from', in: 'query', schema: { type: 'string', format: 'date' } }, { name: 'date_to', in: 'query', schema: { type: 'string', format: 'date' } }], responses: { 200: { description: 'Followup report' } } } },
    '/reports/quotations': { get: { tags: ['Reports'], summary: 'Quotation conversion funnel', parameters: [{ name: 'date_from', in: 'query', schema: { type: 'string', format: 'date' } }, { name: 'date_to', in: 'query', schema: { type: 'string', format: 'date' } }], responses: { 200: { description: 'Quotation report' } } } },
    '/reports/deals': { get: { tags: ['Reports'], summary: 'Deals report by stage', parameters: [{ name: 'date_from', in: 'query', schema: { type: 'string', format: 'date' } }, { name: 'date_to', in: 'query', schema: { type: 'string', format: 'date' } }], responses: { 200: { description: 'Deals report' } } } },
    '/reports/pipeline': { get: { tags: ['Reports'], summary: 'Pipeline value report with weighted forecast', responses: { 200: { description: 'Pipeline analysis' } } } },
    '/reports/revenue': { get: { tags: ['Reports'], summary: 'Monthly revenue trends', parameters: [{ name: 'year', in: 'query', schema: { type: 'integer', example: 2026 } }], responses: { 200: { description: 'Revenue by month' } } } },
    '/reports/team-performance': { get: { tags: ['Reports'], summary: 'Team performance (leads, deals, revenue per user)', parameters: [{ name: 'date_from', in: 'query', schema: { type: 'string', format: 'date' } }, { name: 'date_to', in: 'query', schema: { type: 'string', format: 'date' } }], responses: { 200: { description: 'Team performance data' } } } },
    '/reports/lead-sources': { get: { tags: ['Reports'], summary: 'Lead source breakdown with percentages', responses: { 200: { description: 'Lead sources analysis' } } } },
    '/reports/conversion-rate': { get: { tags: ['Reports'], summary: 'Lead-to-deal conversion rate', parameters: [{ name: 'date_from', in: 'query', schema: { type: 'string', format: 'date' } }, { name: 'date_to', in: 'query', schema: { type: 'string', format: 'date' } }], responses: { 200: { description: 'Conversion metrics' } } } },
    '/reports/forecast': { get: { tags: ['Reports'], summary: 'Revenue forecast based on open deals & probability', responses: { 200: { description: 'Forecast data' } } } }
  }
};

window.onload = () => {
  SwaggerUIBundle({
    spec,
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
    layout: 'BaseLayout',
    tryItOutEnabled: true,
    requestInterceptor: (req) => {
      const token = localStorage.getItem('crm_swagger_token');
      if (token && !req.headers.Authorization) {
        req.headers.Authorization = 'Bearer ' + token;
      }
      return req;
    },
    responseInterceptor: (res) => {
      // Auto-save token on login
      try {
        const body = JSON.parse(res.text || '{}');
        if (body.data && body.data.token) {
          localStorage.setItem('crm_swagger_token', body.data.token);
        }
      } catch(e) {}
      return res;
    }
  });
};
</script>
</body>
</html>
