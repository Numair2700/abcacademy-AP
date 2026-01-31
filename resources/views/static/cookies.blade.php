@extends('layout')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Cookie Policy</h1>
        <p class="mb-4">Last updated: {{ date('F j, Y') }}</p>
    </div>

    <div class="cookies-content">
        <div class="cookies-section">
            <h2>What Are Cookies?</h2>
            <p>Cookies are small text files that are placed on your computer or mobile device when you visit our website. They help us provide you with a better experience by remembering your preferences and enabling certain functionality.</p>
        </div>

        <div class="cookies-section">
            <h2>How We Use Cookies</h2>
            <p>We use cookies for several purposes:</p>
            <ul>
                <li><strong>Essential Cookies:</strong> Required for the website to function properly (login, security)</li>
                <li><strong>Performance Cookies:</strong> Help us understand how visitors use our website</li>
                <li><strong>Functional Cookies:</strong> Remember your preferences and settings</li>
                <li><strong>Analytics Cookies:</strong> Help us improve our website and services</li>
            </ul>
        </div>

        <div class="cookies-section">
            <h2>Types of Cookies We Use</h2>
            <div class="cookie-types">
                <div class="cookie-type">
                    <h3>Session Cookies</h3>
                    <p>Temporary cookies that expire when you close your browser. These are essential for maintaining your login session and website functionality.</p>
                </div>
                
                <div class="cookie-type">
                    <h3>Persistent Cookies</h3>
                    <p>Cookies that remain on your device for a set period. These remember your preferences and settings for future visits.</p>
                </div>
                
                <div class="cookie-type">
                    <h3>Third-Party Cookies</h3>
                    <p>Cookies set by third-party services we use, such as analytics tools, to help us understand website usage.</p>
                </div>
            </div>
        </div>

        <div class="cookies-section">
            <h2>Managing Cookies</h2>
            <p>You can control and manage cookies in several ways:</p>
            <ul>
                <li>Use your browser settings to block or delete cookies</li>
                <li>Set your browser to notify you when cookies are being used</li>
                <li>Use browser extensions to manage cookie preferences</li>
            </ul>
            <p><strong>Note:</strong> Disabling certain cookies may affect the functionality of our website.</p>
        </div>

        <div class="cookies-section">
            <h2>Updates to This Policy</h2>
            <p>We may update this Cookie Policy from time to time. Any changes will be posted on this page with an updated revision date.</p>
        </div>

        <div class="cookies-section">
            <h2>Contact Us</h2>
            <p>If you have questions about our use of cookies, please contact us at:</p>
            <p>Email: privacy@abcacademy.edu<br>
            Address: 123 Education Street, Learning City, LC 12345</p>
        </div>
    </div>
</div>

<style>
.cookies-content {
    max-width: 800px;
    margin: 0 auto;
}

.cookies-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.cookies-section h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
}

.cookies-section p {
    color: #7f8c8d;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.cookies-section ul {
    color: #7f8c8d;
    padding-left: 1.5rem;
}

.cookies-section li {
    margin-bottom: 0.5rem;
}

.cookie-types {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.cookie-type {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 4px solid #3498db;
}

.cookie-type h3 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.cookie-type p {
    color: #7f8c8d;
    margin: 0;
}
</style>
@endsection
