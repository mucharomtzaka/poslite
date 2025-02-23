<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPrintController extends Controller
{
    //
    public function print(Order $order)
    {
        $pdf = Pdf::loadView('pdf.order', ['order' => $order]);
        return $pdf->stream('Order_'.$order->order_id.'.pdf');
    }
}
