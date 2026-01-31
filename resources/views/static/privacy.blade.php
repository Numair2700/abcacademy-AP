@extends('layout')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Privacy Policy</h1>
        <p class="mb-4">Last updated: {{ date('F j, Y') }}</p>
    </div>

    <div class="privacy-content">
        <div class="privacy-section">
            <h2>Information We Collect</h2>
            <p>We collect information you provide directly to us, such as when you create an account, enroll in courses, or contact us for support.</p>
            <ul>
                <li>Personal information (name, email address, phone number)</li>
                <li>Academic information (enrollment records, course progress)</li>
                <li>Account credentials (username, password)</li>
                <li>Communication records (support requests, feedback)</li>
            </ul>
        </div>

        <div class="privacy-section">
            <h2>How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Provide and maintain our educational services</li>
                <li>Process enrollments and manage your account</li>
                <li>Send important updates about your courses</li>
                <li>Provide customer support</li>
                <li>Improve our services and develop new features</li>
            </ul>
        </div>

        <div class="privacy-section">
            <h2>Information Sharing</h2>
            <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except:</p>
            <ul>
                <li>To comply with legal obligations</li>
                <li>To protect our rights and safety</li>
                <li>With trusted service providers who assist in our operations</li>
            </ul>
        </div>

        <div class="privacy-section">
            <h2>Data Security</h2>
            <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
        </div>

        <div class="privacy-section">
            <h2>Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access your personal information</li>
                <li>Correct inaccurate information</li>
                <li>Request deletion of your information</li>
                <li>Opt out of certain communications</li>
            </ul>
        </div>

        <div class="privacy-section">
            <h2>Contact Us</h2>
            <p>If you have questions about this Privacy Policy, please contact us at:</p>
            <p>Email: privacy@abcacademy.edu<br>
            Address: 123 Education Street, Learning City, LC 12345</p>
        </div>
    </div>
</div>

<style>
.privacy-content {
    max-width: 800px;
    margin: 0 auto;
}

.privacy-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.privacy-section h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
}

.privacy-section p {
    color: #7f8c8d;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.privacy-section ul {
    color: #7f8c8d;
    padding-left: 1.5rem;
}

.privacy-section li {
    margin-bottom: 0.5rem;
}
</style>
@endsection
