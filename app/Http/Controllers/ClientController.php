<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ClientController extends Controller
{
    function getAllClients()
    {
        log::info("estoy");
        $clients = DB::select('select * from clients');

        return $clients;
    }

    function getClient(Request $request)
    {
        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $image = $request->input('image');

        if (count($cif) !== 0) {
            return DB::select('select all from clients where cif ="' . $cif . '"');
        }
        return DB::select('select all from clients where name ="' . $name . '"or surname ="' . $surname . '"');


    }


    function newAndUpdateClient(Request $request){ // create and actualize a client
        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $image = $request->input('image');

        $client = DB::select('select * from clients where  cif ="' . $cif . '"');

        log::info($request);
        log::info($client);
        log::info(count($client));


        if (count($client) <= 0){
            $data = new Client();
            $data->name = $name;
            $data->surname = $surname;
            $data->cif = $cif;
            $data->image = $image;
            $data->idUser = Auth::id();
            $data->mCIdUser = Auth::id();
            $data->save();
            return "New registered customer";

        }
        return "Already registered customer";

    }

function updateClient(Request $request)
{
    $cif = $request->input('cif');
    $name = $request->input('name');
    $surname = $request->input('surname');
    $image = $request->input('image');

    if(count($name) !== 0){
        DB::select('update clients set name ="'.$name.'"and mCIdUser ="'.Auth::id().'"where cif="'.$cif.'"');
        return "Update Client CIF: ".$cif. "new name: ".$name;
    }
    if(count($surname) !== 0){
        DB::select('update clients set surname ="'.$surname.'"and mCIdUser ="'.Auth::id().'"where cif="'.$cif.'"');
        return "Update Client CIF: ".$cif. "new surname: ".$name;

    }
    if(count($image) !== 0){
        DB::select('update clients set image ="'.$image.'"and mCIdUser ="'.Auth::id().'"where cif="'.$cif.'"');
        return "Update Client CIF: ".$cif. "a new Photo";

    }
}
}
