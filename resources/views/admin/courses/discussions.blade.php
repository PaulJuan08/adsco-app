@extends('layouts.admin')

@section('title', $course->title . ' — Discussion')

@section('content')
<div class="dashboard-container">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.courses.index') }}">Courses</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.courses.show', $encryptedId) }}">{{ Str::limit($course->title, 25) }}</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current">Discussion</span>
    </div>

    @include('discussions._board')
</div>
@endsection
