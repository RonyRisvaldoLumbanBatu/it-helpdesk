<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ValidationHelperTest extends TestCase
{
    public function testValidateEmailRejectsEmpty(): void
    {
        $result = ValidationHelper::validateEmail('');

        $this->assertFalse($result['valid']);
        $this->assertSame('Email tidak boleh kosong', $result['error']);
    }

    public function testValidateEmailRejectsInvalidFormat(): void
    {
        $result = ValidationHelper::validateEmail('invalid-email');

        $this->assertFalse($result['valid']);
        $this->assertSame('Format email tidak valid', $result['error']);
    }

    public function testValidateEmailRejectsTooLong(): void
    {
        $localPart = str_repeat('a', 245);
        $email = $localPart . '@example.com';

        $result = ValidationHelper::validateEmail($email);

        $this->assertFalse($result['valid']);
        $this->assertSame('Format email tidak valid', $result['error']);
    }

    public function testValidateEmailAcceptsValidAddress(): void
    {
        $result = ValidationHelper::validateEmail('user@example.com');

        $this->assertTrue($result['valid']);
        $this->assertSame('', $result['error']);
    }

    public function testValidateUsernameHandlesCommonFailures(): void
    {
        $empty = ValidationHelper::validateUsername('');
        $tooShort = ValidationHelper::validateUsername('ab');
        $tooLong = ValidationHelper::validateUsername(str_repeat('a', 31));
        $invalidChars = ValidationHelper::validateUsername('user!');
        $nonLetterStart = ValidationHelper::validateUsername('1username');
        $withSpaces = ValidationHelper::validateUsername('user name');

        $this->assertSame('Username tidak boleh kosong', $empty['error']);
        $this->assertSame('Username minimal 3 karakter', $tooShort['error']);
        $this->assertSame('Username maksimal 30 karakter', $tooLong['error']);
        $this->assertSame('Username hanya boleh huruf, angka, underscore (_), dan dash (-)', $invalidChars['error']);
        $this->assertSame('Username harus diawali dengan huruf', $nonLetterStart['error']);
        $this->assertSame('Username hanya boleh huruf, angka, underscore (_), dan dash (-)', $withSpaces['error']);
    }

    public function testValidateUsernameAcceptsValidInput(): void
    {
        $result = ValidationHelper::validateUsername('User_name-123');

        $this->assertTrue($result['valid']);
        $this->assertSame('', $result['error']);
    }

    public function testValidatePasswordRejectsCommonIssues(): void
    {
        $empty = ValidationHelper::validatePassword('');
        $tooShort = ValidationHelper::validatePassword('abc123');
        $tooLong = ValidationHelper::validatePassword(str_repeat('a', 129) . '1');
        $noLetter = ValidationHelper::validatePassword('12345678');
        $noNumber = ValidationHelper::validatePassword('abcdefgh');

        $this->assertSame('Password tidak boleh kosong', $empty['error']);
        $this->assertSame('Password minimal 8 karakter', $tooShort['error']);
        $this->assertSame('Password maksimal 128 karakter', $tooLong['error']);
        $this->assertSame('Password harus mengandung minimal 1 huruf', $noLetter['error']);
        $this->assertSame('Password harus mengandung minimal 1 angka', $noNumber['error']);
    }

    public function testValidatePasswordAcceptsValidInput(): void
    {
        $result = ValidationHelper::validatePassword('Abcdef12');

        $this->assertTrue($result['valid']);
        $this->assertSame('', $result['error']);
    }

    public function testValidateNameRejectsInvalidValues(): void
    {
        $empty = ValidationHelper::validateName('');
        $tooShort = ValidationHelper::validateName('Al');
        $tooLong = ValidationHelper::validateName(str_repeat('a', 101));
        $invalid = ValidationHelper::validateName('John123');

        $this->assertSame('Nama tidak boleh kosong', $empty['error']);
        $this->assertSame('Nama minimal 3 karakter', $tooShort['error']);
        $this->assertSame('Nama maksimal 100 karakter', $tooLong['error']);
        $this->assertSame("Nama hanya boleh huruf, spasi, tanda petik ('), dash (-), dan titik (.)", $invalid['error']);
    }

    public function testValidateNameAcceptsValidValue(): void
    {
        $result = ValidationHelper::validateName('John Doe');

        $this->assertTrue($result['valid']);
        $this->assertSame('', $result['error']);
    }

    public function testValidateRoleRequiresAllowedValues(): void
    {
        $invalid = ValidationHelper::validateRole('manager');
        $valid = ValidationHelper::validateRole('staff');

        $this->assertFalse($invalid['valid']);
        $this->assertSame('Role tidak valid. Pilih: admin, staff, mahasiswa, user', $invalid['error']);
        $this->assertTrue($valid['valid']);
    }

    public function testSanitizeStringTrimsAndEscapes(): void
    {
        $dirty = "  <script>alert('x')</script>  ";

        $clean = ValidationHelper::sanitizeString($dirty);

        $this->assertSame('&lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt;', $clean);
    }

    public function testValidateLengthChecksBoundaries(): void
    {
        $tooShort = ValidationHelper::validateLength('ab', 3, 5, 'Kode');
        $tooLong = ValidationHelper::validateLength('abcdef', 3, 5, 'Kode');
        $valid = ValidationHelper::validateLength('abcd', 3, 5, 'Kode');

        $this->assertSame('Kode minimal 3 karakter', $tooShort['error']);
        $this->assertSame('Kode maksimal 5 karakter', $tooLong['error']);
        $this->assertTrue($valid['valid']);
    }

    public function testValidateMultipleAggregatesErrors(): void
    {
        $results = [
            'email' => ValidationHelper::validateEmail('invalid'),
            'username' => ValidationHelper::validateUsername('validName'),
            'password' => ValidationHelper::validatePassword('12345678')
        ];

        $aggregate = ValidationHelper::validateMultiple($results);

        $this->assertFalse($aggregate['valid']);
        $this->assertSame(['email' => 'Format email tidak valid', 'password' => 'Password harus mengandung minimal 1 huruf'], $aggregate['errors']);
    }

    public function testValidateMultipleReturnsValidWhenAllPass(): void
    {
        $results = [
            'email' => ValidationHelper::validateEmail('user@example.com'),
            'username' => ValidationHelper::validateUsername('ValidUser'),
            'password' => ValidationHelper::validatePassword('Abcdef12')
        ];

        $aggregate = ValidationHelper::validateMultiple($results);

        $this->assertTrue($aggregate['valid']);
        $this->assertSame([], $aggregate['errors']);
    }

    public function testFormatErrorsReturnsFormattedList(): void
    {
        $errors = [
            'email' => 'Format email tidak valid',
            'password' => 'Password kurang <strong>aman</strong>'
        ];

        $html = ValidationHelper::formatErrors($errors);

        $this->assertStringContainsString('<ul', $html);
        $this->assertStringContainsString('<li><strong>Email</strong>: Format email tidak valid</li>', $html);
        $this->assertStringContainsString('&lt;strong&gt;aman&lt;/strong&gt;', $html);
    }

    public function testFormatErrorsReturnsEmptyStringWhenNoErrors(): void
    {
        $this->assertSame('', ValidationHelper::formatErrors([]));
    }
}
