<?php

declare(strict_types=1);


namespace App\Interfaces;

interface ImportInterface
{
    public function getValidationArray(): array;

    public function getImportUrl(): string;

    public function createFromImport(array $data): void;
}
