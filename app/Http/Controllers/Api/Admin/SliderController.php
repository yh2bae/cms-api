<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
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

        $img = $request->file('image');
        $slider['image'] = $img->getClientOriginalName();

        $filePath = public_path('/upload/sliders');
        $img->move($filePath, $slider['image']);

        $slider = Slider::create([
            'image' => $slider['image'],
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
        $old_image = public_path('upload/sliders/'.basename($slider->image)); 
        if(File::exists($old_image)) {
            File::delete($old_image);
        } 

        if($slider->delete()) {
            //return success with Api Resource
            return new SliderResource(true, 'Slider Data Deleted Successfully!', null);
        }

        //return failed with Api Resource
        return new SliderResource(false, 'Slider Data Deleted Failed!', null);
    }
}
