<?php

namespace App\Repositories;


use App\Models\Training;
use Illuminate\Database\Eloquent\Collection;

class TrainingRepository
{
    protected  $model;
    public function __construct(Training $trainingModel)
    {
        $this->model = $trainingModel;
    }


    public function getAll() :Collection
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        return $this->model->with('executive')->where('status','3')->where('id', $id)->firstOrFail();
    }

    public function getPlaningAll()
    {
        return $this->model->with('executive')->where('status',3)->orderBy('created_at','desc')->get();
    }

    public function viewCount($id)
    {
        $training = $this->model->find($id);
    
        if ($training) {
            $training->increment('viewCount');
            return $training->fresh()->viewCount; 
        }
        return null; 
    }

    public function getRecentTrainings($last=2)
    {
        return $this->model->with('executive')
            ->where('status', 3)
            ->where(function($query) {
                $query->where('date', '>', now()->toDateString())
                      ->orWhere(function($query) {
                          $query->where('date', '=', now()->toDateString())
                                ->where('hour', '>=', now()->format('H:i'));
                      });
            })
            ->orderBy('date', 'asc')
            ->orderBy('hour', 'asc')
            ->take($last)
            ->get();
    }
    
    

}
