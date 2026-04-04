<?php

test('landing page returns successful response and shows NandoRAG', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('NandoRAG');
});
