<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parameter;
use App\Models\ParameterImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParameterController extends Controller
{
    public function index(Request $request)
    {
        $query = Parameter::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('id', $search)->orWhere('title', 'like', '%' . $search . '%');
        }

        $parameters = $query->with('images')->get(); // Загружаем связанные записи из таблицы parameter_images
        return view('index', compact('parameters'));
    }

    public function storeImages(Request $request, $id)
    {
        $request->validate([
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg',
            'icon_gray' => 'nullable|image|mimes:jpeg,png,jpg,svg',
        ]);

        $parameter = Parameter::findOrFail($id);
        $images = $parameter->images ?? new ParameterImage(['parameter_id' => $id]);

        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconName = $this->generateFileName($icon);
            $iconPath = $icon->storeAs('images', $iconName, 'public');
            if ($images->icon) {
                Storage::disk('public')->delete($images->icon);
            }
            $images->icon = $iconPath;
        }

        if ($request->hasFile('icon_gray')) {
            $iconGray = $request->file('icon_gray');
            $iconGrayName = $this->generateFileName($iconGray);
            $iconGrayPath = $iconGray->storeAs('images', $iconGrayName, 'public');
            if ($images->icon_gray) {
                Storage::disk('public')->delete($images->icon_gray);
            }
            $images->icon_gray = $iconGrayPath;            
        }

        $images->save();
        return redirect()->route('parameters.index')->with('success', 'Успешно загружено');
    }

    public function deleteImage($id, $imageName)
    {
        $parameter = Parameter::findOrFail($id);
        $images = $parameter->images;

        if ($imageName == 'icon' && $images->icon) {
            Storage::disk('public')->delete($images->icon);
            $images->icon = null;
        } elseif ($imageName == 'icon_gray' && $images->icon_gray) {
            Storage::disk('public')->delete($images->icon_gray);
            $images->icon_gray = null;
        }

        $images->save();
        return redirect()->route('parameters.index')->with('succes', ' Успешно удалено');        
    }

    private function generateFileName($file)
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $transliteratedName = Str::ascii($originalName);
        $slugName = Str::slug($transliteratedName);
        $timestamp = time();
        $extension = $file->getClientOriginalExtension();

        return $slugName . '-' . $timestamp . '.' . $extension;
    }

    public function getParameters() {
        $parameters = Parameter::with('images')->where('type', 2)->get();
        $result = $parameters->map(function($parameter) {
            return[
                'title' => $parameter->title,
                'type' => $parameter->type,
                'images' => $parameter->images ? [
                    'icon' => $parameter->images->icon ? [
                        'name' => pathinfo($parameter->images->icon, PATHINFO_BASENAME),
                        'url' => url('/storage/' . $parameter->images->icon)
                    ] : null,
                    'icon_gray' => $parameter->images->icon_gray ? [
                        'name' => pathinfo($parameter->images->icon_gray, PATHINFO_BASENAME),
                        'url' => url('/storage/' . $parameter->images->icon_gray)
                    ] : null,
                ]: null,
            ];
        });
        return response()->json($result);
    }
}
