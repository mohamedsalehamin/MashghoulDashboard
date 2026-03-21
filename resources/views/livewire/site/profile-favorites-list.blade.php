<div class="container">
    <h1 class="section-title mb-4">{{ __('site.heading.favorites') }}</h1>

            <!-- Start Pagination -->
            <!-- <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="salon-details.html" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item"><a class="page-link active" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav> -->
            <!-- End Pagination -->
    @if($providers->isEmpty())
        <p class="text-muted">{{ __('site.no_data') }}</p>
    @else
        <div class="row">
            @foreach($providers as $provider)
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <x-site.provider-card :provider="$provider" :show-map-button="true" />
                </div>
            @endforeach
        </div>
        @if($providers->hasPages())
            <div class="mt-4">
                {{ $providers->links('vendor.pagination.categories') }}
            </div>
        @endif
    @endif
</div>
