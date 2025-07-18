<?php

namespace Tests\Feature\Enums;


use App\Enums\SenderTypeEnum;
use Tests\Helpers\TestingTraits\EnumsTestTrait;
use Tests\TestCase;

class SenderTypeEnumTest extends TestCase
{
    use EnumsTestTrait;

    protected function enumClass(): string
    {
        return SenderTypeEnum::class;
    }

    protected function translationPrefix(): string
    {
        return 'enums/sender_type';
    }
}
