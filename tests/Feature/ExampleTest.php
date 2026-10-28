<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

// the home page reads the quotes table, so build the database first
uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
