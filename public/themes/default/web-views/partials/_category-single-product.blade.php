@if(isset($product))
@php($overallRating = getOverallRating($product?->reviews))
<div class="product-single-hover style--category shadow-none rounded">
    <div class="overflow-hidden position-relative">
        <div class=" inline_product clickable d-flex justify-content-center">
            @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                <div class="d-flex">
                    <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                        <span class="direction-ltr d-block">
                            -{{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                        </span>
                    </span>
                </div>
            @else
                <div class="d-flex justify-content-end">
                    <span class="for-discount-value-null"></span>
                </div>
            @endif
            <div class="d-flex pb-0">
                <a href="{{route('product', $product->slug)}}" class="d-block rounded">
                    <img alt="" class="border border-black-50"
                        src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}">
                </a>
            </div>

            <div class="quick-view">
                {{-- <div class="d-none d-md-flex gap-2 align-items-center quick-view-tag">
                    @if($product->product_type == 'digital')
                    <div class="bg-white btn-circle" style="--size: 26px" data-toggle="tooltip"
                        title="{{ translate('Digital_Product') }}" data-placement="left">
                        <img class="h-16px aspect-1 svg" src="{{theme_asset(path: "
                            public/assets/front-end/img/icons/digital-product.svg")}}" alt="">
                    </div>
                    @else
                    <div class="bg-white btn-circle" style="--size: 26px" data-toggle="tooltip"
                        title="{{ translate('Physical_Product') }}" data-placement="left">
                        <img class="h-16px aspect-1 svg" src="{{theme_asset(path: "
                            public/assets/front-end/img/icons/physical-product.svg")}}" alt="">
                    </div>
                    @endif
                </div> --}}
                <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                    data-product-id="{{ $product->id }}">
                    <i class="czi-eye align-middle"></i>
                </a>
            </div>
            @if($product->product_type == 'physical' && $product->current_stock <= 0)
                <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
            @endif
        </div>
        <div class="single-product-details {{Session::get('direction') === "rtl" ? 'rtl' : ''}}">
            @if($overallRating[0] != 0)
                <div class="rating-show justify-content-between mb-2">
                    <span class="d-inline-block font-size-sm text-body">
                        @for($inc = 1; $inc <= 5; $inc++)
                            @if ($inc <= (int) $overallRating[0])
                                <i class="tio-star text-warning"></i>
                            @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                                <i class="tio-star-half text-warning"></i>
                            @else
                                <i class="tio-star-outlined text-warning"></i>
                            @endif
                        @endfor
                        <label class="badge-style">( {{ count($product->reviews) }} )</label>
                    </span>
                </div>
            @endif
            <h3 class="mb-2 letter-spacing-0">
                <a href="{{route('product', $product->slug)}}" class="text-capitalize fw-semibold">
                    {{ $product['name'] }}
                </a>
            </h3>
            <div class="mb-0">
                <h4 class="product-price d-flex flex-wrap gap-8 align-items-center row-gap-0 mb-0 letter-spacing-0">
                    @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                        <del class="category-single-product-price fs-14 fw-bold">
                            {{ webCurrencyConverter(amount: $product->unit_price) }}
                        </del>
                    @endif
                    <span class="text-accent text-dark fs-15">
                        {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                    </span>
                </h4>
                <div class="mt-2">
                    <button class="btn btn--primary string-limit product-add-to-cart-button" type="button"
                        data-form=".add-to-cart-details-form" data-update="{{ translate('update_cart') }}"
                        data-add="{{ translate('add_to_cart') }}">
                        {{ translate('add_to_cart') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
@endif