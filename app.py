import os
from flask import Flask, render_template, request, jsonify
from flask_cors import CORS
from openai import OpenAI
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

app = Flask(__name__, template_folder='templates', static_folder='static')

# Enable CORS for all routes (required for iframe embedding)
CORS(app, resources={
    r"/*": {
        "origins": ["*"],
        "methods": ["GET", "POST", "OPTIONS"],
        "allow_headers": ["Content-Type"]
    }
})

# System prompt to instruct the AI
SYSTEM_PROMPT = """
You are an expert AI Legal Advisor named 'Lexi'. Your role is to provide accurate, easy-to-understand legal information and guidance.
Please adhere to the following rules:
1. Always maintain a professional, empathetic, and objective tone.
2. Provide clear and concise answers. Do not use overly complex legal jargon unless you explain it.
3. Crucially, you must always include a brief disclaimer in your responses (or at least state it clearly when appropriate) that you are an AI and your advice does not constitute official legal counsel. Users should always consult a qualified lawyer for official legal matters.
4. Try to be helpful and constructive, offering general steps or principles related to the user's legal question.
"""

def get_ai_response(user_prompt):
    try:
        api_key = os.environ.get("OPENROUTER_API_KEY")
        if not api_key or api_key == "your_api_key_here":
            return "Error: OpenRouter API key is not configured. Please add your OPENROUTER_API_KEY to the .env file."

        client = OpenAI(
            base_url="https://openrouter.ai/api/v1",
            api_key=api_key,
        )

        # Use a high-quality free model on OpenRouter, or a widely available one
        # 'google/gemini-1.5-flash' or 'meta-llama/llama-3-8b-instruct:free'
        # Let's use deepseek/deepseek-chat or mistral/mistral-large as an example
        # Since the user wants to test, I'll use a reliable model like openchat/openchat-7b:free or default
        response = client.chat.completions.create(
            model="openai/gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": SYSTEM_PROMPT},
                {"role": "user", "content": user_prompt}
            ],
            temperature=0.7,
        )
        return response.choices[0].message.content
    except Exception as e:
        import traceback
        traceback.print_exc()
        print(f"Error calling OpenRouter: {e}", flush=True)
        return "I'm sorry, I encountered an error while processing your request. Please try again later."

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/api', defaults={'path': ''})
@app.route('/api/<path:path>')
def api_route(path):
    """Handle API requests - default to index for iframe"""
    if path == '' or path == '/':
        return render_template('index.html')
    return render_template('index.html')

@app.route('/chat', methods=['POST', 'OPTIONS'])
def chat():
    """Handle chat messages"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        data = request.get_json()
        if not data:
            return jsonify({'error': 'No JSON data provided'}), 400
            
        user_message = data.get('message', '').strip()
        
        if not user_message:
            return jsonify({'error': 'Message is required'}), 400
            
        ai_response = get_ai_response(user_message)
        
        return jsonify({'response': ai_response})
    except Exception as e:
        print(f"Error in chat route: {e}")
        return jsonify({'error': 'Internal server error'}), 500

if __name__ == '__main__':
    # For production, bind to 0.0.0.0 to accept connections from reverse proxy
    app.run(debug=False, host='0.0.0.0', port=5000, threaded=True)

