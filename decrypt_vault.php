<?php
// decrypt_vault.php - Secure AES-256-GCM Decryption

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

?>

<form method="POST">
    <p>IV</p>
    <input type="text" name="iv" size="70"><br><br>

    <p>Ciphertext</p>
    <textarea name="ciphertext" rows="4" cols="80"></textarea><br><br>

    <p>Authentication Tag</p>
    <input type="text" name="tag" size="70"><br><br>

    <button type="submit">Decrypt</button>
</form>

<hr>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $secret_key = $_ENV['VAULT_KEY'] ?? '';

        if (strlen($secret_key) !== 32) {
            throw new Exception("Invalid encryption key.");
        }

        // Decode Base64 values
        $iv = base64_decode($_POST['iv']);
        $ciphertext = base64_decode($_POST['ciphertext']);
        $tag = base64_decode($_POST['tag']);

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $secret_key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new Exception("Authentication Tag Verification Failed.");
        }

        echo "<h3>Decrypted Medical Record</h3>";
        echo "<pre>" . htmlspecialchars($plaintext) . "</pre>";

    } catch (Exception $e) {

        http_response_code(400);

        echo "<h3>Secure Runtime Failure</h3>";
        echo htmlspecialchars($e->getMessage());
    }
}
?>