<?php
/**
 * Test JavaScript Rename Button
 * Simple test to verify the rename button functionality
 */

echo "🔍 Test JavaScript Rename Button\n";
echo "================================\n\n";

echo "🧪 Testing JavaScript Rename Button Functionality...\n\n";

echo "📋 The rename button should work if:\n";
echo "1. ✅ jQuery is loaded\n";
echo "2. ✅ SweetAlert2 is loaded\n";
echo "3. ✅ Event delegation is working\n";
echo "4. ✅ Data attributes are set correctly\n";
echo "5. ✅ AJAX requests are working\n\n";

echo "🔍 Checking for potential issues:\n";
echo "- Make sure jQuery is loaded before the rename script\n";
echo "- Check that SweetAlert2 is available\n";
echo "- Verify that the .rename-revision class is being used\n";
echo "- Ensure data attributes (data-id, data-filename, data-table1, data-table2) are set\n";
echo "- Check browser console for JavaScript errors\n\n";

echo "📝 JavaScript code for rename button:\n";
echo "```javascript\n";
echo "\$(document).on('click', '.rename-revision', function() {\n";
echo "    const revisionId = \$(this).data('id');\n";
echo "    const currentFilename = \$(this).data('filename');\n";
echo "    const table1 = \$(this).data('table1');\n";
echo "    const table2 = \$(this).data('table2');\n";
echo "    // ... rest of the code\n";
echo "});\n";
echo "```\n\n";

echo "📝 HTML structure for rename button:\n";
echo "```html\n";
echo "<button class=\"btn btn-info btn-sm rename-revision\" \n";
echo "        data-id=\"\${revision.id}\" \n";
echo "        data-filename=\"\${revision.filename}\" \n";
echo "        data-table1=\"\${table1}\" \n";
echo "        data-table2=\"\${table2}\" \n";
echo "        title=\"Rename this revision file\">\n";
echo "    <i class=\"fas fa-tag\"></i> Rename\n";
echo "</button>\n";
echo "```\n\n";

echo "🎯 Troubleshooting Steps:\n";
echo "1. Open browser developer tools (F12)\n";
echo "2. Go to the Console tab\n";
echo "3. Click the rename button\n";
echo "4. Check for any JavaScript errors\n";
echo "5. Look for console.log messages from the rename function\n\n";

echo "✅ If the button is not clickable, check:\n";
echo "- JavaScript errors in console\n";
echo "- Missing jQuery or SweetAlert2 libraries\n";
echo "- Incorrect data attributes\n";
echo "- CSS conflicts preventing clicks\n";
echo "- Event delegation issues\n\n";

echo "🎉 Test completed! Check the browser console for detailed information.\n";
?> 