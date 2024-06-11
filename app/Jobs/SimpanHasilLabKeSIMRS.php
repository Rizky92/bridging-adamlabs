<?php

namespace App\Jobs;

use App\Models\SimpanHasilLab;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SimpanHasilLabKeSIMRS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $noLabLIS;

    /**
     * Create a new job instance.
     * 
     * @param  array{no_laboratorium: string}
     */
    public function __construct(array $options)
    {
        $this->noLabLIS = $options['no_laboratorium'];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        //
    }

    private function simpanHasilLab(): void
    {
        $hasilLab = SimpanHasilLab::query()
            ->with('detail')
            ->firstWhere('no_laboratorium', $this->noLabLIS);
    }

    private function updatePermintaan(): void
    {
        
    }

    private function catatJurnal(): bool
    {
        return true;
    }
}
