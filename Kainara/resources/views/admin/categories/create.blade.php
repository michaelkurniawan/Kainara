@extends('admin.layouts.app')

@section('title', 'Create Category')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-2xl font-bold">Create New Category</h2>

        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="mt-4">
                <label for="name" class="block">Category Name</label>
                <input type="text" name="name" id="name" class="border px-4 py-2 w-full mt-2" required>
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
@endsection
