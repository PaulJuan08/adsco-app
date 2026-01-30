@extends('layout.student')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $topic->title }}</h1>
            <p class="text-muted mb-0">
                Course: {{ $topic->course->title }} ({{ $topic->course->course_code }})
            </p>
        </div>
        <a href="{{ route('student.topics.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Content</h5>
                </div>
                <div class="card-body">
                    <div class="topic-content">
                        {!! nl2br(e($topic->content)) !!}
                    </div>
                </div>
            </div>
            
            @if($topic->attachment)
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Attachment</h5>
                </div>
                <div class="card-body">
                    <a href="{{ $topic->attachment }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-paperclip"></i> Download Attachment
                    </a>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Course:</strong><br>
                            {{ $topic->course->title }}
                        </li>
                        <li class="mb-2">
                            <strong>Code:</strong><br>
                            {{ $topic->course->course_code }}
                        </li>
                        <li class="mb-2">
                            <strong>Teacher:</strong><br>
                            {{ $topic->course->teacher->f_name }} {{ $topic->course->teacher->l_name }}
                        </li>
                        <li>
                            <strong>Order:</strong><br>
                            Topic #{{ $topic->order }}
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Navigation</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('student.assignments.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tasks me-2"></i> View All Assignments
                        </a>
                        <a href="{{ route('student.quizzes.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-question-circle me-2"></i> View All Quizzes
                        </a>
                        <a href="{{ route('student.course.show', Crypt::encrypt($topic->course_id)) }}" 
                           class="list-group-item list-group-item-action">
                            <i class="fas fa-book me-2"></i> Back to Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection