@extends('layout')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Terms of Service</h1>
        <p class="mb-4">Last updated: {{ date('F j, Y') }}</p>
    </div>

    <div class="terms-content">
        <div class="terms-section">
            <h2>Acceptance of Terms</h2>
            <p>By accessing and using ABC Academy's website and services, you accept and agree to be bound by the terms and provision of this agreement.</p>
        </div>

        <div class="terms-section">
            <h2>Use License</h2>
            <p>Permission is granted to temporarily access ABC Academy's educational materials for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
            <ul>
                <li>Modify or copy the materials</li>
                <li>Use the materials for any commercial purpose or for any public display</li>
                <li>Attempt to reverse engineer any software contained on the website</li>
                <li>Remove any copyright or other proprietary notations from the materials</li>
            </ul>
        </div>

        <div class="terms-section">
            <h2>Enrollment Terms</h2>
            <p>By enrolling in our courses, you agree to:</p>
            <ul>
                <li>Attend classes regularly and complete assignments</li>
                <li>Maintain academic integrity and avoid plagiarism</li>
                <li>Respect other students and instructors</li>
                <li>Follow all institutional policies and procedures</li>
            </ul>
        </div>

        <div class="terms-section">
            <h2>Payment Terms</h2>
            <p>Course fees are due according to the payment schedule provided at enrollment. Late payments may result in additional fees or suspension of services.</p>
        </div>

        <div class="terms-section">
            <h2>Privacy Policy</h2>
            <p>Your privacy is important to us. Please review our <a href="{{ route('privacy') }}">Privacy Policy</a>, which also governs your use of the website.</p>
        </div>

        <div class="terms-section">
            <h2>Limitation of Liability</h2>
            <p>In no event shall ABC Academy or its suppliers be liable for any damages arising out of the use or inability to use the materials on ABC Academy's website.</p>
        </div>

        <div class="terms-section">
            <h2>Modifications</h2>
            <p>ABC Academy may revise these terms of service at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms.</p>
        </div>

        <div class="terms-section">
            <h2>Contact Information</h2>
            <p>If you have any questions about these Terms of Service, please contact us at:</p>
            <p>Email: legal@abcacademy.edu<br>
            Address: 123 Education Street, Learning City, LC 12345</p>
        </div>
    </div>
</div>

<style>
.terms-content {
    max-width: 800px;
    margin: 0 auto;
}

.terms-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.terms-section h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
}

.terms-section p {
    color: #7f8c8d;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.terms-section ul {
    color: #7f8c8d;
    padding-left: 1.5rem;
}

.terms-section li {
    margin-bottom: 0.5rem;
}
</style>
@endsection
