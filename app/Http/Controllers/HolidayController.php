<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Company;
use Carbon\Carbon;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('holiday_view')){
                $holidays = Holiday::where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
                return view('hr.holiday.holiday_list', compact('holidays'));
        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('holiday_add')){

            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            return response()->json([
                'companies' =>$companies,
            ]);

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('holiday_add')){

            request()->validate([
                'title'           => 'required|string|max:255',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'company_id'   => 'required',
            ]);

            Holiday::create([
                'company_id'   => $request['company_id'],
                'title'           => $request['title'],
                'start_date'      => $request['start_date'],
                'end_date'        => $request['end_date'],
                'description'     => $request['description'],
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
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
        $user_auth = auth()->user();
		if ($user_auth->can('holiday_edit')){

            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            return response()->json([
                'companies' =>$companies,
            ]);

        }
        return abort('403', __('You are not authorized'));
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
        $user_auth = auth()->user();
		if ($user_auth->can('holiday_edit')){

            request()->validate([
                'title'           => 'required|string|max:255',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'company_id'   => 'required',
            ]);

            Holiday::whereId($id)->update([
                'company_id'   => $request['company_id'],
                'title'           => $request['title'],
                'start_date'      => $request['start_date'],
                'end_date'        => $request['end_date'],
                'description'     => $request['description'],
            ]);
        
            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('holiday_delete')){

            Holiday::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);
            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {
       $user_auth = auth()->user();
       if($user_auth->can('holiday_delete')){
           $selectedIds = $request->selectedIds;
   
           foreach ($selectedIds as $holiday_id) {
                Holiday::whereId($holiday_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
           }
           return response()->json(['success' => true]);
       }
       return abort('403', __('You are not authorized'));
    }
}
