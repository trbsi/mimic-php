<?php
namespace Tests\Functional\Api\V2\User;

use Tests\Assert\AssertInterface;
use Tests\Assert\AssertAbstract;

class Assert extends AssertAbstract implements AssertInterface
{

    /**
     * @inheritdoc
     */
    public function getAssertJsonStructureOnSuccess(?string $type = null): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAssertJsonOnSuccess(array $data, ?string $type = null): array
    {
        return [];
    }
}
