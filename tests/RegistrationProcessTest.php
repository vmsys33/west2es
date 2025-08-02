<?php
use PHPUnit\Framework\TestCase;

class RegistrationProcessTest extends TestCase
{
    private $url = 'http://localhost/west2es/functions/register_process.php'; // Adjust if needed
    private static $uniqueCounter = 10000;

    private function postRegistration($data)
    {
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($this->url, false, $context);
        return json_decode($result, true);
    }

    private function uniqueData($overrides = []) {
        self::$uniqueCounter++;
        // Use letters for uniqueness (A, B, C, ... Z, AA, AB, ...)
        $letters = $this->numberToLetters(self::$uniqueCounter);
        $base = [
            'last_name' => 'Test-' . $letters,
            'first_name' => 'User' . $letters,
            'middle_name' => 'Middle' . $letters,
            'deped_id_no' => (string)(7000000 + self::$uniqueCounter),
            'email_prefix' => 'testuserphpunit' . self::$uniqueCounter,
            'contact_no' => '0912345' . str_pad(self::$uniqueCounter % 10000, 4, '0', STR_PAD_LEFT),
            'password' => 'password123',
            'confirm_password' => 'password123',
        ];
        return array_merge($base, $overrides);
    }

    // Helper to convert a number to letters (A, B, ..., Z, AA, AB, ...)
    private function numberToLetters($num) {
        $letters = '';
        while ($num > 0) {
            $num--;
            $letters = chr(65 + ($num % 26)) . $letters;
            $num = intval($num / 26);
        }
        return $letters;
    }

    // Helper to print error message if test fails
    private function assertSuccess($response, $msg = '') {
        if ($response['status'] !== 'success') {
            $this->fail(($msg ? $msg . ' - ' : '') . 'Backend message: ' . ($response['message'] ?? 'No message'));
        }
        $this->assertEquals('success', $response['status']);
    }
    private function assertNotError($response, $msg = '') {
        if ($response['status'] === 'error') {
            $this->fail(($msg ? $msg . ' - ' : '') . 'Backend message: ' . ($response['message'] ?? 'No message'));
        }
        $this->assertNotEquals('error', $response['status']);
    }

    // --- DepEd ID No. tests ---
    public function testDepedIdNoTooShort() {
        $data = $this->uniqueData(['deped_id_no' => '123456']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
        $this->assertStringContainsString('7 digits', $response['message']);
    }
    public function testDepedIdNoTooLong() {
        $data = $this->uniqueData(['deped_id_no' => '12345678']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
        $this->assertStringContainsString('7 digits', $response['message']);
    }
    public function testDepedIdNoNonNumeric() {
        $data = $this->uniqueData(['deped_id_no' => 'abcdefg']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
        $this->assertStringContainsString('7 digits', $response['message']);
    }
    public function testDepedIdNoValid() {
        $data = $this->uniqueData();
        $response = $this->postRegistration($data);
        $this->assertSuccess($response, 'DepEd ID No. valid');
    }

    // --- Name validation tests ---
    public function testInvalidLastName() {
        $data = $this->uniqueData(['last_name' => 'Test123']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
        $this->assertStringContainsString('name', $response['message']);
    }
    public function testInvalidFirstName() {
        $data = $this->uniqueData(['first_name' => 'User@']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
        $this->assertStringContainsString('name', $response['message']);
    }
    public function testInvalidMiddleName() {
        $data = $this->uniqueData(['middle_name' => 'Middle!']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
        $this->assertStringContainsString('name', $response['message']);
    }
    public function testValidNames() {
        $data = $this->uniqueData(['last_name' => 'Test-Name', 'first_name' => 'User', 'middle_name' => 'Middle']);
        $response = $this->postRegistration($data);
        $this->assertNotError($response, 'Valid names');
    }

    // --- Email prefix validation ---
    public function testInvalidEmailPrefix() {
        $data = $this->uniqueData(['email_prefix' => 'invalid@prefix']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
    }
    public function testValidEmailPrefix() {
        $data = $this->uniqueData(['email_prefix' => 'valid.prefix']);
        $response = $this->postRegistration($data);
        $this->assertNotError($response, 'Valid email prefix');
    }

    // --- Contact number validation ---
    public function testInvalidContactNoShort() {
        $data = $this->uniqueData(['contact_no' => '12345']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
    }
    public function testInvalidContactNoLong() {
        $data = $this->uniqueData(['contact_no' => '1234567890123456']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
    }
    public function testInvalidContactNoNonNumeric() {
        $data = $this->uniqueData(['contact_no' => '12345abcde']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
    }
    public function testValidContactNo() {
        $data = $this->uniqueData(['contact_no' => '09123456789']);
        $response = $this->postRegistration($data);
        $this->assertNotError($response, 'Valid contact no');
    }

    // --- Password validation ---
    public function testPasswordTooShort() {
        $data = $this->uniqueData(['password' => 'short', 'confirm_password' => 'short']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
        $this->assertStringContainsString('8 characters', $response['message']);
    }
    public function testPasswordMismatch() {
        $data = $this->uniqueData(['password' => 'password123', 'confirm_password' => 'password321']);
        $response = $this->postRegistration($data);
        $this->assertEquals('error', $response['status']);
        $this->assertStringContainsString('Passwords do not match', $response['message']);
    }
    public function testValidPassword() {
        $data = $this->uniqueData(['password' => 'password123', 'confirm_password' => 'password123']);
        $response = $this->postRegistration($data);
        $this->assertNotError($response, 'Valid password');
    }
} 