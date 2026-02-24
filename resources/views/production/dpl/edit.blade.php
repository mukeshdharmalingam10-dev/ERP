@extends('layouts.dashboard')

@section('title', 'Edit Daily Production List - ERP System')

@section('content')
    @include('production.dpl._form', ['mode' => 'edit'])
@endsection

