from flask import Flask, request, jsonify
import pandas as pd
import pickle
import os
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Enable CORS for cross-origin requests

# Load models and scaler
model_dir = 'models'
with open(os.path.join(model_dir, 'scaler.pkl'), 'rb') as f:
    scaler = pickle.load(f)

with open(os.path.join(model_dir, 'kmeans_model.pkl'), 'rb') as f:
    kmeans_model = pickle.load(f)

with open(os.path.join(model_dir, 'isolation_forest_model.pkl'), 'rb') as f:
    isolation_forest_model = pickle.load(f)

# Mapping for cluster number to malware types
malware_mapping = {
    0: 'Trojan.Generic',
    1: 'Ransomware',
    2: 'Adware',
    3: 'Spyware',
    4: 'Worm'
}

@app.route('/predict', methods=['POST'])
def predict():
    # Handle file upload
    file = request.files.get('file')
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400

    try:
        data = pd.read_csv(file)
    except Exception as e:
        return jsonify({'error': f'Failed to read CSV file: {e}'}), 400

    # Preprocess the data
    data = data.select_dtypes(include=[float, int])
    if data.empty:
        return jsonify({'error': 'The dataset does not contain any numeric columns suitable for analysis.'}), 400

    data = data.fillna(data.mean())
    data_scaled = scaler.transform(data)

    # Predict using K-Means
    kmeans_predictions = kmeans_model.predict(data_scaled)
    kmeans_counts = pd.Series(kmeans_predictions).map(malware_mapping).value_counts().to_dict()

    # Predict using Isolation Forest
    isolation_forest_predictions = isolation_forest_model.predict(data_scaled)
    isolation_forest_counts = pd.Series(isolation_forest_predictions).value_counts().to_dict()

    # Return the predictions as a JSON response
    return jsonify({
        'kmeans_prediction': kmeans_counts,
        'isolation_forest_prediction': isolation_forest_counts
    })

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
