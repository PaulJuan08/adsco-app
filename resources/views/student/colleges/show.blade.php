@extends('layouts.student')

@section('title', $college->college_name . ' - Student Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Header with Back Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('student.colleges.index') }}" class="btn btn-outline" style="padding: 0.5rem 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Colleges
        </a>
    </div>

    <!-- College Profile Card -->
    <div class="form-container" style="max-width: 1000px; margin: 0 auto;">
        <div class="card-header" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
            <div class="card-title-group">
                <i class="fas fa-university card-icon"></i>
                <h2 class="card-title">College Details</h2>
            </div>
        </div>
        
        <div class="card-body">
            <!-- College Avatar & Basic Info -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 700; margin: 0 auto 1rem; border: 4px solid white; box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);">
                    {{ strtoupper(substr($college->college_name, 0, 1)) }}
                </div>
                <h3 style="font-size: 1.8rem; font-weight: 800; color: #1a202c; margin-bottom: 0.25rem;">{{ $college->college_name }}</h3>
                
                <div style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 0.5rem;">
                    <span class="badge badge-success" style="background: #d1fae5; color: #059669; padding: 0.5rem 1.25rem; border-radius: 50px; font-size: 0.9rem;">
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                </div>
            </div>
            
            <!-- Statistics Grid -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin: 2rem 0;">
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center; border: 1px solid #e5e7eb;">
                    <div style="font-size: 2rem; font-weight: 700; color: #4f46e5;">{{ $college->programs_count }}</div>
                    <div style="font-size: 0.9rem; color: #6b7280;">Programs Offered</div>
                </div>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center; border: 1px solid #e5e7eb;">
                    <div style="font-size: 2rem; font-weight: 700; color: #10b981;">{{ $college->students_count }}</div>
                    <div style="font-size: 0.9rem; color: #6b7280;">Enrolled Students</div>
                </div>
            </div>
            
            <!-- College Information -->
            @if($college->description)
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h4 style="font-size: 1.1rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #4f46e5; margin-right: 0.5rem;"></i>
                    About the College
                </h4>
                <p style="color: #4b5563; line-height: 1.6;">{{ $college->description }}</p>
            </div>
            @endif
            
            <!-- Year Levels Available -->
            @if($college->college_year)
            <div style="margin-bottom: 2rem;">
                <h4 style="font-size: 1.1rem; font-weight: 600; color: #2d3748; margin-bottom: 1rem;">
                    <i class="fas fa-calendar-alt" style="color: #4f46e5; margin-right: 0.5rem;"></i>
                    Year Levels Available
                </h4>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach(explode(',', $college->college_year) as $year)
                        @if(trim($year))
                            <span style="background: #f3f4f6; padding: 0.5rem 1rem; border-radius: 999px; font-size: 0.9rem; color: #4b5563; border: 1px solid #e5e7eb;">
                                {{ trim($year) }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Programs Section -->
            <div>
                <h4 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 1.5rem; border-bottom: 2px solid #edf2f7; padding-bottom: 0.75rem;">
                    <i class="fas fa-graduation-cap" style="color: #4f46e5; margin-right: 0.5rem;"></i>
                    Degree Programs ({{ $programs->count() }})
                </h4>
                
                @if($programs->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="empty-title">No programs available</h3>
                        <p class="empty-text">This college doesn't have any active programs at the moment.</p>
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                        @foreach($programs as $program)
                        @php
                            $programEncryptedId = Crypt::encrypt($program->id);
                        @endphp
                        <div class="program-card" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.25rem; transition: all 0.2s ease; cursor: pointer;" onclick="window.location.href='{{ route('student.programs.show', $programEncryptedId) }}'">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                    {{ strtoupper(substr($program->program_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h5 style="font-weight: 600; color: #1f2937; margin: 0;">{{ $program->program_name }}</h5>
                                    @if($program->program_code)
                                    <p style="font-size: 0.85rem; color: #4f46e5; margin: 0.25rem 0 0;">
                                        <i class="fas fa-code"></i> {{ $program->program_code }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @if($program->description)
                            <p style="color: #6b7280; font-size: 0.9rem; line-height: 1.5; margin-bottom: 0.5rem;">
                                {{ Str::limit($program->description, 80) }}
                            </p>
                            @endif
                            <div style="display: flex; justify-content: flex-end;">
                                <span style="color: #4f46e5; font-size: 0.9rem;">
                                    View Details <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="message-success" style="margin-top: 2rem;">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="message-error" style="margin-top: 2rem;">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
    </footer>
</div>
@endsection