<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Support\Config\UserAvatarConfig;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\Laravel\Facades\Image;

class OptimizeAvatarImage implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * Create a new job instance.
     *
     * @param string $path
     */
    public function __construct(private string $path)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $disk = Storage::disk('s3-user-avatar');
        $scale = UserAvatarConfig::AVATAR_SCALE;
        $quality = UserAvatarConfig::QUALITY;

        $image = Image::read($disk->get($this->path))
                ->scaleDown($scale)
                ->crop($scale, $scale, position: 'center')
                ->encode(new AutoEncoder($quality));

        $disk->put($this->path, $image);
    }
}
