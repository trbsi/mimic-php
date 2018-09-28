<?php
namespace App\Helpers\Traits;

trait PaginationTrait
{
    /**
     * @param  object $model
     * @return array
     */
    private function getPagination(object $model): array
    {
        return [
            'pagination' => array_except($model->toArray(), 'data'),
        ];
    }
}
