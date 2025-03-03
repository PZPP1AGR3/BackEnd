<?php

namespace App\Http\Resources;

use App\Enum\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $role
 * @property string $created_at
 * @property string $updated_at
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /** @var int $id */
            'id' => $this->id,
            /** @var string $name */
            'name' => $this->name,
            /**
             * @var Role $role
             */
            'role' => $this->role,
            /** @var string $created_at */
            'created_at' => $this->created_at,
            /** @var string $updated_at */
            'updated_at' => $this->updated_at,
        ];
    }
}
