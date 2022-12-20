<?php

namespace App\DataTransferObjects;

use App\Http\Requests\UpsertTaskRequest;
use App\Models\Frequency;

class TaskData
{
    public function __construct(public readonly string $name,
        public readonly Frequency $frequency,
        public readonly bool $status)
    {
    }

    public static function fromRequest(UpsertTaskRequest $request): self
    {
        return new static(
            $request->name,
            $request->getFrequency(),
            $request->status
        );
    }
}
