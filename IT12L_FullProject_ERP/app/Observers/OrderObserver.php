<?php

namespace App\Observers;

use App\Models\Order;
use App\Jobs\SyncOrderToSupabase;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        SyncOrderToSupabase::dispatch($order->id);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        SyncOrderToSupabase::dispatch($order->id);
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // Optional: Sync deletion to Supabase if needed
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        SyncOrderToSupabase::dispatch($order->id);
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        // Optional: Sync force deletion to Supabase
    }
}
