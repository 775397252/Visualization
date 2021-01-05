<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\RecruitmentInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VisController extends Controller
{
    public $seconds = 3600;

    public function getCompany()
    {
        $id = request('id');
        $company = CompanyInfo::query()->where('company_id', $id)->first();
        return $this->json($company);

    }

    public function hotPosition()
    {
        $city = request('city');
        $value = Cache::get('hotPosition'.$city, function () use ($city) {
            $query = RecruitmentInfo::query();
            if ($city) {
                $query->where('City', $city);
            }
            $company = $query->select(DB::raw('count(PositionName) as count,PositionName'))
                ->groupBy("PositionName")
                ->orderBy("count", 'desc')
                ->limit(10)
                ->get();
            Cache::put('hotPosition'.$city, $company, $this->seconds);
            return $company;
        });
        return $this->json($value);
    }

    public function workYear()
    {
        $type = request('type');
        if ($type) {
            $value = Cache::get('workYear1', function () {
                $k['0-5k'] = RecruitmentInfo::query()->whereBetween('meanSalary', [0, 5000])->count();
                $k['5-10k'] = RecruitmentInfo::query()->whereBetween('meanSalary', [5000, 10000])->count();
                $k['10-20k'] = RecruitmentInfo::query()->whereBetween('meanSalary', [10000, 20000])->count();
                $k['20-30k'] = RecruitmentInfo::query()->whereBetween('meanSalary', [20000, 30000])->count();
                $k['30-50k'] = RecruitmentInfo::query()->whereBetween('meanSalary', [30000, 50000])->count();
                $k['50k以上'] = RecruitmentInfo::query()->whereBetween('meanSalary', [50000, 10000000])->count();
                foreach ($k as $key => $item) {
                    $company[] = ["count" => $item, "Salary" => $key];
                }
                Cache::put('workYear1', $company, $this->seconds);
                return $company;
            });
        } else {
            $value = Cache::get('workYear2', function () {
                $company = RecruitmentInfo::query()->select(DB::raw('count(WorkYear) as count,WorkYear'));
                $company->groupBy("WorkYear")
                    ->orderBy("WorkYear", 'desc');
                $company = $company->get()->sortByDesc('count')->values()->take(10);
                Cache::put('workYear2', $company, $this->seconds);
                return $company;
            });
        }
        return $this->json($value);
    }

    public function city()
    {
        $value = Cache::get('city', function () {
            $company = RecruitmentInfo::query()
                ->select(DB::raw('count(City) as count,City'))
                ->groupBy("City")
                ->orderBy("count", 'desc')
                ->get();
            Cache::put('city', $company, $this->seconds);
            return $company;
        });
        return $this->json($value);
    }

    public function education()
    {
        $StageLabel = [
            "初创型(不需要融资)",
            "成长型(A轮)",
            "上市公司",
            "初创型(天使轮)",
            "成长型(B轮)",
            "成长型(不需要融资)",
            "成熟型(C轮)",
            "成熟型(不需要融资)",
            "成熟型(D轮及以上)",
        ];
        $value = Cache::get('education1', function () use ($StageLabel) {
            $company = RecruitmentInfo::query()
                ->select(DB::raw('count(*) as count,Education,FinanceStage'))
                ->groupBy("Education")
                ->groupBy("FinanceStage")
                ->orderBy("count", 'desc')
                ->get();
            $res = [];
            $Stage = [
                "初创型(不需要融资)" => 0,
                "成长型(A轮)" => 1,
                "上市公司" => 2,
                "初创型(天使轮)" => 3,
                "成长型(B轮)" => 4,
                "成长型(不需要融资)" => 5,
                "成熟型(C轮)" => 6,
                "成熟型(不需要融资)" => 7,
                "成熟型(D轮及以上)" => 8,
            ];
            $Education = [
                "大专" => 0,
                "本科" => 1,
                "学历不限" => 2,
                "硕士" => 3,
                "博士" => 4,
                "高中" => 5,
                "中专" => 6,
                "初中" => 7,
            ];
            $EducationLabel = [
                "大专",
                "本科",
                "学历不限",
                "硕士",
                "博士",
                "高中",
                "中专",
                "初中",
            ];
            foreach ($EducationLabel as $k => $item) {
                for ($i = 0; $i < count($StageLabel); $i++) {
                    $res[$item][$i] = 0;
                }
            }
            foreach ($company as $k => $item) {
                if (in_array($item['Education'], $EducationLabel) && in_array($item["FinanceStage"], $StageLabel)) {
                    $res[$item['Education']][$Stage[$item["FinanceStage"]]] = $item['count'];
                }
            }
            Cache::put('education1', $company, $this->seconds);
            return $res;
        });

        return $this->json($value, $StageLabel);
    }


    public function positionSalary()
    {
        $city = request('city');
        $value = Cache::get('positionSalary'.$city, function () use ($city) {
            $query = RecruitmentInfo::query();
            if ($city) {
                $query->where('City', $city);
            }
            $company = $query->select(DB::raw('sum(meanSalary)/1000 as meanSalary,count(1) as count,PositionName'))
                ->groupBy("PositionName")
                ->orderBy("meanSalary", 'desc')
                ->limit(10)
                ->get();
            foreach ($company as $k => $item) {
                $company[$k]['order'] = round($item['meanSalary'] / $item['count'], 1) * 10;
                $company[$k]['meanSalary'] = round($item['meanSalary'] / $item['count'], 1) . 'k';
            }
            $company = collect($company)->sortByDesc('order')->values();
            Cache::put('positionSalary'.$city, $company, $this->seconds);
            return $company;
        });

        return $this->json($value);
    }
}
