@extends('layout')

@section('content')

<div class="fade-in">
    <div class="card">
        <div class="card-header">
            
            <h1>Welcome to ABC Academy</h1>
            <p>Empowering students through quality education and innovative learning</p>
        </div>
        <div class="card-body">
            <div class="grid grid-3 mb-4">
                <div class="text-center">
                    
                    <h3 class="section-title">Quality Programs</h3>
                    <p>Comprehensive courses designed by industry experts</p>
                </div>
                
                <div class="text-center">
                    
                    <h3 class="section-title">Expert Instructors</h3>
                    <p>Learn from experienced professionals in their fields</p>
                </div>
                
                <div class="text-center">
                   
                    <h3 class="section-title">Flexible Learning</h3>
                    <p>Study at your own pace with our modern platform</p>
                </div>
            </div>
            
            <div class="text-center">
                <a href="{{ route('programs.index') }}" class="btn btn-primary btn-lg">Explore Programs</a>
                <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">Explore Courses</a>
                <div class="mt-3">
                    @auth
                        <a href="/dashboard" class="btn btn-success">Go to Dashboard →</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-success">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}
</style>
@endsection


