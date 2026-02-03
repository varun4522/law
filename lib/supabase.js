// Supabase Configuration
const SUPABASE_URL = 'https://zcuadqnwnradhwgytspb.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InpjdWFkcW53bnJhZGh3Z3l0c3BiIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzAwNTY1NDMsImV4cCI6MjA4NTYzMjU0M30.btn0Ag5Zeri27QG2NQxFIiQoaLTzSA7RMlOG3ggF9tg';

// Initialize Supabase Client
const { createClient } = supabase;
const supabaseClient = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

export { supabaseClient, SUPABASE_URL, SUPABASE_ANON_KEY };
