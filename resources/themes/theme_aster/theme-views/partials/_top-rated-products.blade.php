<section class="bg-primary-light">
    <div class="container">
        <div class="">
            <div class="py-4">
                <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                    <h2 class="text-capitalize">{{ translate('top_rated_products') }}</h2>
                    <a href="{{route('products',['data_from'=>'top-rated','page'=>1])}}" class="btn-link">{{ translate('View_All') }}
                        <i class="bi bi-chevron-right text-primary"></i></a>
                </div>
                <div class="swiper-container">
                    <div class="position-relative">
                        <div class="swiper" data-swiper-loop="true" data-swiper-margin="16"
                             data-swiper-pagination-el="null" data-swiper-navigation-next=".top-rated-nav-next"
                             data-swiper-navigation-prev=".top-rated-nav-prev"
                             data-swiper-breakpoints='{"0": {"slidesPerView": "1"}, "340": {"slidesPerView": "2"}, "992": {"slidesPerView": "3"}, "1200": {"slidesPerView": "4"}, "1400": {"slidesPerView": "5"}}'>
                            <div class="swiper-wrapper">
                                @foreach($topRatedProducts as $key=> $product)
                                    <div class="swiper-slide">
                                        @include('theme-views.partials._product-large-card', ['product'=> $product])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="swiper-button-prev top-rated-nav-prev"></div>
                        <div class="swiper-button-next top-rated-nav-next"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
