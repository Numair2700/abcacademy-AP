<?php

namespace App\Services\Reports;

use App\Models\Course;
use App\Services\ReportGenerator;
use Illuminate\Support\Collection;

/**
 * CourseReportGenerator - Concrete implementation of ReportGenerator
 * 
 * Demonstrates Strategy Pattern and Single Responsibility Principle.
 * Focused solely on course-related reporting functionality.
 */
class CourseReportGenerator extends ReportGenerator
{
    /**
     * Collect course data based on filters
     */
    public function collectData(): Collection
    {
        $query = Course::with(['program', 'units', 'enrollments', 'favoriteUsers']);

        // Apply filters using the Open/Closed Principle
        if (isset($this->filters['program_id'])) {
            $query->where('program_id', $this->filters['program_id']);
        }

        if (isset($this->filters['published'])) {
            $query->where('published', $this->filters['published']);
        }

        if (isset($this->filters['language'])) {
            $query->where('language', $this->filters['language']);
        }

        if (isset($this->filters['price_min'])) {
            $query->where('price', '>=', $this->filters['price_min']);
        }

        if (isset($this->filters['price_max'])) {
            $query->where('price', '<=', $this->filters['price_max']);
        }

        return $query->get();
    }

    /**
     * Process course data into report format
     */
    public function processData(Collection $courses): Collection
    {
        return $courses->map(function ($course) {
            $enrollments = $course->enrollments;
            $totalEnrollments = $enrollments->count();
            $recentEnrollments = $enrollments->where('created_at', '>=', now()->subMonths(6))->count();
            $totalFavorites = $course->favoriteUsers->count();

            return collect([
                'course_id' => $course->id,
                'course_code' => $course->code,
                'course_title' => $course->title,
                'program_title' => $course->program->title,
                'program_level' => $course->program->qualification_level,
                'language' => $course->language,
                'price' => $course->price,
                'published' => $course->published ? 'Yes' : 'No',
                'total_units' => $course->units->count(),
                'total_enrollments' => $totalEnrollments,
                'recent_enrollments' => $recentEnrollments,
                'total_favorites' => $totalFavorites,
                'popularity_score' => $this->calculatePopularityScore($totalEnrollments, $totalFavorites),
                'created_date' => $course->created_at->format('Y-m-d'),
                'last_updated' => $course->updated_at->format('Y-m-d H:i:s')
            ]);
        });
    }

    /**
     * Calculate course popularity score
     * Demonstrates encapsulation of business logic
     */
    private function calculatePopularityScore(int $enrollments, int $favorites): float
    {
        // Simple algorithm: enrollments weight 70%, favorites weight 30%
        $enrollmentScore = min($enrollments * 0.7, 100);
        $favoriteScore = min($favorites * 3, 100); // Favorites are rarer, so weight them more
        
        return round(($enrollmentScore + $favoriteScore) / 2, 1);
    }
}
