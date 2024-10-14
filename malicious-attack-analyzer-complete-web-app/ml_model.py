import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.cluster import KMeans
from sklearn.ensemble import IsolationForest
import pickle
import os
import sys

# Step 1: Get the dataset file path from the command line arguments
if len(sys.argv) < 2:
    print("Please provide the dataset file path.")
    sys.exit(1)

dataset_path = sys.argv[1]

# Step 2: Load the dataset
try:
    data = pd.read_csv(dataset_path)
    print("Dataset loaded successfully.")
except Exception as e:
    print(f"Failed to load the dataset: {e}")
    sys.exit(1)

# Step 3: Preprocess the data
# Drop non-numeric columns (if any) for this unsupervised approach
data = data.select_dtypes(include=[float, int])

# Check if the dataset is empty after filtering
if data.empty:
    print("The dataset does not contain any numeric columns suitable for analysis.")
    sys.exit(1)

# Handle missing values
data = data.fillna(data.mean())

# Split data for training and scaling
X_train, X_test = train_test_split(data, test_size=0.2, random_state=42)

# Scale the features
scaler = StandardScaler()
X_train_scaled = scaler.fit_transform(X_train)

# Step 4: Train the models
# Model 1: K-Means Clustering
kmeans_model = KMeans(n_clusters=5, random_state=42)
kmeans_model.fit(X_train_scaled)
print("K-Means model trained successfully.")

# Model 2: Isolation Forest for anomaly detection
isolation_forest_model = IsolationForest(random_state=42)
isolation_forest_model.fit(X_train_scaled)
print("Isolation Forest model trained successfully.")

# Step 5: Save the trained models and scaler
model_dir = 'models'
os.makedirs(model_dir, exist_ok=True)

with open(os.path.join(model_dir, 'scaler.pkl'), 'wb') as f:
    pickle.dump(scaler, f)

with open(os.path.join(model_dir, 'kmeans_model.pkl'), 'wb') as f:
    pickle.dump(kmeans_model, f)

with open(os.path.join(model_dir, 'isolation_forest_model.pkl'), 'wb') as f:
    pickle.dump(isolation_forest_model, f)

print(f"Models and scaler have been saved in the '{model_dir}' directory successfully.")
