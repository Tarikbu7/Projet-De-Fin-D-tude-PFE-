# Projet-De-Fin-D-tude-PFE-

## AI chatbot setup

The chatbot uses `chat.php` to call the OpenAI Responses API from the server, so the API key is never exposed in browser JavaScript.

Create a `.env` file in the project root:

```ini
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_MODEL=gpt-5-mini
```

The PHP server must have the `curl` extension enabled.
