@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-2xl font-bold">Edit Category</h2>

        <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
            @csrf
            @method('PUT')
            <div class="mt-4">
                <label for="name" class="block">Category Name</label>
                <input type="text" name="name" id="name" class="border px-4 py-2 w-full mt-2" value="{{ $category->name }}" required>
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Update</button>
        </form>
    </div>
@endsection
