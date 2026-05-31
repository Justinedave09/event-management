<?php
// ============================================================
// AI Configuration - Groq Free Tier
// Get your free API key at: https://console.groq.com/keys
// ============================================================

// ── Groq Settings (Free - No Credit Card) ──────────────────
define('GROQ_API_KEY', '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'); // ← paste your key here
define('GROQ_MODEL',   'llama-3.3-70b-versatile');   // free and very powerful
define('GROQ_ENDPOINT','https://api.groq.com/openai/v1/chat/completions');

// ── Chat Behaviour ──────────────────────────────────────────
define('AI_SYSTEM_PROMPT', 
    'You are an AI assistant for an veterinary appointment system. ' .
    'Help users with appointment scheduling, patient management, ' .
    'vet service information, and general veterinary-related questions. Be concise and helpful.'
);define('AI_TEMPERATURE',    0.7);
define('AI_MAX_TOKENS',     1024);
define('AI_MAX_HISTORY',    10);   // remember last 10 messages

// ── Available Free Groq Models (just change GROQ_MODEL above)
// 'llama-3.3-70b-versatile'   ← smartest, best for chat
// 'llama-3.1-8b-instant'      ← fastest response time
// 'llama3-8b-8192'            ← good balance
// 'gemma2-9b-it'              ← Google Gemma (also free on Groq)
// 'mixtral-8x7b-32768'        ← great for long conversations