<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Services\InquiryService;
use Illuminate\Http\Response;

class InquiryController extends Controller
{
    protected $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    /**
     * Store a newly created inquiry.
     *
     * @return Response
     */
    public function store(StoreInquiryRequest $request)
    {
        $inquiry = $this->inquiryService->createInquiry(
            $request->nama,
            $request->phone,
            $request->kavling_id,
            $request->project_id
        );

        return (new InquiryResource($inquiry))
            ->response()
            ->setStatusCode(201);
    }
}
