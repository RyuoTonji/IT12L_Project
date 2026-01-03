<?php

namespace App\View\Composers;

use App\Models\VoidRequest;
use Illuminate\View\View;

class VoidRequestComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Count pending void/refund requests
        $pendingVoidRequestsCount = VoidRequest::where('status', 'pending')->count();

        $view->with('pendingVoidRequestsCount', $pendingVoidRequestsCount);
    }
}
