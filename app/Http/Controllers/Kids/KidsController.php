<?php

namespace App\Http\Controllers\Kids;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kids;
use App\Http\Requests\Kids\KidsImageRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use App\Services\Kids\KidsService;
use App\Http\Requests\Kids\KidsCreateRequest;
use App\Http\Requests\Kids\KidsUpdateRequest;

class KidsController extends Controller{

    public function __construct(protected KidsService $kidsService) {}

    public function all(){
        return response()->json($this->kidsService->all());
    }

    public function active(){
        return response()->json($this->kidsService->active());
    }

    public function inactive(){
        return response()->json($this->kidsService->inactive());
    }

    public function isActive(Request $request){
        return response()->json(
            $this->kidsService->toggleActive($request)
        );
    }

    public function create(KidsCreateRequest $request) {
        $result = $this->kidsService->create($request);
        if (isset($result['errors'])) {return response()->json($result['errors'], $result['status']);}
        return response()->json([
            'message' => $result['message'],
            'kid'     => $result['kid']
        ], $result['status']);
    }

    public function createPhoto(KidsImageRequest $request, $id) {
        return $this->uploadFile($request, $id, 'kids/photos', 'photo_path');
    }

    public function createDocument(KidsImageRequest $request, $id) {
        return $this->uploadFile($request, $id, 'kids/documents', 'document_photo_path');
    }

    public function createPassport(KidsImageRequest $request, $id) { 
        return $this->uploadFile($request, $id, 'kids/passport', 'guardian_passport_path');
    }

    public function createCertificate(KidsImageRequest $request, $id) {
        return $this->uploadFile($request, $id, 'kids/certificates', 'health_certificate_path');
    }

    public function update(Request $request, $id) {
        $kid = Kids::findOrFail($id);
        $kid->update($request->except(['photo_path', 'document_photo_path', 'guardian_passport_path', 'health_certificate_path']));
        return response()->json(['message' => 'Yangilandi', 'kid' => $kid]);
    }

    private function uploadFile(Request $request, $id, $folder, $column) {
        if (!$request->hasFile('photo')) {
            return response()->json(['error' => 'Fayl yuborilmadi (photo kaliti bilan yuboring)'], 422);
        }
        $file = $request->file('photo');
        $kid = Kids::findOrFail($id);
        $oldPath = $kid->getRawOriginal($column);
        if ($oldPath && Storage::disk('public')->exists($oldPath)) Storage::disk('public')->delete($oldPath);
        try {
            $fileName = Str::random(32) . '.jpg';
            $savePath = $folder . '/' . $fileName;
            $image = Image::read($file->getRealPath());
            $image->scale(width: 1000); 
            $encoded = $image->toJpeg(70); 
            Storage::disk('public')->put($savePath, (string) $encoded);
            $kid->update([$column => $savePath]);
            return response()->json(['message' => 'Yuklandi', 'path' => asset('storage/' . $savePath)]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function upadteKid(KidsUpdateRequest $request, $id){
        $result = $this->kidsService->update($request, $id);
        return response()->json([
            'message' => $result['message'],
            'kid'     => $result['kid'],
        ], $result['status']);
    }

    
}