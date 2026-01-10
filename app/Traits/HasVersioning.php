<?php

namespace App\Traits;

use App\Models\Version;
use Illuminate\Support\Facades\Auth;

trait HasVersioning
{
    public static function bootHasVersioning()
    {
        static::updating(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();

            Version::create([
                'versionable_type' => get_class($model),
                'versionable_id'   => $model->getKey(),
                'user_id'          => Auth::id() ?? null,
                'old_data'         => $original,
                'new_data'         => array_merge($original, $changes),
                'action'           => 'update',
            ]);
        });

        static::created(function ($model) {
            Version::create([
                'versionable_type' => get_class($model),
                'versionable_id'   => $model->getKey(),
                'user_id'          => Auth::id() ?? null,
                'old_data'         => [],
                'new_data'         => $model->getAttributes(),
                'action'           => 'create',
            ]);
        });

        static::deleted(function ($model) {
            Version::create([
                'versionable_type' => get_class($model),
                'versionable_id'   => $model->getKey(),
                'user_id'          => Auth::id() ?? null,
                'old_data'         => $model->getAttributes(),
                'new_data'         => [],
                'action'           => 'delete',
            ]);
        });
    }
}
