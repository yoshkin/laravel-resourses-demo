<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    /**
     * BookController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return BookResource::collection(Book::with('ratings')->paginate(25));
    }

    /**
     * @param Request $request
     * @return BookResource
     */
    public function store(Request $request)
    {
        $book = Book::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return new BookResource($book);
    }

    /**
     * @param Book $book
     * @return BookResource
     */
    public function show(Book $book)
    {
        return new BookResource($book);
    }

    /**
     * @param Request $request
     * @param Book $book
     * @return BookResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Book $book)
    {
        // check if currently authenticated user is the owner of the book
        if ($request->user()->id !== $book->user_id) {
            return response()->json(['error' => 'You can only edit your own books.'], 403);
        }

        $book->update($request->only(['title', 'description']));

        return new BookResource($book);
    }

    /**
     * @param Book $book
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(null, 204);
    }
}
