
<?php
// auth.php - Secure Staff Key Authentication System
?>

<!DOCTYPE html>
<html>
<head>
    <title>Authentication Test</title>
</head>
<body>

<form method="POST">
    <label>Authentication Key:</label><br><br>

    <input type="text" name="auth_key" size="40">

    <br><br>

    <button type="submit">Login</button>
</form>

<hr>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Secure Improvement 1: Retrieve authentication key safely
    $inputKey = trim($_POST['auth_key'] ?? '');

    // Secure Improvement 2: Character-based boundary validation
    if (mb_strlen($inputKey, 'UTF-8') > 256) {
        die("Fatal Error: Input exceeds the maximum allowed length.");
    }

    // Secure Improvement 3: Store an Argon2id hash instead of an MD5 hash
    // Example hash generated using: password_hash("test", PASSWORD_ARGON2ID)
    $stored_hash = '$argon2id$v=19$m=65536,t=4,p=1$QVQ1MTlnNTN6b2VBU1dxRA$usxwEn/U6TNk5sl85ZCdptex1IOIk/KCp0dRN3sN8PY';

    // Secure Improvement 4: Verify the authentication key securely
    if (password_verify($inputKey, $stored_hash)) {
        echo "Access Granted.";
    } else {
        echo "Access Denied.";
    }
}
?>