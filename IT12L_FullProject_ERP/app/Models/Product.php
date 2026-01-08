<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Product Model
 * 
 * Represents products available for sale in the ERP system.
 * Products are tied to branches and categories, with soft delete support
 * and comprehensive logging of deletions.
 * 
 * @property int $id Primary key
 * @property int $branch_id Foreign key to crm_branches table
 * @property int $category_id Foreign key to crm_categories table
 * @property string $name Product name
 * @property string|null $description Product description
 * @property float $price Product price (2 decimal places)
 * @property string|null $image Product image path
 * @property bool $is_available Availability status
 * @property \Illuminate\Support\Carbon $created_at Creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last update timestamp
 * @property \Illuminate\Support\Carbon|null $deleted_at Soft delete timestamp
 */
class Product extends Model
{
    use SoftDeletes;

    // Explicitly define the table name to match the crm_products migration
    protected $table = 'crm_products';

    /**
     * Mass assignable attributes.
     * 
     * These fields can be filled using mass assignment (e.g., Product::create($data))
     */
    protected $fillable = [
        'branch_id',      // ID of the branch this product belongs to
        'category_id',    // ID of the product category
        'name',           // Product name (required)
        'description',    // Detailed product description (optional)
        'price',          // Product price in decimal format
        'image',          // Path to product image file (optional)
        'is_available',   // Boolean flag for product availability
    ];

    /**
     * Attribute casting for type safety and consistency.
     * 
     * Ensures price is always treated as decimal and is_available as boolean
     */
    protected $casts = [
        'price' => 'decimal:2',       // Cast to decimal with 2 decimal places
        'is_available' => 'boolean',  // Cast to boolean (true/false)
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the branch that owns this product.
     * 
     * A product belongs to one branch. This establishes the relationship
     * between the crm_products and crm_branches tables.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the category that owns this product.
     * 
     * A product belongs to one category for organizational purposes.
     * This establishes the relationship between crm_products and crm_categories tables.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all cart items containing this product.
     * 
     * A product can be added to multiple carts by different users.
     * This is a one-to-many relationship.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // ========================================
    // ACCESSOR ATTRIBUTES (for view compatibility)
    // ========================================

    /**
     * Get the category name attribute.
     * 
     * This accessor provides a convenient way to access the category name
     * without explicitly loading the relationship in views.
     * Usage: $product->category_name
     * 
     * @return string|null
     */
    public function getCategoryNameAttribute()
    {
        return $this->category?->name;
    }

    /**
     * Get the branch name attribute.
     * 
     * This accessor provides a convenient way to access the branch name
     * without explicitly loading the relationship in views.
     * Usage: $product->branch_name
     * 
     * @return string|null
     */
    public function getBranchNameAttribute()
    {
        return $this->branch?->name;
    }

    // ========================================
    // QUERY SCOPES (for reusable query filters)
    // ========================================

    /**
     * Scope to filter only available products.
     * 
     * Returns only products with is_available = true
     * Usage: Product::available()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to filter products by branch.
     * 
     * Filters products belonging to a specific branch
     * Usage: Product::byBranch($branchId)->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $branchId The branch ID to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter products by category.
     * 
     * Filters products belonging to a specific category
     * Usage: Product::byCategory($categoryId)->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $categoryId The category ID to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to search products by name.
     * 
     * Performs a case-insensitive partial match search on product name
     * Usage: Product::search('burger')->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search The search term to look for
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // ========================================
    // MODEL EVENTS (Boot method)
    // ========================================

    /**
     * The "booted" method of the model.
     * 
     * This method is called when the model is booted. It sets up event listeners
     * that automatically log soft deletions to the crm_deletion_logs table.
     * This provides an audit trail of all deleted products.
     * 
     * @return void
     */
    protected static function booted()
    {
        // Listen for the 'deleted' event (triggered by soft delete)
        static::deleted(function ($product) {
            // Insert a record into the deletion logs table
            // This creates an audit trail for compliance and recovery purposes
            \Illuminate\Support\Facades\DB::table('crm_deletion_logs')->insert([
                'table_name' => 'crm_products',              // Source table name
                'record_id' => $product->id,                  // ID of deleted product
                'data' => json_encode($product->toArray()),   // Full product data as JSON
                'deleted_by' => auth()->id(),                 // User who performed the deletion
                'reason' => 'Soft delete',                    // Reason for deletion
                'deleted_at' => now(),                        // Timestamp of deletion
                'created_at' => now(),                        // Log creation timestamp
                'updated_at' => now(),                        // Log update timestamp
            ]);
        });
    }
}