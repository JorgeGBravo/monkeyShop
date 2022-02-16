<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateDataController extends Controller
{
    public function registerValidateData(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'isAdmin' => 'nullable|bool',
        ]);
    }

    public function changeIsAdminValidateData(Request $request)
    {
        return Validator::make($request->all(), [
            'id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ]);
    }

    public function newClientValidateData(Request $request)
    {
        return Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'cif' => 'required|string|min:6|max:255',
        ]);
    }

    public function updateClientValidateData(Request $request)
    {
        return Validator::make($request->all(),[
            'cif' => 'required|string|min:6|max:255',
            'name' => 'string|max:255',
            'surname' => 'string|max:255'
        ]);
    }

    public function updateImageValidateData(Request $request)
    {
        return Validator::make($request->all(), [
            'cif' => 'required|string|min:6|max:255',
            'image' => 'required|image|dimensions:min_width=200,min_height=200',
        ]);
    }
}
