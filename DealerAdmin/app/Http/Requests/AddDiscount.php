<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\Exceptions\HttpResponseException;

class AddDiscount extends FormRequest
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
            'NameOfAPI' => 'required',
            'DealerCode' => 'required|exists:fca_ore_input,dealer_code',
            'FinanceOption' => 'required|in:1,2,3',
            'VinNumber' => 'required|exists:fca_ore_input,vin',
            'Discount' => 'required|array|min:1',
            'Discount.*.name_of_discount' => 'required',
            'Discount.*.discount_start_date' => 'required|date',
            'Discount.*.discount_end_date' => 'required|date',
            'Discount.*.saved_discount' => 'required|integer',
            'Discount.*.flat_rate' => 'required_without:Discount.*.percent_offer',
            'Discount.*.percent_offer' => 'required_without:Discount.*.flat_rate'
        ];
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'Discount.*.name_of_discount.required' => 'Discount Name is required',
            'Discount.*.flat_rate.required_without' => 'Discount Flat rate is required',
            'Discount.*.percent_offer.required_without' => 'Discount Percent Offer is required',
            'Discount.*.discount_start_date.required' => 'Discount Start Date is required',
            'Discount.*.discount_start_date.date' => 'Start Date is not a valid date',
            'Discount.*.discount_end_date.required' => 'Discount End Date is required',
            'Discount.*.discount_end_date.date' => 'End Date is not a valid date',
            'Discount.*.saved_discount.required' => 'Saved Discount is required',
            'Discount.*.saved_discount.integer' => 'Saved Discount is not valid number'
        ];
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
