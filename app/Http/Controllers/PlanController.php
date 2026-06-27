<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Upload;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $col_name = null;
        $query = null;
        $sort_search = null;
        $plans = Plan::orderBy('created_at', 'desc');
        $plans = $plans->paginate(15);

        return view('backend.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $plan = new Plan($request->all());
        $plan->save();

        flash('Plan registrado exitosamente!')->success();
        return redirect()->route('plans.index');
    }

    public function show($id)
    {
        return view('backend.plans.show', [
            'plan' => Plan::findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);

        return view('backend.plans.edit', compact('plan'));
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->save($request->all());

        flash('Plan actualizado exitosamente!')->success();
        return redirect()->route('plans.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        if (Plan::destroy($id)) {
            flash('Plan eliminado exitosamente!')->success();
            return redirect()->route('plans.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }
}
