<?php

namespace App\Models;

use App\Interfaces\ImportInterface;
use App\Providers\ImportSkipped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements ImportInterface
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'price'];

    public function getValidationArray(): array
    {
        return [
            'ID',
            "productname",
            "price",
        ];
    }

    public function getImportUrl(): string
    {
        return env("LOOP_BASE_URL") . '/' . env("LOOP_PRODUCTS_SUFIX");
    }

    public function createFromImport(array $data): void
    {
        try {
            self::query()->create([
                'id' => $data[0],
                'name' => $data[1],
                'price' => $data[2],
            ]);
        } catch (\Exception $exception) {
            ImportSkipped::dispatch($data, 'Products');
        }
    }
}
