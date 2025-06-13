@extends('admin.layouts.app')

@section('title', 'Category List')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-2xl font-bold">Category List</h2>
        <a href="{{ route('admin.categories.create') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">Create New Category</a>

        <!-- Example Table for listing categories -->
        <table class="min-w-full mt-6">
            <thead>
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td class="border px-4 py-2">{{ $category->id }}</td>
                        <td class="border px-4 py-2">{{ $category->name }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-blue-500">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
