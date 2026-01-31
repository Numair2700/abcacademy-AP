@extends('layout')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Contact Us</h1>
        <p class="mb-4">Get in touch with our team</p>
    </div>

    <div class="contact-content">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <div class="contact-methods">
                <div class="contact-method">
                    <h3>📧 Email</h3>
                    <p>info@abcacademy.edu</p>
                    <p>support@abcacademy.edu</p>
                </div>
                
                <div class="contact-method">
                    <h3>📞 Phone</h3>
                    <p>+1 (555) 123-4567</p>
                    <p>Mon-Fri: 9:00 AM - 5:00 PM</p>
                </div>
                
                <div class="contact-method">
                    <h3>📍 Address</h3>
                    <p>123 Education Street<br>
                    Learning City, LC 12345<br>
                    United States</p>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <h2>Send us a Message</h2>
            <form>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</div>

<style>
.contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.contact-methods {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.contact-method {
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.contact-method h3 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.contact-method p {
    color: #7f8c8d;
    margin: 0.25rem 0;
}

.contact-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

@media (max-width: 768px) {
    .contact-content {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
