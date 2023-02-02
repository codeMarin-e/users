<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserRequest extends FormRequest
{
    private $mergeReturn = [];

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
    public function rules()
    {
        $chUser = request()->route('chUser');
        $rules = [
            'addr.fname' => ['nullable', 'max:255'],
            'addr.lname' => ['nullable', 'max:255'],
            'addr.email' => ['required', 'email', Rule::unique('users', 'email')->where(function($qry) use ($chUser){
                if($chUser) return $qry->where('id', '!=', $chUser->id);
                return $qry;
            }), 'max:255'],
            'type' => ['nullable', 'max:255'],
            'addr.company' => ['nullable', 'max:255'],
            'addr.phone' => ['nullable', 'max:255'],
            'addr.orgnum' => ['nullable', 'max:255'],
            'addr.city' => ['nullable', 'max:255'],
            'addr.postcode' => ['nullable', 'max:255'],
            'addr.street' => ['nullable', 'max:255'],
            'addr.country' => ['nullable', 'max:255'],
            'roles' => [ 'required', function($attribute, $value, $fail) use ($chUser) {
                if(!is_array($value) || empty($value))
                    return $fail( trans('admin/users/validation.roles.required') );
                $roleQRY = Role::when(!auth()->user()->hasRole('Super Admin', 'admin'), function($qry) { //only Super Admin can add Super Admin
                    $qry->where(function($qry2) {
                        $qry2->where('name', '!=', 'Super_Admin')
                            ->orWhere('quard', '!=', 'admin');
                    });
                })->whereIn('id', $value);
                if($roleQRY->count() != count($value))
                    return $fail( trans('admin/users/validation.roles.not_valid') );
            }
            ],
            'password' => [ 'sometimes', Rule::requiredIf(!$chUser), 'nullable', 'confirmed', 'min:8', 'max:255' ],
            'active' => 'boolean',
        ];

        // @HOOK_USER_REQUEST_RULES

        return $rules;
    }

    public function messages() {
        $return = Arr::dot((array)trans('admin/users/validation'));

        // @HOOK_USER_REQUEST_MESSAGES

        return $return;
    }

    public function validationData() {
        $inputBag = 'users';
        $this->errorBag = $inputBag;
        $inputs = $this->all();
        if(!isset($inputs[$inputBag])) {
            throw new ValidationException(trans('admin/users/validation.no_inputs') );
        }
        $inputs[$inputBag]['active'] = isset($inputs[$inputBag]['active']);

        // @HOOK_USER_REQUEST_PREPARE

        $this->replace($inputs);
        request()->replace($inputs); //global request should be replaced, too
        return $inputs[$inputBag];
    }

    public function validated($key = null, $default = null) {
        $validatedData = parent::validated($key, $default);
        // @HOOK_USER_REQUEST_VALIDATED
        if(is_null($key)) {
            if(is_null($validatedData['password'])) unset($validatedData['password']);
            else $validatedData['password'] = User::cryptPassword( $validatedData['password'] );
            $validatedData['name'] = $validatedData['addr']['fname'];
            $validatedData['email'] =  $validatedData['addr']['email'];

            // @HOOK_USER_REQUEST_AFTER_VALIDATED

            return array_merge($validatedData, $this->mergeReturn);
        }
        if($key === 'password') {
            return is_null($validatedData)? null : User::cryptPassword($validatedData);
        }

        // @HOOK_USER_REQUEST_AFTER_VALIDATED_KEY

        return $validatedData;
    }
}
