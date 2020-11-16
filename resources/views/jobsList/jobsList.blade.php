@extends('app')

@section('title', 'job list')

@section('ownCss')
    <link href="{{asset('css/jobsList.css')}}" rel="stylesheet">
    <title></title>
@endsection

@section('content')
    <job-list-main-component></job-list-main-component>
@endsection
