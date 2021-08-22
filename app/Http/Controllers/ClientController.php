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
        return DB::select('select * from clients');
    }

    function getClient(Request $request)
    {
        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');


        if (isset($cif)) {
            return DB::select('select * from clients where cif ="' . $cif . '"');
        }
        return DB::select('select * from clients where name ="' . $name . '"or surname ="' . $surname . '"');


    }

    function newClient(Request $request)
    {
        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return 'You do not have Administrator permissions';
        }

        $validatedData = $request->validate([                           // validate the data format
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'cif' => 'required|string|max:255',
            'image' => 'required|image|dimensions:min_width=200,min_height=200',
        ]);

        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');

        $client = DB::select('select * from clients where  cif ="' . $cif . '"');


        if (isset($client)) {

            $path = $validatedData['image']->store('public/storage');      // save image in images
            //$url_path = asset($path);
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
        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return 'You do not have Administrator permissions';
        }

        $validatedData = $request->validate([                           // validate the data format
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'cif' => 'required|string|max:255',
        ]);


        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');


        if ((isset($name)) && isset($surname)) {
            log::info('estoy en name y surname');
            log::info($surname);

            DB::select('update clients set name ="' . $name . '", surname ="' . $surname . '", mCIdUser ="' . Auth::id() . '" where cif="' . $cif . '"');
            return "Update Client CIF: " . $cif . " new name: " . $name . " and surname: " . $surname;
        }
        if (isset($name)) {
            log::info('estoy en name ');
            DB::select('update clients set name ="' . $name . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');
            return "Update Client CIF: " . $cif . " new name: " . $name;
        }
        if (isset($surname)) {
            log::info('estoy en surname');
            DB::select('update clients set surname ="' . $surname . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');
            return "Update Client CIF: " . $cif . " new surname: " . $surname;
        }

    }

    function deleteClient(Request $request)
    {
        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return 'You do not have Administrator permissions';
        }

        $validatedData = $request->validate([                           // validate the data format
            'cif' => 'required|string|max:255',
        ]);

        $cif = $request->input('cif');
        $client = DB::select('select * from clients where  cif ="' . $cif . '"');
        if (isset($client)) {
            DB::select('delete from clients where cif ="' . $cif . '"');
            return "the user has been deleted";
        }

    }

    function updateImage(Request $request)
    {
        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return 'You do not have Administrator permissions';
        }

        $validatedData = $request->validate([                           // validate the data format
            'cif' => 'required|string|max:255',
            'image' => 'required|image|dimensions:min_width=200,min_height=200',
        ]);

        $cif = $request->input('cif');
        $client = DB::select('select * from clients where  cif ="' . $cif . '"');
        //log::info($client);

        if (isset($client)) {
            $old_path = public_path() . $client[0]->image;
            unlink($old_path);

            $path = $validatedData['image']->store('public/storage');      // save image in images
            return DB::select('update clients set image ="' . $path . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');
        }

        return 'user with cif:' . $cif . ', does not exist';
    }
}
