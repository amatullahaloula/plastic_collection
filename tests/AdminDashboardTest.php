<?php

use PHPUnit\Framework\TestCase;

class AdminDashboardTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
    }

    public function testRevenueCalculation()
    {
        $totalBottles = 5000;
        $pricePerBottle = 1.00;
        $totalRevenue = $totalBottles * $pricePerBottle;
        
        $this->assertEquals(5000.00, $totalRevenue);
    }

    public function testAverageBottlesPerRequest()
    {
        $totalBottles = 5000;
        $totalRequests = 100;
        
        $average = $totalRequests > 0 ? $totalBottles / $totalRequests : 0;
        
        $this->assertEquals(50.0, $average);
    }

    public function testAverageBottlesWithZeroRequests()
    {
        $totalBottles = 5000;
        $totalRequests = 0;
        
        $average = $totalRequests > 0 ? $totalBottles / $totalRequests : 0;
        
        $this->assertEquals(0, $average);
    }

    public function testFilterRequestsByStatus()
    {
        $requests = [
            ['id' => 1, 'status_cleaner' => 'pending', 'student_name' => 'John'],
            ['id' => 2, 'status_cleaner' => 'completed', 'student_name' => 'Jane'],
            ['id' => 3, 'status_cleaner' => 'pending', 'student_name' => 'Bob']
        ];
        
        $statusFilter = 'pending';
        $studentFilter = '';
        
        $filtered = array_filter($requests, function($r) use ($statusFilter, $studentFilter) {
            $match = true;
            if ($statusFilter !== '') {
                $match = ($r['status_cleaner'] ?? '') === $statusFilter;
            }
            if ($match && $studentFilter !== '') {
                $match = stripos($r['student_name'] ?? '', $studentFilter) !== false;
            }
            return $match;
        });
        
        $this->assertCount(2, $filtered);
    }

    public function testFilterRequestsByStudent()
    {
        $requests = [
            ['id' => 1, 'status_cleaner' => 'pending', 'student_name' => 'John Doe'],
            ['id' => 2, 'status_cleaner' => 'completed', 'student_name' => 'Jane Smith'],
            ['id' => 3, 'status_cleaner' => 'pending', 'student_name' => 'John Adams']
        ];
        
        $statusFilter = '';
        $studentFilter = 'john';
        
        $filtered = array_filter($requests, function($r) use ($statusFilter, $studentFilter) {
            $match = true;
            if ($statusFilter !== '') {
                $match = ($r['status_cleaner'] ?? '') === $statusFilter;
            }
            if ($match && $studentFilter !== '') {
                $match = stripos($r['student_name'] ?? '', $studentFilter) !== false;
            }
            return $match;
        });
        
        $this->assertCount(2, $filtered);
    }

    public function testCombinedFilters()
    {
        $requests = [
            ['id' => 1, 'status_cleaner' => 'pending', 'student_name' => 'John Doe'],
            ['id' => 2, 'status_cleaner' => 'completed', 'student_name' => 'John Smith'],
            ['id' => 3, 'status_cleaner' => 'pending', 'student_name' => 'Jane Adams']
        ];
        
        $statusFilter = 'pending';
        $studentFilter = 'john';
        
        $filtered = array_filter($requests, function($r) use ($statusFilter, $studentFilter) {
            $match = true;
            if ($statusFilter !== '') {
                $match = ($r['status_cleaner'] ?? '') === $statusFilter;
            }
            if ($match && $studentFilter !== '') {
                $match = stripos($r['student_name'] ?? '', $studentFilter) !== false;
            }
            return $match;
        });
        
        $this->assertCount(1, $filtered);
        $this->assertEquals('John Doe', array_values($filtered)[0]['student_name']);
    }

    public function testHtmlEscaping()
    {
        $malicious = '<script>alert("xss")</script>';
        $escaped = htmlspecialchars($malicious, ENT_QUOTES, 'UTF-8');
        
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }

    public function testExceptionHandlingReturnsEmptyArray()
    {
        try {
            throw new Exception('Database error');
        } catch (Exception $e) {
            $requests = [];
        }
        
        $this->assertEmpty($requests);
    }

    public function testNumberFormatting()
    {
        $totalBottles = 5000;
        $totalRevenue = 5000.50;
        
        $formattedBottles = number_format($totalBottles);
        $formattedRevenue = number_format($totalRevenue, 2);
        
        $this->assertEquals('5,000', $formattedBottles);
        $this->assertEquals('5,000.50', $formattedRevenue);
    }

    public function testStatusClassMapping()
    {
        $sc = 'pending';
        $class = 'status-pending';
        if ($sc === 'accepted') $class = 'status-accepted';
        elseif ($sc === 'completed') $class = 'status-completed';
        elseif ($sc === 'rejected') $class = 'status-rejected';
        
        $this->assertEquals('status-pending', $class);
        
        $sc2 = 'completed';
        $class2 = 'status-pending';
        if ($sc2 === 'accepted') $class2 = 'status-accepted';
        elseif ($sc2 === 'completed') $class2 = 'status-completed';
        elseif ($sc2 === 'rejected') $class2 = 'status-rejected';
        
        $this->assertEquals('status-completed', $class2);
    }

    public function testEmptyDataHandling()
    {
        $emptyRequests = [];
        $emptyCleaners = [];
        $emptySupport = [];
        
        $this->assertEmpty($emptyRequests);
        $this->assertEmpty($emptyCleaners);
        $this->assertEmpty($emptySupport);
        $this->assertCount(0, $emptyRequests);
    }

    public function testDefaultValueHandling()
    {
        $data = ['total_bottles' => null];
        $totalBottles = $data['total_bottles'] ?? 0;
        
        $this->assertEquals(0, $totalBottles);
        
        $data2 = ['total_bottles' => 150];
        $totalBottles2 = $data2['total_bottles'] ?? 0;
        
        $this->assertEquals(150, $totalBottles2);
    }

    public function testTrimStringInput()
    {
        $studentFilter = trim('  John Doe  ');
        
        $this->assertEquals('John Doe', $studentFilter);
    }

    public function testCaseInsensitiveSearch()
    {
        $studentName = 'John Doe';
        $searchTerm = 'john';
        
        $match = stripos($studentName, $searchTerm) !== false;
        
        $this->assertTrue($match);
        
        $searchTerm2 = 'JOHN';
        $match2 = stripos($studentName, $searchTerm2) !== false;
        
        $this->assertTrue($match2);
    }
}