<?php

namespace App\Services;

use App\Repositories\TrainingRepository;

class TrainingService
{

    protected $repository;

    public function __construct(TrainingRepository $trainingRepository)
    {
        $this->repository = $trainingRepository;
        
    }


    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getPlaningAll()
    {
        return $this->repository->getPlaningAll();
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function incrementViewCount($id)
    {
        return $this->repository->viewCount($id);
    }

    public function getRecentTrainings($last)
    {
        return $this->repository->getRecentTrainings($last);
    }

}