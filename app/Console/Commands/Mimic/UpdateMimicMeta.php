<?php

namespace App\Console\Commands\Mimic;

use Illuminate\Console\Command;

class UpdateMimicMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mimic:update-meta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Mimic and its responses meta';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mimicModel = resolve('MimicModel');
        $mimics = $mimicModel->get();
        $bar = $this->output->createProgressBar(count($mimics));

        foreach ($mimics as $mimic) {
            $dimensions = $this->getWidthAndHeight($mimic);
            $this->updateOrCreate($mimic, $dimensions);
            
            foreach ($mimic->responses as $response) {
                $dimensions = $this->getWidthAndHeight($response, $mimic);
                $this->updateOrCreate($response, $dimensions);
            }
            $bar->advance();
        }
        $bar->finish();
    }

    /**
     * @param object $model
     * @param array $dimensions
     * @return void
     */
    private function updateOrCreate(object $model, array $dimensions): void
    {
        $model->meta()->updateOrCreate(['mimic_id' => $model->id], $dimensions);
    }

    /**
     * @param object $model
     * @param string $file
     * @param object|null $mimic
     * @return array
     */
    private function getWidthAndHeight(object $model, ?object $mimic = null): array
    {
        $data = [
            'height' => null,
            'width' => null,
            'thumbnail_height' => null,
            'thumbnail_width' => null,
        ];

        if (null === $mimic) {
            $mimic = $model;
        }

        if($model->mimic_type === $mimic::TYPE_PHOTO_STRING) {
            $imagePath = $mimic->getAbsolutePathToFile($model->user_id, $model->file, $model);
            list($width, $height) = getimagesize($imagePath);
            $data['width'] = $width;
            $data['height'] = $height;
        }

        if($model->mimic_type === $mimic::TYPE_VIDEO_STRING) {
            $imagePath = $mimic->getAbsolutePathToFile($model->user_id, $model->video_thumb, $model);
            list($width, $height) = getimagesize($imagePath);

            $data['width'] = 1280;
            $data['height'] = 720;
            $data['thumbnail_width'] = $width;
            $data['thumbnail_height'] = $height;

        }

        return $data;
    }
}
