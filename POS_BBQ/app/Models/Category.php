<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;
use App\Traits\SyncsToSupabase;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MenuItem[] $menuItems
 */
class Category extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes, SyncsToSupabase;
    protected $table = 'pos_categories';

    protected $fillable = ['name', 'description', 'sort_order'];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
