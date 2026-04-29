import os
from flask import Flask, render_template, request, jsonify
from openai import OpenAI
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

app = Flask(__name__)

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

@app.route('/chat', methods=['POST'])
def chat():
    data = request.json
    user_message = data.get('message', '')
    
    if not user_message:
        return jsonify({'error': 'Message is required'}), 400
        
    ai_response = get_ai_response(user_message)
    
    return jsonify({'response': ai_response})

if __name__ == '__main__':
    app.run(debug=True, port=5000)
