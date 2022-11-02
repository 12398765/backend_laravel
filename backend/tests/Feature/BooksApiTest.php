<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    function can_get_all_books()
    {
        $book = Book::factory(4)->create();

        $response = $this->getJson(route('books.index'));
        $response->assertJsonFragment([
            'title' => $book[0]->title
        ])->assertJsonFragment([
            'title' => $book[1]->title
        ]);
    }

    /** @test */
    function can_get_one_book()
    {
        $book = Book::factory()->create();
        $response = $this->getJson(route('books.show', $book));
        $response->assertJsonFragment([
            'title' => $book->title
        ]);
    }

    /** @test */
    function can_create_books()
    {
        //checa si tiene la validacion
        $this->postJson(route('books.store'), [])->assertJsonValidationErrorFor('title');


        $this->postJson(route('books.store'), [
            'title' => 'My New Book'
        ])->assertJsonFragment([
            'title' => 'My New Book'
        ]);

        //verificando si se creó correctamente en la BD
        $this->assertDatabaseHas('books', [
            'title' => 'My New Book'
        ]);
    }

    /** @test */
    function can_update_books()
    {
        $book = Book::factory()->create();

        //checa si tiene la validacion
        $this->patchJson(route('books.update', $book), [])->assertJsonValidationErrorFor('title');

        $this->patchJson(route('books.update', $book), [
            'title' => 'Edited book'
        ])->assertJsonFragment([
            'title' => 'Edited book'
        ]);


        $this->assertDatabaseHas('books', [
            'title' => 'Edited book'
        ]);
    }

    /** @test */
    function can_delete_books()
    {
        $book = Book::factory()->create();
        $this->deleteJson(route('books.destroy', $book))->assertNoContent();

        $this->assertDatabaseCount('books', 0);
    }
}
