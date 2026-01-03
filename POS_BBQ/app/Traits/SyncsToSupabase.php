<?php

namespace App\Traits;

use App\Jobs\SyncModelToSupabase;
use Illuminate\Support\Facades\Log;

trait SyncsToSupabase
{
    public static function bootSyncsToSupabase()
    {
        static::saved(function ($model) {
            try {
                // Dispatch job to sync to Supabase
                // We pass attributes as array to avoid serializing the model connection
                SyncModelToSupabase::dispatch(
                    get_class($model),
                    $model->getAttributes(),
                    $model->getKey(),
                    'save'
                );
            } catch (\Exception $e) {
                Log::error("Error dispatching sync job for saved: " . $e->getMessage());
            }
        });

        static::deleted(function ($model) {
            try {
                SyncModelToSupabase::dispatch(
                    get_class($model),
                    [], // Attributes not needed for delete
                    $model->getKey(),
                    'delete'
                );
            } catch (\Exception $e) {
                Log::error("Error dispatching sync job for deleted: " . $e->getMessage());
            }
        });
    }
}
