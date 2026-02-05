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

/**
 * @OA\Info(title="SmartKids API Documentation", version="1.0.0")
 * @OA\Server(url="/api")
 */
class KidsController extends Controller
{
    public function all() {
        return response()->json(Kids::with('creator')->latest()->get());
    }

    public function active() {
        return response()->json(Kids::where('is_active', true)->with('creator')->latest()->get());
    }

    public function inactive() {
        return response()->json(Kids::where('is_active', false)->with('creator')->latest()->get());
    }

    public function isActive(Request $request) {
        $request->validate(['id' => 'required|exists:kids,id']);
        $kid = Kids::findOrFail($request->id);
        $kid->update(['is_active' => !$kid->is_active]);
        return response()->json(['message' => 'Status o\'zgardi', 'is_active' => $kid->is_active]);
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'full_name'       => 'required|string|max:255',
            'birth_date'      => 'required|date',
            'document_series' => 'required|string|unique:kids,document_series',
            'guardian_name'   => 'required|string',
            'guardian_phone'  => 'required|string',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $kid = Kids::create($data);
        return response()->json(['message' => 'Bola yaratildi', 'kid' => $kid], 210);
    }

    /**
     * @OA\Post(
     * path="/kids/create/photo/{id}",
     * summary="Bolaning rasmi",
     * tags={"Kids Media"},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/KidsImageRequest"))),
     * @OA\Response(response=200, description="Muvaffaqiyatli")
     * )
     */
    public function createPhoto(KidsImageRequest $request, $id) {
        return $this->uploadFile($request, $id, 'kids/photos', 'photo_path');
    }

    /**
     * @OA\Post(
     * path="/kids/create/document/{id}",
     * summary="Guvohnoma rasmi",
     * tags={"Kids Media"},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/KidsImageRequest"))),
     * @OA\Response(response=200, description="Muvaffaqiyatli")
     * )
     */
    public function createDocument(KidsImageRequest $request, $id) {
        return $this->uploadFile($request, $id, 'kids/documents', 'document_photo_path');
    }

    /**
     * @OA\Post(
     * path="/kids/create/passport/{id}",
     * summary="Pasport rasmi",
     * tags={"Kids Media"},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/KidsImageRequest"))),
     * @OA\Response(response=200, description="Muvaffaqiyatli")
     * )
     */
    public function createPassport(KidsImageRequest $request, $id) { 
        return $this->uploadFile($request, $id, 'kids/passport', 'guardian_passport_path');
    }

    /**
     * @OA\Post(
     * path="/kids/create/certificate/{id}",
     * summary="Sertifikat rasmi",
     * tags={"Kids Media"},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/KidsImageRequest"))),
     * @OA\Response(response=200, description="Muvaffaqiyatli")
     * )
     */
    public function createCertificate(KidsImageRequest $request, $id) {
        return $this->uploadFile($request, $id, 'kids/certificates', 'health_certificate_path');
    }

    public function update(Request $request, $id) {
        $kid = Kids::findOrFail($id);
        $kid->update($request->except(['photo_path', 'document_photo_path', 'guardian_passport_path', 'health_certificate_path']));
        return response()->json(['message' => 'Yangilandi', 'kid' => $kid]);
    }

    private function uploadFile(Request $request, $id, $folder, $column) {
        // Universal 'photo' kalitini tekshiramiz
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
}