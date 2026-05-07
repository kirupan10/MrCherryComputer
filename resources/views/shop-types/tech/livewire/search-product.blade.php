<div class="position-relative">
    <div class="input-group">
        <input wire:keydown.escape="resetQuery"
               wire:model.live.debounce.500ms="query" type="text"
               class="form-control form-control-solid"
               placeholder="Type product name or code...."
        >
    </div>

    <div wire:loading class="card position-absolute mt-1 border-0" style="z-index: 1; width: 100%; top: 100%;">
        <div class="card-body shadow">
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">
                        {{ ('Loading...') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($query))
        <div wire:click="resetQuery" class="position-fixed w-100 h-100" style="left: 0; top: 0; right: 0; bottom: 0; z-index: 1;"></div>
        @if($search_results->isNotEmpty())
            <div class="card position-absolute mt-1 border-0" style="z-index: 2; width: 100%; top: 100%; max-height: 500px; overflow-y: auto;">
                <div class="card-body shadow p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($search_results as $result)
                            <li class="list-group-item list-group-item-action">
                                <a wire:click="resetQuery" wire:click.prevent="selectProduct({{ $result }})" href="#">
                                    {{ $result->name }} | {{ $result->code }}
                                </a>
                            </li>
                        @endforeach
                        @if(safe_count($search_results) >= 20)
                            <li class="list-group-item text-center text-muted">
                                <small>
                                    <i class="bi bi-info-circle"></i>
                                    Showing top 20 most relevant results.
                                </small>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @else
            <div class="card position-absolute mt-1 border-0" style="z-index: 1; width: 100%; top: 100%;">
                <div class="card-body shadow">
                    <div class="alert alert-warning mb-0">
                        {{ __('No Product Found...') }}
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

<style>
    /* Custom scrollbar for search results */
    .position-relative > .card {
        scrollbar-width: thin;
        scrollbar-color: #3b82f6 #f1f5f9;
    }

    .position-relative > .card::-webkit-scrollbar {
        width: 6px;
    }

    .position-relative > .card::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .position-relative > .card::-webkit-scrollbar-thumb {
        background: #3b82f6;
        border-radius: 3px;
    }

    .position-relative > .card::-webkit-scrollbar-thumb:hover {
        background: #1e40af;
    }
</style>
