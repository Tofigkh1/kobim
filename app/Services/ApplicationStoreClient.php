<?php

namespace App\Services;

use App\Models\Application;
use App\Models\EmployeeTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationStoreClient 
{
    /**
     * Store application data from external API.
     *
     * @param array $data
     * @param array $userDetails
     * @return Application|null
     */
    public function store(array $data, array $userDetails)
    {
        DB::beginTransaction(); // Transaction başlayırıq
        try {
            // Validate required fields and merge with user details
            $validatedData = $this->validateData($data, $userDetails);
            
            // Create and store application data
            $application = Application::create($validatedData);

            // Validate and store EmployeeTask with application_id
            $validateDataTask = $this->validateDataTask($data, $application->id);
            $employeeTask = EmployeeTask::create($validateDataTask);

            DB::commit(); // Əməliyyat uğurla başa çatdığı üçün commit edirik

            return $application;
        } catch (\Exception $e) {
            DB::rollBack(); // Xəta olduqda rollback edirik

            // Log the error for debugging
            Log::error('Error storing application data: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Validate and map input data to match the Application model's fillable fields.
     *
     * @param array $data
     * @param array $userDetails
     * @return array
     */
    protected function validateData(array $data, array $userDetails): array
    {
        return [
            "voen" => $data['voen'] ?? null,
            "fin" => $data['fin'] ?? null,
            "voenPersonName" => $data['voenPersonName'] ?? null,
            "voenMeyar" => $data['voenMeyar'] ?? null,
            "voenAddress" => $data['voenAddress'] ?? null,
            "voenActivityName" => $data['voenActivityName'] ?? null,
            "voenFieldActivity" => $data['voenFieldActivity'] ?? null,
            "voenContactInfo" => $data['voenContactInfo'] ?? null,
            "fieldActivity" => $data['fieldActivity'] ?? 88888,
            "fieldWantAct" => $data['fieldWantAct'] ?? null,
            "otherFieldActivity" => $data['otherFieldActivity'] ?? null,
            "employeeType" => $data['employeeType'] ?? 1,
            "fullName" => $userDetails['Name'] . " " . $userDetails['Surname'] . " " . $userDetails['FatherName'],
            "actualCity" => null,
            "actualAddress" => $userDetails['RegistrationAddress'] ?? null,
            "education" => $data['education'] ?? null,
            "duty" => $data['duty'] ?? null,
            "accepted_user_id" =>$data['user_id']?? null,
            "contactNumber" => $data['contactNumber'] ?? null,
            "contactEmail" => $data['contactEmail'] ?? null,
            "city" => isset($userDetails['RegistrationAddress']) ? explode(',', $userDetails['RegistrationAddress'])[0] : null,
            "address" => $userDetails['RegistrationAddress'] ?? null,
            "employeeCount" => $data['employeeCount'] ?? null,
            "finPlaceOfBirth" => $userDetails['PlaceOfBirth'] ?? null,
            "finRegistrationAddress" => $userDetails['RegistrationAddress'] ?? null,
            "finbirthday" => $userDetails['Birthday'] ?? null,
            "training_id" => $data['training_id'] ?? null,
            "serviceType" => $data['serviceType'] ?? null,
            "finGender" => ($userDetails['Gender'] ?? '') === 'M' ? 'Kişi' : 'Qadın',
            "finAge" => $this->calculateAge($userDetails['Birthday'] ?? null),
        ];
    }

    /**
     * Validate and map input data for EmployeeTask.
     *
     * @param array $data
     * @param int $applicationId
     * @return array
     */
    protected function validateDataTask(array $data, int $applicationId): array
    {
        return [
            "status" => 0,
            "serviceForm" => 'telim',  // Statik olaraq "telim" yazılıb, dinamik olanda dəyişdirilməlidir
            "serviceType" => 1,        // Statik olaraq 1 qeyd edilib, dinamik olanda dəyişdirilməlidir
            "user_id" => $data['user_id'] ?? null,
            "application_id" => $applicationId, // Burada Application-in id-si əlavə edilir
            "training_id" => $data['training_id'] ?? null,
        ];
    }

    /**
     * Calculate age from birthdate.
     *
     * @param string|null $birthday
     * @return int|null
     */
    protected function calculateAge(?string $birthday): ?int
    {
        if (!$birthday) return null;
        return now()->diffInYears(\Carbon\Carbon::parse($birthday));
    }
}
