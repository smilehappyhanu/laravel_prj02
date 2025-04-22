<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
{
    public function index() {
        $brands = Brand::latest()->paginate(10);
        return view('pages.admin.brand.list',compact('brands'));
    }

    public function create () {
        return view('pages.admin.brand.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpeg,jpg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        // Save image here
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $brand->image = $file_name;
        $this->generateBrandThumbImages($image,$file_name);
        $brand->save();

        return redirect()->route('brands.index')->with('success','Brand created successfully.');
    }

    public function generateBrandThumbImages($image, $imageName) {
        $destinationPath = public_path('/uploads/brands');
        
        $img = Image::make($image->path());
       // $img->cover(124,124,'top');
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function edit($id) {
        $brand = Brand::find($id);
        return view('pages.admin.brand.edit',compact('brand'));
    }

    public function update (Request $request, $id) {
        $brand = Brand::find($id);
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id,'.id',
            'image' => 'mimes:png,jpeg,jpg|max:2048'
        ]);

        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        // Save image here
        if($request->hasFile('image')) {
            if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
                File::delete(public_path('uploads/brands').'/'.$brand->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->generateBrandThumbImages($image,$file_name);
            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('brands.index')->with('success','Brand updated successfully.');
    }

    public function destroy($id) {
        $brand = Brand::find($id);
        if($brand == null) {
            session()->flash('error','Record not found');
            return redirect()->route('brands.index');
        }
        if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
            File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $brand->delete();
        session()->flash('success','Brand deleted successfully.');
        return redirect()->route('brands.index');
    }
}
