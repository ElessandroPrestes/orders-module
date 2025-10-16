<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;



class Order extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['user_id', 'status', 'total_value'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}
