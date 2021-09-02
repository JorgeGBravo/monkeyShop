<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ClientController extends Controller
{
    function getAllClients()
    {
        return response()->json(DB::select('select * from clients'), 200);
    }

    function getClient(Request $request)
    {
        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');


        if (isset($cif)) {

            return response()->json(DB::select('select * from clients where cif ="' . $cif . '"'), 200);
        }
        if (isset($name)) {

            return response()->json(DB::select('select * from clients where name ="' . $name . '"'), 200);
        }
        if (isset($surname)) {

            return response()->json(DB::select('select * from clients where surname ="' . $surname . '"'), 200);
        }

        return response()->json(DB::select('select * from clients where name ="' . $name . '"and surname ="' . $surname . '"'), 200);


    }

    function newClient(Request $request)
    {
        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return response()->json(['message' =>'You do not have Administrator permissions'], 403);
        }
        $validatedData = Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'cif' => 'required|string|min:6|max:255',

        ]);

        if($validatedData->fails()) {

            return response()->json(['message' => $validatedData->getMessageBag()->first()], 400);

        } else {
            $name = $request->input('name');
            $surname = $request->input('surname');
            $cif = $request->input('cif');

            $client = DB::select('select * from clients where  cif ="' . $cif . '"');

            if (count($client) == 0) {

                $data = new Client();
                $data->name = $name;
                $data->surname = $surname;
                $data->cif = $cif;
                $data->idUser = Auth::id();
                $data->mCIdUser = Auth::id();
                $data->save();

                return response()->json(['user' => $data], 201);

            }

            return response()->json(['message' => 'Already registered customer'], 409);

        }

    }

    function updateClient(Request $request)
    {
        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return response()->json(['message' => 'You do not have Administrator permissions'], 403);
        }

        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');

        $client = DB::select('select * from clients where cif="' . $cif . '"');

        if (count($client) != 0) {

            if ((isset($name)) && isset($surname)) {

                DB::select('update clients set name ="' . $name . '", surname ="' . $surname . '", mCIdUser ="' . Auth::id() . '" where cif="' . $cif . '"');

                return response()->json(['message' => ['cif'=> $cif, 'name'=> $name, 'surname' =>$surname]], 201);

            }
            if (isset($name)) {

                DB::select('update clients set name ="' . $name . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');

                return response()->json(['message' => ['cif'=> $cif, 'name'=> $name]], 201);

            }
            if (isset($surname)) {

                DB::select('update clients set surname ="' . $surname . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');

                return response()->json(['message' => ['cif'=> $cif, 'surname' =>$surname]], 201);

            }
        }

        return response()->json(['message' => $cif.'no exist' ], 409);

    }

    function deleteClient(Request $request)
    {

        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return response()->json(['message' => 'You do not have Administrator permissions'], 403);
        }
        $cif = $request->input('cif');

        $client = DB::select('select * from clients where  cif ="' . $cif . '"');

        if (count($client) != 0) {
            DB::select('delete from clients where cif ="' . $cif . '"');
            return response()->json(['message' => 'the user has been deleted'], 200);
        }
        return response()->json(['message' => 'User not exist'], 404);
    }

    function updateImage(Request $request)
    {
        $admin = DB::select('select isAdmin from users where id ="' . Auth::id() . '"');

        if ($admin[0]->isAdmin === 0) {
            return response()->json(['message' => 'You do not have Administrator permissions'], 403);
        }

        $validatedData = Validator::make($request->all(), [
            'cif' => 'required|string|min:6|max:255',
            'image' => 'required|image|dimensions:min_width=200,min_height=200',
        ]);

        if ($validatedData->fails()) {

            return response()->json(['message' => $validatedData->getMessageBag()->first()], 400);

        } else {
            $cif = $request->input('cif');
            $intoImage = $request->allFiles()['image'];

            $client = DB::select('select * from clients where  cif ="' . $cif . '"');

            if (count($client) != 0) {
                $imageClient = $client[0]->image;

                if ($imageClient == null) {
                    $path = $intoImage->store('public/images');      // save image in images
                    $newUrlPath = $this->parseUrlImage($path);
                    DB::select('update clients set image ="' . env('APP_URL') . '/' . $newUrlPath . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');

                    return response()->json(['message' =>'Image entered', 'image' => env('APP_URL').'/'.$newUrlPath], 200);
                }

                $newPathImage = $this->parseUrlImage($imageClient);
                Storage::delete($newPathImage);
                $path = $intoImage->store('public/images');      // save image in images
                $newUrlPath = $this->parseUrlImage($path);
                DB::select('update clients set image ="' . env('APP_URL') . '/' . $newUrlPath . '", mCIdUser ="' . Auth::id() . '"where cif="' . $cif . '"');

                return response()->json(['message' => 'Updated image', 'image' => env('APP_URL').'/'.$newUrlPath], 200);
            }
        }
        return response()->json(['message' => 'User with cif:' . $cif . ', does not exist'], 409);
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
