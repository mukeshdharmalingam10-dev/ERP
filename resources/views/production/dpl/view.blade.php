@extends('layouts.dashboard')

@section('title', 'View Daily Production List - ERP System')

@section('content')
    @include('production.dpl._form', ['mode' => 'view'])
@endsection

