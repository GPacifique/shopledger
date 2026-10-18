{{-- resources/views/shop/assignment-required.blade.php --}}
@extends('layouts.app')

@section('title', 'Shop Assignment Required')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                <div class="p-4 text-center text-white"
                     style="background: linear-gradient(135deg, #14532d, #16a34a);">

                    <div class="mb-3">
                        <span style="font-size: 54px;">🏪</span>
                    </div>

                    <h2 class="fw-bold mb-2">Welcome to Mahwi</h2>

                    <p class="mb-0 opacity-75">
                        Let's get your account ready
                    </p>
                </div>

                <div class="card-body p-4 p-md-5 text-center">

                    <div class="mb-4">
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                            Setup Incomplete
                        </span>
                    </div>

                    <h3 class="fw-bold mb-3">
                        No Shop Assigned
                    </h3>

                    <p class="text-muted mb-4">
                        Your account is active, but it is not yet linked
                        to a shop. A shop assignment is required before
                        you can access your business workspace.
                    </p>

                    <div class="text-start bg-light rounded-3 p-4 mb-4">
                        <h6 class="fw-bold mb-3">What happens next?</h6>

                        <div class="d-flex gap-3 mb-3">
                            <span class="text-success fw-bold">01</span>
                            <div>
                                <strong>Contact your administrator</strong>
                                <p class="text-muted small mb-0">
                                    Ask your business administrator to
                                    assign you to the correct shop.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-3">
                            <span class="text-success fw-bold">02</span>
                            <div>
                                <strong>Wait for confirmation</strong>
                                <p class="text-muted small mb-0">
                                    Your shop access and permissions
                                    must be configured.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <span class="text-success fw-bold">03</span>
                            <div>
                                <strong>Access your workspace</strong>
                                <p class="text-muted small mb-0">
                                    Return to Mahwi once your assignment
                                    has been completed.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                            onclick="window.location.reload()"
                            class="btn btn-success btn-lg w-100 rounded-3">
                        <span class="me-2">↻</span>
                        Check Again
                    </button>

                    @if (Route::has('logout'))
                        <form method="POST"
                              action="{{ route('logout') }}"
                              class="mt-3">
                            @csrf
                            <button type="submit"
                                    class="btn btn-outline-secondary w-100 rounded-3">
                                Sign Out
                            </button>
                        </form>
                    @endif

                    <p class="text-muted small mt-4 mb-0">
                        Need help? Contact your business administrator.
                    </p>

                </div>
            </div>

            <p class="text-center text-muted small mt-4">
                &copy; {{ date('Y') }} Mahwi. Your business backbone.
            </p>
        </div>
    </div>
</div>
@endsection