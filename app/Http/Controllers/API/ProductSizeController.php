<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{
    /**
     * @param Request $request
     * @return Size[]|Collection
     */
    public function getAll()
    {
        $size = Size::all();

        return response()->json(['success' => true ,'data'=> $size,
        'message'=> 'Sizes retrieved successfully', 'description'=> 'list Of Sizes', 'code'=>'200'],200);
    }

    public function getById(Size $size)
    {
        return response()->json(['success' => true ,'data'=> $size,
        'message'=> 'Size retrieved successfully', 'description'=> 'Size Details', 'code'=>'200'],200);
    }

    public function getActiveProductSizes()
    {
        return response()->json(['success' => true ,'data'=> Size::where('is_active', '1')->get(),
        'message'=> 'Active Product Sizes retrieved successfully', 'description'=> 'List Of Active Product Sizes', 'code'=> 200 ],200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $validateDate = $request->validate([
            'name_ar' => 'required|max:255',
            'name_en' => 'required|max:255',
            'weight' => 'sometimes|max:255',
            // 'calories' => 'sometimes|max:255',
            'price' => 'required|numeric',
            'sale_price' => 'required|numeric'
        ]);

        $productSize = Size::create($validateDate);
        if ($productSize) {

           return response()->json(['success' => true ,'data'=> $productSize,
            'message'=> 'Successfully Added!', 'description'=> 'Add Size', 'code'=>'200'],200);

        }

        return response()->json(['message' => 'Something went wrong!'], 500);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Size $productSize)
    {
        if ($productSize->delete()) {
            
            return response()->json(['message' => 'Successfully Deleted!'], 200);
        }

        return response()->json(['message' => 'Something went wrong!'], 500);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Size  $productSize)
    {
            $productSize->is_active = !$productSize->is_active;
            if ($productSize->update()) {
                return response()->json(['message' => 'Successfully updated!', 'data' => $productSize], 200);
            }
        return response()->json(['message' => 'Something went wrong!'], 500);

    }

    public function update(Request $request, Size $productSize)
    {

        $validateDate = $request->validate([
            'price' => 'sometimes|numeric',
            'name_ar' => 'sometimes|max:255',
            'name_en' => 'sometimes|max:255',
            'weight' => 'sometimes',
            // 'calories' => 'sometimes',
            'sale_price' => 'sometimes|numeric'
        ]);

        if ($productSize->update($validateDate)) {

            return response()->json(['success' => true ,'data'=> $productSize,
            'message'=> 'Successfully updated!', 'description'=> 'Update Size', 'code'=> 200 ],200);
           
        }
        return response()->json(['message' => 'Something went wrong!'], 500);
    }

}
