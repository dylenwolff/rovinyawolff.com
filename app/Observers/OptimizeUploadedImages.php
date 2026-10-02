<?php

namespace App\Observers;

use App\Models\Award;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class OptimizeUploadedImages
{
    public function saved(Model $model): void
    {
        $attributes = match (true) {
            $model instanceof Project => ['image_path', 'gallery'],
            $model instanceof Award => ['image_path'],
            $model instanceof SiteSetting => ['profile_photo'],
            default => [],
        };

        $updates = [];

        foreach ($attributes as $attribute) {
            if (! $model->wasChanged($attribute)) {
                continue;
            }

            $value = $model->getAttribute($attribute);
            $optimized = is_array($value)
                ? array_map(fn (?string $path) => $this->toAvif($path), $value)
                : $this->toAvif($value);

            if ($optimized !== $value) {
                $updates[$attribute] = $optimized;
            }
        }

        if ($updates !== []) {
            $model->forceFill($updates)->saveQuietly();
        }
    }

    private function toAvif(?string $path): ?string
    {
        if (blank($path) || strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'avif') {
            return $path;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return $path;
        }

        $avifPath = preg_replace('/\.[^.]+$/', '.avif', $path);
        $source = $disk->path($path);
        $destination = $disk->path($avifPath);

        $process = new Process([
            'convert',
            $source,
            '-auto-orient',
            '-resize',
            '1800x1800>',
            '-strip',
            '-quality',
            '60',
            '-define',
            'heic:speed=6',
            $destination,
        ]);

        try {
            $process->setTimeout(120)->run();
        } catch (\Throwable $exception) {
            Log::warning('Image AVIF optimization could not start; original retained.', [
                'path' => $path,
                'error' => $exception->getMessage(),
            ]);

            return $path;
        }

        if (! $process->isSuccessful() || ! is_file($destination)) {
            Log::warning('Image AVIF optimization failed; original retained.', [
                'path' => $path,
                'error' => trim($process->getErrorOutput()),
            ]);

            return $path;
        }

        $disk->delete($path);

        return $avifPath;
    }
}
