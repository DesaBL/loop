<?php

namespace App\Models;


use App\Interfaces\ImportInterface;
use App\Providers\ImportSkipped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model implements ImportInterface
{
    use HasFactory;

    protected $fillable = ['id', 'job_title', 'email', 'name', 'registered_at', 'phone_number'];

    public function getValidationArray(): array
    {
        return [
            'ID',
            "Job Title",
            "Email Address",
            "FirstName LastName",
            "registered_since",
            "phone",
        ];
    }

    public function getImportUrl(): string
    {
        return env("LOOP_BASE_URL") . '/' . env("LOOP_CUSTOMERS_SUFIX");
    }

    public function createFromImport(array $data): void
    {
        try {
            self::query()->create([
                'id' => $data[0],
                'job_title' => $data[1],
                'email' => $data[2],
                'name' => $data[3],
                'registered_at' => Carbon::createFromDate($data[4]),
                'phone_number' => $data[5],
            ]);
        } catch (\Exception $exception) {
            ImportSkipped::dispatch($data, 'Customer');
        }
    }
}
