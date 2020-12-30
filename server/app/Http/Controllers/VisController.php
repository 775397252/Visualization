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
        $company=$company->get();
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
//            ->limit(7)
            ->get();
        return $this->json($company);
    }

}
