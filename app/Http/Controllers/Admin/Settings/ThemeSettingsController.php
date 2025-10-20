<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Utils\Helpers;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Devrabiul\ToastMagic\Facades\ToastMagic;


class ThemeSettingsController extends Controller
{

    public function index()
    {
        $color_theme = ThemeSetting::get('color_theme');
        $fonts = Helpers::getGoogleFontsList();
        $selectedFont = ThemeSetting::get('google_font');
        return view('admin-views.system-setup.theme_settings', compact('fonts', 'selectedFont', 'color_theme'));
    }

    public function update(Request $request)
    {
        if ($request->has('google_font')) {
            ThemeSetting::set('google_font', $request->google_font);
        }
        ThemeSetting::set('color_theme', $request->color_theme);
        
        ToastMagic::success(translate('updated_successfully'));
        return back();
    }
}
