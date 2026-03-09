@extends('layouts.teacher')

@section('title', $course->title . ' — Discussion')

@section('content')
@include('discussions._board')
@endsection
