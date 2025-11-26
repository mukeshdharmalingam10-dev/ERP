@extends('layouts.dashboard')

@section('title', 'Designation Master - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Designation Master</h2>
        <a href="{{ route('designations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('designations.store') }}" method="POST" id="designationForm">
        @csrf
        <div style="margin-bottom: 20px;">
            <label for="department_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Department Name <span style="color: red;">*</span></label>
            <select name="department_id" id="department_id" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\"%23333\" height=\"20\" viewBox=\"0 0 24 24\" width=\"20\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 12px center; padding-right: 40px;">
                <option value="">Select</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
            @error('department_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Designation Name <span style="color: red;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="Designation Name">
            @error('name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
            <input type="text" name="description" id="description" value="{{ old('description') }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="Description">
            @error('description')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Clear
            </button>
            <button type="submit" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Submit
            </button>
            <a href="{{ route('designations.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                List
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function clearForm() {
        document.getElementById('designationForm').reset();
        // Clear any validation messages
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.remove());
    }
</script>
@endpush
@endsection

