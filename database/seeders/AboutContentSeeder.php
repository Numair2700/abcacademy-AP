<?php

namespace Database\Seeders;

use App\Models\AboutContent;
use Illuminate\Database\Seeder;

class AboutContentSeeder extends Seeder
{
    public function run(): void
    {
        $aboutContent = [
            // Mission & Vision
            [
                'section' => 'mission',
                'title' => 'Our Mission',
                'content' => 'ABC Academy is committed to providing high-quality education that empowers students to achieve their academic and professional goals. We strive to create an inclusive learning environment that fosters critical thinking, creativity, and practical skills essential for success in today\'s dynamic world.',
                'order' => 1,
            ],
            [
                'section' => 'vision',
                'title' => 'Our Vision',
                'content' => 'To be a leading educational institution recognized for excellence in applied sciences, computing, and business education, producing graduates who are well-prepared to contribute meaningfully to society and excel in their chosen fields.',
                'order' => 2,
            ],
            
            // History
            [
                'section' => 'history',
                'title' => 'Our History',
                'content' => 'Founded in 2010, ABC Academy has grown from a small educational center to a comprehensive institution offering BTEC qualifications in Applied Science, Computing, and Business. Over the years, we have built strong partnerships with industry leaders and maintained high academic standards.',
                'order' => 3,
            ],
            
            // Values
            [
                'section' => 'values',
                'title' => 'Our Values',
                'content' => 'We are guided by the principles of excellence, integrity, innovation, and inclusivity. We believe in providing equal opportunities for all students and maintaining the highest standards of academic and professional conduct.',
                'order' => 4,
            ],
            
            // Achievements
            [
                'section' => 'achievements',
                'title' => 'Our Achievements',
                'content' => 'ABC Academy has achieved numerous milestones including 95% student satisfaction rates, 90% graduate employment rates, and recognition from leading industry partners. Our students have won regional competitions and secured positions at top companies worldwide.',
                'order' => 5,
            ],
            
            // Facilities
            [
                'section' => 'facilities',
                'title' => 'Our Facilities',
                'content' => 'We boast state-of-the-art laboratories for applied sciences, modern computer labs with the latest software, well-equipped business simulation rooms, and a comprehensive library with digital resources. Our campus provides an ideal environment for learning and collaboration.',
                'order' => 6,
            ],
        ];

        foreach ($aboutContent as $content) {
            AboutContent::create($content);
        }
    }
}
