<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


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
        if (isset($name)) {

            return DB::select('select * from clients where name ="' . $name . '"');
        }
        if (isset($surname)) {

            return DB::select('select * from clients where surname ="' . $surname . '"');
        }

        return DB::select('select * from clients where name ="' . $name . '"and surname ="' . $surname . '"');


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
        ]);

        $client = DB::select('select * from clients where  cif ="' . $validatedData['cif'] . '"');

        if (count($client) == 0) {

            //$path = $validatedData['image']->store('public/storage');      // save image in images
            //$url_path = asset($path);
            $data = new Client();
            $data->name = $validatedData['name'];
            $data->surname = $validatedData['surname'];
            $data->cif = $validatedData['cif'];
            //$data->image = $path;
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


        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');

        $client = DB::select('select * from clients where cif="' . $cif . '"');

        if (count($client) != 0) {

            if ((isset($name)) && isset($surname)) {

                DB::select('update clients set name ="' . $name . '", surname ="' . $surname . '", mCIdUser ="' . Auth::id() . '" where cif="' . $cif . '"');
                return "Update Client CIF: " . $cif . " new name: " . $name . " and surname: " . $surname;
            }
            if (isset($name)) {

                DB::select('update clients set name ="' . $name . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');
                return "Update Client CIF: " . $cif . " new name: " . $name;
            }
            if (isset($surname)) {

                DB::select('update clients set surname ="' . $surname . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');
                return "Update Client CIF: " . $cif . " new surname: " . $surname;
            }

        }
        return 'the client with cif: ' . $cif . ' does not exist';

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


        if (count($client) != 0) {
            $imageClient = $client[0]->image;

            if ($imageClient == null) {

                $path = $validatedData['image']->store('public/images');      // save image in images

                $newUrlPath = $this->parseUrlImage($path);

                DB::select('update clients set image ="'.env('APP_URL').'/'.$newUrlPath . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');
                return 'Image entered';
            }

            $newPathImage = $this->parseUrlImage($imageClient);
            Log::info($newPathImage);

            Storage::delete($newPathImage);
            $path = $validatedData['image']->store('public/images');      // save image in images

            $newUrlPath = $this->parseUrlImage($path);

            DB::select('update clients set image ="'.env('APP_URL').'/'.$newUrlPath . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');
            return 'Updated image ';
        }

        return 'User with cif:' . $cif . ', does not exist';
    }

    function parseUrlImage($path)
    {
        $urlExplode = explode('/', $path);

        if ($urlExplode[0] == 'public') {
            $pathSource = 'storage';
            $urlExplode[0] = $pathSource;
            return implode('/', $urlExplode);
        }

        $pathSource = 'public';
        $urlExplode[0] = '';
        $urlExplode[1] = '';
        $urlExplode[2] = '';
        $urlExplode[3] = $pathSource;
        return implode('/', $urlExplode);
    }

}
