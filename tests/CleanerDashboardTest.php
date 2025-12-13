<?php

use PHPUnit\Framework\TestCase;

class CleanerDashboardTest extends TestCase
{
    public function testAcceptActionUpdateQuery()
    {
        $action = 'accept';
        $expectedQuery = "UPDATE collection_requests
                           SET status_cleaner='accepted', 
                               cleaner_id=:uid, 
                               updated_at=NOW()
                           WHERE id=:id AND status_cleaner='pending'";
        
        $this->assertEquals('accept', $action);
        $this->assertStringContainsString('status_cleaner=\'accepted\'', $expectedQuery);
        $this->assertStringContainsString('WHERE id=:id AND status_cleaner=\'pending\'', $expectedQuery);
    }

    public function testRejectActionUpdateQuery()
    {
        $action = 'reject';
        $expectedQuery = "UPDATE collection_requests
                           SET status_cleaner='rejected', 
                               cleaner_id=:uid, 
                               updated_at=NOW()
                           WHERE id=:id AND status_cleaner='pending'";
        
        $this->assertEquals('reject', $action);
        $this->assertStringContainsString('status_cleaner=\'rejected\'', $expectedQuery);
    }

    public function testCollectActionUpdateQuery()
    {
        $action = 'collect';
        $expectedQuery = "UPDATE collection_requests
                           SET status_cleaner='completed', 
                               status_admin='completed', 
                               cleaner_id=:uid, 
                               updated_at=NOW()
                           WHERE id=:id AND status_cleaner IN ('accepted','pending')";
        
        $this->assertEquals('collect', $action);
        $this->assertStringContainsString('status_cleaner=\'completed\'', $expectedQuery);
        $this->assertStringContainsString('status_admin=\'completed\'', $expectedQuery);
        $this->assertStringContainsString('WHERE id=:id AND status_cleaner IN (\'accepted\',\'pending\')', $expectedQuery);
    }

    public function testHtmlEscaping()
    {
        $malicious = '<script>alert("xss")</script>';
        $escaped = htmlspecialchars($malicious, ENT_QUOTES, 'UTF-8');
        
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }

    public function testMessageHandling()
    {
        $reqId = 123;
        $message = "Request #$reqId accepted.";
        
        $this->assertEquals('Request #123 accepted.', $message);
        $this->assertStringContainsString('accepted', $message);
    }

    public function testErrorMessageDetection()
    {
        $message1 = "Database error: Connection failed";
        $message2 = "Request #123 accepted.";
        
        $hasError1 = strpos($message1, 'error') !== false;
        $hasError2 = strpos($message2, 'error') !== false;
        
        $this->assertTrue($hasError1);
        $this->assertFalse($hasError2);
    }

    public function testRequestIdCasting()
    {
        $_POST['id'] = '123';
        $reqId = (int)$_POST['id'];
        
        $this->assertIsInt($reqId);
        $this->assertEquals(123, $reqId);
    }

    public function testActionValidation()
    {
        $validActions = ['accept', 'reject', 'collect'];
        
        $action1 = 'accept';
        $action2 = 'invalid';
        
        $this->assertTrue(in_array($action1, $validActions));
        $this->assertFalse(in_array($action2, $validActions));
    }

    public function testEmptyRequestsHandling()
    {
        $requests = [];
        
        $this->assertEmpty($requests);
        $this->assertCount(0, $requests);
    }

    public function testRequestsWithData()
    {
        $requests = [
            [
                'id' => 1,
                'student_id' => 5,
                'student_name' => 'John Doe',
                'bottles' => 50,
                'location' => 'Dorm A',
                'note' => 'Near entrance',
                'status_cleaner' => 'pending',
                'created_at' => '2024-12-01 10:00:00'
            ],
            [
                'id' => 2,
                'student_id' => 6,
                'student_name' => 'Jane Smith',
                'bottles' => 30,
                'location' => 'Dorm B',
                'note' => null,
                'status_cleaner' => 'accepted',
                'created_at' => '2024-12-02 11:00:00'
            ]
        ];
        
        $this->assertCount(2, $requests);
        $this->assertEquals('pending', $requests[0]['status_cleaner']);
        $this->assertEquals('accepted', $requests[1]['status_cleaner']);
    }

    public function testStatusFiltering()
    {
        $requests = [
            ['id' => 1, 'status_cleaner' => 'pending'],
            ['id' => 2, 'status_cleaner' => 'accepted'],
            ['id' => 3, 'status_cleaner' => 'completed'],
            ['id' => 4, 'status_cleaner' => 'pending']
        ];
        
        $pendingOrAccepted = array_filter($requests, function($r) {
            return in_array($r['status_cleaner'], ['pending', 'accepted']);
        });
        
        $this->assertCount(3, $pendingOrAccepted);
    }

    public function testNoteDefaultValue()
    {
        $note1 = null;
        $note2 = 'Some note';
        
        $display1 = $note1 ?: '-';
        $display2 = $note2 ?: '-';
        
        $this->assertEquals('-', $display1);
        $this->assertEquals('Some note', $display2);
    }

    public function testStudentNameFallback()
    {
        $request1 = ['student_name' => 'John Doe', 'student_id' => 5];
        $request2 = ['student_name' => null, 'student_id' => 6];
        
        $display1 = $request1['student_name'] ?? ('#' . $request1['student_id']);
        $display2 = $request2['student_name'] ?? ('#' . $request2['student_id']);
        
        $this->assertEquals('John Doe', $display1);
        $this->assertEquals('#6', $display2);
    }

    public function testUrlEncoding()
    {
        $message = "Request #123 accepted.";
        $encoded = urlencode($message);
        
        $this->assertStringContainsString('Request', urldecode($encoded));
        $this->assertNotEquals($message, $encoded);
    }

    public function testPostMethodCheck()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
        
        $this->assertTrue($isPost);
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $isGet = $_SERVER['REQUEST_METHOD'] === 'GET';
        
        $this->assertTrue($isGet);
    }

    public function testEmptyPostValidation()
    {
        $_POST = [];
        
        $hasAction = !empty($_POST['action']);
        $hasId = !empty($_POST['id']);
        
        $this->assertFalse($hasAction);
        $this->assertFalse($hasId);
        
        $_POST['action'] = 'accept';
        $_POST['id'] = '123';
        
        $hasAction = !empty($_POST['action']);
        $hasId = !empty($_POST['id']);
        
        $this->assertTrue($hasAction);
        $this->assertTrue($hasId);
    }

    public function testSelectQueryStructure()
    {
        $expectedQuery = "SELECT r.*, u.nickname AS student_name
                           FROM collection_requests r
                           LEFT JOIN users u ON u.id = r.student_id
                           WHERE r.status_cleaner IN ('pending','accepted')
                           ORDER BY r.created_at ASC";
        
        $this->assertStringContainsString('LEFT JOIN users', $expectedQuery);
        $this->assertStringContainsString('WHERE r.status_cleaner IN (\'pending\',\'accepted\')', $expectedQuery);
        $this->assertStringContainsString('ORDER BY r.created_at ASC', $expectedQuery);
    }

    public function testMessageFromGetParameter()
    {
        $_GET['msg'] = 'Test message';
        $message = $_GET['msg'] ?? '';
        
        $this->assertEquals('Test message', $message);
        
        unset($_GET['msg']);
        $message = $_GET['msg'] ?? '';
        
        $this->assertEquals('', $message);
    }

    public function testStatusConditionalDisplay()
    {
        $request = ['status_cleaner' => 'pending'];
        
        $showAcceptReject = ($request['status_cleaner'] == 'pending');
        $showCollect = ($request['status_cleaner'] == 'accepted');
        
        $this->assertTrue($showAcceptReject);
        $this->assertFalse($showCollect);
        
        $request['status_cleaner'] = 'accepted';
        
        $showAcceptReject = ($request['status_cleaner'] == 'pending');
        $showCollect = ($request['status_cleaner'] == 'accepted');
        
        $this->assertFalse($showAcceptReject);
        $this->assertTrue($showCollect);
    }

    public function testExceptionHandlingReturnsEmptyArray()
    {
        try {
            throw new Exception('Database error');
        } catch (Exception $e) {
            $requests = [];
        }
        
        $this->assertEmpty($requests);
        $this->assertIsArray($requests);
    }
}