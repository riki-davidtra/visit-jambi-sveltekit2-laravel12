<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Http\Resources\DestinationResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class DestinationApiController extends Controller
{
    public function index(Request $request)
    {
        $page         = $request->input('page');
        $limit        = $request->input('limit', null);
        $destinations = Destination::orderBy('id', 'desc')
            ->when($page, function ($query) use ($limit) {
                return $query->paginate($limit);
            }, function ($query) use ($limit) {
                return $query->limit($limit)->get();
            });

        return DestinationResource::collection($destinations)->response()->setStatusCode(200);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'     => 'nullable|exists:users,id',
                'category_id' => 'nullable|exists:categories,id',
                'name'        => 'required|string|max:255',
                'location'    => 'required|string|max:255',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('destinations', 'public');
            }

            $data          = $validator->validated();
            $data['image'] = $imagePath;
            $destination   = Destination::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data created successfully.',
                'data'    => new DestinationResource($destination)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $destination = Destination::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'data'    => new DestinationResource($destination)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'     => 'nullable|exists:users,id',
                'category_id' => 'nullable|exists:categories,id',
                'name'        => 'required|string|max:255',
                'location'    => 'required|string|max:255',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $destination = Destination::findOrFail($id);
            if ($request->hasFile('image')) {
                if ($destination->image) {
                    Storage::disk('public')->delete($destination->image);
                }
                $imagePath          = $request->file('image')->store('destinations', 'public');
                $destination->image = $imagePath;
            }

            $data = $validator->validated();
            $destination->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully.',
                'data'    => new DestinationResource($destination)
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $destination = Destination::findOrFail($id);

            if ($destination->image) {
                Storage::disk('public')->delete($destination->image);
            }

            $destination->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data deleted successfully.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
