@pushonce('page-styles')
    <link href="{{ asset('vendor/tom-select/tom-select.css') }}" rel="stylesheet">
@endpushonce

@props([
    'label' => '',
    'name' => $name,
    'id' => '' ?? $name,
    'placeholder' => '',
    'data',
    'value'
])

<div class="col-md-4">
    <label for="{{ $id }}" class="form-label required" >
        {{ $label }}
    </label>

    <select id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}" autocomplete="off"
            class="form-control form-select @error($name) is-invalid @enderror"
    >
        <option value="">
            Select a person...
        </option>

        @foreach($data as $option)
            <option value="{{ $option->id }}" @selected(old($name, $value = '' ?? null) == $option->id)>
                {{ $option->name }}
            </option>
        @endforeach
    </select>

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

@pushonce('page-scripts')
    <script src="{{ asset('vendor/tom-select/tom-select.complete.min.js') }}"></script>

    <script>
        new TomSelect("#{{ $id }}",{
            create: true,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    </script>
@endpushonce


