<style>
    .category-table-shell .category-data-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
    }

    .category-table-shell .category-data-table th a {
        color: inherit;
        text-decoration: none;
    }

    .category-table-shell .category-name {
        font-weight: 600;
    }

    .category-table-shell .action-cell {
        min-width: 130px;
    }

    .category-table-shell .action-cell .btn-list {
        justify-content: center;
        gap: 0.35rem;
    }

    .category-table-shell .action-cell .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        padding: 0;
    }

    .category-table-shell .action-cell form {
        margin: 0;
        display: inline-flex;
    }

    @media (max-width: 767.98px) {
        .category-table-shell .toolbar-stack {
            width: 100%;
        }

        .category-table-shell .toolbar-stack .form-select,
        .category-table-shell .toolbar-stack .form-control {
            width: 100%;
        }
    }
</style>

<div class="card border-0 shadow-none rounded-0 category-table-shell">
    <div class="card-header bg-transparent border-0 pb-2">
        <div>
            <h3 class="card-title mb-0" style="font-size: 0.98rem; font-weight: 700; letter-spacing: 0.01em;">
                {{ __('Categories') }}
            </h3>
        </div>
    </div>
    <div class="card-body border-bottom py-2 px-3">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-md-between">
            <div class="d-flex align-items-center gap-2 toolbar-stack">
                <span class="text-secondary small">Show</span>
                <div>
                    <select wire:model.live="perPage" class="form-select form-select-sm" aria-label="result per page" style="min-width: 86px;">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                    </select>
                </div>
                <span class="text-secondary small">entries</span>
            </div>
            <div class="d-flex align-items-center gap-2 toolbar-stack">
                <span class="text-secondary small">Search</span>
                <div class="input-group input-group-sm" style="min-width: 240px;">
                    <input type="text" wire:model.live="search" class="form-control" aria-label="Search category" placeholder="Name or slug">
                    @if($search)
                        <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')" title="Clear search">
                            Clear
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-spinner.loading-spinner/>

    <div class="table-responsive">
        <table wire:loading.remove class="table table-bordered table-hover card-table table-vcenter text-nowrap datatable mb-0 category-data-table">
            <thead class="thead-light">
            <tr>
                <th class="align-middle text-center w-1">
                    {{ __('No.') }}
                </th>
                <th scope="col" class="align-middle text-center">
                    <a wire:click.prevent="sortBy('name')" href="#" role="button">
                        {{ __('Name') }}
                        @include('inclues._sort-icon', ['field' => 'name'])
                    </a>
                </th>
                <th scope="col" class="align-middle text-center d-none d-sm-table-cell">
                    <a wire:click.prevent="sortBy('slug')" href="#" role="button">
                        {{ __('Slug') }}
                        @include('inclues._sort-icon', ['field' => 'slug'])
                    </a>
                </th>
                <th scope="col" class="align-middle text-center">
                    {{ __('Action') }}
                </th>
            </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td class="align-middle text-center">
                            {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                        </td>
                        <td class="align-middle category-name">
                            {{ $category->name }}
                        </td>
                        <td class="align-middle d-none d-sm-table-cell">
                            <span class="badge bg-blue-lt text-blue">{{ $category->slug }}</span>
                        </td>
                        <td class="align-middle text-center action-cell">
                            <div class="btn-list" aria-label="Category actions">
                                <x-button.show class="btn-icon" route="{{ shop_route('categories.show', $category) }}"/>
                                <x-button.edit class="btn-icon" route="{{ shop_route('categories.edit', $category) }}"/>
                                <x-button.delete class="btn-icon" route="{{ shop_route('categories.destroy', $category) }}"/>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="align-middle text-center py-4" colspan="4">
                            <div class="text-muted fw-medium">No categories found</div>
                            <div class="small text-secondary">Try another search term or clear filters.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center py-2">
        <p class="m-0 text-secondary">
            Showing <span>{{ $categories->firstItem() }}</span> to <span>{{ $categories->lastItem() }}</span> of <span>{{ $categories->total() }}</span> entries
        </p>

        <ul class="pagination m-0 ms-auto">
            {{ $categories->links() }}
        </ul>
    </div>
</div>
