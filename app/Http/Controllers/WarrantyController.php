<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Only shop owners and managers can access warranties
        abort_unless(in_array(auth()->user()->role, ['shop_owner', 'shop_manager']), 403);

        // Global scope automatically filters by current shop
        // Load product counts in one query to avoid N+1 queries in the index view.
        $warranties = Warranty::withCount('products')
            ->orderBy('name')
            ->paginate(15);

        return view('warranties.index', compact('warranties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only shop owners and managers can create warranties
        abort_unless(in_array(auth()->user()->role, ['shop_owner', 'shop_manager']), 403);

        return view('warranties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only shop owners and managers can create warranties
        abort_unless(in_array(auth()->user()->role, ['shop_owner', 'shop_manager']), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:1',
        ]);

        $user = auth()->user();
        $shopId = $user instanceof User ? $user->getActiveShop()?->id : null;

        if (!$shopId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'No active shop is assigned to this account.']);
        }

        $warranty = Warranty::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'duration' => $request->duration,
            'years' => $request->duration ? round($request->duration / 12, 1) : null,
            'shop_id' => $shopId,
        ]);

        return redirect()->route('warranties.index')
            ->with('success', 'Warranty created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warranty $warranty)
    {
        // Only shop owners and managers can view warranties
        abort_unless(in_array(auth()->user()->role, ['shop_owner', 'shop_manager']), 403);

        // Global scope already filtered by shop, so warranty is guaranteed to belong to current shop
        return view('warranties.show', compact('warranty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warranty $warranty)
    {
        // Only shop owners and managers can edit warranties
        abort_unless(in_array(auth()->user()->role, ['shop_owner', 'shop_manager']), 403);

        // Global scope already filtered by shop, so warranty is guaranteed to belong to current shop
        return view('warranties.edit', compact('warranty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warranty $warranty)
    {
        // Only shop owners and managers can update warranties
        abort_unless(in_array(auth()->user()->role, ['shop_owner', 'shop_manager']), 403);

        // Global scope already filtered by shop, so warranty is guaranteed to belong to current shop

        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:1',
        ]);

        $warranty->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'duration' => $request->duration,
            'years' => $request->duration ? round($request->duration / 12, 1) : null,
        ]);

        return redirect()->route('warranties.index')
            ->with('success', 'Warranty updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warranty $warranty)
    {
        // Only shop owners and managers can delete warranties
        abort_unless(in_array(auth()->user()->role, ['shop_owner', 'shop_manager']), 403);

        // Global scope already filtered by shop, so warranty is guaranteed to belong to current shop

        // Check if warranty is used by any products
        if ($warranty->products()->count() > 0) {
            return redirect()->route('warranties.index')
                ->withErrors(['error' => 'Cannot delete warranty that is assigned to products.']);
        }

        $warranty->delete();

        return redirect()->route('warranties.index')
            ->with('success', 'Warranty deleted successfully.');
    }
}
