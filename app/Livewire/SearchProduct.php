<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class SearchProduct extends Component
{
    public $query;

    public $search_results;

    public $how_many = 20;

    public function mount()
    {
        $this->query = '';
        $this->how_many = 20;
        $this->search_results = Collection::empty();
    }

    public function render()
    {
        return view('livewire.search-product');
    }

    public function updatedQuery()
    {
        if (empty($this->query)) {
            $this->search_results = Collection::empty();
            return;
        }

        // Get all matching products first
        $query = $this->query;
        $allMatches = Product::query()
            ->where(function($q) use ($query) {
                // Exact code match gets priority
                $q->where('code', 'like', $query.'%')
                  ->orWhere('code', '=', $query);
            })
            ->orWhere(function($q) use ($query) {
                // Exact starts-with name match
                $q->where('name', 'like', $query.'%');
            })
            ->orWhere(function($q) use ($query) {
                // Contains anywhere in name
                $q->where('name', 'like', '%'.$query.'%');
            })
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%');
            })
            ->with('category')
            ->get();

        // Score and sort results for relevance
        $scored = $allMatches->map(function($product) use ($query) {
            $score = 0;
            $queryLower = strtolower($query);
            $codeLower = strtolower($product->code ?? '');
            $nameLower = strtolower($product->name);

            // Code matches (highest priority)
            if ($codeLower === $queryLower) {
                $score += 1000;  // Exact code match
            } elseif (strpos($codeLower, $queryLower) === 0) {
                $score += 800;   // Code starts with query
            } elseif (strpos($codeLower, $queryLower) !== false) {
                $score += 600;   // Code contains query
            }

            // Name matches
            if ($nameLower === $queryLower) {
                $score += 500;   // Exact name match
            } elseif (strpos($nameLower, $queryLower) === 0) {
                $score += 300;   // Name starts with query
            } elseif (strpos($nameLower, $queryLower) !== false) {
                $score += 100;   // Name contains query
            }

            // Add stock status bonus
            if ($product->quantity > 0) {
                $score += 50;
            }

            $product->search_score = $score;
            return $product;
        })
        ->sortByDesc('search_score')
        ->take(20)
        ->values();

        $this->search_results = $scored;
    }

    public function loadMore()
    {
        // Disabled - Maximum 20 most relevant products shown
        return;
    }

    public function resetQuery()
    {
        $this->query = '';
        $this->how_many = 20;
        $this->search_results = Collection::empty();
    }

    public function selectProduct($product)
    {
        $this->dispatch('productSelected', $product);
    }
}
