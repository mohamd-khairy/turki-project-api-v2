<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    public function getAll()
    {
        $tage = Tag::all();

        return response()->json(['success' => true ,'data'=> $tage,
        'message'=> 'retrieved successfully', 'description'=> '', 'code'=>'200'],200);
    }

    public function getById(Tag $tag)
    {
        return response()->json(['success' => true ,'data'=> $tag,
        'message'=> 'retrieved successfully', 'description'=> '', 'code'=>'200'],200);
    }

    public function add(Request $request)
    {
        $validateDate = $request->validate([
            'name_ar' => 'required|max:255',
            'name_en' => 'required|max:255',
            'color' => 'nullable|max:255',
        ]);

        $productTag = Tag::create($validateDate);
        if ($productTag) {

           return response()->json(['success' => true ,'data'=> $productTag,
            'message'=> 'Successfully Added!', 'description'=> '', 'code'=>'200'],200);
        }
        return response()->json(['message' => 'Something went wrong!'], 500);
    }

    public function updateStatus(Tag $productTag)
    {
            $productTag->is_active = !$productTag->is_active;
            if ($productTag->update()) {
                return response()->json(['message' => 'Successfully updated!', 'data' => $productTag], 200);
            }
        return response()->json(['message' => 'Something went wrong!'], 500);

    }

    public function update(Request $request, Tag $productTag)
    {
        $validateDate = $request->validate([
            'name_ar' => 'nullable|max:255',
            'name_en' => 'nullable|max:255',
            'color' => 'nullable|max:255',
        ]);

        if ($productTag->update($validateDate)) {

            return response()->json(['success' => true ,'data'=> $productTag,
            'message'=> 'Successfully updated!', 'description'=> '', 'code'=> 200 ],200);
           
        }
        return response()->json(['message' => 'Something went wrong!'], 500);
    }

    public function delete(Tag $productTag)
    {
        if ($productTag->delete()) {
            
            return response()->json(['message' => 'Successfully Deleted!'], 200);
        }

        return response()->json(['message' => 'Something went wrong!'], 500);

    }

}
