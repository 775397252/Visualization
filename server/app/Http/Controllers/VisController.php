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
            $company = RecruitmentInfo::query()->select(DB::raw('count(WorkYear) as count,WorkYear,Salary'));
            $company->groupBy("WorkYear")
                ->groupBy("Salary")->orderBy("Salary", 'desc');
        } else {
            $company = RecruitmentInfo::query()->select(DB::raw('count(WorkYear) as count,WorkYear'));
            $company->groupBy("WorkYear")
                ->orderBy("WorkYear", 'desc');
        }
        $company = $company->get();
        $res = [];
        $price=[];
        foreach ($company as $item) {
//            $k=mb_substr($item['Salary'],0,2);
//            if(in_array($k,$price)){
                $res[$item['WorkYear']][]=$item;
//            }else{
//                $res[$k]=$item;
//            }
//            if ($item['count'] > 500) {
//                $res[$item['WorkYear']][] = $item;
//            }
        }
        $new=[];
        foreach ($res as $kwy=>$re) {
            $mail=collect($re)->sortByDesc('count')->values()->take(5);
//            $res2=collect($re)->sortBy('count')->take(5);
            $new[$kwy]=$mail;
        }
        return $this->json($new);


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
//            ->limit(7)
            ->get();
        $res = [];
        foreach ($company as $item) {
            if( false!==strpos($item['FinanceStage'],'null')){

            }else{
                $res[$item['FinanceStage']][] = $item;
            }

        }
        return $this->json($res);
    }

}
