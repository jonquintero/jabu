<?php

namespace App\Models;


use App\Helpers\SearchTaskHelper;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    use SearchTaskHelper;


    protected $fillable = ['name', 'user_id', 'frequency_id', 'status'];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function frequencies(): BelongsTo
    {
        return $this->belongsTo(Frequency::class, 'frequency_id', 'id');
    }

   public function scopeFilter($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        });
    }


}
