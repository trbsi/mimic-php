<?php

namespace Tests\Functional\Api\V2\User\Resources\Profile;

use Tests\Assert\AssertInterface;
use Tests\Assert\AssertAbstract;

class Assert extends AssertAbstract implements AssertInterface 
{
	/**
     * @inheritdoc
     */
    public function getAssertJsonStructureOnSuccess(?string $type = null): array
    {

    }

    /**
     * @inheritdoc
     */
    public function getAssertJsonOnSuccess(array $data, ?string $type = null): array
    {

    }
}