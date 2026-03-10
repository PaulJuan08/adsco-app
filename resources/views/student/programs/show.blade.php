@extends('layouts.student')

@section('title', $program->program_name . ' - Student Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('student.programs.index') }}" class="btn btn-outline" style="padding: 0.5rem 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Programs
        </a>
    </div>

    <div class="form-container" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header" style="background: linear-gradient(135deg, #059669, #10b981);">
            <div class="card-title-group">
                <i class="fas fa-graduation-cap card-icon"></i>
                <h2 class="card-title">Program Details</h2>
            </div>
        </div>

        <div class="card-body">
            <!-- Program Avatar & Name -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #059669, #10b981); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 700; margin: 0 auto 1rem; border: 4px solid white; box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);">
                    {{ strtoupper(substr($program->program_name, 0, 1)) }}
                </div>
                <h3 style="font-size: 1.8rem; font-weight: 800; color: #1a202c; margin-bottom: 0.25rem;">{{ $program->program_name }}</h3>
                @if($program->program_code)
                <p style="font-size: 1rem; color: #059669; font-weight: 600;"><i class="fas fa-code"></i> {{ $program->program_code }}</p>
                @endif
                <div style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 0.5rem;">
                    <span style="background: #d1fae5; color: #059669; padding: 0.4rem 1.1rem; border-radius: 50px; font-size: 0.9rem; font-weight: 500;">
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                </div>
            </div>

            <!-- Stats -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center; border: 1px solid #e5e7eb;">
                    <div style="font-size: 2rem; font-weight: 700; color: #059669;">{{ number_format($program->students_count) }}</div>
                    <div style="font-size: 0.9rem; color: #6b7280;">Enrolled Students</div>
                </div>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center; border: 1px solid #e5e7eb;">
                    <div style="font-size: 2rem; font-weight: 700; color: #4f46e5;">
                        <i class="fas fa-university" style="font-size: 1.5rem;"></i>
                    </div>
                    <div style="font-size: 0.9rem; color: #6b7280;">{{ $program->college->college_name ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Description -->
            @if($program->description)
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h4 style="font-size: 1.1rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #059669; margin-right: 0.5rem;"></i>
                    About This Program
                </h4>
                <p style="color: #4b5563; line-height: 1.6; margin: 0;">{{ $program->description }}</p>
            </div>
            @endif

            <!-- Program Info -->
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h4 style="font-size: 1.1rem; font-weight: 600; color: #2d3748; margin-bottom: 1rem;">
                    <i class="fas fa-list" style="color: #059669; margin-right: 0.5rem;"></i>
                    Program Information
                </h4>
                <div style="display: grid; gap: 0.75rem;">
                    @if($program->program_code)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="font-weight: 500; color: #4b5563;"><i class="fas fa-code" style="margin-right: 0.5rem; color: #9ca3af;"></i>Program Code</span>
                        <span style="color: #1f2937;">{{ $program->program_code }}</span>
                    </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="font-weight: 500; color: #4b5563;"><i class="fas fa-university" style="margin-right: 0.5rem; color: #9ca3af;"></i>College</span>
                        <span style="color: #1f2937;">{{ $program->college->college_name ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                        <span style="font-weight: 500; color: #4b5563;"><i class="fas fa-calendar-alt" style="margin-right: 0.5rem; color: #9ca3af;"></i>Established</span>
                        <span style="color: #1f2937;">{{ $program->created_at->format('Y') }}</span>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="message-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="message-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
