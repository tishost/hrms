@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6 text-blue-800">Dashboard</h1>

    {{-- Owner Management Section --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-blue-700">Registered Owners</h2>
            @if(auth()->user()->hasRole('super_admin'))
                <button onclick="document.getElementById('addOwnerModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    + Add Owner
                </button>
            @endif
        </div>

        <table class="table-auto w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Owner ID</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Phone</th>
                    <th class="px-4 py-2">Country</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($owners as $owner)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $owner->owner_uid }}</td>
                    <td class="px-4 py-2">{{ $owner->name }}</td>
                    <td class="px-4 py-2">{{ $owner->email }}</td>
                    <td class="px-4 py-2">{{ $owner->phone }}</td>
                    <td class="px-4 py-2">{{ $owner->country }}</td>
                    <td class="px-4 py-2">
                        <button class="text-blue-500 hover:underline" onclick="editOwner({{ $owner->id }})">Edit</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Add Owner Modal --}}
    <div id="addOwnerModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Add New Owner</h3>
            <form method="POST" action="{{ route('owners.store') }}">
                @csrf
                <input type="text" name="name" placeholder="Full Name" class="input-field mb-2" required>
                <input type="email" name="email" placeholder="Email" class="input-field mb-2" required>
                <input type="text" name="phone" placeholder="Phone" class="input-field mb-2">
                <input type="text" name="address" placeholder="Address" class="input-field mb-2">
                <select name="country" class="input-field mb-2">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}">{{ $country }}</option>
                    @endforeach
                </select>
                <input type="password" name="password" placeholder="Set Password" class="input-field mb-2" required>
                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="input-field mb-2" required>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
                    <button type="button" onclick="document.getElementById('addOwnerModal').classList.add('hidden')" class="ml-2 px-4 py-2">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .input-field {
        @apply w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500;
    }
</style>
@endpush