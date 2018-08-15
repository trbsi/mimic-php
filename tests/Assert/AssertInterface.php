<?php

namespace Tests\Assert;

interface AssertInterface
{
    /**
     * @param  string|null $type What type of response you want to return
     * @return array
     */
    public function getAssertJsonStructureOnSuccess(?string $type = null): array;

    /**
     * @param array $data
     * @param  string|null $type What type of response you want to return
     * @return array
     */
    public function getAssertJsonOnSuccess(array $data, ?string $type = null): array;
}
