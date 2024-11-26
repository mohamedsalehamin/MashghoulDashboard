<?php

namespace App\DefaultPanel\Requests\Api\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $return = [

        ];
        if(request()->has('name')) {
            $return['name'] = 'required';
        }
        if(request()->has('email')) {
            $return['email'] = 'required|email|unique:users,email,'.\Auth::user()->id;
        }
        if(request()->has('phone')) {
            $return['phone'] = 'required|unique:users,phone,'.\Auth::user()->id;
        }
        if (request()->has('password')){
            $return['password'] =  'required|confirmed|min:6';
        }
        return $return;
    }
    public function attributes() {
        return [
            'name' => __('name'),
            'phone' => __('phone'),
            'email' => __('email'),
            'password' => __('Password'),
        ];
    }
}
