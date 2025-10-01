<?php

namespace Modules\AI\app\Contracts;

interface PromptTemplateInterface
{
    public function build(?string $context = null, ?string $langCode = null, ?string $description = null): string;

    public function getType(): string;
}
