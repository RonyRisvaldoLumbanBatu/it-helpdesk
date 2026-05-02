<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use ValidationHelper;

/**
 * Smoke test untuk ValidationHelper.
 *
 * Memastikan aturan validasi inti tetap stabil:
 *  - email, username, password, nama, role.
 *  - sanitisasi string (anti-XSS dasar).
 */
final class ValidationHelperTest extends TestCase
{
    public function testEmailValid(): void
    {
        $result = \ValidationHelper::validateEmail('user@example.com');
        $this->assertTrue($result['valid']);
        $this->assertSame('', $result['error']);
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testEmailInvalid(string $email): void
    {
        $result = \ValidationHelper::validateEmail($email);
        $this->assertFalse($result['valid']);
        $this->assertNotSame('', $result['error']);
    }

    public static function invalidEmailProvider(): array
    {
        return [
            'kosong' => [''],
            'tanpa @' => ['userexample.com'],
            'tanpa domain' => ['user@'],
            'tanpa local part' => ['@example.com'],
            'spasi' => ['user @example.com'],
        ];
    }

    public function testUsernameValid(): void
    {
        $result = \ValidationHelper::validateUsername('rony_dev');
        $this->assertTrue($result['valid']);
    }

    /**
     * @dataProvider invalidUsernameProvider
     */
    public function testUsernameInvalid(string $username): void
    {
        $result = \ValidationHelper::validateUsername($username);
        $this->assertFalse($result['valid']);
    }

    public static function invalidUsernameProvider(): array
    {
        return [
            'kosong' => [''],
            'terlalu pendek' => ['ab'],
            'mulai angka' => ['1user'],
            'karakter spesial' => ['user!@#'],
            'mengandung spasi' => ['user name'],
        ];
    }

    public function testPasswordValid(): void
    {
        $result = \ValidationHelper::validatePassword('rahasia123');
        $this->assertTrue($result['valid']);
    }

    /**
     * @dataProvider invalidPasswordProvider
     */
    public function testPasswordInvalid(string $password): void
    {
        $result = \ValidationHelper::validatePassword($password);
        $this->assertFalse($result['valid']);
    }

    public static function invalidPasswordProvider(): array
    {
        return [
            'kosong' => [''],
            'kurang dari 8' => ['abc12'],
            'tanpa huruf' => ['12345678'],
            'tanpa angka' => ['abcdefgh'],
        ];
    }

    public function testRoleValid(): void
    {
        foreach (['admin', 'staff', 'mahasiswa', 'user'] as $role) {
            $result = \ValidationHelper::validateRole($role);
            $this->assertTrue($result['valid'], "Role {$role} seharusnya valid");
        }
    }

    public function testRoleInvalid(): void
    {
        $result = \ValidationHelper::validateRole('superuser');
        $this->assertFalse($result['valid']);
    }

    public function testSanitizeStringMengamankanXss(): void
    {
        $payload = '<script>alert("xss")</script>';
        $sanitized = \ValidationHelper::sanitizeString($payload);
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringContainsString('&lt;script&gt;', $sanitized);
    }

    public function testValidateMultipleMengumpulkanError(): void
    {
        $hasil = \ValidationHelper::validateMultiple([
            'email' => \ValidationHelper::validateEmail('bukan-email'),
            'username' => \ValidationHelper::validateUsername('rony_dev'),
            'password' => \ValidationHelper::validatePassword('short'),
        ]);

        $this->assertFalse($hasil['valid']);
        $this->assertArrayHasKey('email', $hasil['errors']);
        $this->assertArrayHasKey('password', $hasil['errors']);
        $this->assertArrayNotHasKey('username', $hasil['errors']);
    }

    public function testValidateLength(): void
    {
        $this->assertTrue(\ValidationHelper::validateLength('halo dunia', 3, 50, 'Pesan')['valid']);
        $this->assertFalse(\ValidationHelper::validateLength('ab', 3, 50, 'Pesan')['valid']);
        $this->assertFalse(\ValidationHelper::validateLength(str_repeat('a', 60), 3, 50, 'Pesan')['valid']);
    }
}
