<?php
    return \Illuminate\Support\Arr::undot([
        'no_inputs' => 'No data send',
        'addr.fname.max' => 'First name field is too long',
        'addr.lname.max' => 'Last name field is too long',
        'type.max' => 'Type field is too long',
        'addr.phone.max' => 'Phone field is too long',
        'addr.company.max' => 'Company field is too long',
        'addr.orgnum.max' => 'Org. num. field is too long',
        'addr.city.max' => 'City field is too long',
        'addr.postcode.max' => 'Post code field is too long',
        'addr.street.max' => 'Street field is too long',
        'addr.country.max' => 'Country field is too long',
        'addr.mail.required' => 'Email is required',
        'addr.mail.email' => 'Email is not valid',
        'addr.mail.unique' => 'There is already profile with such email address',
        'addr.mail.max' => 'E-mail field is too long',
        'roles.required' => 'At least one user role is required',
        'roles.not_valid' => 'Not valid user roles',
        'password.min' => 'Password field is too short [:min symbols needed]',
        'password.max' => 'Password field is too long',
        'password.confirmed' => 'Confirm password should be same',

        //@HOOK_USERS_VALIDATION_LANG
    ]);
