<form action="{{ route('admin.category.update') }}" method="POST"
      enctype="multipart/form-data">
    @csrf
    <div class="offcanvas offcanvas-end" tabindex="-1" id="categoryEditOffcanvas-{{ $category['id'] }}"
         aria-labelledby="categoryEditOffcanvasLabel" style="--bs-offcanvas-width: 500px;">
        <div class="offcanvas-header bg-body">
            <h3 class="mb-0">
                @if ($category['position'] == 1)
                    {{ translate('Edit_Sub_Category') }}
                @elseif($category['position'] == 2)
                    {{ translate('Edit_Sub_Sub_Category') }}
                @else
                    {{ translate('Edit_Category') }}
                @endif
            </h3>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-flex flex-column gap-20">
                @if($category['position'] == 0)
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <label for="" class="form-label mb-0 flex-grow-1">
                            {{ translate('availability') }}
                            <span class="tooltip-icon"
                                  data-bs-toggle="tooltip" data-bs-placement="bottom"
                                  aria-label="{{ translate('turn_on_to_show_this_category_as_a_home_category,_or_turn_off_to_hide_it.') }}"
                                  data-bs-title="{{ translate('turn_on_to_show_this_category_as_a_home_category,_or_turn_off_to_hide_it.') }}"
                            >
                            <i class="fi fi-sr-info"></i>
                        </span>
                        </label>
                        <label
                            class="d-flex justify-content-between align-items-center gap-2 border rounded px-3 py-10 user-select-none flex-grow-1">
                            <span class="fw-medium text-dark">{{ translate('status') }}</span>
                            <label class="switcher">
                                <input type="checkbox" class="switcher_input" value="1" id="" name="home_status"
                                    {{ $category['home_status'] == 1 ? 'checked' : '' }}>
                                <span class="switcher_control"></span>
                            </label>
                        </label>
                    </div>
                @endif

                <div class="p-12 p-sm-20 bg-section rounded">
                    <input type="hidden" name="id" value="{{ $category['id'] }}">
                    <div class="table-responsive w-auto overflow-y-hidden mb-20">
                        <div class="position-relative nav--tab-wrapper">
                            <ul class="nav nav-pills nav--tab lang_tab" id="pills-tab" role="tablist">
                                @foreach ($languages as $lang)
                                    <li class="nav-item px-0">
                                        <a data-bs-toggle="pill" data-bs-target="#{{ $lang }}-edit-form-{{ $category['id'] }}" role="tab"
                                           class="nav-link px-2 {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                           id="{{ $lang }}-link">
                                            {{ ucfirst(getLanguageName($lang)) . '(' . strtoupper($lang) . ')' }}
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
                    <div class="tab-content" id="pills-tabContent">
                        @foreach ($languages as $lang)
                            <div class="tab-pane fade {{ $lang == $defaultLanguage ? 'show active' : '' }}"
                                 id="{{ $lang }}-edit-form-{{ $category['id'] }}"
                                 aria-labelledby="{{ $lang }}-link" role="tabpanel">
                                <?php
                                    if (count($category['translations'])) {
                                        $translate = [];
                                        foreach ($category['translations'] as $t) {
                                            if ($t->locale == $lang && $t->key == 'name') {
                                                $translate[$lang]['name'] = $t->value;
                                            }
                                        }
                                    }
                                    $categoryName = ($category['position'] == 1) ? "sub_category_Name" : (($category['position'] == 2) ? "sub_sub_category_Name" : "category_Name");
                                ?>
                                <div class="form-group mb-20">
                                    <label class="form-label text-capitalize">
                                        {{ translate($categoryName) }} ({{ strtoupper($lang) }})
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name[]"
                                           value="{{ $lang == $defaultLanguage ? $category['name'] : $translate[$lang]['name'] ?? '' }}"
                                           class="form-control" placeholder="{{ translate('type_category_name') }}"
                                        {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                </div>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                <input type="hidden" name="id" value="{{ $category['id'] }}">
                            </div>
                        @endforeach
                    </div>

                    @if($category['position'] == 1 && isset($parentCategories))
                        <div class="form-group mb-20">
                            <label class="form-label"
                                   for="exampleFormControlSelect1">{{ translate('main_Category') }}
                                <span class="text-danger">*</span>
                                <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                      aria-label="{{ translate('select_the_main_category_under_which_this_item_will_be_listed.') }}"
                                      data-bs-title="{{ translate('select_the_main_category_under_which_this_item_will_be_listed.') }}">
                                    <i class="fi fi-sr-info"></i>
                                </span>
                            </label>
                            <select class="custom-select form-control" name="parent_id"
                                    data-placeholder="{{ translate('Select') }}" required>
                                <option value=""></option>
                                @foreach($parentCategories as $parentCategory)
                                    <option value="{{ $parentCategory['id'] }}"
                                        {{ $parentCategory['id'] == $category['parent_id'] ? 'selected' : '' }}>
                                        {{ $parentCategory['defaultname']}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-group mb-20">
                        <label class="form-label text-capitalize" for="priority">
                            {{ translate('priority') }}
                            <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                  aria-label="{{ translate('the_lowest_number_will_get_the_highest_priority') }}"
                                  data-bs-title="{{ translate('the_lowest_number_will_get_the_highest_priority') }}">
                                    <i class="fi fi-sr-info"></i>
                                </span>
                        </label>
                        <div>
                            <select class="custom-select" name="priority" id=""
                                    data-placeholder="{{ translate('set_Priority') }}" required>
                                <option></option>
                                @for ($index = 0; $index <= 10; $index++)
                                    <option value="{{ $index }}"
                                        {{ $category['priority'] == $index ? 'selected' : '' }}>
                                        {{ $index }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    @if ($category->position == 0)
                        @if ($categoryWiseTax)
                            <div class="form-group mb-0">
                                <label class="form-label" for="tax-ids-update-{{ $category['id'] }}">
                                    {{ translate('Select_Vat/Tax_Rate') }}
                                    <span class="input-required-icon">*</span>
                                </label>

                                <select class="custom-select multiple-select2" id="tax-ids-update-{{ $category['id'] }}"
                                        name="tax_ids[]" multiple="multiple"
                                        data-placeholder="{{ translate('type_&_Select_Vat/Tax_Rate') }}">
                                    @foreach ($taxVats as $taxVat)
                                        <option {{ in_array($taxVat->id, $category?->taxVats->pluck('tax_id')->toArray()) ? 'selected' : '' }}
                                                value="{{ $taxVat->id }}"> {{ $taxVat->name }}
                                            ({{ $taxVat->tax_rate }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endif
                </div>

                @if ($category->position == 0 || ($category->position == 1 && theme_root_path() == 'theme_aster'))
                    <div class="p-12 p-sm-20 bg-section rounded">
                    <div class="d-flex flex-column gap-30">
                        <div class="text-center">
                            <label for="" class="form-label fw-semibold mb-1">
                                {{ translate('category_Logo') }}
                                <span class="text-danger">*</span>
                            </label>
                            <p class="fs-12 mb-0"> {{ translate('Upload_image') }}</p>
                        </div>
                        <div class="upload-file">
                            <input type="file" name="image" id="category-image"
                                   class="upload-file__input single_file_input"
                                   accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                            <label class="upload-file__wrapper">
                                <div class="upload-file-textbox text-center">
                                    <img width="34" height="34" class="svg"
                                         src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}"
                                         alt="image upload">
                                    <h6 class="mt-1 fw-medium lh-base text-center">
                                            <span class="text-info">
                                                {{ translate('Click_to_upload') }}
                                            </span><br>
                                        {{ translate('or_drag_and_drop') }}
                                    </h6>
                                </div>
                                <img class="upload-file-img" loading="lazy"
                                     src="{{ getStorageImages(path: $category->icon_full_url, type: 'backend-basic') ?? '' }}"
                                     data-default-src="{{ getStorageImages(path: $category->icon_full_url, type: 'backend-basic') ?? '' }}"
                                     alt="">
                            </label>

                            <div class="overlay">
                                <div class="d-flex gap-10 justify-content-center align-items-center h-100">
                                    <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                        <i class="fi fi-sr-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                        <i class="fi fi-rr-camera"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <p class="fs-10 mb-0 text-center">
                            {{ translate('JPG,_JPEG,_PNG_image_size_:_max_2_MB') }}
                            <span class="text-dark fw-medium">
                                {{ THEME_RATIO[theme_root_path()]['Category Image'] }}
                            </span>
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="offcanvas-footer shadow-popup">
            <div class="d-flex justify-content-center flex-wrap gap-3 bg-white px-3 py-2">
                <button type="reset" id="reset" class="btn btn-secondary flex-grow-1">
                    {{ translate('reset') }}
                </button>
                <button type="submit" class="btn btn-primary flex-grow-1">
                    {{ translate('update') }}
                </button>
            </div>
        </div>
    </div>
</form>
