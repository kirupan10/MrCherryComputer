@extends('layouts.nexora')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Create Customer') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row row-cards">

            <form action="{{ shop_route('customers.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title mb-0">{{ __('Customer Details') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row row-cards">
                                    <div class="col-md-12">
                                        <x-input name="name" :required="true"/>

                                        <x-input name="email" label="Email address"/>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input label="Phone Number" name="phone" :required="true"/>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">
                                            Address
                                        </label>

                                        <textarea name="address"
                                                  id="address"
                                                  rows="3"
                                                  class="form-control form-control-solid @error('address') is-invalid @enderror"
                                            >{{ old('address') }}</textarea>

                                        @error('address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <x-button.save type="submit">
                                    {{ __('Save') }}
                                </x-button.save>

                                <x-button.back route="{{ shop_route('customers.index') }}">
                                    {{ __('Cancel') }}
                                </x-button.back>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Quick Tips</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="fw-semibold">Use a clear name</div>
                                    <div class="text-muted small">Full customer names improve search and invoice clarity.</div>
                                </div>
                                <div class="mb-3">
                                    <div class="fw-semibold">Keep phone accurate</div>
                                    <div class="text-muted small">Phone number is required for delivery and follow-up.</div>
                                </div>
                                <div>
                                    <div class="fw-semibold">Add address when possible</div>
                                    <div class="text-muted small">Address details help with service visits and future records.</div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="fw-semibold mb-1">Note</div>
                                <div class="text-muted small mb-0">Customer image upload is disabled. Profile images are only available for system users.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
