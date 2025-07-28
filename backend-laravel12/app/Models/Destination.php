<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted()
    {
        static::saving(function ($destination) {
            if ($destination->isDirty('image')) {
                $oldFile = $destination->getOriginal('image');
                if ($oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
            }
        });

        static::deleting(function ($destination) {
            if ($destination->image) {
                Storage::disk('public')->delete($destination->image);
            }
        });
    }
}
