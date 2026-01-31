<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * NotificationService - Demonstrates Strategy Pattern and Single Responsibility Principle
 * 
 * This service handles all notification operations using different strategies
 * for different notification types.
 */
class NotificationService
{
    protected array $strategies = [];

    /**
     * Constructor - Register notification strategies
     */
    public function __construct()
    {
        $this->registerStrategies();
    }

    /**
     * Register available notification strategies
     */
    private function registerStrategies(): void
    {
        $this->strategies = [
            'email' => new EmailNotificationStrategy(),
            'log' => new LogNotificationStrategy(),
            'database' => new DatabaseNotificationStrategy(),
        ];
    }

    /**
     * Send notification using specified strategy
     */
    public function sendNotification(User $user, string $type, array $data, string $strategy = 'email'): bool
    {
        if (!isset($this->strategies[$strategy])) {
            throw new \InvalidArgumentException("Unknown notification strategy: {$strategy}");
        }

        return $this->strategies[$strategy]->send($user, $type, $data);
    }

    /**
     * Send multiple notifications using different strategies
     */
    public function sendMultipleNotifications(User $user, string $type, array $data, array $strategies): array
    {
        $results = [];
        
        foreach ($strategies as $strategy) {
            $results[$strategy] = $this->sendNotification($user, $type, $data, $strategy);
        }

        return $results;
    }
}

/**
 * NotificationStrategy Interface - Demonstrates Strategy Pattern
 */
interface NotificationStrategy
{
    public function send(User $user, string $type, array $data): bool;
}

/**
 * Email Notification Strategy
 */
class EmailNotificationStrategy implements NotificationStrategy
{
    public function send(User $user, string $type, array $data): bool
    {
        try {
            // In a real implementation, this would send actual emails
            Log::info('Email notification sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'type' => $type,
                'data' => $data
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}

/**
 * Log Notification Strategy
 */
class LogNotificationStrategy implements NotificationStrategy
{
    public function send(User $user, string $type, array $data): bool
    {
        Log::info('Log notification', [
            'user_id' => $user->id,
            'type' => $type,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
        
        return true;
    }
}

/**
 * Database Notification Strategy
 */
class DatabaseNotificationStrategy implements NotificationStrategy
{
    public function send(User $user, string $type, array $data): bool
    {
        try {
            // In a real implementation, this would store notifications in database
            Log::info('Database notification stored', [
                'user_id' => $user->id,
                'type' => $type,
                'data' => $data
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to store database notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}

