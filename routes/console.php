<?php

use Illuminate\Support\Facades\Schedule;
use App\Services\AutoUpdateService;

/*
 |---------------------------------------------------------
 | Guruh bolalari uchun oylik to‘lov
 | Har 30 daqiqada ishlaydi
 |---------------------------------------------------------
*/
Schedule::call(function () {app(AutoUpdateService::class)->ChildPayRun();})->name('child-pay-run-every-30-minutes')->everyThirtyMinutes()->withoutOverlapping()->timezone(config('app.timezone'));
