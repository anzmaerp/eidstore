@if(isset($product))
    @php($overallRating = getOverallRating($product?->reviews))

    <div class="product-single-hover style--category shadow-none rounded">
        <div class="overflow-hidden position-relative">
            <div class="inline_product clickable d-flex justify-content-center">

                {{-- Discount badge --}}
                @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                    <div class="d-flex">
                        <span class="for-discount-value p-1 px-2 font-bold fs-13">
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

                {{-- Product Image --}}
                <div class="d-flex pb-0">
                    <a href="{{ route('product', $product->slug) }}" class="d-block rounded">
                        <img alt="{{ $product->name }}"
                             class="border border-black-50"
                             src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}">
                    </a>
                </div>

                {{-- Quick View --}}
                <div class="quick-view">
                    <a class="btn-circle stopPropagation action-product-quick-view"
                       href="javascript:void(0)"
                       data-product-id="{{ $product->id }}">
                        <i class="czi-eye align-middle"></i>
                    </a>
                </div>

                {{-- Out of stock --}}
                @if($product->product_type === 'physical' && $product->current_stock <= 0)
                    <span class="out_fo_stock">{{ translate('out_of_stock') }}</span>
                @endif
            </div>

            {{-- Product details --}}
            <div class="single-product-details {{ Session::get('direction') === 'rtl' ? 'rtl' : '' }}">

                {{-- Rating --}}
                @if($overallRating[0] != 0)
                    <div class="rating-show justify-content-between mb-2">
                        <span class="d-inline-block font-size-sm text-body">
                            @for($inc = 1; $inc <= 5; $inc++)
                                @if($inc <= (int) $overallRating[0])
                                    <i class="tio-star text-warning"></i>
                                @elseif($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1 && $overallRating[0] > ((int) $overallRating[0]))
                                    <i class="tio-star-half text-warning"></i>
                                @else
                                    <i class="tio-star-outlined text-warning"></i>
                                @endif
                            @endfor
                            <label class="badge-style">( {{ count($product->reviews) }} )</label>
                        </span>
                    </div>
                @endif

                {{-- Product Name --}}
                <h3 class="mb-2 letter-spacing-0">
                    <a href="{{ route('product', $product->slug) }}" class="text-capitalize fw-semibold">
                        {{ $product->name }}
                    </a>
                </h3>

                {{-- Price --}}
                <div class="mb-0">
                    <h4 class="product-price d-flex flex-wrap gap-8 align-items-center mb-0 letter-spacing-0">
                        @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                            <del class="category-single-product-price fs-14 fw-bold">
                                {{ webCurrencyConverter(amount: $product->unit_price) }}
                            </del>
                        @endif

                        <span class="text-accent text-dark fs-15">
                            {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                        </span>
                    </h4>

                    {{-- Add to Cart Button --}}
                    <div class="mt-2">
                        <button class="btn btn--primary string-limit product-add-to-cart-button"
                                type="button"
                                data-form=".add-to-cart-details-form"
                                data-update="{{ translate('update_cart') }}"
                                data-add="{{ translate('add_to_cart') }}">
                            {{ translate('add_to_cart') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
