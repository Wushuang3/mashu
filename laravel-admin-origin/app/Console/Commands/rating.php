<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rating as R;

class rating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rating:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计教练评价';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $aa = R::get();
        var_dump($aa);die;
        echo 111;die;
    }
}
