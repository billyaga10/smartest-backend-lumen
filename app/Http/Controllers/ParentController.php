<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function children()
    {
        return response()->json([
            [
                'id' => 'std_001',
                'name' => 'Ahmad Faiz Al-Ghifari',
                'nisn' => '0087462819',
                'className' => 'XII MIPA 1',
                'avatarUrl' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400',
            ],
            [
                'id' => 'std_002',
                'name' => 'Fatimah Az-Zahra',
                'nisn' => '0091238472',
                'className' => 'X IPS 2',
                'avatarUrl' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400',
            ],
        ]);
    }
}
