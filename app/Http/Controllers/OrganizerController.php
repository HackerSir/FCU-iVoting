<?php

namespace App\Http\Controllers;

use App\Organizer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class OrganizerController extends Controller
{
    public function __construct()
    {
        //限管理員
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $organizerList = Organizer::paginate(20);
        return view('vote.organizer.list')->with('organizerList', $organizerList);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('vote.organizer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|unique:organizers',
            'url' => 'url|max:255',
            'logo_url' => 'url|max:255'
        ]);
        if ($validator->fails()) {
            return Redirect::route('organizer.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            $organizer = Organizer::create([
                'name' => $request->get('name'),
                'url' => $request->get('url'),
                'logo_url' => $request->get('logo_url')
            ]);
            return Redirect::route('organizer.show', $organizer->id)
                ->with('global', '主辦單位已建立');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $organizer = Organizer::find($id);
        if (!$organizer) {
            return Redirect::route('organizer.index')
                ->with('global', '主辦單位不存在');

        }
        return view('vote.organizer.show')->with('organizer', $organizer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $organizer = Organizer::find($id);
        if (!$organizer) {
            return Redirect::route('organizer.index')
                ->with('global', '主辦單位不存在');

        }
        return view('vote.organizer.edit')->with('organizer', $organizer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $organizer = Organizer::find($id);
        if (!$organizer) {
            return Redirect::route('organizer.index')
                ->with('global', '主辦單位不存在');

        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|unique:organizers,name,' . $id,
            'url' => 'url|max:255',
            'logo_url' => 'url|max:255'
        ]);
        if ($validator->fails()) {
            return Redirect::route('organizer.edit', $id)
                ->withErrors($validator)
                ->withInput();
        } else {
            $organizer->name = $request->get('name');
            $organizer->url = $request->get('url');
            $organizer->logo_url = $request->get('logo_url');
            $organizer->save();
            return Redirect::route('organizer.show', $id)
                ->with('global', '主辦單位已更新');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $organizer = Organizer::find($id);
        if (!$organizer) {
            return Redirect::route('organizer.index')
                ->with('global', '主辦單位不存在');

        }
        $organizer->delete();
        return Redirect::route('organizer.index')
            ->with('global', '主辦單位已移除');
    }
}