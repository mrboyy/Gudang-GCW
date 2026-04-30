<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'old_values', 'new_values', 'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public static function log(string $action, string $description, $model = null, array $old = [], array $new = []): void
    {
        try {
            static::create([
                'user_id'    => auth()->id(),
                'action'     => $action,
                'model_type' => $model ? class_basename($model) : null,
                'model_id'   => $model?->id,
                'description'=> $description,
                'old_values' => $old ?: null,
                'new_values' => $new ?: null,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable) {}
    }
}
