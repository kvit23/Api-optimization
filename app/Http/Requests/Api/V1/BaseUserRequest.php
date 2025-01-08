<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseUserRequest extends FormRequest
{

    public function mappedAttribute() {
        $attributeMap = [
            'data.attributes.name' => 'name',
            'data.attributes.email' => 'email',
            'data.attributes.isManager' => 'is_manager',
            'data.attributes.password' => 'password',
        ];

        $attributeToUpdate = [];

        foreach($attributeMap as $key => $attribute)
        {
            if($this->has($key))
            {
                $value = $this->input($key);

                if($attribute === 'password') {

                    $value = bcrypt($value);
                }

                $attributeToUpdate[$attribute] = $value;
            }
        }

        return $attributeToUpdate;
    }


}
