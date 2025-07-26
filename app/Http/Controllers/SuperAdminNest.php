<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nest;

class SuperAdminNest extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nest = Nest::all();
        return view('superadmin.nests', compact('nest'));
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
        //
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
        $nest = Nest::findOrFail($id);
        return view('superadmin.nests_edit', compact('nest'));
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $nest = Nest::findOrFail($id);

        $nest->update($validatedData);

        return redirect()->route('superadmin.nests')->with('success', 'Update Nest Berhasil');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nest = Nest::findOrFail($id);
        $nest->delete();

        return redirect()->route('superadmin.nests')->with('success', 'Hapus Nest Berhasil');
    }
}
