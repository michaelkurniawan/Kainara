<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArtisanRegistrationController extends Controller
{
    //
    public function showForm()
    {
        $provinces = [
            'Aceh', 'Bali', 'Banten', 'Bengkulu', 'Gorontalo', 'DKI Jakarta', 'Jambi',
            'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Kalimantan Barat', 'Kalimantan Selatan',
            'Kalimantan Tengah', 'Kalimantan Timur', 'Kalimantan Utara', 'Kepulauan Bangka Belitung',
            'Kepulauan Riau', 'Lampung', 'Maluku', 'Maluku Utara', 'Nusa Tenggara Barat',
            'Nusa Tenggara Timur', 'Papua', 'Papua Barat', 'Papua Barat Daya', 'Papua Pegunungan',
            'Papua Selatan', 'Papua Tengah', 'Riau', 'Sulawesi Barat', 'Sulawesi Selatan',
            'Sulawesi Tengah', 'Sulawesi Tenggara', 'Sulawesi Utara', 'Sumatera Barat',
            'Sumatera Selatan', 'Sumatera Utara', 'DI Yogyakarta'
        ];
        sort($provinces); // Urutkan berdasarkan abjad

        return view('artisan.form', [
            'provinces' => $provinces
        ]);
    }

}

