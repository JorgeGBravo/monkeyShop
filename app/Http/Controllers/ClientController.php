<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
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
        return response()->json(Client::all(), 200);
    }

    function getClient(Request $request)
    {
        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');

        if (isset($cif)) {
            return response()->json(Client::all()->where('cif', $cif), 200);
        }
        if (isset($name)) {
            return response()->json(Client::all()->where('name', $name), 200);
        }
        if (isset($surname)) {
            return response()->json(Client::all()->where('surname', $surname), 200);
        }
        return response()->json(Client::all()->where('name', $name)->where('surname', $surname), 200);
    }

    function newClient(Request $request)
    {
        if (auth()->user()->isAdmin === 0) {
            return response()->json(['message' => 'You do not have Administrator permissions'], 403);
        }
        $validatedData = Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'cif' => 'required|string|min:6|max:255',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['message' => $validatedData->getMessageBag()->first()], 400);
        }

        $name = $request->input('name');
        $surname = $request->input('surname');
        $cif = $request->input('cif');
        $client = Client::all()->where('cif', $cif);

        if (count($client) == 0) {
            $data = new Client();
            $data->name = $name;
            $data->surname = $surname;
            $data->cif = $cif;
            $data->idUser = Auth::id();
            $data->lastUserWhoModifiedTheField = Auth::id();
            $data->save();
            return response()->json(['user' => $data], 201);
        }
        return response()->json(['message' => 'Already registered customer'], 409);
    }

    function updateClient(Request $request)
    {
        if (auth()->user()->isAdmin === 0) {
            return response()->json(['message' => 'You do not have Administrator permissions'], 403);
        }
        $cif = $request->input('cif');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $client = Client::all()->where('cif', $cif);

        if (count($client) != 0) {
            if ((isset($name)) && isset($surname)) {
                Client::where('cif', $cif)->update(['name' => $name, 'surname' => $surname, 'lastUserWhoModifiedTheField' => Auth::id()]);
                return response()->json(Client::all()->where('cif', $cif), 201);
            }
            if (isset($name)) {
                Client::where('cif', $cif)->update(['name' => $name, 'lastUserWhoModifiedTheField' => Auth::id()]);
                return response()->json(Client::all()->where('cif', $cif), 201);
            }
            if (isset($surname)) {
                Client::where('cif', $cif)->update(['surname' => $surname, 'lastUserWhoModifiedTheField' => Auth::id()]);
                return response()->json(Client::all()->where('cif', $cif), 201);
            }
        }
        return response()->json(['message' =>'The client with cif: '.$cif.' does not exist' ], 409);
    }

    function deleteClient(Request $request)
    {
        if (auth()->user()->isAdmin === 0) {
            return response()->json(['message' => 'You do not have Administrator permissions'], 403);
        }
        $cif = $request->input('cif');
        $client = Client::where('cif', $cif)->get();

        if (count($client) != 0) {
            Client::where('cif', $cif)->delete();
            return response()->json(['message' => 'The user has been deleted'], 200);
        }
        return response()->json(['message' => 'User not exist'], 404);
    }

    function updateImage(Request $request)
    {
        if (auth()->user()->isAdmin === 0) {
            return response()->json(['message' => 'You do not have Administrator permissions'], 403);
        }
        $validatedData = Validator::make($request->all(), [
            'cif' => 'required|string|min:6|max:255',
            'image' => 'required|image|dimensions:min_width=200,min_height=200',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['message' => $validatedData->getMessageBag()->first()], 400);
        }

        $cif = $request->input('cif');
        $intoImage = $request->allFiles()['image'];
        $client = Client::all()->where('cif', $cif);

        if (count($client) != 0) {
            foreach ($client as $info) {
                $infoClient = $info;
            }
            $imageClient = $infoClient->image;

            if ($imageClient == null) {
                $path = $intoImage->store('public/images');      // save image in images
                $newUrlPath = $this->parseUrlImage($path);
                Client::where('cif', $cif)->update(['image' => env('APP_URL') . '/' . $newUrlPath, 'lastUserWhoModifiedTheField' => Auth::id()]);
                return response()->json(['message' => 'Image entered', 'image' => env('APP_URL') . '/' . $newUrlPath], 200);
            }
            $newPathImage = $this->parseUrlImage($imageClient);
            Storage::delete($newPathImage);
            $path = $intoImage->store('public/images');      // save image in images
            $newUrlPath = $this->parseUrlImage($path);
            Client::where('cif', $cif)->update(['image' => env('APP_URL') . '/' . $newUrlPath, 'lastUserWhoModifiedTheField' => Auth::id()]);
            return response()->json(['message' => 'Update image', 'image' => env('APP_URL') . '/' . $newUrlPath], 200);
        }
        return response()->json(['message' => 'User with cif: ' . $cif . ', does not exist'], 409);
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
