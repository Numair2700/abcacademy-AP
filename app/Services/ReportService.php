<?php

namespace App\Services;

use App\Services\Reports\StudentReportGenerator;
use App\Services\Reports\EnrollmentReportGenerator;
use App\Services\Reports\CourseReportGenerator;
use Illuminate\Support\Facades\Storage;

/**
 * ReportService - Demonstrates Service Layer Pattern and Dependency Injection
 * 
 * This service class handles all reporting operations and demonstrates:
 * - Single Responsibility Principle (SRP)
 * - Dependency Inversion Principle (DIP)
 * - Open/Closed Principle (OCP)
 * - Service Layer Pattern
 */
class ReportService
{
    protected ReportGenerator $reportGenerator;

    /**
     * Constructor - Dependency Injection
     * Demonstrates Dependency Inversion Principle
     */
    public function __construct(?ReportGenerator $reportGenerator = null)
    {
        $this->reportGenerator = $reportGenerator ?? new StudentReportGenerator();
    }

    /**
     * Generate a student report
     * Demonstrates method overloading and polymorphism
     */
    public function generateStudentReport(array $filters = [], string $format = 'json'): array
    {
        $generator = new StudentReportGenerator();
        return $generator
            ->setFilters($filters)
            ->setFormat($format)
            ->generate();
    }

    /**
     * Generate an enrollment report
     */
    public function generateEnrollmentReport(array $filters = [], string $format = 'json'): array
    {
        $generator = new EnrollmentReportGenerator();
        return $generator
            ->setFilters($filters)
            ->setFormat($format)
            ->generate();
    }

    /**
     * Generate a course report
     */
    public function generateCourseReport(array $filters = [], string $format = 'json'): array
    {
        $generator = new CourseReportGenerator();
        return $generator
            ->setFilters($filters)
            ->setFormat($format)
            ->generate();
    }

    /**
     * Generate multiple reports
     * Demonstrates composition and aggregation
     */
    public function generateMultipleReports(array $reportTypes, array $filters = [], string $format = 'json'): array
    {
        $reports = [];

        foreach ($reportTypes as $type) {
            $reports[$type] = $this->generateReportByType($type, $filters, $format);
        }

        return $reports;
    }

    /**
     * Generate report by type - Factory method pattern
     */
    public function generateReportByType(string $type, array $filters = [], string $format = 'json'): array
    {
        switch ($type) {
            case 'students':
                return $this->generateStudentReport($filters, $format);
            case 'enrollments':
                return $this->generateEnrollmentReport($filters, $format);
            case 'courses':
                return $this->generateCourseReport($filters, $format);
            default:
                throw new \InvalidArgumentException("Unknown report type: {$type}");
        }
    }

    /**
     * Export report to file
     * Demonstrates file handling and storage abstraction
     */
    public function exportReportToFile(string $type, array $filters = [], string $format = 'json'): string
    {
        $report = $this->generateReportByType($type, $filters, $format);
        $filename = $this->generateFilename($type, $format);
        
        $content = $this->formatForExport($report, $format);
        
        \Log::info('Exporting report', [
            'type' => $type,
            'format' => $format,
            'filename' => $filename,
            'content_length' => strlen($content),
            'content_preview' => substr($content, 0, 200)
        ]);
        
        // Ensure reports directory exists
        $reportsDir = storage_path('app/reports');
        if (!is_dir($reportsDir)) {
            mkdir($reportsDir, 0755, true);
        }
        
        // Write file directly instead of using Storage facade
        $filePath = storage_path("app/reports/{$filename}");
        $result = file_put_contents($filePath, $content);
        
        \Log::info('File write result', [
            'filepath' => $filePath,
            'bytes_written' => $result,
            'file_exists' => file_exists($filePath),
            'file_size' => file_exists($filePath) ? filesize($filePath) : 0
        ]);
        
        if ($result === false) {
            throw new \Exception("Failed to write file to {$filePath}");
        }
        
        return $filename;
    }

    /**
     * Get available report types
     * Demonstrates introspection and metadata
     */
    public function getAvailableReportTypes(): array
    {
        return [
            'students' => 'Student Reports',
            'enrollments' => 'Enrollment Reports',
            'courses' => 'Course Reports'
        ];
    }

    /**
     * Get available formats
     */
    public function getAvailableFormats(): array
    {
        return [
            'json' => 'JSON',
            'csv' => 'CSV',
            'html' => 'HTML'
        ];
    }

    /**
     * Generate filename for export
     */
    private function generateFilename(string $type, string $format): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        return "{$type}_report_{$timestamp}.{$format}";
    }

    /**
     * Format data for export
     */
    private function formatForExport(array $data, string $format): string
    {
        switch ($format) {
            case 'json':
                return json_encode($data, JSON_PRETTY_PRINT);
            case 'csv':
                return implode("\n", $data);
            case 'html':
                return implode("\n", $data);
            default:
                return json_encode($data);
        }
    }
}

/**
 * ReportService - Context class that uses different strategies
 */
/**
 * ReportService - Context class that uses different strategies
 */
