<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('items:check-expired')
    ->daily()
    ->description('Otomatis ubah status barang temuan yang tidak diklaim lebih dari 30 hari menjadi expired_charity');
