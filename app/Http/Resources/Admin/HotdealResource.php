<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class HotdealResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'message'=>"successfully",
            'data'=>parent::toArray($request)
        ];
    }
}
