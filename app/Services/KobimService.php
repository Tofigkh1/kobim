<?php

namespace App\Services;

use App\Repositories\KobimRepository;

class KobimService
{
    protected $kobimRepository;

    public function __construct(KobimRepository $kobimRepository)
    {
        $this->kobimRepository = $kobimRepository;
    }

    public function getAllKobimData()
    {
        return $this->kobimRepository->getAll();
    }

    public function getKobimDataById($id)
    {
        return $this->kobimRepository->getById($id);
    }

}
