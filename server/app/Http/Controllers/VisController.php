<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\RecruitmentInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class VisController extends Controller
{
    public function getCompany()
    {
        $id = request('id');
        $company = CompanyInfo::query()->where('company_id', $id)->first();
        return $this->json($company);

    }

    public function hotPosition()
    {
        $city = request('city');
        $query = RecruitmentInfo::query();
        if ($city) {
            $query->where('City', $city);
        }
        $company = $query->select(DB::raw('count(PositionName) as count,PositionName'))
            ->groupBy("PositionName")
            ->orderBy("count", 'desc')
            ->limit(10)
            ->get();
        return $this->json($company);
    }

    public function workYear()
    {
        $type = request('type');
        if ($type) {
            $company = RecruitmentInfo::query()->select(DB::raw('count(Salary) as count,Salary'));
            $company->groupBy("Salary")->orderBy("Salary", 'desc');
        } else {
            $company = RecruitmentInfo::query()->select(DB::raw('count(WorkYear) as count,WorkYear'));
            $company->groupBy("WorkYear")
                ->orderBy("WorkYear", 'desc');
        }
        $company = $company->get()->sortByDesc('count')->values()->take(10);
        return $this->json($company);
    }

    public function city()
    {
        $company = RecruitmentInfo::query()
            ->select(DB::raw('count(City) as count,City'))
            ->groupBy("City")
            ->orderBy("count", 'desc')
            ->get();

        return $this->json($company);
    }

    public function education()
    {
        $company = RecruitmentInfo::query()
            ->select(DB::raw('count(*) as count,Education,FinanceStage'))
            ->groupBy("Education")
            ->groupBy("FinanceStage")
            ->orderBy("count", 'desc')
            ->get();
//        return $company;
        $res = [];
        foreach ($company as $item) {
            if (false !== strpos($item['FinanceStage'], 'null')) {

            } else {
                $res[$item['FinanceStage']][] = $item;
            }
        }
        //
        $column = [
            "初创型(未融资)",
            "成长型(A轮)",
            "上市公司",
            "初创型(天使轮)",
            "成长型(B轮)",
            "成长型(B轮)",
            "成长型(不需要融资)",
            "成熟型(C轮)",
            "成熟型(不需要融资)",
            "初创型(不需要融资)",
            "成熟型(D轮及以上)",
        ];
        $column2 = [
            "大专" => 0,
            "本科" => 1,
            "学历不限" => 2,
            "硕士" => 3,
            "博士" => 4,
            "高中" => 5,
            "中专" => 6,
            "初中" => 7,
        ];
        $label = [
            "大专" ,
            "本科" ,
            "学历不限" ,
            "硕士",
            "博士" ,
            "高中" ,
            "中专" ,
            "初中" ,
        ];
        foreach ($column as $item) {
            for ($i = 0; $i < 8; $i++) {
                $res[$item][$i] = 0;
            }
        }
        foreach ($company as $k => $item) {
//            $res[$k]['大专']=
            if (in_array($item['FinanceStage'], $column)) {
                $res[$item['FinanceStage']][$column2[$item["Education"]]] = $item['count'];
            }

        }
        return $this->json($res,$label);
    }


    public function positionSalary()
    {
        $city = request('city');
        $query = RecruitmentInfo::query();
        if ($city) {
            $query->where('City', $city);
        }
        $company = $query->select(DB::raw('sum(meanSalary)/1000 as meanSalary,count(1) as count,PositionName'))
            ->groupBy("PositionName")
            ->orderBy("meanSalary", 'desc')
            ->limit(10)
            ->get();
        return $this->json($company);
    }
}
