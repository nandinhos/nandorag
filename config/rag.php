<?php

return [

    'chunk_size' => (int) env('RAG_CHUNK_SIZE', 256),

    'chunk_overlap' => (int) env('RAG_CHUNK_OVERLAP', 50),

    'similarity_threshold' => (float) env('RAG_SIMILARITY_THRESHOLD', 0.5),

    'top_k' => (int) env('RAG_TOP_K', 10),

    'embedding_model' => env('RAG_EMBEDDING_MODEL', 'nomic-embed-text'),

    'embedding_dimensions' => (int) env('RAG_EMBEDDING_DIMENSIONS', 768),

    'chat_model' => env('RAG_CHAT_MODEL', 'minimax-m2.5:cloud'),

];
