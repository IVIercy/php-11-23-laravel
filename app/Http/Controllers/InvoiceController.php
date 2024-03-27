<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __invoke(Order $order)
    {
        $this->authorize('view', $order);
        return app(\App\Services\Contract\InvoicesServiceContract::class)->generate($order)->stream();

    }
}
