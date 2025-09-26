<?php
// save.php will handle saving the content
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['editor_content'];
    file_put_contents("saved_content.html", $content); // save to file
    echo "<h3>Content Saved Successfully ✅</h3>";
    echo "<a href='saved_content.html' target='_blank'>View Saved Page</a>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PHP WYSIWYG Editor</title>
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/ttj2kf6d1bua2pith435afoqp9jobwljejqain9hp2rsfymn/tinymce/8/tinymce.min.js"
        referrerpolicy="origin" crossorigin="anonymous"></script>

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: [
                // Core editing features
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media',
                'searchreplace', 'table', 'visualblocks', 'wordcount',
                // Your account includes a free trial of TinyMCE premium features
                // Try the most popular premium features until Oct 4, 2025:
                'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker',
                'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai',
                'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags',
                'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [{
                    value: 'First.Name',
                    title: 'First Name'
                },
                {
                    value: 'Email',
                    title: 'Email'
                },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject(
                'See docs to implement AI Assistant')),
            uploadcare_public_key: 'f87ca15b3ee4a638919b',
        });
    </script>
</head>

<body style="font-family: Arial, sans-serif; margin:20px;">

    <h2>PHP WYSIWYG Editor Example</h2>

    <form method="POST">

        <textarea id="editor" name="editor_content">
      <h1 style="text-align:center;">Invoice</h1>
      <p><strong>Company:</strong> Sample Company Inc.</p>
      <p><strong>Invoice #:</strong> INV-2025-001</p>
      <p><strong>Date:</strong> <?php echo date("Y-m-d"); ?></p>
      <p><strong>Customer:</strong> John Doe</p>
      <table border="1" cellspacing="0" cellpadding="5" width="100%">
        <tr>
          <th>Item</th><th>Qty</th><th>Price</th><th>Total</th>
        </tr>
        <tr>
          <td>Product A</td><td>2</td><td>$100</td><td>$200</td>
        </tr>
        <tr>
          <td>Product B</td><td>1</td><td>$150</td><td>$150</td>
        </tr>
      </table>
      <h3 style="text-align:right;">Grand Total: $350</h3>
    </textarea>
        <br>
        <button type="submit">Save Content</button>
    </form>

</body>

</html>