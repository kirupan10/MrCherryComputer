<?php

namespace App\Livewire\Tables;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class ProductTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshProducts' => '$refresh'];

    public $perPage = 20;  // Show 20 products initially

    public function loadMore()
    {
        $this->perPage += 15;  // Load 15 more on each "Load More" action
    }

    public $search = '';  // Search query

    public $sortField = 'created_at';

    public $sortAsc = false;

    public $categoryFilter = '';

    public $unitFilter = '';

    public $stockFilter = 'all'; // all, in_stock, low_stock, out_of_stock

    public $noBuyingPriceFilter = false; // Filter for products without buying price

    public $searchDelay = 300;  // Milliseconds before search executes

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'unitFilter' => ['except' => ''],
        'stockFilter' => ['except' => 'all'],
        'noBuyingPriceFilter' => ['except' => false, 'as' => 'filter'],
    ];

    public function mount()
    {
        // Check if the no_buying_price filter is requested via URL
        if (request()->get('filter') === 'no_buying_price') {
            $this->noBuyingPriceFilter = true;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingUnitFilter()
    {
        $this->resetPage();
    }

    public function updatingStockFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->unitFilter = '';
        $this->stockFilter = 'all';
        $this->noBuyingPriceFilter = false;
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()
            ->with(['category', 'unit', 'warranty']);

        // Check if any filters are active
        $hasActiveFilters = !empty($this->search) ||
                           !empty($this->categoryFilter) ||
                           !empty($this->unitFilter) ||
                           $this->stockFilter !== 'all';

        // Apply search with better relevance
        if (!empty($this->search)) {
            $searchTerm = trim($this->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('notes', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Apply category filter
        if (!empty($this->categoryFilter)) {
            if ($this->categoryFilter === 'uncategorized') {
                $query->whereNull('category_id');
            } else {
                $query->where('category_id', $this->categoryFilter);
            }
        }

        // Apply unit filter
        if (!empty($this->unitFilter)) {
            $query->where('unit_id', $this->unitFilter);
        }

        // Apply stock filter
        switch ($this->stockFilter) {
            case 'in_stock':
                $query->whereRaw('quantity > quantity_alert');
                break;
            case 'low_stock':
                $query->whereRaw('quantity <= quantity_alert AND quantity > 0');
                break;
            case 'out_of_stock':
                $query->where('quantity', '<=', 0);
                break;
        }

        // Apply no buying price filter
        if ($this->noBuyingPriceFilter) {
            $query->where(function($q) {
                $q->whereNull('buying_price')
                  ->orWhere('buying_price', '<=', 0);
            });
        }

        // Use pagination with perPage limit that respects load more
        $products = $query
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        // Count products without buying price
        $productsWithoutBuyingPrice = Product::where('shop_id', auth()->user()->shop_id)
            ->where(function($q) {
                $q->whereNull('buying_price')
                  ->orWhere('buying_price', '<=', 0);
            })
            ->count();

        return view('livewire.tables.product-table', [
            'products' => $products,
            'categories' => Category::all(['id', 'name']),
            'units' => Unit::all(['id', 'name']),
            'totalResults' => $products->total(),
            'hasActiveFilters' => $hasActiveFilters,
            'productsWithoutBuyingPrice' => $productsWithoutBuyingPrice,
        ]);
    }
}
