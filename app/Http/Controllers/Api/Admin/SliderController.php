<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->paginate(5);
        //return with Api Resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        $slider = Slider::create([
            'image' => $image->hashName(),
        ]);

        if($slider) {
            //return success with Api Resource
            return new SliderResource(true, 'Slider Data Saved Successfully!', $slider);
        }

        //return failed with Api Resource
        return new SliderResource(false, 'Slider Data Saved Failed!', null);
    }

    public function destroy(Slider $slider)
    {
        //remove image
        Storage::disk('local')->delete('public/sliders/'.basename($slider->image));

        if($slider->delete()) {
            //return success with Api Resource
            return new SliderResource(true, 'Slider Data Deleted Successfully!', null);
        }

        //return failed with Api Resource
        return new SliderResource(false, 'Slider Data Deleted Failed!', null);
    }
}
