<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function getCheckoutConfirmationPath(): string
    {
        return URL::signedRoute('checkout.show', ['order' => $this->id]);
    }
}
