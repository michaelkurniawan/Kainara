{{-- resources/views/admin-test/index.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Test - Pending Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Pending Artisan Submissions</h1>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if($submissions->isEmpty())
            <p>No pending submissions.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Business Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                        <tr>
                            <td>{{ $submission->id }}</td>
                            <td>{{ $submission->name }}</td>
                            <td>{{ $submission->email }}</td>
                            <td>{{ $submission->business_name }}</td>
                            <td>
                                {{-- TOMBOL DETAIL BARU --}}
                                <a href="{{ route('admin.test.show', $submission->id) }}" class="btn btn-sm btn-info">Details</a>

                                {{-- Tombol Approve & Reject (menggunakan form agar lebih aman di masa depan) --}}
                                <form action="{{ route('admin.test.approve', $submission->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <form action="{{ route('admin.test.reject', $submission->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>