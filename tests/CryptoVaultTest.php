<?php

use PHPUnit\Framework\TestCase;

class CryptoVaultTest extends TestCase
{

    private string $key;

    protected function setUp(): void
    {
        // Same key used in your .env
        $this->key = "0123456789abcdef0123456789abcdef";
    }

    /**
     * Test 1
     * Valid AES-256-GCM Encryption & Decryption
     */
    public function testValidEncryptionLifecycle()
    {
        $plaintext = "Patient: Ali\nDiagnosis: Diabetes";

        $iv = random_bytes(12);
        $tag = "";

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        $decrypted = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     * Test 2
     * Tampered Ciphertext Detection
     */
    public function testTamperedCiphertext()
    {
        $plaintext = "Patient: Ali";

        $iv = random_bytes(12);
        $tag = "";

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        // Modify ciphertext
        $ciphertext[0] = chr(ord($ciphertext[0]) ^ 1);

        $decrypted = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        $this->assertFalse($decrypted);
    }

    /**
     * Test 3
     * Argon2id Password Verification
     */
    public function testArgon2idVerification()
    {
        $password = "test";

        $hash = password_hash($password, PASSWORD_ARGON2ID);

        $this->assertTrue(
            password_verify($password, $hash)
        );

        $this->assertFalse(
            password_verify("wrongpassword", $hash)
        );
    }

}