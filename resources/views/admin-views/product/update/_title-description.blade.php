<div class="card">
    <div class="card-body">
        <div class="row gy-4">
            <div class="col-md-12">
                <div class="position-relative nav--tab-wrapper">
                    <ul class="nav nav-pills nav--tab text-capitalize lang_tab" id="pills-tab"
                        role="tablist">
                        @foreach ($languages as $lang)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                   id="{{ getLanguageCode($lang) }}-link" data-bs-toggle="pill" href="#{{ getLanguageCode($lang) }}-form"
                                   role="tab" aria-controls="{{ $lang }}-form" aria-selected="true">
                                    {{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="nav--tab__prev">
                        <button class="btn btn-circle border-0 bg-white text-primary">
                            <i class="fi fi-sr-angle-left"></i>
                        </button>
                    </div>
                    <div class="nav--tab__next">
                        <button class="btn btn-circle border-0 bg-white text-primary">
                            <i class="fi fi-sr-angle-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="tab-content" id="pills-tabContent">
                    @foreach ($languages as $language)
                            <?php
                            if (count($product['translations'])) {
                                $translate = [];
                                foreach ($product['translations'] as $translation) {
                                    if ($translation->locale == $language && $translation->key == "name") {
                                        $translate[$language]['name'] = $translation->value;
                                    }
                                    if ($translation->locale == $language && $translation->key == "description") {
                                        $translate[$language]['description'] = $translation->value;
                                    }
                                }
                            }
                            ?>
                        <div class="tab-pane fade {{ $language == $defaultLanguage ? 'show active' : '' }}"
                             id="{{ getLanguageCode($language) }}-form" role="tabpanel">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label" for="{{ getLanguageCode($language) }}_name">
                                    {{ translate('product_name') }}
                                    ({{ strtoupper($language) }})
                                    @if($language == $defaultLanguage)
                                        <span class="input-required-icon text-danger">*</span>
                                    @endif
                                </label>
                                <button type="button" class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 auto_fill_title"
                                        id="title-{{ getLanguageCode($language) }}-action-btn"   data-item='@json(["title" => $translate[$language]["name"] ?? $product["name"]])'  data-lang="{{getLanguageCode($language)}}" data-route="{{ route('admin.product.title-auto-fill') }}">
                                    <div class="btn-svg-wrapper">
                                        <img width="18" height="18" class=""
                                            src="{{ dynamicAsset(path: 'public/assets//back-end/img/ai/blink-right-small.svg') }}" alt="">
                                    </div>
                                    <span class="ai-text-animation d-none" role="status">
                                        {{ translate('Just_a_second') }}
                                    </span>
                                    <span class="btn-text">{{ translate('Generate') }}</span>
                                </button>

                                </div>
                                <div class="outline-wrapper" id="title-container-{{ getLanguageCode($language) }}">
                                    <input type="text" {{ $language == $defaultLanguage ? 'required' : '' }} name="name[]"
                                        id="{{ getLanguageCode($language) }}_name"
                                        value="{{ $translate[$language]['name'] ?? $product['name'] }}"
                                        class="form-control {{ $language == $defaultLanguage ? 'product-title-default-language' : '' }}"
                                        placeholder="{{ translate('ex') }}: {{ translate('new_Product') }}">
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $language }}">
                            <div class="form-group pt-2">
                                <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label" for="{{ getLanguageCode($language) }}_description">
                                    {{ translate('description') }} ({{ strtoupper($language) }})
                                    @if($language == $defaultLanguage)
                                        <span class="input-required-icon text-danger">*</span>
                                    @endif
                                </label>
                                <button type="button" class="btn bg-white text-primary bg-transparent shadow-none border-0 opacity-1 generate_btn_wrapper p-0 auto_fill_description"
                                        id="description-{{ getLanguageCode($language) }}-action-btn" data-item='@json(["description" => $translate[$language]["description"] ?? $product["details"]])'
                                        data-lang="{{ getLanguageCode($language) }}"
                                        data-route="{{ route('admin.product.description-auto-fill') }}">
                                    <div class="btn-svg-wrapper">
                                        <img width="18" height="18" class=""
                                            src="{{ dynamicAsset(path: 'public/assets//back-end/img/ai/blink-right-small.svg') }}" alt="">
                                    </div>
                                    <span class="ai-text-animation d-none" role="status">
                                        {{ translate('Just_a_second') }}
                                    </span>
                                    <span class="btn-text">{{ translate('Generate') }}</span>
                                </button>
                                </div>
                                <div class="outline-wrapper" id="editor-container-{{ getLanguageCode($language) }}">
                                    <div id="description-{{ getLanguageCode($language) }}-editor" class="quill-editor">{!! $translate[$language]['description']??$product['details'] !!}</div>
                                    <textarea name="description[]" id="description-{{getLanguageCode($language)}}"
                                              style="display:none;">{!! $translate[$language]['description']??$product['details'] !!}</textarea>
                                    <div class="blue-fire-animation"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
