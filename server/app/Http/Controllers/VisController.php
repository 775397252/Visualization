<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VisController extends Controller
{
    public function getCompany()
    {
        $id = request('id');
        $company = CompanyInfo::query()->where('company_id',$id)->get();
        return response()->json($company);
    }
}
