<?php

namespace App\Http\Controllers;

use App\CustomField;
use App\Document;
use COM;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PDO;

class ReportController extends AppBaseController
{
    // for solve persian text search
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function index()
    {
        $customField = CustomField::whereName('حوزه ارجاع')->first();
        $hoze = $customField->suggestions ?? array();
        array_unshift($hoze, '');
        return view('reports.index', compact('hoze'));
    }

    public function rptKol(Request $request)
    {
        $reportType = $request->get('slc_rpt');
        $cnt = null;
        $gardesh = null;
        $docs = null;
        switch ($reportType) {
            case 0:
            case 8:
            case 9:
                $docs = Document::all();
                break;
            case 1:
                $docs = Document::whereHas('tags', function ($q) {
                    $q->where('name', '=', 'جاری');
                })->get();
                break;
            case 2:
                $docs = Document::whereHas('tags', function ($q) {
                    $q->where('name', '=', 'راکد');
                })->get();
                break;
            case 3:
                $docs = Document::whereHas('tags', function ($q) {
                    $q->where('name', '=', 'حقیقی');
                })->get();
                break;
            case 4:
                $docs = Document::whereHas('tags', function ($q) {
                    $q->where('name', '=', 'حقوقی');
                })->get();
                break;
            case 5:
                $hozeErja = $request->get('txt_erja');
                $docs = Document::where('custom_fields->حوزه ارجاع', 'LIKE', '%' . $hozeErja . '%')->get();
                break;
            case 6:

                $customField = CustomField::whereName('حوزه ارجاع')->first()->suggestions;
                array_shift($customField);

                foreach ($customField as $item) {
                    $erj1 = Document::where('custom_fields->حوزه ارجاع', 'LIKE', '%' . $item . '%')->get();
                    $erj2 = Document::where('custom_fields->حوزه ارجاع دوم', 'LIKE', '%' . $item . '%')->get();
                    $erj3 = Document::where('custom_fields->حوزه ارجاع سوم', 'LIKE', '%' . $item . '%')->get();
                    $erj4 = Document::where('custom_fields->حوزه ارجاع چهارم', 'LIKE', '%' . $item . '%')->get();
                    $repeat = $erj1->merge($erj2)->merge($erj3)->merge($erj4)->count();
                    if ($repeat != 0) $cnt[$item] = $repeat;
                }
                break;

            case 7:
                $docs = collect([]);
                $startDate = $request->get('startDate');
                $endDate = $request->get('endDate');
                $temp1 = Document::where('custom_fields->تاریخ نامه ارجاع' , '<>' , 'null')->select('*', 'custom_fields->تاریخ نامه ارجاع as date1')->get();
                $temp2 = Document::where('custom_fields->تاریخ نامه ارجاع دوم' , '<>' , 'null')->select('*', 'custom_fields->تاریخ نامه ارجاع دوم as date2')->get();
                $temp3 = Document::where('custom_fields->تاریخ نامه ارجاع سوم' , '<>' , 'null')->select('*', 'custom_fields->تاریخ نامه ارجاع سوم as date3')->get();
                $temp4 = Document::where('custom_fields->تاریخ نامه ارجاع چهارم' , '<>' , 'null')->select('*', 'custom_fields->تاریخ نامه ارجاع چهارم as date4')->get();

                $temp = $temp1->concat($temp2)->concat($temp3)->concat($temp4);
                foreach ($temp as $item) {
                    if(($item->date1 >= $startDate && $item->date1 <=$endDate) || ($item->date2 >= $startDate && $item->date2 <=$endDate)
                        || ($item->date3 >= $startDate && $item->date3 <=$endDate)
                        || ($item->date4 >= $startDate && $item->date4 <=$endDate)){
                        $docs->push($item);
                    }
                }
                break;

            default:
                # code...
                break;
        };
        return view('reports.rptKol', compact('docs','cnt','gardesh'));
    }
}
