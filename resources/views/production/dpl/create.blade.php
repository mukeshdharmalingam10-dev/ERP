@extends('layouts.dashboard')

@section('title', 'Create Daily Production List - ERP System')

@section('content')
    @include('production.dpl._form', ['mode' => 'create'])
@endsection

