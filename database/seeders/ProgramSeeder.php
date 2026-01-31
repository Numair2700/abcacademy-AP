<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'title' => 'L2 Applied Science',
                'description' => 'Level 2 Applied Science program focusing on fundamental scientific principles and practical laboratory skills. Students will develop a strong foundation in biology, chemistry, and physics while gaining hands-on experience in scientific research methods.',
                'qualification_level' => 'Certificate',
                'published' => true,
            ],
            [
                'title' => 'L3 Applied Science',
                'description' => 'Level 3 Applied Science program building upon Level 2 foundations with advanced scientific concepts and specialized laboratory techniques. Students will engage in independent research projects and develop critical thinking skills essential for higher education.',
                'qualification_level' => 'Certificate',
                'published' => true,
            ],
            [
                'title' => 'HND Computing 2022',
                'description' => 'Higher National Diploma in Computing covering software development, database management, networking, and cybersecurity. Students will gain practical skills in programming languages, system analysis, and IT project management.',
                'qualification_level' => 'Diploma',
                'published' => true,
            ],
            [
                'title' => 'HND Business 2021',
                'description' => 'Higher National Diploma in Business providing comprehensive knowledge in business management, marketing, finance, and human resources. Students will develop leadership skills and business acumen through real-world case studies and projects.',
                'qualification_level' => 'Diploma',
                'published' => true,
            ],
            [
                'title' => 'L7 Extended Diploma',
                'description' => 'Level 7 Extended Diploma offering advanced studies in specialized fields with emphasis on research, innovation, and professional development. This program prepares students for senior roles in their chosen industry.',
                'qualification_level' => 'Degree',
                'published' => true,
            ],
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}
