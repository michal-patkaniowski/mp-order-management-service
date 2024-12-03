<?php

declare(strict_types=1);

namespace App\Service;

interface ExternalApiServiceInterface
{
    public function fetchData($url): array;
}
