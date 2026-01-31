@extends('layout')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Help Center</h1>
        <p class="mb-4">Find answers to common questions and get support</p>
    </div>

    <div class="help-content">
        <div class="help-section">
            <h2>Getting Started</h2>
            <div class="help-item">
                <h3>How do I enroll in a course?</h3>
                <p>Browse our programs and courses, then click "Enroll" on any course you're interested in. You'll receive a confirmation email with your enrollment details.</p>
            </div>
            
            <div class="help-item">
                <h3>How do I access my enrolled courses?</h3>
                <p>After logging in, go to your Dashboard to see all your enrolled courses and track your progress.</p>
            </div>
        </div>

        <div class="help-section">
            <h2>Account Management</h2>
            <div class="help-item">
                <h3>How do I update my profile?</h3>
                <p>Click on your profile in the top navigation, then select "Edit Profile" to update your information.</p>
            </div>
            
            <div class="help-item">
                <h3>How do I change my password?</h3>
                <p>Go to your profile settings and use the "Change Password" option.</p>
            </div>
        </div>

        <div class="help-section">
            <h2>Technical Support</h2>
            <div class="help-item">
                <h3>I'm having trouble logging in</h3>
                <p>Make sure you're using the correct email and password. If you've forgotten your password, use the "Forgot Password" link on the login page.</p>
            </div>
            
            <div class="help-item">
                <h3>My course isn't loading properly</h3>
                <p>Try refreshing the page or clearing your browser cache. If the problem persists, contact our technical support team.</p>
            </div>
        </div>

        <div class="help-section">
            <h2>Still Need Help?</h2>
            <p>If you can't find the answer you're looking for, please <a href="{{ route('contact') }}">contact us</a> and we'll be happy to help!</p>
        </div>
    </div>
</div>

<style>
.help-content {
    max-width: 800px;
    margin: 0 auto;
}

.help-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.help-section h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
}

.help-item {
    margin-bottom: 1.5rem;
}

.help-item h3 {
    color: #34495e;
    margin-bottom: 0.5rem;
}

.help-item p {
    color: #7f8c8d;
    line-height: 1.6;
}
</style>
@endsection
