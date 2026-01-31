@extends('layout')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Frequently Asked Questions</h1>
        <p class="mb-4">Find quick answers to common questions</p>
    </div>

    <div class="faq-content">
        <div class="faq-section">
            <h2>General Questions</h2>
            
            <div class="faq-item">
                <h3>What is ABC Academy?</h3>
                <p>ABC Academy is a leading educational institution offering BTEC qualifications in Applied Science, Computing, and Business. We provide high-quality education with experienced instructors and modern learning facilities.</p>
            </div>
            
            <div class="faq-item">
                <h3>What qualifications do you offer?</h3>
                <p>We offer Level 2 and Level 3 Applied Science certificates, HND Computing and Business diplomas, and Level 7 Extended Diplomas in Strategic Management and Leadership.</p>
            </div>
            
            <div class="faq-item">
                <h3>How long are the programs?</h3>
                <p>Program duration varies by level: Certificate programs typically take 1 year, Diploma programs take 2 years, and Extended Diplomas take 1-2 years depending on the specialization.</p>
            </div>
        </div>

        <div class="faq-section">
            <h2>Enrollment & Courses</h2>
            
            <div class="faq-item">
                <h3>How do I enroll in a course?</h3>
                <p>Browse our programs and courses, select the one you're interested in, and click the "Enroll" button. You'll receive confirmation and enrollment details via email.</p>
            </div>
            
            <div class="faq-item">
                <h3>Can I enroll in multiple courses?</h3>
                <p>Yes, you can enroll in multiple courses as long as they don't have conflicting schedules. Check the course details for specific requirements.</p>
            </div>
            
            <div class="faq-item">
                <h3>What if I need to withdraw from a course?</h3>
                <p>You can unenroll from courses through your dashboard. Please note that withdrawal policies may apply depending on the timing and course requirements.</p>
            </div>
        </div>

        <div class="faq-section">
            <h2>Technical Support</h2>
            
            <div class="faq-item">
                <h3>I forgot my password. How do I reset it?</h3>
                <p>Click "Forgot Password" on the login page and enter your email address. You'll receive instructions to reset your password.</p>
            </div>
            
            <div class="faq-item">
                <h3>I'm having trouble accessing my courses</h3>
                <p>Try refreshing your browser or clearing your cache. If the problem persists, contact our technical support team at support@abcacademy.edu.</p>
            </div>
        </div>

        <div class="faq-section">
            <h2>Still Have Questions?</h2>
            <p>If you can't find the answer you're looking for, please visit our <a href="{{ route('help') }}">Help Center</a> or <a href="{{ route('contact') }}">contact us</a> directly.</p>
        </div>
    </div>
</div>

<style>
.faq-content {
    max-width: 800px;
    margin: 0 auto;
}

.faq-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.faq-section h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
}

.faq-item {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #ecf0f1;
}

.faq-item:last-child {
    border-bottom: none;
}

.faq-item h3 {
    color: #34495e;
    margin-bottom: 0.5rem;
}

.faq-item p {
    color: #7f8c8d;
    line-height: 1.6;
}
</style>
@endsection
