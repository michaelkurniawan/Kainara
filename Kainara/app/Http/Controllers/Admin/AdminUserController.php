<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Admin\AdminUserStoreRequest;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query(); // Mulai query

        // Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->input('search');
            $searchLower = strtolower($search); // <--- Konversi input pencarian ke lowercase

            $query->where(function($q) use ($searchLower) {
                $q->where(DB::raw("LOWER(CONCAT(first_name, ' ', last_name))"), 'like', '%' . $searchLower . '%');
                
                $q->orWhere(DB::raw('LOWER(first_name)'), 'like', '%' . $searchLower . '%');
                $q->orWhere(DB::raw('LOWER(last_name)'), 'like', '%' . $searchLower . '%');
                $q->orWhere(DB::raw('LOWER(email)'), 'like', '%' . $searchLower . '%');
            });
        }

        $users = $query->paginate(10);

        $users->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminUserStoreRequest $request)
    {
        $validatedData = $request->validated();

        User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'dob' => $validatedData['dob'] ?? null,
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
