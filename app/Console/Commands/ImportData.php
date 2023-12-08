<?php

namespace App\Console\Commands;

use App\Exceptions\ImportDataFailed;
use App\Models\Customer;
use App\Models\Log;
use App\Models\Product;
use Illuminate\Console\Command;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customers or products';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $module = $this->anticipate(
            'What do you want to import (Products or Customers)?',
            ['Products', 'Customers']
        );

        $data = file_get_contents($this->getUrl($module), false, $this->getAuthHeader());

        $lines = explode(PHP_EOL, $data);
        $model = $this->getModel($module);
        $firstLine = true;

        $this->withProgressBar($lines, function ($line) use ($model, &$firstLine, $module, $data) {
            $lineArray = str_getcsv($line);
            if ($firstLine) {
                if ($lineArray !== $model->getValidationArray()) {
                    throw new ImportDataFailed(
                        ['module' => $module, 'data' => $data],
                        'Import validation failed'
                    );
                }
                $firstLine = false;
            } else {
                $model->createFromImport($lineArray);
            }
        });
    }

    /**
     * Get url
     *
     * @param  string  $module
     * @return string
     * @throws \Exception
     */
    private function getUrl(string $module): string
    {
        $moduleName = strtoupper($module);

        if (env("LOOP_" . $moduleName . "_SUFIX") === null) {
            throw new \Exception('Invalid module name');
        }

        return env("LOOP_BASE_URL") . '/' . env("LOOP_" . $moduleName . "_SUFIX");
    }

    private function getAuthHeader()
    {
        $auth = base64_encode(env('LOOP_USERNAME') . ":" . env('LOOP_PASSWORD'));

        return stream_context_create(["http" => ["header" => "Authorization: Basic $auth"]]);
    }

    /**
     * @throws \Exception
     */
    private function getModel(string $moduleName): Customer|Product
    {
        $module = strtoupper($moduleName);

        if ($module === 'CUSTOMERS') {
            return new Customer();
        }

        if ($module === 'PRODUCTS') {
            return new Product();
        }

        throw new \Exception('Invalid CSV headers');
    }
}
