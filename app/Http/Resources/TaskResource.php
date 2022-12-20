<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status ? 'completed' : 'pending',
            'relationships' => [
                'user'  => UserResource::make($this->user),
                'frequency'  => FrequencyResource::make($this->frequencies),
          ],
            'links' => [
               'self' => route('tasks.show', ['task' => $this->id]),
            ],
        ];
    }
}
