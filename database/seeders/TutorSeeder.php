<?php

namespace Database\Seeders;

use App\Models\Tutor;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class TutorSeeder extends Seeder
{
    public function run(): void
    {
        // Create tutors directly without users table
        $tutors = [
            [
                'employee_id' => 'T' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'name' => 'Dr. Sarah Johnson',
                'specialization' => 'Applied Science & Biology',
                'qualifications' => 'PhD in Biology, MSc in Applied Sciences, BSc in Chemistry',
                'experience_years' => 15,
                'bio' => 'Dr. Johnson brings over 15 years of experience in applied sciences and biology. She has published numerous research papers and is passionate about making science accessible to all students.',
                'status' => 'active',
            ],
            [
                'employee_id' => 'T' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'name' => 'Prof. Michael Chen',
                'specialization' => 'Computer Science & Programming',
                'qualifications' => 'PhD in Computer Science, MSc in Software Engineering, Industry Certifications',
                'experience_years' => 12,
                'bio' => 'Professor Chen is a leading expert in computer science with extensive industry experience. He has worked with major tech companies and brings real-world knowledge to the classroom.',
                'status' => 'active',
            ],
            [
                'employee_id' => 'T' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'name' => 'Dr. Emily Rodriguez',
                'specialization' => 'Business Management & HR',
                'qualifications' => 'PhD in Business Administration, MBA, Certified HR Professional',
                'experience_years' => 18,
                'bio' => 'Dr. Rodriguez specializes in business management and human resources. She has helped numerous organizations improve their management practices and employee relations.',
                'status' => 'active',
            ],
            [
                'employee_id' => 'T' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'name' => 'Mr. David Thompson',
                'specialization' => 'Engineering & Mathematics',
                'qualifications' => 'MSc in Engineering, BEng in Mechanical Engineering, Professional Engineer',
                'experience_years' => 10,
                'bio' => 'Mr. Thompson is an experienced engineer and mathematics educator. He combines theoretical knowledge with practical engineering applications.',
                'status' => 'active',
            ],
            [
                'employee_id' => 'T' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'name' => 'Dr. Lisa Wang',
                'specialization' => 'Marketing & Digital Business',
                'qualifications' => 'PhD in Marketing, MBA, Digital Marketing Certifications',
                'experience_years' => 14,
                'bio' => 'Dr. Wang is an expert in modern marketing strategies and digital business. She helps students understand the evolving landscape of business and marketing.',
                'status' => 'active',
            ],
        ];

        foreach ($tutors as $tutorData) {
            Tutor::updateOrCreate(
                ['employee_id' => $tutorData['employee_id']],
                $tutorData
            );
        }

        // Clear existing tutor-unit relationships
        \DB::table('tutor_unit')->truncate();

        // Assign tutors to units based on their specializations
        $tutors = Tutor::all();
        
        // Dr. Sarah Johnson - Applied Science & Biology
        $sarahJohnson = $tutors->where('specialization', 'Applied Science & Biology')->first();
        if ($sarahJohnson) {
            $appliedScienceUnits = Unit::whereIn('btec_code', [
                'A/618/7388', 'B/618/7389', 'C/618/7390', 'D/618/7391',
                'E/618/7392', 'F/618/7393', 'G/618/7394', 'H/618/7395',
                'I/618/7396', 'J/618/7397', 'K/618/7398', 'L/618/7399', 'M/618/7400'
            ])->get();
            $sarahJohnson->units()->attach($appliedScienceUnits->pluck('id'));
        }

        // Prof. Michael Chen - Computer Science & Programming
        $michaelChen = $tutors->where('specialization', 'Computer Science & Programming')->first();
        if ($michaelChen) {
            $computingUnits = Unit::whereIn('btec_code', [
                'N/618/7401', 'O/618/7402', 'P/618/7403', 'Q/618/7404',
                'R/618/7405', 'S/618/7406', 'T/618/7407', 'U/618/7408',
                'V/618/7409', 'W/618/7410', 'X/618/7411', 'Y/618/7412',
                'Z/618/7413', 'A/618/7414', 'B/618/7415', 'C/618/7416',
                'D/618/7417', 'E/618/7418', 'F/618/7419', 'G/618/7420'
            ])->get();
            $michaelChen->units()->attach($computingUnits->pluck('id'));
        }

        // Dr. Emily Rodriguez - Business Management & HR
        $emilyRodriguez = $tutors->where('specialization', 'Business Management & HR')->first();
        if ($emilyRodriguez) {
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
            $emilyRodriguez->units()->attach($businessUnits->pluck('id'));
        }

        // Mr. David Thompson - Engineering & Mathematics
        $davidThompson = $tutors->where('specialization', 'Engineering & Mathematics')->first();
        if ($davidThompson) {
            // Engineering and Math units from Applied Science
            $engineeringUnits = Unit::whereIn('btec_code', [
                'E/618/7392', 'F/618/7393', 'G/618/7394', 'H/618/7395',
                'Y/618/7412', 'Z/618/7413' // Discrete Maths, Data Structures
            ])->get();
            $davidThompson->units()->attach($engineeringUnits->pluck('id'));
        }

        // Dr. Lisa Wang - Marketing & Digital Business
        $lisaWang = $tutors->where('specialization', 'Marketing & Digital Business')->first();
        if ($lisaWang) {
            // Marketing and Digital Business units
            $marketingUnits = Unit::whereIn('btec_code', [
                'I/618/7422', 'D/618/7440', 'E/618/7441', 'F/618/7442',
                'H/618/7444', 'O/618/7451' // Marketing Processes, Digital Marketing, etc.
            ])->get();
            $lisaWang->units()->attach($marketingUnits->pluck('id'));
        }
    }
}
