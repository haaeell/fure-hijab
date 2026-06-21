<?php

namespace App\Http\Controllers;

use App\Models\LandingBanner;
use App\Models\LandingSection;
use App\Traits\UploadsImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingContentController extends Controller
{
    use UploadsImages;

    public function index()
    {
        $banners = LandingBanner::orderBy('sort_order')->latest()->get();
        $sections = LandingSection::orderBy('sort_order')->latest()->get();

        return view('landing-content.index', compact('banners', 'sections'));
    }

    public function storeBanner(Request $request)
    {
        $data = $this->validateBanner($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadAsWebp($request->file('image'), 'landing/banners');
        }

        if ($request->hasFile('mobile_image')) {
            $data['mobile_image'] = $this->uploadAsWebp($request->file('mobile_image'), 'landing/banners');
        }

        $data['is_active'] = $request->has('is_active');

        LandingBanner::create($data);

        return redirect()->back()->with('success', 'Banner berhasil ditambahkan.');
    }

    public function updateBanner(Request $request, LandingBanner $banner)
    {
        $data = $this->validateBanner($request, true);

        if ($request->hasFile('image')) {
            $this->deletePublicFile($banner->image);
            $data['image'] = $this->uploadAsWebp($request->file('image'), 'landing/banners');
        }

        if ($request->hasFile('mobile_image')) {
            $this->deletePublicFile($banner->mobile_image);
            $data['mobile_image'] = $this->uploadAsWebp($request->file('mobile_image'), 'landing/banners');
        }

        $data['is_active'] = $request->has('is_active');

        $banner->update($data);

        return redirect()->back()->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroyBanner(LandingBanner $banner)
    {
        $this->deletePublicFile($banner->image);
        $this->deletePublicFile($banner->mobile_image);
        $banner->delete();

        return redirect()->back()->with('success', 'Banner berhasil dihapus.');
    }

    public function storeSection(Request $request)
    {
        $data = $this->validateSection($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadAsWebp($request->file('image'), 'landing/sections');
        }

        $data['is_active'] = $request->has('is_active');

        LandingSection::create($data);

        return redirect()->back()->with('success', 'Section berhasil ditambahkan.');
    }

    public function updateSection(Request $request, LandingSection $section)
    {
        $data = $this->validateSection($request, true);

        if ($request->hasFile('image')) {
            $this->deletePublicFile($section->image);
            $data['image'] = $this->uploadAsWebp($request->file('image'), 'landing/sections');
        }

        $data['is_active'] = $request->has('is_active');

        $section->update($data);

        return redirect()->back()->with('success', 'Section berhasil diperbarui.');
    }

    public function destroySection(LandingSection $section)
    {
        $this->deletePublicFile($section->image);
        $section->delete();

        return redirect()->back()->with('success', 'Section berhasil dihapus.');
    }

    private function validateBanner(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'eyebrow'              => 'nullable|string|max:120',
            'title'                => 'nullable|string|max:160',
            'subtitle'             => 'nullable|string|max:500',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'mobile_image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'primary_button_text'  => 'nullable|string|max:60',
            'primary_button_url'   => 'nullable|string|max:255',
            'secondary_button_text' => 'nullable|string|max:60',
            'secondary_button_url' => 'nullable|string|max:255',
            'sort_order'           => 'nullable|integer|min:0',
            'is_active'            => 'nullable',
        ]);
    }

    private function validateSection(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'eyebrow'          => 'nullable|string|max:120',
            'title'            => 'required|string|max:160',
            'subtitle'         => 'nullable|string|max:500',
            'button_text'      => 'nullable|string|max:60',
            'button_url'       => 'nullable|string|max:255',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'icon'             => 'nullable|string|max:80',
            'background_color' => 'required|string|max:20',
            'text_color'       => 'required|string|max:20',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'nullable',
        ]);
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
