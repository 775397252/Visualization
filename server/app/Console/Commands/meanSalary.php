<?php

namespace App\Console\Commands;

use App\Models\CompanyInfo;
use App\Models\PositionInfo;
use App\Models\RecruitmentInfo;
use Illuminate\Console\Command;

class meanSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        // 正在更新平均工资
        RecruitmentInfo::query()->whereNull('meanSalary')->chunkById(100, function ($values) {
            foreach ($values as $value) {
                echo "正在更新平均工资：{$value['PositionId']}" . PHP_EOL;
                $meanSalary = explode('-', $value['Salary']);
                if (count($meanSalary) == 2) {
                    $meanSalary = ((int)$meanSalary[0] +(int)$meanSalary[1]) * 500;
                    RecruitmentInfo::query()
                        ->where('PositionId', $value['PositionId'])
                        ->where('CompanyId', $value['CompanyId'])
                        ->update([
                            'meanSalary'=>$meanSalary
                        ]);
                }
            }
        }, 'PositionId');

        return 0;
    }
}
