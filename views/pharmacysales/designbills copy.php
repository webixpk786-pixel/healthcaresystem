<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_template'])) {
        $templateName = $_POST['template_name'];
        $templateContent = $_POST['template_content'];

        // Basic validation
        if (!empty($templateName) && !empty($templateContent)) {
            // Sanitize filename
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $templateName) . '.html';
            $filepath = 'templates/' . $filename;

            // Create templates directory if it doesn't exist
            if (!is_dir('templates')) {
                mkdir('templates', 0755, true);
            }

            // Save template
            if (file_put_contents($filepath, $templateContent)) {
                $successMessage = "Template '$templateName' saved successfully!";
            } else {
                $errorMessage = "Error saving template. Please try again.";
            }
        } else {
            $errorMessage = "Template name and content are required.";
        }
    }
}

// Load existing templates
$templates = [];
if (is_dir('templates')) {
    $files = scandir('templates');
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            $templates[] = $file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Receipt Template Designer</title>
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f5f7f9;
        color: #333;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    header {
        background: linear-gradient(135deg, #2c3e50, #4a6491);
        color: white;
        padding: 20px 0;
        text-align: center;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .description {
        font-size: 1.1rem;
        max-width: 800px;
        margin: 0 auto;
    }

    .main-content {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .designer-panel {
        flex: 1;
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .preview-panel {
        flex: 1;
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .panel-title {
        font-size: 1.5rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eaeaea;
        color: #2c3e50;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    textarea {
        min-height: 200px;
        font-family: monospace;
    }

    .toolbar {
        display: flex;
        gap: 10px;
        margin: 15px 0;
        flex-wrap: wrap;
    }

    .tool-btn {
        padding: 8px 12px;
        background: #2c3e50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .tool-btn:hover {
        background: #4a6491;
    }

    .btn-primary {
        background: #27ae60;
        padding: 12px 20px;
        font-size: 1rem;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: #2ecc71;
    }

    .preview {
        border: 1px dashed #ddd;
        padding: 20px;
        min-height: 400px;
        background: #f9f9f9;
    }

    .receipt {
        max-width: 400px;
        margin: 0 auto;
        background: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .receipt-details {
        margin-bottom: 20px;
    }

    .receipt-items {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .receipt-items th {
        background: #f2f2f2;
        padding: 8px;
        text-align: left;
    }

    .receipt-items td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
    }

    .receipt-total {
        text-align: right;
        font-weight: bold;
        font-size: 1.1rem;
        margin-top: 10px;
    }

    .templates-panel {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .template-card {
        background: #f9f9f9;
        border: 1px solid #eaeaea;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
        transition: transform 0.3s;
    }

    .template-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 900px) {
        .main-content {
            flex-direction: column;
        }
    }
    </style>
</head>

<body>
    <div class="container" style=" height: 80vh; overflow: auto;">
        <header>
            <h1>Bill Receipt Template Designer</h1>
            <p class="description">Design and save custom templates for your billing receipts. Use the editor to create
                your template and save it for later use.</p>
        </header>

        <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="main-content">
            <div class="designer-panel">
                <h2 class="panel-title">Template Designer</h2>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="template_name">Template Name:</label>
                        <input type="text" id="template_name" name="template_name" required
                            placeholder="Enter template name">
                    </div>

                    <div class="form-group">
                        <label for="template_content">HTML Template:</label>
                        <div class="toolbar">
                            <button type="button" class="tool-btn" onclick="insertTag('{{company_name}}')">Company
                                Name</button>
                            <button type="button" class="tool-btn" onclick="insertTag('{{customer_name}}')">Customer
                                Name</button>
                            <button type="button" class="tool-btn" onclick="insertTag('{{invoice_number}}')">Invoice
                                #</button>
                            <button type="button" class="tool-btn" onclick="insertTag('{{date}}')">Date</button>
                            <button type="button" class="tool-btn" onclick="insertTag('{{items}}')">Items Table</button>
                            <button type="button" class="tool-btn" onclick="insertTag('{{total}}')">Total</button>
                        </div>
                        <textarea id="template_content" name="template_content" required rows="15"
                            placeholder="Enter your HTML template here. Use the buttons above to insert placeholders."><?php echo isset($_POST['template_content']) ? htmlspecialchars($_POST['template_content']) : defaultTemplate(); ?></textarea>
                    </div>

                    <button type="submit" name="save_template" class="tool-btn btn-primary">Save Template</button>
                </form>
            </div>

            <div class="preview-panel">
                <h2 class="panel-title">Template Preview</h2>
                <div class="preview">
                    <?php
                    $templateContent = isset($_POST['template_content']) ? $_POST['template_content'] : defaultTemplate();
                    echo generatePreview($templateContent);
                    ?>
                </div>
            </div>
        </div>

        <div class="templates-panel">
            <h2 class="panel-title">Saved Templates</h2>
            <?php if (count($templates) > 0): ?>
            <div class="templates-grid">
                <?php foreach ($templates as $template): ?>
                <div class="template-card">
                    <h3><?php echo pathinfo($template, PATHINFO_FILENAME); ?></h3>
                    <p>Created: <?php echo date('Y-m-d', filemtime('templates/' . $template)); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No templates saved yet. Design and save your first template above.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function insertTag(tag) {
        const textarea = document.getElementById('template_content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const before = text.substring(0, start);
        const after = text.substring(end, text.length);

        textarea.value = before + tag + after;
        textarea.focus();
        textarea.selectionStart = start + tag.length;
        textarea.selectionEnd = start + tag.length;

        // Trigger input event to update preview
        const event = new Event('input', {
            bubbles: true
        });
        textarea.dispatchEvent(event);
    }

    // Live preview update
    document.getElementById('template_content').addEventListener('input', function() {
        // In a real application, you would use AJAX to update the preview
        // For simplicity, we'll just reload the page with the content as POST data
        // This is a simplified implementation for demonstration
    });
    </script>
</body>

</html>

<?php
function defaultTemplate()
{
    return <<<'EOD'
<div class="receipt">
    <div class="receipt-header">
        <h2>{{company_name}}</h2>
        <p>123 Business Street, City, State</p>
        <p>Phone: (123) 456-7890 | Email: info@company.com</p>
    </div>
    
    <div class="receipt-details">
        <p><strong>Invoice #:</strong> {{invoice_number}}</p>
        <p><strong>Date:</strong> {{date}}</p>
        <p><strong>Customer:</strong> {{customer_name}}</p>
    </div>
    
    <table class="receipt-items">
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            {{items}}
        </tbody>
    </table>
    
    <div class="receipt-total">
        <p>Total: {{total}}</p>
    </div>
    
    <div class="receipt-footer">
        <p>Thank you for your business!</p>
    </div>
</div>
EOD;
}

function generatePreview($content)
{
    // Replace placeholders with sample data for preview
    $preview = str_replace('{{company_name}}', 'Sample Company Inc.', $content);
    $preview = str_replace('{{invoice_number}}', 'INV-2023-001', $preview);
    $preview = str_replace('{{date}}', date('Y-m-d'), $preview);
    $preview = str_replace('{{customer_name}}', 'John Doe', $preview);
    $preview = str_replace('{{items}}', '
        <tr>
            <td>Product A</td>
            <td>2</td>
            <td>$10.00</td>
            <td>$20.00</td>
        </tr>
        <tr>
            <td>Product B</td>
            <td>1</td>
            <td>$15.00</td>
            <td>$15.00</td>
        </tr>
    ', $preview);
    $preview = str_replace('{{total}}', '$35.00', $preview);

    return $preview;
}
?>