<?php

namespace App\Http\Controllers;

use App\Models\SymptomTracking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use \Carbon\Carbon;
use DB;
class SymptomTrakingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {

        switch ($request->tabValue) {
            case 'today':
                $data = SymptomTracking::
                      with(['symptom'=>function($query){$query->select('id','title');}])
                    ->where('user_id',Auth::user()->id)
                    ->whereDate('date', Carbon::today())
                    ->where('symptom_id', $id)->get();


                    break;
            case 'thismonth':

                $data = SymptomTracking::
                with(['symptom'=>function($query){$query->select('id','title');}])
                    ->where('user_id',Auth::user()->id)
                    ->whereMonth('date',Carbon::now()->month())
                ->where('symptom_id', $id)->get();
                break;
            case 'year':

                $data = SymptomTracking::select('rating','date')
                    -> with(['symptom'=>function($query){$query->select('id','title');}])
                    ->where('user_id',Auth::user()->id)
                    -> where( DB::raw('YEAR(date)'), '=', $request->year )
                    ->where('symptom_id', $id)->get()->toArray();

                $x =array();

                 foreach ($data as $dat){
                     $dat['month'] = Carbon::parse($dat['date'])->format('F');
                   $x[$dat['month']][]=$dat['rating'];

                 }

   $final = array();
                $previous =0;
            foreach ($x as $key=>$d){
                $count =count($d);
                if($d != '' || $d != 0) {
                    $previous=$d;
                    $final[] = array('month' => $key, 'rating' => array_sum($d) / $count);
                }
                else{
                    $final[] = array('month' => $key, 'rating' => array_sum($previous) / $count);
                }
            }


return $final;
                break;
                case 'yearwithmonth':

                $data = SymptomTracking::
                with(['symptom'=>function($query){$query->select('id','title');}])
                    ->where('user_id',Auth::user()->id)
                    ->whereMonth('date',$request->month)->
                  where( DB::raw('YEAR(date)'), '=', $request->year )
                    ->where('symptom_id', $id)->get();

    $getdateandrating =array();
                    foreach ($data as $date){
                        $getdateandrating[date('d', strtotime($date->date))] =$date->rating;
                    }
                    $arr=array('','January','February','March','April','May','June','July','August','September','October','November','Deccember');
                    $month =$arr[$request->month];
$year =$request->year;

                    $first_day_this_month2010 = date('01-m-Y 00:00:00', strtotime( "$month $year"));
                    $last_day_april_2010 = date('t-m-Y 12:59:59', strtotime("$month $year"));

//                    return array($first_day_this_month2010,$last_day_april_2010);
                    $start = new \DateTime("$first_day_this_month2010");
                    $end = new \DateTime("$last_day_april_2010");

                    $interval = new \DateInterval('P1D');
                    $dateRange = new \DatePeriod($start, $interval, $end);

                    $weekNumber = 1;
                    $weeks = array();
                    foreach ($dateRange as $date) {
                        $weeks[$weekNumber][] = $date->format('d');
                        if ($date->format('w') == 6) {
                            $weekNumber++;
                        }
                    }

                    $finalweek= array();
                    $previous =0;
                    $days = array();
                    foreach ($weeks as $key =>$we){

                        $week=array();
                    // $previous =0;
                        foreach ($we as $key=>$we2){
//                           echo '<pre>'; print_r($we2);
                            if(@$getdateandrating[$we2] != ''){
                                $previous  =@$getdateandrating[$we2];
                                $week[$we2]=@$getdateandrating[$we2];
                            }
                            else{
                                $week[$we2]=$previous;
                            }

                        }
                        $days[]= $week;

             $finalweek[] =ceil(array_sum($week) / count($week));

                }
  $pre =0;
                    $wekss =array();
                    foreach ($finalweek as $key=>$we2) {
//                           echo '<pre>'; print_r($we2);

                        $keys =$key+1;


                        if (@$we2 != 0) {
                            $pre = $we2;
                            $wekss[] = array('week'=>$keys,'rating'=>$we2,'daily'=>$days[$key]);
                        } else {
                            $wekss[] = array('week'=>$keys,'rating'=>$pre,'daily'=>$days[$key]);
                        }
                    }


                    return $wekss;
                break;
        }

        return response()->json([
            'success' => true,
            'data'=>$data
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $date = new \DateTime("now", new \DateTimeZone($request->timezone) );

        foreach ($request->symptom_ids as $key => $value){
            SymptomTracking::create([
                'user_id'=>Auth::user()->id, 'symptom_id'=>$key,'rating'=>$value,'date'=> $date->format('Y-m-d H:i:s')]);

        }
        return response()->json([
            'success' => true,
            'message' => "Tracking Created Successfully"
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
