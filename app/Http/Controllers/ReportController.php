<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ReportController - Demonstrates Controller Pattern and Separation of Concerns
 * 
 * This controller handles HTTP requests for reporting functionality while
 * delegating business logic to the ReportService (SRP).
 */
class ReportController extends Controller
{
    protected ReportService $reportService;

    /**
     * Constructor - Dependency Injection
     * Demonstrates Dependency Inversion Principle
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the reporting dashboard
     */
    public function index(): View
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'Admin';
        
        $reportTypes = $this->reportService->getAvailableReportTypes();
        $formats = $this->reportService->getAvailableFormats();

        return view('reports.index', compact('reportTypes', 'formats', 'isAdmin'));
    }

    /**
     * Preview a report without generating the full output
     */
    public function preview(Request $request): JsonResponse
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'Admin';
        
        $request->validate([
            'type' => 'required|in:students,enrollments,courses',
            'filters' => 'sometimes|array'
        ]);

        $type = $request->input('type');
        $filtersInput = $request->input('filters', []);
        $filters = is_array($filtersInput) ? $filtersInput : (json_decode($filtersInput, true) ?? []);
        
        // Add user-specific filters for students
        if (!$isAdmin && $type === 'students') {
            $filters['student_id'] = $user->student->id ?? null;
        } elseif (!$isAdmin && $type === 'enrollments') {
            $filters['student_id'] = $user->student->id ?? null;
        }

        try {
            // Get total count first (without limit)
            $totalCount = $this->getTotalCount($type, $filters);
            
            // Generate report and limit to 10 records for preview
            $report = $this->reportService->generateReportByType($type, $filters, 'json');
            $report = array_slice($report, 0, 10);
            $showing = count($report);
            
            return response()->json([
                'success' => true,
                'preview' => $report,
                'total_records' => $totalCount,
                'showing' => $showing,
                'data' => $report,
                'type' => $type,
                'metadata' => [
                    'type' => $type,
                    'filters' => $filters,
                    'preview' => true,
                    'record_count' => $showing,
                    'total_available' => $totalCount
                ],
                'message' => $showing > 10 ? 
                    "Preview showing first 10 of {$totalCount} records. Use 'Generate Report' to see all data." : 
                    "Preview showing all " . $showing . " records."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get total count of records for a report type
     */
    private function getTotalCount(string $type, array $filters): int
    {
        try {
            $countFilters = $filters;
            unset($countFilters['limit']); // Remove limit for count
            
            $report = $this->reportService->generateReportByType($type, $countFilters, 'json');
            return count($report);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Generate and display a report
     */
    public function generate(Request $request): JsonResponse|View|RedirectResponse
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'Admin';
        
        $request->validate([
            'type' => 'required|in:students,enrollments,courses',
            'format' => 'required|in:json,csv,html',
            'filters' => 'sometimes|array'
        ]);

        $type = $request->input('type');
        $format = $request->input('format');
        $filtersInput = $request->input('filters', []);
        $filters = is_array($filtersInput) ? $filtersInput : (json_decode($filtersInput, true) ?? []);
        
        // Add user-specific filters for students
        if (!$isAdmin && $type === 'students') {
            // Students can only see their own data
            $filters['student_id'] = $user->student->id ?? null;
        } elseif (!$isAdmin && $type === 'enrollments') {
            // Students can only see their own enrollments
            $filters['student_id'] = $user->student->id ?? null;
        }

        try {
            \Log::info('Generating report', ['type' => $type, 'format' => $format, 'filters' => $filters]);
            $report = $this->reportService->generateReportByType($type, $filters, $format);
            \Log::info('Report generated successfully', ['count' => count($report)]);
            
            // Ensure we always return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax() || $format === 'json') {
                return response()->json([
                    'success' => true,
                    'data' => $report,
                    'metadata' => [
                        'type' => $type,
                        'format' => $format,
                        'filters' => $filters,
                        'generated_at' => now()->toISOString(),
                        'record_count' => count($report)
                    ]
                ]);
            }

            return view('reports.display', [
                'report' => $report,
                'type' => $type,
                'format' => $format,
                'title' => ucfirst($type) . ' Report'
            ]);

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Export report to file
     */
    public function export(Request $request): BinaryFileResponse|RedirectResponse|JsonResponse|Response
    {
        $request->validate([
            'type' => 'required|in:students,enrollments,courses',
            'format' => 'required|in:json,csv,html',
            'filters' => 'sometimes|array'
        ]);

        try {
            $type = $request->input('type');
            $format = $request->input('format');
            $filtersInput = $request->input('filters', []);
            $filters = is_array($filtersInput) ? $filtersInput : (json_decode($filtersInput, true) ?? []);

            \Log::info('Export request', ['type' => $type, 'format' => $format, 'filters' => $filters]);
            
            // Check if reports directory exists
            $reportsDir = storage_path('app/reports');
            if (!is_dir($reportsDir)) {
                mkdir($reportsDir, 0755, true);
                \Log::info('Created reports directory', ['path' => $reportsDir]);
            }
            
            $filename = $this->reportService->exportReportToFile($type, $filters, $format);
            $filePath = storage_path("app/reports/{$filename}");
            
            \Log::info('Export file created', ['filename' => $filename, 'path' => $filePath, 'exists' => file_exists($filePath), 'size' => file_exists($filePath) ? filesize($filePath) : 0]);

            // Verify file exists before attempting download
            if (!file_exists($filePath)) {
                throw new \Exception("Export file does not exist: {$filePath}");
            }
            
            // Read file content and return with proper headers
            $content = file_get_contents($filePath);
            
            // Set proper headers for file download
            $headers = [
                'Content-Type' => $this->getContentType($format),
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($content),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];
            
            \Log::info('Download headers', ['headers' => $headers, 'content_length' => strlen($content)]);
            
            // Delete the temporary file
            unlink($filePath);
            
            return response($content, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Export failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            // Always return JSON for AJAX requests to avoid HTML error pages
            return response()->json([
                'success' => false,
                'error' => 'Failed to export report: ' . $e->getMessage(),
                'details' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get available report types
     */
    public function getReportTypes(): JsonResponse
    {
        $reportTypes = $this->reportService->getAvailableReportTypes();
        $formats = $this->reportService->getAvailableFormats();
        
        return response()->json([
            'types' => $reportTypes,
            'formats' => $formats
        ]);
    }

    /**
     * Test method for debugging
     */
    public function test(): JsonResponse
    {
        return response()->json([
            'message' => 'Reports API is working',
            'timestamp' => now()->toISOString(),
            'user' => auth()->user() ? auth()->user()->only(['id', 'name', 'role']) : null
        ]);
    }

    /**
     * Get content type for file format
     */
    private function getContentType(string $format): string
    {
        return match($format) {
            'csv' => 'text/csv',
            'json' => 'application/json',
            'html' => 'text/html',
            default => 'text/plain'
        };
    }
}