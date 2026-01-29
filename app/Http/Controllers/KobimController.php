<?php

namespace App\Http\Controllers;

use App\Services\KobimService;
use App\Http\Resources\KobimResource;
use Illuminate\Http\Request;

class KobimController extends Controller
{
    protected $kobimService;

    public function __construct(KobimService $kobimService)
    {
        $this->kobimService = $kobimService;
    }

    public function index()
    {
        $kobimData = $this->kobimService->getAllKobimData();
        return KobimResource::collection($kobimData);
    }

    public function show($id)
    {
        $kobimData = $this->kobimService->getKobimDataById($id);
        if ($kobimData) {
            return new KobimResource($kobimData);
        }
        return response()->json(['message' => 'Data not found'], 404);
    }
}
