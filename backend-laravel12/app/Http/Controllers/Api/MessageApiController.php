<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class MessageApiController extends Controller
{
    public function index(Request $request)
    {
        $page     = $request->input('page');
        $limit    = $request->input('limit', null);
        $messages = Message::orderBy('id', 'desc')
            ->when($page, function ($query) use ($limit) {
                return $query->paginate($limit);
            }, function ($query) use ($limit) {
                return $query->limit($limit)->get();
            });

        return MessageResource::collection($messages)->response()->setStatusCode(200);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:255',
                'email'   => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data    = $validator->validated();
            $message = Message::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data created successfully.',
                'data'    => new MessageResource($message)
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
            $message = Message::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'data'    => new MessageResource($message)
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
                'name'    => 'required|string|max:255',
                'email'   => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $message = Message::findOrFail($id);
            $data = $validator->validated();
            $message->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully.',
                'data'    => new MessageResource($message)
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
            $message = Message::findOrFail($id);
            $message->delete();

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
