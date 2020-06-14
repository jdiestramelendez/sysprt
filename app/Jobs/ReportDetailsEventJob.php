<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exports\ReportDetailsEventExport;

class ReportDetailsEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ids;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // \Excel::store(new ReportDetailsEventExport($this->ids))->queue('invoices.xlsx');

// return back()->withSuccess('Export started!');

ini_set('memory_limit', '2G'); // or you could use 1G
ini_set('max_execution_time', 0); //300 seconds = 5 minutes

(new ReportDetailsEventExport($this->ids))->store('driver_detailed.csv', \Maatwebsite\Excel\Excel::CSV);

        // \Excel::store(new ReportDetailsEventExport($this->ids), 'driver_detailed.xlsx');
        //  (new ReportDetailsEventExport)->download('driver_detailed.xlsx');
    }
}
