@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('content')

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary">
                    {{ __('affiliate.title') }}
                </h1>
                <p class="lead text-muted">
                    {{ __('affiliate.subtitle') }}
                </p>
            </div>

            <!-- Introduction -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-3">{{ __('affiliate.what_is_title') }}</h2>
                    <p class="text-muted">
                        {{ __('affiliate.what_is_description') }}
                    </p>
                </div>
            </div>

            <!-- Benefits -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4">{{ __('affiliate.benefits_title') }}</h2>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>{{ __('affiliate.benefit_1_title') }}</h5>
                                    <p class="text-muted">{{ __('affiliate.benefit_1_description') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>{{ __('affiliate.benefit_2_title') }}</h5>
                                    <p class="text-muted">{{ __('affiliate.benefit_2_description') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>{{ __('affiliate.benefit_3_title') }}</h5>
                                    <p class="text-muted">{{ __('affiliate.benefit_3_description') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>{{ __('affiliate.benefit_4_title') }}</h5>
                                    <p class="text-muted">{{ __('affiliate.benefit_4_description') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How it works -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4">{{ __('affiliate.how_it_works_title') }}</h2>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <span class="fs-4 fw-bold">1</span>
                                </div>
                                <h5>{{ __('affiliate.step_1_title') }}</h5>
                                <p class="text-muted">{{ __('affiliate.step_1_description') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <span class="fs-4 fw-bold">2</span>
                                </div>
                                <h5>{{ __('affiliate.step_2_title') }}</h5>
                                <p class="text-muted">{{ __('affiliate.step_2_description') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <span class="fs-4 fw-bold">3</span>
                                </div>
                                <h5>{{ __('affiliate.step_3_title') }}</h5>
                                <p class="text-muted">{{ __('affiliate.step_3_description') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Structure -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-3">{{ __('affiliate.commission_title') }}</h2>
                    <p class="text-muted mb-4">
                        {{ __('affiliate.commission_description') }}
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('affiliate.commission_tier') }}</th>
                                    <th>{{ __('affiliate.commission_rate') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('affiliate.tier_1') }}</td>
                                    <td><strong class="text-success">{{ __('affiliate.tier_1_rate') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ __('affiliate.tier_2') }}</td>
                                    <td><strong class="text-success">{{ __('affiliate.tier_2_rate') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ __('affiliate.tier_3') }}</td>
                                    <td><strong class="text-success">{{ __('affiliate.tier_3_rate') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="card shadow-sm bg-primary text-white mb-4">
                <div class="card-body p-4 text-center">
                    <h2 class="h4 mb-3">{{ __('affiliate.cta_title') }}</h2>
                    <p class="mb-4">{{ __('affiliate.cta_description') }}</p>
                    <a href="/login" class="btn btn-light btn-lg">
                        {{ __('affiliate.cta_button') }}
                    </a>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4">{{ __('affiliate.faq_title') }}</h2>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    {{ __('affiliate.faq_1_question') }}
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    {{ __('affiliate.faq_1_answer') }}
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    {{ __('affiliate.faq_2_question') }}
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    {{ __('affiliate.faq_2_answer') }}
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    {{ __('affiliate.faq_3_question') }}
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    {{ __('affiliate.faq_3_answer') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="card shadow-sm">
                <div class="card-body p-4 text-center">
                    <h2 class="h4 mb-3">{{ __('affiliate.contact_title') }}</h2>
                    <p class="text-muted mb-3">
                        {{ __('affiliate.contact_description') }}
                    </p>
                    <a href="mailto:support@example.com" class="btn btn-outline-primary">
                        {{ __('affiliate.contact_button') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
