@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-2xl font-bold">Edit User</h2>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="mt-4">
                <label for="name" class="block">Name</label>
                <input type="text" name="name" id="name" class="border px-4 py-2 w-full mt-2" value="{{ $user->name }}" required>
            </div>
            <div class="mt-4">
                <label for="email" class="block">Email</label>
                <input type="email" name="email" id="email" class="border px-4 py-2 w-full mt-2" value="{{ $user->email }}" required>
            </div>
            <div class="mt-4">
                <label for="role" class="block">Role</label>
                <select name="role" id="role" class="border px-4 py-2 w-full mt-2" required>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Update</button>
        </form>
    </div>
@endsection
