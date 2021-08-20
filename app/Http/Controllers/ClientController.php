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
        //$image = $request->input('image');

        log::info('en getclient');

        if (isset($cif)) {
            return DB::select('select * from clients where cif ="' . $cif . '"');
        }
        return DB::select('select * from clients where name ="' . $name . '"or surname ="' . $surname . '"');


    }


    function newAndUpdateClient(Request $request){ // create and actualize a client

        $validatedData = $request->validate([                           // validate the data format
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'cif' => 'required|string|max:255',
            'image' => 'required|image|dimensions:min_width=200,min_height=200',
        ]);


        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $image = $request->input('image');

        $client = DB::select('select * from clients where  cif ="' . $cif . '"');

        log::info($request);
        log::info('en new client');
        //og::info(count($client));


        if (count($client) <= 0){
            $path = $validatedData['image']->store('public/storage');      // save image in images
            $url_path = asset($path);
            log::info($path);
            log::info($url_path);
            $data = new Client();
            $data->name = $name;
            $data->surname = $surname;
            $data->cif = $cif;
            $data->image = $path;
            $data->idUser = Auth::id();
            $data->mCIdUser = Auth::id();
            $data->save();
            return "New registered customer";

        }
        return "Already registered customer";

    }

function updateClient(Request $request)
{
    log::info('estoy en updateclient');
    log::info($request);

    $cif = $request->input('cif');
    $name = $request->input('name');
    $surname = $request->input('surname');
/*
    log::info(isset($name));
    if ( isset($name) ) {
        return 'Hola';
    }
    else{
        return 'else';
    }*/


    if((isset($name)) && isset($surname)){
        log::info('estoy en name y surname');
        log::info($surname);

        DB::select('update clients set name ="'.$name.'", surname ="'.$surname.'", mCIdUser ="'.Auth::id().'" where cif="'.$cif.'"');
        return "Update Client CIF: ".$cif. " new name: ".$name. " and surname: ".$surname;
    }
    if(isset($name)){
        log::info('estoy en name ');
        DB::select('update clients set name ="'.$name.'", mCIdUser ="'.Auth::id().'"where cif="'.$cif.'"');
        return "Update Client CIF: ".$cif. " new name: ".$name;
    }
    if(isset($surname)){
        log::info('estoy en surname');
        DB::select('update clients set surname ="'.$surname.'", mCIdUser ="'.Auth::id().'"where cif="'.$cif.'"');
        return "Update Client CIF: ".$cif. " new surname: ".$surname;
    }

}

function updateImage(Request $request)
{
    $cif = $request->input('cif');
    $image = $request->input('image');

    DB::select('update clients set image ="'.$image.'", mCIdUser ="'.Auth::id().'"where cif="'.$cif.'"');
}
}
