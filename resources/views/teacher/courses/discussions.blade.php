@extends('layouts.teacher')

@section('title', $course->title . ' — Discussion')

@section('content')
<div class="dashboard-container">
    <div class="breadcrumb">
        <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('teacher.courses.index') }}">Courses</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('teacher.courses.show', $encryptedId) }}">{{ Str::limit($course->title, 25) }}</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current">Discussion</span>
    </div>

    @include('discussions._board')
</div>
@endsection
