<?php
// crypto_vault.php - Secure Patient Medical Records Symmetric Protection

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crypto Vault Test</title>
</head>
<body>

<form method="POST">
    <textarea name="payload" rows="6" cols="50"
        placeholder="Enter medical record..."></textarea>
    <br><br>
    <button type="submit">Encrypt</button>
</form>

</body>
</html>

<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // Secure Improvement 1: Retrieve medical payload safely
        $medical_payload = $_POST['payload'] ?? '';

        if ($medical_payload === '') {
            throw new Exception("Empty payload.");
        }

        // Secure Improvement 2: Load encryption key from .env
        $secret_key = $_ENV['VAULT_KEY'] ?? '';

        if (strlen($secret_key) !== 32) {
            throw new Exception("Invalid encryption key.");
        }

        // Secure Improvement 3: Generate random 12-byte IV
        $iv = random_bytes(12);

        // Authentication tag
        $tag = '';

        // Secure Improvement 4: Encrypt using AES-256-GCM
        $ciphertext = openssl_encrypt(
            $medical_payload,
            'aes-256-gcm',
            $secret_key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($ciphertext === false) {
            throw new Exception("Encryption failed.");
        }

        // Secure Improvement 5: Serialize payload
        $vault_payload = [
            'iv' => base64_encode($iv),
            'ciphertext' => base64_encode($ciphertext),
            'tag' => base64_encode($tag)
        ];

        echo json_encode([
            'status' => 'vaulted',
            'data' => $vault_payload
        ], JSON_PRETTY_PRINT);

    } catch (Exception $e) {

        http_response_code(500);

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ], JSON_PRETTY_PRINT);

    }

}
?>