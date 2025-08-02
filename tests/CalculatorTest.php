<?php

use PHPUnit\Framework\TestCase;

require_once 'calculator.php'; // Include your calculator code

class CalculatorTest extends TestCase {
    public function testAdd() {
        $result = add(2, 3);
        $this->assertEquals(5, $result); // Check if 2 + 3 equals 5
    }
}
?>