<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function storeData(Request $request)
    {
        $data = [
            'status' => 'active',
            'device_id' => 'device-001',
        ];

        $this->firebase->setData('/device/status', $data);

        return redirect()->back()->with('message', 'Data sent to Firebase successfully!');
    }

    public function getData()
    {
        $data = $this->firebase->getData('/device/status');
        return response()->json($data);
    }
}
