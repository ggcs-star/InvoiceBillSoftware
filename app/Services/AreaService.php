<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Branch;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AreaService
{
    public function create(Branch $branch, array $data, ?UploadedFile $image): Area
    {
        if ($image) {
            $data['image_path'] = $image->store('areas', 'public');
        }

        return $branch->areas()->create($data);
    }

    public function update(Area $area, array $data, ?UploadedFile $image): Area
    {
        if ($image) {
            if ($area->image_path) {
                Storage::disk('public')->delete($area->image_path);
            }

            $data['image_path'] = $image->store('areas', 'public');
        }

        $area->update($data);

        return $area;
    }
}
