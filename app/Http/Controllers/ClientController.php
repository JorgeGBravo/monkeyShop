<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ClientController extends Controller
{
    function getAllClients()
    {
        return DB::select('select all from clients where idUser = ' . Auth::id());
    }

    function getClientes(Request $request)
    {
        $cif = $request->input('idData');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $image = $request->input('image');

        if (count($cif) !== 0) {
            return DB::select('select all from clients where cif ="' . $cif . '"');
        }
        return DB::select('select all from clients where name ="' . $name . '"or surname ="' . $surname . '"');


    }


    function newAndUpdateClient(Request $request){ // create and actualize a client
        $cif = $request->input('idData');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $image = $request->input('image');

        $client = DB::select('select all from clients where  cif ="' . $cif . '"');

        if (count($client) == 0){


            if(count($name) !== 0){
                DB::select('update clients set name ="'.$name.'"and mCIdUser ="'.Auth::id().'"');
            }
            if(count($surname) !== 0){
                DB::select('update clients set surname ="'.$surname.'"and mCIdUser ="'.Auth::id().'"');

            }
            if(count($image)){
                DB::select('update clients set image ="'.$image.'"and mCIdUser ="'.Auth::id().'"');

            }
        }

        $data = new Client();
        $data->name = $name;
        $data->surname = $surname;
        $data->cif = $cif;
        $data->image = $image;
        $data->image = Auth::id();
        $data->mCIdUser = Auth::id();
    }


}
