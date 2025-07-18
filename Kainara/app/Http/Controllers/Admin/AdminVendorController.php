<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Http\Requests\Admin\StoreVendorRequest;
use App\Http\Requests\Admin\UpdateVendorRequest;
use Illuminate\Support\Facades\DB;

class AdminVendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vendor::query();

        // Logika pencarian (tetap ada)
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->where(DB::raw('LOWER(name)'), 'like', '%' . $search . '%')
                  ->orWhere(DB::raw('LOWER(email)'), 'like', '%' . $search . '%')
                  ->orWhere(DB::raw('LOWER(business_type)'), 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan status persetujuan DIHAPUS karena semua dianggap disetujui

        $vendors = $query->orderBy('created_at', 'desc')->paginate(10);
        $vendors->appends($request->query());

        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVendorRequest $request)
    {
        $validatedData = $request->validated();

        try {
            Vendor::create($validatedData);
            return redirect()->route('admin.vendors.index')->with('success', 'Vendor created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create vendor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        return view('admin.vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        return view('admin.vendors.update', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        $validatedData = $request->validated();

        try {
            $vendor->update($validatedData);
            return redirect()->route('admin.vendors.index')->with('success', 'Vendor updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update vendor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();
            return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete vendor: ' . $e->getMessage());
        }
    }
}