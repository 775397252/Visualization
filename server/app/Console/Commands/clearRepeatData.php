<?php

namespace App\Console\Commands;

use App\Models\CompanyInfo;
use App\Models\PositionInfo;
use Illuminate\Console\Command;

class clearRepeatData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear';

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
        // 清除职位信息
        PositionInfo::query()->chunkById(100, function ($values) {
            foreach ($values as $value) {
                $is = PositionInfo::query()->where('position_id', $value['position_id'])->count();
                if ($is > 1) {
                    echo "正在清除重复职位信息company_id：{$value['position_id']}".PHP_EOL;
                    PositionInfo::query()->where('position_id', $value['position_id'])->limit(1)->delete();
                }
            }
        },'position_id');

        // 清除公司信息
        CompanyInfo::query()->chunkById(100, function ($values) {
            foreach ($values as $value) {
                $is = CompanyInfo::query()->where('company_id', $value['company_id'])->count();
                if ($is > 1) {
                    echo "正在清除重复公司信息company_id：{$value['company_id']}".PHP_EOL;
                    CompanyInfo::query()->where('company_id', $value['company_id'])->limit(1)->delete();
                }
            }
        }, 'company_id');

        return 0;
    }
}
