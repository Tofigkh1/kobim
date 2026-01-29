<?php

namespace App\Http\Controllers;

use App\Services\ApplicationStoreClient;
use App\Interfaces\UserDetailsServiceInterface;
use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    protected $applicationStoreClient;
    protected $userDetailsService;

    public function __construct(ApplicationStoreClient $applicationStoreClient, UserDetailsServiceInterface $userDetailsService)
    {
        $this->applicationStoreClient = $applicationStoreClient;
        $this->userDetailsService = $userDetailsService;
    }

    /**
     * Store application data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $userFin = $data['fin'] ?? null;
        $trainingId = $data['training_id'] ?? null;

        if (!$userFin || !$trainingId) {
            return response()->json([
                'success' => false,
                'statusCode' => 400,
                'message' => 'Qeydiyyat zamanı xəta baş verdi.',
            ], 400);
        }

        // İstifadəçinin artıq həmin təlimə müraciət edib-etmədiyini yoxlayırıq
        $existingApplication = Application::where('fin', $userFin)
            ->where('training_id', $trainingId)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'statusCode' => 409,
                'message' => 'Siz artıq bu təlimə müraciət etmisiniz.',
            ], 409);
        }

        // İstifadəçi məlumatlarını əldə edirik
        $userDetails = $this->userDetailsService->getUserDetailsByFin($userFin);

        if (empty($userDetails) || (isset($userDetails['statusCode']) && $userDetails['statusCode'] == 417)) {
            return response()->json([
                'success' => false,
                'message' => 'İstifadəçi məlumatları tapılmadı.',
            ], 404);
        }

        // ApplicationStoreClient-ə fin məlumatlarını ötürmək
        $application = $this->applicationStoreClient->store($data, $userDetails);


        if ($application) {
            return response()->json([
                'success' => true,
                'message' => 'Müraciətiniz uğurla yaradıldı.',
                'data' => $application,
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Müraciətiniz uğursuz oldu, zəhmət olmasa yenidən yoxlayın.',
        ], 500);
    }
}
