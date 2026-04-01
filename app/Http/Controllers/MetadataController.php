<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReferenceDataService;
use Illuminate\Http\JsonResponse;

class MetadataController extends Controller
{
    public function __construct(
        private ReferenceDataService $referenceDataService
    ) {
    }

    public function countries(): JsonResponse
    {
        return response()->json([
            'data' => $this->referenceDataService->countries(),
        ]);
    }

    public function languages(): JsonResponse
    {
        return response()->json([
            'data' => $this->referenceDataService->languages(),
        ]);
    }

    public function phoneCountries(): JsonResponse
    {
        return response()->json([
            'data' => $this->referenceDataService->phoneCountries(),
        ]);
    }
}
