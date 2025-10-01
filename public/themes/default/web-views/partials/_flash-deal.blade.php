<section class="overflow-hidden">
    <div class="container">
        <div class="flash-deals-wrapper rounded {{ count($bannerTypeMainBanner) > 0 ? '' : 'mt-20' }}">
            <div class="flash-deal-view-all-web row d-flex justify-content-end align-items-center mb-3">
                <div class="flash-deal-text web-text-primary mt-0 flex-grow-1">
                    <h1 class="web-text-primary lh-1 h3 letter-spacing-0 line--limit-3 text-start mb-0">
                        <span>{{$web_config['flash_deals']->title}}</span>
                    </h1>
                </div>
                @if ($web_config['flash_deals']->products_count > 0)
                    <a class="text-capitalize view-all-text web-text-primary"
                       href="{{route('flash-deals',[$web_config['flash_deals']?$web_config['flash_deals']['id']:0])}}">
                        {{ translate('view_all')}}
                        <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1'}}"></i>
                    </a>
                @endif
            </div>
            <?php
            $startDate = \Carbon\Carbon::parse($web_config['flash_deals']['start_date']);
            $endDate = \Carbon\Carbon::parse($web_config['flash_deals']['end_date']);
            $now = \Carbon\Carbon::now();
            $totalDuration = $endDate->diffInSeconds($startDate);
            $elapsedDuration = $now->diffInSeconds($startDate);
            if ($totalDuration > 0) {
                $flashDealsPercentage = ($elapsedDuration / $totalDuration) * 100;
            } else {
                $flashDealsPercentage = 0;
            }
            ?>
            <div class="d-none d-md-block">
                <div class="row g-3">
                    <div class="col-lg-4 flashdeal-responsive">
                        <a href="{{route('flash-deals',[$web_config['flash_deals']?$web_config['flash_deals']['id']:0])}}" class="countdown-card bg-transparent d-block">
                            <div class="countdown-background text-white w-100 max-w-100 p-3 mt-0">
                                <h2 class="fs-16 text-center font-weight-normal text-white mb-4">{{translate('hurry_Up')}} ! {{translate('the_offer_is_limited')}}. <br> {{translate('grab_while_it_lasts')}}</h2>
                                <div class="text-center">
                                   <span class="cz-countdown d-flex justify-content-center align-items-center flash-deal-countdown"
                                         data-countdown="{{$web_config['flash_deals']?date('m/d/Y',strtotime($web_config['flash_deals']['end_date'])):''}} 23:59:00 ">
                                       <span class="cz-countdown-days">
                                           <span class="cz-countdown-value"></span>
                                           <span class="cz-countdown-text text-nowrap">{{ translate('days')}}</span>
                                       </span>
                                       <span class="cz-countdown-value p-1">:</span>
                                       <span class="cz-countdown-hours">
                                           <span class="cz-countdown-value"></span>
                                           <span class="cz-countdown-text text-nowrap">{{ translate('hours')}}</span>
                                       </span>
                                       <span class="cz-countdown-value p-1">:</span>
                                       <span class="cz-countdown-minutes">
                                           <span class="cz-countdown-value"></span>
                                           <span class="cz-countdown-text text-nowrap">{{ translate('minutes')}}</span>
                                       </span>
                                       <span class="cz-countdown-value p-1">:</span>
                                       <span class="cz-countdown-seconds">
                                           <span class="cz-countdown-value"></span>
                                           <span class="cz-countdown-text text-nowrap">{{ translate('seconds')}}</span>
                                       </span>
                                   </span>
                                    <div class="progress __progress">
                                        <div class="progress-bar flash-deal-progress-bar" role="progressbar" style="width: {{ number_format($flashDealsPercentage, 2) }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @php($flashDealCount = count($flashDeal['flashDealProducts']))
                    @if($flashDealCount == 10 || $flashDealCount <= 4)
                        @foreach($flashDeal['flashDealProducts'] as $key=>$flashDealProduct)
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                @include('web-views.partials._feature-product',['product'=> $flashDealProduct,'decimal_point_settings'=>$decimal_point_settings])
                            </div>
                        @endforeach
                    @endif
                    @if($flashDealCount > 4 && $flashDealCount < 10)
                        <div class="col-lg-8 d-none d-md-block">
                            <div class="owl-theme owl-carousel flash-deal-slider">
                                @foreach($flashDeal['flashDealProducts'] as $key=>$flashDealProduct)
                                    @include('web-views.partials._feature-product',['product'=> $flashDealProduct,'decimal_point_settings'=>$decimal_point_settings])
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-md-none">
                <div class="row g-3">
                    <div class="col-6">
                        <div>
                            <h3 class="web-text-primary fw-bold lh-1 fs-16 letter-spacing-0 line--limit-3 text-start text-uppercase mb-2">
                                <span>{{$web_config['flash_deals']->title}}</span>
                            </h3>
                            <h5 class="fs-10 font-weight-normal web-text-primary mb-0">{{translate('hurry_Up')}} ! {{translate('the_offer_is_limited')}}. <br> {{translate('grab_while_it_lasts')}}</h5>
                        </div>
                    </div>
                    <div class="col-6 flashdeal-responsive">
                        <a href="{{route('flash-deals',[$web_config['flash_deals']?$web_config['flash_deals']['id']:0])}}" class="countdown-card bg-transparent d-block">
                            <div class="countdown-background text-white w-100 max-w-100 px-2 pb-2 pt-sm-0 mt-0">
                                <div class="text-center">
                                   <span class="cz-countdown d-flex justify-content-center align-items-center flash-deal-countdown pb-sm-3"
                                         data-countdown="{{$web_config['flash_deals']?date('m/d/Y',strtotime($web_config['flash_deals']['end_date'])):''}} 23:59:00 ">
                                       <span class="cz-countdown-days">
                                           <span class="cz-countdown-value fs-8 lh-2"></span>
                                           <span class="cz-countdown-text text-nowrap fs-8">{{ translate('days')}}</span>
                                       </span>
                                       <span class="cz-countdown-value p-1 fs-8">:</span>
                                       <span class="cz-countdown-hours">
                                           <span class="cz-countdown-value fs-8 lh-2"></span>
                                           <span class="cz-countdown-text text-nowrap fs-8">{{ translate('hours')}}</span>
                                       </span>
                                       <span class="cz-countdown-value p-1 fs-8">:</span>
                                       <span class="cz-countdown-minutes">
                                           <span class="cz-countdown-value fs-8 lh-2"></span>
                                           <span class="cz-countdown-text text-nowrap fs-8">{{ translate('minutes')}}</span>
                                       </span>
                                       <span class="cz-countdown-value p-1 fs-8">:</span>
                                       <span class="cz-countdown-seconds">
                                           <span class="cz-countdown-value fs-8 lh-2"></span>
                                           <span class="cz-countdown-text text-nowrap fs-8">{{ translate('seconds')}}</span>
                                       </span>
                                   </span>
                                    <div class="progress __progress h-2px mt-0">
                                        <div class="progress-bar flash-deal-progress-bar" role="progressbar" style="width: {{ number_format($flashDealsPercentage, 2) }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-12 pb-0 d-md-none">
                        <div class="owl-theme owl-carousel flash-deal-slider-mobile">
                            @foreach($flashDeal['flashDealProducts'] as $key=>$flashDealProduct)
                                @include('web-views.partials._product-card-1',['product' => $flashDealProduct,'decimal_point_settings'=>$decimal_point_settings])
                            @endforeach
                        </div>
                    </div>
                    @if (count($flashDeal['flashDealProducts']) > 0)
                        <div class="col-12 d-md-none text-center">
                            <a class="text-capitalize view-all-text web-text-primary"
                               href="{{route('flash-deals',[$web_config['flash_deals']?$web_config['flash_deals']['id']:0])}}">
                                {{ translate('view_all')}}
                                <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1'}}"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>


        </div>
    </div>
</section>
