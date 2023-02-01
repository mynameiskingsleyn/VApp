<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\Exceptions\HttpResponseException;

class SearchRequest extends FormRequest
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
            'DealerCode' => 'required|exists:fca_ore_input,dealer_code',
            'FinanceOption' => 'required|in:1,2,3',
        ];

        switch($this->input('NameOfAPI')){
            case 'SearchByVIN':
                $rules['VinNumber'] = 'required|exists:fca_ore_input,vin';
                break;

            default:
                $rules += [
                    'MakeCode' => 'required|regex:/^[\pL\s\-]+$/u', //allow letters,hypens,spaces
                    'ModelYear' => 'required|exists:fca_ore_input,year',
                    'Model' => 'required|exists:fca_ore_input,model',
                    'Trim' =>'required|exists:fca_ore_input,trim_code',
                    'MsrpHighest' => 'required',
                    'MsrpLowest' => 'required',
                    'DriveNames'=> 'sometimes|required',
                    'ColorNames'=> 'sometimes|required',
                    'EngineDescNames'=> 'sometimes|required',
                    'TransmissionNames'=> 'sometimes|required'
                ];
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
