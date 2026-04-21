@extends('user.layout.home', ['title' => 'Tutorial'])

@section('content')
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-7 col-md-6 mt-4 pt-2">
                <div class="card shadow rounded border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Video Guide</h5>
                        <p class="text-muted">Watch this quick walkthrough to understand the end-to-end procurement
                            process on our platform.</p>

                        <div class="ratio ratio-16x9 mt-4 rounded overflow-hidden shadow-sm">
                            <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID" title="E-Vendor Tutorial Video"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-6 mt-4 pt-2">
                <div class="card shadow rounded border-0 text-center">
                    <div class="card-body p-4 py-5">
                        <div class="icon-block mx-auto text-primary mb-4">
                            <i class="uil uil-book-open" style="font-size: 64px;"></i>
                        </div>
                        <h5 class="card-title mb-3">User Manual Book</h5>
                        <p class="text-muted mb-4">Prefer reading? Download the comprehensive step-by-step PDF guide,
                            covering everything from registration to invoicing.</p>

                        <div class="d-grid gap-2">
                            <a href="{{ asset('files/manual-book.pdf') }}" target="_blank" class="btn btn-primary">
                                <i class="uil uil-eye me-1"></i> Read Online
                            </a>
                            <a href="{{ asset('files/manual-book.pdf') }}" download class="btn btn-soft-primary mt-2">
                                <i class="uil uil-download-alt me-1"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
