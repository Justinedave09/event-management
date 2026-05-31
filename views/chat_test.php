<?php
require_once '../library/ai_config.php';

echo "<pre>";
echo "GROQ_API_KEY defined : " . (defined('GROQ_API_KEY')  ? 'YES' : 'NO') . "\n";
echo "GROQ_MODEL defined   : " . (defined('GROQ_MODEL')    ? 'YES' : 'NO') . "\n";
echo "GROQ_ENDPOINT defined: " . (defined('GROQ_ENDPOINT') ? 'YES' : 'NO') . "\n";

if (defined('GROQ_API_KEY')) {
    $key = GROQ_API_KEY;
    echo "Key starts with     : " . substr($key, 0, 8) . "...\n";
    echo "Key length          : " . strlen($key) . " chars\n";
    echo "Is placeholder      : " . ($key === 'gsk_PASTE_YOUR_REAL_KEY_HERE' ? 'YES (replace it!)' : 'NO ✓') . "\n";
}
echo "</pre>";