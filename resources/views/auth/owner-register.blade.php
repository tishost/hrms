@extends('layouts.front')

@section('title', 'Owner Registration')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
    <h2 class="text-3xl font-bold text-center text-blue-700 mb-6">Register as a Building Owner</h2>

    @if(session('success'))
        <div class="mb-4 text-green-600 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('owner.register') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="name" placeholder="Full Name" class="input-field" required>
            <input type="email" name="email" placeholder="Email Address" class="input-field" required>
            <input type="text" name="phone" placeholder="Phone Number" class="input-field">
            <input type="text" name="address" placeholder="Street Address" class="input-field">
        </div>

       <div class="mt-4">
        <label for="country-select" class="block text-sm font-medium mb-1">Country</label>
        <select id="country-select" name="country" class="w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Select Country</option>
            @foreach($countries as $country)
                <option value="{{ $country }}">{{ $country }}</option>
            @endforeach
        </select>
        </div>

       <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    <input type="password" name="password" placeholder="Set Your Password" class="input-field" required>
    <input type="password" name="password_confirmation" placeholder="Confirm Password" class="input-field" required>
    </div>

        <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded shadow font-semibold transition">
            Complete Registration
        </button>
    </form>
</div>
@endsection

@push('styles')
<style>
    .input-field {
        @apply w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition;
    }
</style>
@endpush