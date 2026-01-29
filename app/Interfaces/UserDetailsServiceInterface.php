<?php

namespace App\Interfaces;

interface UserDetailsServiceInterface
{
    public  function getUserDetailsByVoen(string $voen): array;

    public  function getUserDetailsByFin(string $fin): array;
}
