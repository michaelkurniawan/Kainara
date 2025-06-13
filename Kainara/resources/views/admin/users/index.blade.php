@extends('admin.layouts.app')

@section('title', 'User List')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-2xl font-bold">User List</h2>
        <a href="{{ route('admin.users.create') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">Create New User</a>

        <!-- Example Table for listing users -->
        <table class="min-w-full mt-6">
            <thead>
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Role</th>
                    <th class="border px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="border px-4 py-2">{{ $user->id }}</td>
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2">{{ $user->role }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-500">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
