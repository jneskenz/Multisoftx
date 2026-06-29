<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $iso2
 * @property string|null $phone_code
 * @property-read Collection<int, Team> $teams
 */
class Country extends Model
{
    protected $fillable = ['name', 'iso2', 'phone_code'];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
