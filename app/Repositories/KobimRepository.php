<?php

namespace App\Repositories;

use App\Models\Kobim;
use App\Models\SmeInformation;

class KobimRepository
{
    public function getAll()
    {
        return SmeInformation::all();
    }

    public function getById($id)
    {
        return SmeInformation::find($id);
    }

}
