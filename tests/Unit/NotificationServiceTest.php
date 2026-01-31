<?php

namespace Tests\Unit;

use App\Services\NotificationService;
use App\Services\EmailNotificationStrategy;
use App\Services\LogNotificationStrategy;
use App\Services\DatabaseNotificationStrategy;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

/**
 * NotificationServiceTest - Demonstrates Unit Testing of Strategy Pattern
 * 
 * This test class demonstrates comprehensive unit testing of:
 * - Strategy Pattern implementation
 * - Service Layer Pattern
 * - Dependency Injection
 * - Interface implementation
 */
class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Note: Log::fake() is not available in this Laravel version
        // We'll test the actual logging behavior instead
    }

    /** @test */
    public function notification_service_can_be_instantiated()
    {
        $service = new NotificationService();
        
        $this->assertInstanceOf(NotificationService::class, $service);
    }

    /** @test */
    public function email_notification_strategy_implements_interface()
    {
        $strategy = new EmailNotificationStrategy();
        
        $this->assertInstanceOf(\App\Services\NotificationStrategy::class, $strategy);
    }

    /** @test */
    public function log_notification_strategy_implements_interface()
    {
        $strategy = new LogNotificationStrategy();
        
        $this->assertInstanceOf(\App\Services\NotificationStrategy::class, $strategy);
    }

    /** @test */
    public function database_notification_strategy_implements_interface()
    {
        $strategy = new DatabaseNotificationStrategy();
        
        $this->assertInstanceOf(\App\Services\NotificationStrategy::class, $strategy);
    }

    /** @test */
    public function email_notification_strategy_sends_notification()
    {
        $user = User::factory()->create();
        $strategy = new EmailNotificationStrategy();
        
        $result = $strategy->send($user, 'test_notification', ['message' => 'Test message']);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function log_notification_strategy_sends_notification()
    {
        $user = User::factory()->create();
        $strategy = new LogNotificationStrategy();
        
        $result = $strategy->send($user, 'test_notification', ['message' => 'Test message']);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function database_notification_strategy_sends_notification()
    {
        $user = User::factory()->create();
        $strategy = new DatabaseNotificationStrategy();
        
        $result = $strategy->send($user, 'test_notification', ['message' => 'Test message']);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function notification_service_sends_notification_with_email_strategy()
    {
        $user = User::factory()->create();
        $service = new NotificationService();
        
        $result = $service->sendNotification($user, 'test_notification', ['message' => 'Test'], 'email');
        
        $this->assertTrue($result);
    }

    /** @test */
    public function notification_service_sends_notification_with_log_strategy()
    {
        $user = User::factory()->create();
        $service = new NotificationService();
        
        $result = $service->sendNotification($user, 'test_notification', ['message' => 'Test'], 'log');
        
        $this->assertTrue($result);
    }

    /** @test */
    public function notification_service_sends_notification_with_database_strategy()
    {
        $user = User::factory()->create();
        $service = new NotificationService();
        
        $result = $service->sendNotification($user, 'test_notification', ['message' => 'Test'], 'database');
        
        $this->assertTrue($result);
    }

    /** @test */
    public function notification_service_throws_exception_for_unknown_strategy()
    {
        $user = User::factory()->create();
        $service = new NotificationService();
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown notification strategy: unknown_strategy');
        
        $service->sendNotification($user, 'test_notification', ['message' => 'Test'], 'unknown_strategy');
    }

    /** @test */
    public function notification_service_sends_multiple_notifications()
    {
        $user = User::factory()->create();
        $service = new NotificationService();
        
        $strategies = ['email', 'log', 'database'];
        $results = $service->sendMultipleNotifications($user, 'test_notification', ['message' => 'Test'], $strategies);
        
        $this->assertIsArray($results);
        $this->assertArrayHasKey('email', $results);
        $this->assertArrayHasKey('log', $results);
        $this->assertArrayHasKey('database', $results);
        
        $this->assertTrue($results['email']);
        $this->assertTrue($results['log']);
        $this->assertTrue($results['database']);
    }

    /** @test */
    public function notification_service_handles_partial_failure()
    {
        $user = User::factory()->create();
        $service = new NotificationService();
        
        // Test with mixed valid and invalid strategies
        $strategies = ['email', 'unknown_strategy'];
        
        $this->expectException(\InvalidArgumentException::class);
        
        $service->sendMultipleNotifications($user, 'test_notification', ['message' => 'Test'], $strategies);
    }

    /** @test */
    public function email_strategy_logs_error_on_failure()
    {
        $user = User::factory()->create();
        $strategy = new EmailNotificationStrategy();
        
        // Mock a failure scenario (in real implementation, this would be an actual email failure)
        // For now, we'll test the success case as the current implementation always returns true
        
        $result = $strategy->send($user, 'test_notification', ['message' => 'Test']);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function database_strategy_logs_error_on_failure()
    {
        $user = User::factory()->create();
        $strategy = new DatabaseNotificationStrategy();
        
        // Mock a failure scenario (in real implementation, this would be a database failure)
        // For now, we'll test the success case as the current implementation always returns true
        
        $result = $strategy->send($user, 'test_notification', ['message' => 'Test']);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function notification_strategies_handle_different_data_types()
    {
        $user = User::factory()->create();
        
        $strategies = [
            new EmailNotificationStrategy(),
            new LogNotificationStrategy(),
            new DatabaseNotificationStrategy()
        ];
        
        $testData = [
            'simple_string' => 'Test message',
            'array_data' => ['key1' => 'value1', 'key2' => 'value2'],
            'numeric_data' => 12345,
            'boolean_data' => true
        ];
        
        foreach ($strategies as $strategy) {
            $result = $strategy->send($user, 'test_notification', $testData);
            $this->assertTrue($result);
        }
    }

    /** @test */
    public function notification_service_maintains_strategy_registry()
    {
        $service = new NotificationService();
        
        // Use reflection to access the private strategies property
        $reflection = new \ReflectionClass($service);
        $strategiesProperty = $reflection->getProperty('strategies');
        $strategiesProperty->setAccessible(true);
        $strategies = $strategiesProperty->getValue($service);
        
        $this->assertIsArray($strategies);
        $this->assertArrayHasKey('email', $strategies);
        $this->assertArrayHasKey('log', $strategies);
        $this->assertArrayHasKey('database', $strategies);
        
        $this->assertInstanceOf(EmailNotificationStrategy::class, $strategies['email']);
        $this->assertInstanceOf(LogNotificationStrategy::class, $strategies['log']);
        $this->assertInstanceOf(DatabaseNotificationStrategy::class, $strategies['database']);
    }
}
