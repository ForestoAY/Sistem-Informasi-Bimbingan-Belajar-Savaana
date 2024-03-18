<?php

namespace Modules\Perpustakaan\Http\Controllers;

use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use App\Helpers\GlobalHelpers;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Perpustakaan\Entities\Book;
use Modules\Perpustakaan\Entities\Author;
use Modules\Perpustakaan\Entities\Category;
use Illuminate\Contracts\Support\Renderable;
use Modules\Perpustakaan\Entities\BookAuthor;
use Modules\Perpustakaan\Entities\Publisher;
use Modules\Perpustakaan\Http\Requests\BookRequest;
use PHPUnit\Framework\Constraint\Count;

class BooksController extends Controller
{

    use GlobalHelpers;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $book = Book::with('publisher', 'bookAuthors.author', 'category')->orderbY('created_at', 'desc')->get();
        return view('perpustakaan::backend.books.index', compact('book'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $publisher  = Publisher::get();
        $author     = Author::get();
        $category   = Category::get();
        if (empty($publisher) || empty($author) || empty($category)) {
            Session::flash('error', 'Data Publisher, Author atau Category Buku Belum Ada!');
            return view('perpustakaan::backend.books.index');
        }
        return view('perpustakaan::backend.books.create', compact('publisher', 'author', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BookRequest $request)
    {
        try {
            $thumbnail = $request->file('thumbnail');
            $thumbnail_name = time() . "_" . $thumbnail->getClientOriginalName();
            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'public/images/thumbnail';
            $thumbnail->storeAs($tujuan_upload, $thumbnail_name);

            $book = new Book;
            $book->book_code        = $this->generateNumber($book);
            $book->name             = $request->name;
            $book->description      = $request->description;
            $book->category_id      = $request->category_id;
            $book->publisher_id     = $request->publisher_id;
            $book->publication_year = Carbon::parse($request->publication_year)->format('Y');
            $book->isbn             = $request->isbn;
            $book->stock            = $request->stock;
            $book->thumbnail        = $thumbnail_name;
            $book->save();

            $book->book_code        = $this->generateNumber($book);
            $book->save();

            foreach ($request->addmore as $value) {
                $aktor = new BookAuthor();
                $aktor->book_id     = $book->id;
                $aktor->author_id   = $value['author_id'];
                $aktor->save();
                $cek = BookAuthor::where('author_id', $value['author_id'])->get();
                foreach ($cek as $key => $values) {
                    $ceks = BookAuthor::where('book_id', $values->book_id)->count();
                    if ($ceks > 1) {
                        $del = BookAuthor::where('book_id', $values->book_id)->first();
                        $del->delete();
                    }
                }
            }

            Session::flash('success', 'Buku Berhasil di tambah,');
            return redirect()->route('books.index');
        } catch (\ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('perpustakaan::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $book = Book::find($id);
        $category   = Category::get();
        $publisher  = Publisher::get();
        $author     = Author::whereDoesntHave('authorBooks', function ($a) use ($id) {
            $a->where('book_id', $id);
        })->get();

        $bookAuthor = BookAuthor::with('book')->where('book_id', $id)->get();
        return view('perpustakaan::backend.books.edit', compact('book', 'category', 'publisher', 'author', 'bookAuthor'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            if ($request->file('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnail_name = time() . "_" . $thumbnail->getClientOriginalName();
                // isi dengan nama folder tempat kemana file diupload
                $tujuan_upload = 'public/images/thumbnail';
                $thumbnail->storeAs($tujuan_upload, $thumbnail_name);
            }

            $book =  Book::find($id);
            $book->name             = $request->name;
            $book->description      = $request->description;
            $book->category_id      = $request->category_id;
            $book->publisher_id     = $request->publisher_id;
            $book->publication_year = Carbon::parse($request->publication_year)->format('Y');
            $book->isbn             = $request->isbn;
            $book->stock            = $request->stock;
            $book->thumbnail        = $thumbnail_name ?? $book->thumbnail;
            $book->update();

            foreach ($request->addmore as $value) {
                $aktor = new BookAuthor();
                $aktor->book_id     = $book->id;
                $aktor->author_id   = $value['author_id'];
                $aktor->save();
            }

            DB::commit();
            Session::flash('success', 'Buku Berhasil di update.');
            return redirect()->route('books.index');
        } catch (\ErrorException $e) {
            DB::rollBack();
            throw new ErrorException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $data = Book::find($id);
        $data->delete();

        Session::flash('success', 'Buku Berhasil di hapus.');
        return back();
    }

    public function destroyPenulis($id)
    {
        $data = BookAuthor::whereId($id)->first();
        $data->delete();

        Session::flash('success', 'Penulis Berhasil di hapus.');
        return back();
    }
}
