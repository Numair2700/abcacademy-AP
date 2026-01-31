<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Program;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map program labels to actual Program titles to resolve IDs reliably
        $programTitleByNumber = [
            1 => 'L2 Applied Science',
            2 => 'L3 Applied Science',
            3 => 'HND Computing 2022',
            4 => 'HND Business 2021',
            5 => 'L7 Extended Diploma',
        ];

        $courses = [
            // Program 1: L2 Applied Science (Created in 2023)
            ['code' => 'HMHN1', 'title' => 'BTEC Applied Science Certificate 240', 'language' => 'English', 'price' => 2500.00, 'program_id' => 1, 'created_at' => '2023-01-15 10:00:00', 'updated_at' => '2023-01-15 10:00:00'],
            ['code' => 'HMHM8', 'title' => 'BTEC Applied Science Diploma 480', 'language' => 'English', 'price' => 5000.00, 'program_id' => 1, 'created_at' => '2023-02-20 14:30:00', 'updated_at' => '2023-02-20 14:30:00'],
            
            // Program 2: L3 Applied Science (Created in 2023)
            ['code' => 'CZVY5', 'title' => 'BTEC Applied Science Subsidiary', 'language' => 'English', 'price' => 2500.00, 'program_id' => 2, 'created_at' => '2023-03-10 09:15:00', 'updated_at' => '2023-03-10 09:15:00'],
            ['code' => 'CZVY7', 'title' => 'BTEC Applied Science Diploma', 'language' => 'English', 'price' => 5000.00, 'program_id' => 2, 'created_at' => '2023-04-05 16:45:00', 'updated_at' => '2023-04-05 16:45:00'],
            
            // Program 3: HND Computing 2022 (Created in 2022)
            ['code' => 'DZXX1', 'title' => 'BTEC HND COMPUTING General 2022', 'language' => 'English', 'price' => 6000.00, 'program_id' => 3, 'created_at' => '2022-08-15 11:20:00', 'updated_at' => '2022-08-15 11:20:00'],
            ['code' => 'DZXX2', 'title' => 'BTEC HND COMPUTING Software Engineering 2022', 'language' => 'English', 'price' => 6000.00, 'program_id' => 3, 'created_at' => '2022-09-01 13:10:00', 'updated_at' => '2022-09-01 13:10:00'],
            ['code' => 'DZXX6', 'title' => 'BTEC HND COMPUTING Cyber Security 2022', 'language' => 'English', 'price' => 6000.00, 'program_id' => 3, 'created_at' => '2022-09-15 15:30:00', 'updated_at' => '2022-09-15 15:30:00'],
            
            // Program 4: HND Business 2021 (Created in 2021)
            ['code' => 'DZCC7', 'title' => 'BTEC HND Business General 2021', 'language' => 'English', 'price' => 6000.00, 'program_id' => 4, 'created_at' => '2021-07-20 10:00:00', 'updated_at' => '2021-07-20 10:00:00'],
            ['code' => 'DZCC8', 'title' => 'BTEC HND Business Accounting and Finance 2021', 'language' => 'English', 'price' => 6000.00, 'program_id' => 4, 'created_at' => '2021-08-05 14:15:00', 'updated_at' => '2021-08-05 14:15:00'],
            ['code' => 'DZCC9', 'title' => 'BTEC HND Business Management 2021', 'language' => 'English', 'price' => 6000.00, 'program_id' => 4, 'created_at' => '2021-08-20 16:30:00', 'updated_at' => '2021-08-20 16:30:00'],
            ['code' => 'DZCD2', 'title' => 'BTEC HND Business Human Resource Management 2021', 'language' => 'English', 'price' => 6000.00, 'program_id' => 4, 'created_at' => '2021-09-10 12:45:00', 'updated_at' => '2021-09-10 12:45:00'],
            ['code' => 'DZCD3', 'title' => 'BTEC HND Business Marketing 2021', 'language' => 'English', 'price' => 6000.00, 'program_id' => 4, 'created_at' => '2021-09-25 09:20:00', 'updated_at' => '2021-09-25 09:20:00'],
            ['code' => 'HDYD6', 'title' => 'BTEC HND Business General 2021', 'language' => 'Arabic', 'price' => 6000.00, 'program_id' => 4, 'created_at' => '2021-10-15 11:10:00', 'updated_at' => '2021-10-15 11:10:00'],
            ['code' => 'HDYD7', 'title' => 'BTEC HND Business Accounting and Finance 2021', 'language' => 'Arabic', 'price' => 6000.00, 'program_id' => 4, 'created_at' => '2021-11-01 15:25:00', 'updated_at' => '2021-11-01 15:25:00'],
            ['code' => 'HDYD8', 'title' => 'BTEC HND Business Management 2021', 'language' => 'Arabic', 'price' => 6000.00, 'program_id' => 4, 'created_at' => '2021-11-20 13:40:00', 'updated_at' => '2021-11-20 13:40:00'],
            
            // Program 5: L7 Extended Diploma (Created in 2024)
            ['code' => 'L7ED1', 'title' => 'L7 Extended Diploma in Strategic Management', 'language' => 'English', 'price' => 8000.00, 'program_id' => 5, 'created_at' => '2024-01-10 10:30:00', 'updated_at' => '2024-01-10 10:30:00'],
            ['code' => 'L7ED2', 'title' => 'L7 Extended Diploma in Leadership and Management', 'language' => 'English', 'price' => 8000.00, 'program_id' => 5, 'created_at' => '2024-02-15 14:20:00', 'updated_at' => '2024-02-15 14:20:00'],
            ['code' => 'L7ED3', 'title' => 'L7 Extended Diploma in Project Management', 'language' => 'English', 'price' => 8000.00, 'program_id' => 5, 'created_at' => '2024-03-05 16:50:00', 'updated_at' => '2024-03-05 16:50:00'],
        ];

        foreach ($courses as $course) {
            // Resolve robust program_id via title mapping
            $programTitle = $programTitleByNumber[$course['program_id']] ?? null;
            $resolvedProgramId = $programTitle
                ? Program::where('title', $programTitle)->value('id')
                : null;

            // Fallback to provided program_id if resolution fails
            $programIdToUse = $resolvedProgramId ?: $course['program_id'];

            // Ensure published flag default
            $published = $course['published'] ?? true;

            // Use query builder to control timestamps precisely
            DB::table('courses')->updateOrInsert(
                ['code' => $course['code']],
                [
                    'title' => $course['title'],
                    'language' => $course['language'],
                    'price' => $course['price'],
                    'program_id' => $programIdToUse,
                    'published' => $published,
                    'created_at' => $course['created_at'],
                    'updated_at' => $course['updated_at'],
                ]
            );
        }
    }
}
 


