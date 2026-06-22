<?php
// debug_upload.php
// Jalankan di browser untuk diagnosa kenapa upload ke Supabase Storage gagal.
// HAPUS file ini setelah selesai debug.

require_once __DIR__ . '/includes/config.php';

echo "<h2>🔍 Diagnosa Supabase Storage</h2>";
echo "<pre>";

echo "SUPABASE_URL    : " . SUPABASE_URL . "\n";
echo "SUPABASE_BUCKET : " . SUPABASE_BUCKET . "\n";
echo "SUPABASE_KEY    : " . substr(SUPABASE_KEY, 0, 20) . "... (panjang: " . strlen(SUPABASE_KEY) . " char)\n\n";

// Test 1: Cek apakah bucket bisa diakses (list isi bucket)
echo "=== TEST 1: Cek akses ke bucket (list files) ===\n";
$urlList = SUPABASE_URL . '/storage/v1/object/list/' . SUPABASE_BUCKET;
$ch = curl_init($urlList);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['limit' => 10]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . SUPABASE_KEY,
    'apikey: ' . SUPABASE_KEY,
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // hanya untuk debug
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Curl Error: " . ($curlError ?: '(tidak ada)') . "\n";
echo "Response: $response\n\n";

// Test 2: Cek curl & SSL support
echo "=== TEST 2: Info PHP/Curl ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Curl version: " . curl_version()['version'] . "\n";
echo "SSL version: " . curl_version()['ssl_version'] . "\n";
echo "fileinfo extension loaded: " . (extension_loaded('fileinfo') ? 'YA' : 'TIDAK') . "\n\n";

// Test 3: Simulasi upload file kecil (text file dummy)
echo "=== TEST 3: Coba upload file dummy (test.txt) ===\n";
$dummyContent = "test upload " . date('Y-m-d H:i:s');
$urlUpload = SUPABASE_URL . '/storage/v1/object/' . SUPABASE_BUCKET . '/test_debug.txt';
$ch2 = curl_init($urlUpload);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch2, CURLOPT_POSTFIELDS, $dummyContent);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . SUPABASE_KEY,
    'apikey: ' . SUPABASE_KEY,
    'Content-Type: text/plain',
    'x-upsert: true',
]);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false); // hanya untuk debug
$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$curlError2 = curl_error($ch2);
curl_close($ch2);

echo "HTTP Code: $httpCode2\n";
echo "Curl Error: " . ($curlError2 ?: '(tidak ada)') . "\n";
echo "Response: $response2\n";

echo "</pre>";

if ($httpCode2 >= 200 && $httpCode2 < 300) {
    echo "<h3 style='color:green'>✅ Upload test BERHASIL! Storage berfungsi normal.</h3>";
    echo "<p>Coba upload produk lagi sekarang.</p>";
} else {
    echo "<h3 style='color:red'>❌ Upload test GAGAL.</h3>";
    echo "<p>Lihat response di atas untuk detail error dari Supabase.</p>";
}
