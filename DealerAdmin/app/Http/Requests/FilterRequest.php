<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\Exceptions\HttpResponseException;

class FilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
          $rules = [
            'NameOfAPI' => 'required|alpha',
            'DealerCode' => 'required|exists:fca_ore_input,dealer_code'
        ];

        switch($this->input('NameOfAPI')){
            case 'getModelYear':
                $rules['MakeCode'] = 'required|regex:/^[\pL\s\-]+$/u'; //allow letters,hypens,spaces
                break;

            case 'getVehicleSelection':
                $rules['MakeCode'] = 'required|regex:/^[\pL\s\-]+$/u';//allow letters,hypens,spaces
                $rules['ModelYear'] = 'required|exists:fca_ore_input,year';
                break;

            case 'FilterTrimSelection':
                $rules['MakeCode'] = 'required|regex:/^[\pL\s\-]+$/u'; //allow letters,hypens,spaces
                $rules['ModelYear'] = 'required|exists:fca_ore_input,year';
                $rules['Model'] = 'required|exists:fca_ore_input,model';
                break;

            case 'FilterMsrpSelection':
                $rules['MakeCode'] = 'required|regex:/^[\pL\s\-]+$/u'; //allow letters,hypens,spaces
                $rules['ModelYear'] = 'required|exists:fca_ore_input,year';
                $rules['Model'] = 'required|exists:fca_ore_input,model';
                $rules['Trim'] = 'required|exists:fca_ore_input,trim_code';
                break;

            case 'FilterSecondarySelection':
                $rules['MakeCode'] = 'required|regex:/^[\pL\s\-]+$/u'; //allow letters,hypens,spaces
                $rules['ModelYear'] = 'required|exists:fca_ore_input,year';
                $rules['Model'] = 'required|exists:fca_ore_input,model';
                $rules['Trim'] = 'required|exists:fca_ore_input,trim_code';
                //$rules['MsrpHighest'] = 'required';
                //$rules['MsrpLowest'] = 'required';
                break;
            case 'listDiscount':
                $rules['VinNumber'] = 'required|exists:fca_ore_input,vin';
                $rules['FinanceOption'] = 'required|in:1,2,3';
                break;
            case 'vinActivation':
                $rules['VinNumber'] = 'required|exists:fca_ore_input,vin';
                $rules['Operation'] = 'required|integer';
                break;
        }
        return $rules;
    }

    protected function failedValidation(Validator $validator) {
        $response = array();
        $error_messages = array();
        foreach ($validator->errors()->all() as $messages) {
            array_push($error_messages,$messages);
        }
        $response['Message'] = 'Please select mandatory fields';
        $response['errors'] = $error_messages;
        $response['StatusCode'] = 1001;
        throw new HttpResponseException(response()->json($response, 200));
    }
}
