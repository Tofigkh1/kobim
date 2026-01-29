<?php

namespace App\Http\Controllers;

use App\Http\Resources\TrainingResource;
use App\Services\TrainingService;
use Illuminate\Http\Request;

class TrainingController extends Controller
{


    protected $trainingService;

    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function getPlaning()
    {
        $trainings = $this->trainingService->getPlaningAll();
        return TrainingResource::collection($trainings);
    }


    public function getPlaningById($id)
    {
        $training = $this->trainingService->getById($id);
        return new TrainingResource($training);
    }

    public function incrementCount($id)
    {
        $count = $this->trainingService->incrementViewCount($id);
        return response()->json(['message' => 'Count incremented', 'count' => $count], 200);
    }

    public function getLastPlaning($last='2')
    {
        $trainings = $this->trainingService->getRecentTrainings($last);

        return TrainingResource::collection($trainings);
    }



}
