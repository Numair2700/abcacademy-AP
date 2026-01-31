@extends('layout')

@section('title', 'About Us - ABC Academy')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">About ABC Academy</h1>
            <p class="hero-subtitle">Empowering students through quality education and innovative learning</p>
        </div>
    </div>

    <!-- Mission & Vision Section -->
    @if(isset($aboutContent['mission']) || isset($aboutContent['vision']))
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Our Foundation</h2>
         
</div>
        
        <div class="grid grid-2">
            @if(isset($aboutContent['mission']))
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🎯 {{ $aboutContent['mission']->first()->title }}</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $aboutContent['mission']->first()->content }}</p>
                </div>
            </div>
            @endif

            @if(isset($aboutContent['vision']))
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">👁️ {{ $aboutContent['vision']->first()->title }}</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $aboutContent['vision']->first()->content }}</p>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Our Team Section -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Our Team</h2>
           
        </div>
        
        <div class="grid grid-3">
            @foreach($tutors as $tutor)
            <div class="card tutor-card">
                <div class="tutor-avatar">
                    <div class="avatar-circle">
                        {{ substr($tutor->name, 0, 1) }}
                    </div>
                </div>
                <div class="card-body">
                    <h3 class="tutor-name">{{ $tutor->name }}</h3>
                    
                    <p class="tutor-qualifications">{{ $tutor->qualifications }}</p>
                    <p class="tutor-experience">{{ $tutor->experience_years }} years experience</p>
                    <p class="tutor-bio">{{ Str::limit($tutor->bio, 120) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Additional Content Sections -->
    @if(isset($aboutContent['history']) || isset($aboutContent['values']) || isset($aboutContent['achievements']) || isset($aboutContent['facilities']))
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">More About Us</h2>
           
        </div>
        
        <div class="grid grid-2">
            @if(isset($aboutContent['history']))
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📚 {{ $aboutContent['history']->first()->title }}</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $aboutContent['history']->first()->content }}</p>
                </div>
            </div>
            @endif

            @if(isset($aboutContent['values']))
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">💎 {{ $aboutContent['values']->first()->title }}</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $aboutContent['values']->first()->content }}</p>
                </div>
            </div>
            @endif

            @if(isset($aboutContent['achievements']))
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🏆 {{ $aboutContent['achievements']->first()->title }}</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $aboutContent['achievements']->first()->content }}</p>
                </div>
            </div>
            @endif

    
        </div>
    </section>
    @endif

    <!-- Call to Action -->
    <section class="section cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Start Your Journey?</h2>
            <p class="cta-subtitle">Join ABC Academy and unlock your potential</p>
            <div class="cta-buttons">
                <a href="/programs" class="btn btn-primary">Explore Programs</a>
                <a href="/courses" class="btn btn-success">View Courses</a>
            </div>
        </div>
    </section>
</div>

<style>

.hero-section {
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
    margin-bottom: 3rem;
    border-radius: 0 0 2rem 2rem;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
}

.card-text{
    font-size: 1rem;
    color: #2c3e50;
    font-weight: 500;
    margin-bottom: 1rem;
}

.section {
    margin-bottom: 4rem;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
   
}

.section-subtitle {
    font-size: 1.1rem;
    color: #7f8c8d;
    max-width: 600px;
    margin: 0 auto;
}

.grid {
    display: grid;
    gap: 2rem;
}

.grid-2 {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

.grid-3 {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.tutor-card {
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.tutor-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.tutor-avatar {
    margin-bottom: 1.5rem;
}

.avatar-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    color: white;
    margin: 0 auto;
    margin-top: 1.5rem;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.tutor-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.tutor-specialization {
    font-size: 1rem;
    color: #667eea;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.tutor-qualifications {
    font-size: 0.9rem;
    color: #7f8c8d;
    margin-bottom: 0.5rem;
    font-style: italic;
}

.tutor-experience {
    font-size: 0.9rem;
    color: #27ae60;
    font-weight: 500;
    margin-bottom: 1rem;
}

.tutor-bio {
    font-size: 0.9rem;
    color: #5a6c7d;
    line-height: 1.5;
}

.cta-section {
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    color: white;
    padding: 3rem 0;
    text-align: center;
    border-radius: 2rem;
    margin-top: 2rem;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 2rem;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-buttons .btn {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
}



@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .grid-2,
    .grid-3 {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn {
        width: 100%;
        max-width: 300px;
    }
}
</style>
@endsection
