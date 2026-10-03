-- Demonstrator table used by /sandbox. SQLite-compatible (local dev fallback).
-- The real PostgreSQL schema arrives with the Sprint 1 migrations.
CREATE TABLE IF NOT EXISTS sandbox_items (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    name       TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
