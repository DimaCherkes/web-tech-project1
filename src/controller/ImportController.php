<?php

namespace App\controller;

use App\Core\Logger;
use App\service\ImportService;

class ImportController
{
    private ImportService $importService;

    public function __construct()
    {
        $this->importService = new ImportService();
    }

    public function import() {

    }

}