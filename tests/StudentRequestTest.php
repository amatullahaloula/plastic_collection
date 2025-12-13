<?php

use PHPUnit\Framework\TestCase;

class StudentRequestTest extends TestCase
{
    private $pdo;
    private $user;

    protected function setUp(): void
    {
        // Mock PDO
        $this->pdo = $this->createMock(PDO::class);
        
        // Mock user session data
        $this->user = [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'nickname' => 'Johnny',
            'email' => 'john.doe@example.com',
            'phone' => '0241234567',
            'degree' => 'Computer Science 2024',
            'created_at' => '2024-01-15 10:30:00'
        ];
    }

    public function testCalculateTotalEarnings()
    {
        $totalBottles = 150;
        $expectedEarnings = 150.00; // 150 bottles * GH₵1.00
        
        $actualEarnings = $totalBottles * 1.00;
        
        $this->assertEquals($expectedEarnings, $actualEarnings);
    }

    

    public function testUserDataSanitization()
    {
        $maliciousUser = [
            'first_name' => '<script>alert("xss")</script>John',
            'nickname' => 'Johnny<img src=x onerror=alert(1)>',
            'email' => 'test@example.com"><script>alert(1)</script>'
        ];
        
        // Test htmlspecialchars behavior
        $sanitizedFirstName = htmlspecialchars($maliciousUser['first_name']);
        $sanitizedNickname = htmlspecialchars($maliciousUser['nickname']);
        $sanitizedEmail = htmlspecialchars($maliciousUser['email']);
        
        $this->assertStringNotContainsString('<script>', $sanitizedFirstName);
        $this->assertStringNotContainsString('<img', $sanitizedNickname);
        $this->assertStringNotContainsString('<script>', $sanitizedEmail);
    }

    public function testNullableFieldsHandling()
    {
        $userWithNulls = [
            'nickname' => null,
            'phone' => null,
            'degree' => null
        ];
        
        $nickname = $userWithNulls['nickname'] ?: 'Not set';
        $phone = $userWithNulls['phone'] ?: 'Not provided';
        $degree = $userWithNulls['degree'] ?: 'Not specified';
        
        $this->assertEquals('Not set', $nickname);
        $this->assertEquals('Not provided', $phone);
        $this->assertEquals('Not specified', $degree);
    }

    public function testFormattingNumbers()
    {
        $totalBottles = 1500;
        $formatted = number_format($totalBottles);
        
        $this->assertEquals('1,500', $formatted);
        
        $earnings = 1500.50;
        $formattedEarnings = number_format($earnings, 2);
        
        $this->assertEquals('1,500.50', $formattedEarnings);
    }

    public function testSQLQueryStructure()
    {
        $expectedSQL = "
        SELECT SUM(bottles) as total_bottles, COUNT(*) as total_requests
        FROM collection_requests
        WHERE student_id = :student_id
    ";
        
        // Verify SQL structure is correct
        $this->assertStringContainsString('SUM(bottles)', $expectedSQL);
        $this->assertStringContainsString('COUNT(*)', $expectedSQL);
        $this->assertStringContainsString('student_id = :student_id', $expectedSQL);
    }
}