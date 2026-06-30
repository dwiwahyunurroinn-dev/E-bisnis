<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan otomatis pesanan pending yang kedaluwarsa (tiap 10 menit).
Schedule::command('pesanan:expire')->everyTenMinutes()->withoutOverlapping();
