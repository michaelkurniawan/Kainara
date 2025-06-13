@extends('admin.layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-2xl font-bold">Create New User</h2>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="mt-4">
                <label for="name" class="block">Name</label>
                <input type="text" name="name" id="name" class="border px-4 py-2 w-full mt-2" required>
            </div>
            <div class="mt-4">
                <label for="email" class="block">Email</label>
                <input type="email" name="email" id="email" class="border px-4 py-2 w-full mt-2" required>
            </div>
            <div class="mt-4">
                <label for="role" class="block">Role</label>
                <select name="role" id="role" class="border px-4 py-2 w-full mt-2" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
@endsection
