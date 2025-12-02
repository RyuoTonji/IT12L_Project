<?php

namespace App\Traits;

use App\Models\DeletedData;
use Illuminate\Support\Facades\Auth;

trait LogsDeletes
{
    public static function bootLogsDeletes()
    {
        static::deleted(function ($model) {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            DeletedData::create([
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
                'data' => $model->toArray(),
                'deleted_at' => now(),
                'deleted_by' => Auth::id(),
            ]);
        });
    }
}
