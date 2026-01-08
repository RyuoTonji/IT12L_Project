<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncOrderToSupabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::with('items')->find($this->orderId);

        if (!$order) {
            Log::error("Order not found for sync: {$this->orderId}");
            return;
        }

        try {
            DB::connection('supabase')->transaction(function () use ($order) {
                // Upsert Order
                DB::connection('supabase')->table('crm_orders')->updateOrInsert(
                    ['id' => $order->id],
                    [
                        'user_id' => $order->user_id,
                        'branch_id' => $order->branch_id,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                        'notes' => $order->notes,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ]
                );

                foreach ($order->items as $item) {
                    DB::connection('supabase')->table('crm_order_items')->updateOrInsert(
                        ['id' => $item->id, 'order_id' => $item->order_id],
                        [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name,
                            'product_image' => $item->product_image,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'menu_item_id' => $item->menu_item_id,
                            'subtotal' => $item->subtotal,
                            'created_at' => $item->created_at,
                            'updated_at' => $item->updated_at,
                        ]
                    );
                }
            });

            // Mark as synced locally
            $order->update(['is_synced' => true]);
            $order->items()->update(['is_synced' => true]);

            Log::info("Successfully synced order {$order->id} to Supabase.");
        } catch (\Exception $e) {
            Log::error("Failed to sync order {$order->id} to Supabase: " . $e->getMessage());
            throw $e; // Retry according to queue config
        }
    }
}
