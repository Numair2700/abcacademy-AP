<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Course;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        // Create all units first
        $units = [
            // Applied Science Units
            ['btec_code' => 'A/618/7388', 'title' => 'Principles and Application of Biology I', 'credit' => 15],
            ['btec_code' => 'B/618/7389', 'title' => 'Principles and Application of Chemistry I', 'credit' => 15],
            ['btec_code' => 'C/618/7390', 'title' => 'Principles and Application of Physics I', 'credit' => 15],
            ['btec_code' => 'D/618/7391', 'title' => 'Investigative Project Skills', 'credit' => 15],
            ['btec_code' => 'E/618/7392', 'title' => 'Further Engineering Mathematics', 'credit' => 15],
            ['btec_code' => 'F/618/7393', 'title' => 'Electrical Circuits and their Applications', 'credit' => 15],
            ['btec_code' => 'G/618/7394', 'title' => 'Materials Science', 'credit' => 15],
            ['btec_code' => 'H/618/7395', 'title' => 'Sustainable Energy', 'credit' => 15],
            ['btec_code' => 'I/618/7396', 'title' => 'Applied Science Research Project', 'credit' => 60],
            ['btec_code' => 'J/618/7397', 'title' => 'Advanced Biology Applications', 'credit' => 60],
            ['btec_code' => 'K/618/7398', 'title' => 'Advanced Chemistry Applications', 'credit' => 60],
            ['btec_code' => 'L/618/7399', 'title' => 'Advanced Physics Applications', 'credit' => 60],
            ['btec_code' => 'M/618/7400', 'title' => 'Applied Science Extended Project', 'credit' => 60],
            
            // Computing Units
            ['btec_code' => 'N/618/7401', 'title' => 'Programming', 'credit' => 15],
            ['btec_code' => 'O/618/7402', 'title' => 'Networking', 'credit' => 15],
            ['btec_code' => 'P/618/7403', 'title' => 'Professional Practice', 'credit' => 15],
            ['btec_code' => 'Q/618/7404', 'title' => 'Database Design & Development', 'credit' => 15],
            ['btec_code' => 'R/618/7405', 'title' => 'Security', 'credit' => 15],
            ['btec_code' => 'S/618/7406', 'title' => 'Planning a Computing Project', 'credit' => 15],
            ['btec_code' => 'T/618/7407', 'title' => 'Software Development Lifecycles', 'credit' => 15],
            ['btec_code' => 'U/618/7408', 'title' => 'Cyber Security', 'credit' => 15],
            ['btec_code' => 'V/618/7409', 'title' => 'Website Design & Development', 'credit' => 15],
            ['btec_code' => 'W/618/7410', 'title' => 'Computing Research Project', 'credit' => 15],
            ['btec_code' => 'X/618/7411', 'title' => 'Business Process Support', 'credit' => 15],
            ['btec_code' => 'Y/618/7412', 'title' => 'Discrete Maths', 'credit' => 15],
            ['btec_code' => 'Z/618/7413', 'title' => 'Data Structure & Algorithms', 'credit' => 15],
            ['btec_code' => 'A/618/7414', 'title' => 'Applied Programming and Design Principles', 'credit' => 15],
            ['btec_code' => 'B/618/7415', 'title' => 'Application Development', 'credit' => 15],
            ['btec_code' => 'C/618/7416', 'title' => 'Cloud Computing', 'credit' => 15],
            ['btec_code' => 'D/618/7417', 'title' => 'Applied Cryptography in the Cloud', 'credit' => 15],
            ['btec_code' => 'E/618/7418', 'title' => 'Forensics', 'credit' => 15],
            ['btec_code' => 'F/618/7419', 'title' => 'Information Security Management', 'credit' => 15],
            ['btec_code' => 'G/618/7420', 'title' => 'System Analysis & Design', 'credit' => 15],
            
            // Business Units
            ['btec_code' => 'H/618/7421', 'title' => 'The Contemporary Business Environment', 'credit' => 15],
            ['btec_code' => 'I/618/7422', 'title' => 'Marketing Processes and Planning', 'credit' => 15],
            ['btec_code' => 'J/650/2918', 'title' => 'Management of Human Resources', 'credit' => 15],
            ['btec_code' => 'K/618/7423', 'title' => 'Leadership and Management', 'credit' => 15],
            ['btec_code' => 'L/618/5036', 'title' => 'Accounting Principles', 'credit' => 15],
            ['btec_code' => 'M/618/7424', 'title' => 'Managing a Successful Business Project', 'credit' => 15],
            ['btec_code' => 'N/618/7425', 'title' => 'Entrepreneurial Ventures', 'credit' => 15],
            ['btec_code' => 'O/618/7426', 'title' => 'Recording Financial Transactions', 'credit' => 15],
            ['btec_code' => 'P/618/7427', 'title' => 'Research Project', 'credit' => 15],
            ['btec_code' => 'Q/618/7428', 'title' => 'Organisational Behaviour Management', 'credit' => 15],
            ['btec_code' => 'R/650/2920', 'title' => 'Financial Reporting', 'credit' => 15],
            ['btec_code' => 'S/618/7429', 'title' => 'Management Accounting', 'credit' => 15],
            ['btec_code' => 'T/618/7430', 'title' => 'Financial Management', 'credit' => 15],
            ['btec_code' => 'U/618/7431', 'title' => 'Managing and Leading Change', 'credit' => 15],
            ['btec_code' => 'V/618/7432', 'title' => 'Global Business Environment', 'credit' => 15],
            ['btec_code' => 'W/618/7433', 'title' => 'Principles of Operations Management', 'credit' => 15],
            ['btec_code' => 'X/618/7434', 'title' => 'Identifying Entrepreneurial Opportunities', 'credit' => 15],
            ['btec_code' => 'Y/618/7435', 'title' => 'Launching a New Venture', 'credit' => 15],
            ['btec_code' => 'Z/618/7436', 'title' => 'Managing and Running a Small Business', 'credit' => 15],
            ['btec_code' => 'A/618/7437', 'title' => 'Resource and Talent Planning', 'credit' => 15],
            ['btec_code' => 'B/618/7438', 'title' => 'Employee Relations', 'credit' => 15],
            ['btec_code' => 'C/618/7439', 'title' => 'Strategic Human Resource Management', 'credit' => 15],
            ['btec_code' => 'D/618/7440', 'title' => 'Marketing Insights and Analytics', 'credit' => 15],
            ['btec_code' => 'E/618/7441', 'title' => 'Digital Marketing', 'credit' => 15],
            ['btec_code' => 'F/618/7442', 'title' => 'Integrated Marketing Communications', 'credit' => 15],
            ['btec_code' => 'G/618/7443', 'title' => 'Procurement and Supply Chain Management', 'credit' => 15],
            ['btec_code' => 'H/618/7444', 'title' => 'Pitching and Negotiation Skills', 'credit' => 15],
            ['btec_code' => 'I/618/7445', 'title' => 'Law of Contract and Tort', 'credit' => 15],
            ['btec_code' => 'J/618/7446', 'title' => 'Company Law and Corporate Governance', 'credit' => 15],
            ['btec_code' => 'K/618/7447', 'title' => 'Consumer and Intellectual Property Law', 'credit' => 15],
            ['btec_code' => 'L/618/7448', 'title' => 'Taxation', 'credit' => 15],
            ['btec_code' => 'M/618/7449', 'title' => 'Statistics for Management', 'credit' => 15],
            ['btec_code' => 'N/618/7450', 'title' => 'Business Strategy', 'credit' => 15],
            ['btec_code' => 'O/618/7451', 'title' => 'Business Information Technology Systems', 'credit' => 15],
        ];

        foreach ($units as $unitData) {
            Unit::updateOrCreate(
                ['btec_code' => $unitData['btec_code']],
                $unitData
            );
        }
        
        // Clear existing course-unit relationships
        \DB::table('course_unit')->truncate();

        // Now attach units to courses logically based on program and specialization
        $courses = Course::with('program')->get();
        
        // Applied Science courses (Programs 1 & 2: L2 & L3 Applied Science)
        $appliedScienceCourses = $courses->whereIn('program_id', [1, 2]);
        $appliedScienceUnits = Unit::whereIn('btec_code', [
            'A/618/7388', 'B/618/7389', 'C/618/7390', 'D/618/7391',
            'E/618/7392', 'F/618/7393', 'G/618/7394', 'H/618/7395',
            'I/618/7396', 'J/618/7397', 'K/618/7398', 'L/618/7399', 'M/618/7400'
        ])->get();
        
        // Assign core Applied Science units to all Applied Science courses
        $coreAppliedScienceUnits = $appliedScienceUnits->whereIn('btec_code', [
            'A/618/7388', 'B/618/7389', 'C/618/7390', 'D/618/7391'
        ]);
        
        $appliedScienceCourses->each(function($course) use ($coreAppliedScienceUnits, $appliedScienceUnits) {
            // All Applied Science courses get core units
            $course->units()->attach($coreAppliedScienceUnits->pluck('id'));
            
            // Add additional specialized units based on course level
            if ($course->program_id == 1) { // L2 Applied Science - basic level
                $additionalUnits = $appliedScienceUnits->whereIn('btec_code', [
                    'E/618/7392', 'F/618/7393', 'G/618/7394'
                ])->random(2);
            } else { // L3 Applied Science - advanced level
                $additionalUnits = $appliedScienceUnits->whereIn('btec_code', [
                    'H/618/7395', 'I/618/7396', 'J/618/7397', 'K/618/7398'
                ])->random(3);
            }
            $course->units()->attach($additionalUnits->pluck('id'));
        });
        
        // Computing courses (Program 3: HND Computing 2022)
        $computingCourses = $courses->where('program_id', 3);
        $computingUnits = Unit::whereIn('btec_code', [
            'N/618/7401', 'O/618/7402', 'P/618/7403', 'Q/618/7404',
            'R/618/7405', 'S/618/7406', 'T/618/7407', 'U/618/7408',
            'V/618/7409', 'W/618/7410', 'X/618/7411', 'Y/618/7412',
            'Z/618/7413', 'A/618/7414', 'B/618/7415', 'C/618/7416',
            'D/618/7417', 'E/618/7418', 'F/618/7419', 'G/618/7420'
        ])->get();
        
        // Core computing units for all computing courses
        $coreComputingUnits = $computingUnits->whereIn('btec_code', [
            'N/618/7401', 'O/618/7402', 'P/618/7403', 'Q/618/7404',
            'R/618/7405', 'S/618/7406', 'T/618/7407'
        ]);
        
        $computingCourses->each(function($course) use ($coreComputingUnits, $computingUnits) {
            // All computing courses get core units
            $course->units()->attach($coreComputingUnits->pluck('id'));
            
            // Add specialized units based on course specialization
            if (str_contains($course->title, 'Software Engineering')) {
                $specializedUnits = $computingUnits->whereIn('btec_code', [
                    'A/618/7414', 'B/618/7415', 'G/618/7420'
                ]);
            } elseif (str_contains($course->title, 'Cyber Security')) {
                $specializedUnits = $computingUnits->whereIn('btec_code', [
                    'U/618/7408', 'D/618/7417', 'E/618/7418', 'F/618/7419'
                ]);
            } else { // General Computing
                $specializedUnits = $computingUnits->whereIn('btec_code', [
                    'V/618/7409', 'W/618/7410', 'X/618/7411', 'Y/618/7412'
                ]);
            }
            $course->units()->attach($specializedUnits->pluck('id'));
        });
        
        // Business courses (Program 4: HND Business 2021)
        $businessCourses = $courses->where('program_id', 4);
        $businessUnits = Unit::whereIn('btec_code', [
            'H/618/7421', 'I/618/7422', 'J/650/2918', 'K/618/7423',
            'L/618/5036', 'M/618/7424', 'N/618/7425', 'O/618/7426',
            'P/618/7427', 'Q/618/7428', 'R/650/2920', 'S/618/7429',
            'T/618/7430', 'U/618/7431', 'V/618/7432', 'W/618/7433',
            'X/618/7434', 'Y/618/7435', 'Z/618/7436', 'A/618/7437',
            'B/618/7438', 'C/618/7439', 'D/618/7440', 'E/618/7441',
            'F/618/7442', 'G/618/7443', 'H/618/7444', 'I/618/7445',
            'J/618/7446', 'K/618/7447', 'L/618/7448', 'M/618/7449',
            'N/618/7450', 'O/618/7451'
        ])->get();
        
        // Core business units for all business courses
        $coreBusinessUnits = $businessUnits->whereIn('btec_code', [
            'H/618/7421', 'I/618/7422', 'K/618/7423', 'M/618/7424',
            'P/618/7427', 'Q/618/7428'
        ]);
        
        $businessCourses->each(function($course) use ($coreBusinessUnits, $businessUnits) {
            // All business courses get core units
            $course->units()->attach($coreBusinessUnits->pluck('id'));
            
            // Add specialized units based on course specialization
            if (str_contains($course->title, 'Accounting and Finance')) {
                $specializedUnits = $businessUnits->whereIn('btec_code', [
                    'L/618/5036', 'O/618/7426', 'R/650/2920', 'S/618/7429',
                    'T/618/7430', 'L/618/7448'
                ]);
            } elseif (str_contains($course->title, 'Management')) {
                $specializedUnits = $businessUnits->whereIn('btec_code', [
                    'U/618/7431', 'V/618/7432', 'W/618/7433', 'N/618/7450'
                ]);
            } elseif (str_contains($course->title, 'Human Resource Management')) {
                $specializedUnits = $businessUnits->whereIn('btec_code', [
                    'J/650/2918', 'A/618/7437', 'B/618/7438', 'C/618/7439'
                ]);
            } elseif (str_contains($course->title, 'Marketing')) {
                $specializedUnits = $businessUnits->whereIn('btec_code', [
                    'D/618/7440', 'E/618/7441', 'F/618/7442', 'H/618/7444'
                ]);
            } else { // General Business
                $specializedUnits = $businessUnits->whereIn('btec_code', [
                    'N/618/7425', 'X/618/7434', 'Y/618/7435', 'Z/618/7436',
                    'G/618/7443', 'O/618/7451'
                ]);
            }
            $course->units()->attach($specializedUnits->pluck('id'));
        });
        
        // L7 Extended Diploma courses (Program 5) - Advanced Management units
        $l7Courses = $courses->where('program_id', 5);
        $l7Units = $businessUnits->whereIn('btec_code', [
            'K/618/7423', 'U/618/7431', 'V/618/7432', 'N/618/7450',
            'M/618/7449', 'W/618/7433', 'G/618/7443'
        ]);
        
        $l7Courses->each(function($course) use ($l7Units) {
            // All L7 courses get advanced management units
            $course->units()->attach($l7Units->pluck('id'));
        });
    }
}


