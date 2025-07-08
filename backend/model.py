import os
import numpy as np
import sys


os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2' 
import tensorflow as tf
from tensorflow.keras.models import load_model, Model
from tensorflow.keras.layers import LSTM, Dense, Dropout, Input, AdditiveAttention, Concatenate, Reshape
from tensorflow.keras.callbacks import EarlyStopping, ReduceLROnPlateau
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.metrics import RootMeanSquaredError, MeanAbsolutePercentageError
from sklearn.metrics import mean_absolute_error, mean_squared_error

MODEL_DIR = "/Users/ishaanshetty/Documents/stock-frontend/models" 
os.makedirs(MODEL_DIR, exist_ok=True) 

#Model Building
def build_model(input_shape):
    """Sets up the LSTM model layers with Attention."""
    inputs = Input(shape=input_shape)
    
    lstm_1_out = LSTM(units=100, return_sequences=True, name='lstm_1')(inputs)
    dropout_1 = Dropout(0.2, name='dropout_1')(lstm_1_out)
    
    lstm_2_out = LSTM(units=100, return_sequences=False, name='lstm_2')(dropout_1)
    dropout_2 = Dropout(0.2, name='dropout_2')(lstm_2_out)

    # Attention Mechanism 
    query_reshaped = Reshape((1, 100), name='reshape_query')(dropout_2)
    context_vector = AdditiveAttention(name='attention')([query_reshaped, dropout_1])
    context_vector = Reshape((100,), name='reshape_context')(context_vector)
    combined_context = Concatenate(name='concatenate_context')([dropout_2, context_vector])
   
    # Final Dense layer to predict the next 10 prices
    outputs = Dense(units=10, name='output_dense')(combined_context)
    # Create the model object
    model = Model(inputs=inputs, outputs=outputs)

    # Use Adam optimizer, good general choice
    optimizer = Adam(learning_rate=0.001)
    model.compile(optimizer=optimizer,
                  loss='mean_absolute_error',
                  metrics=[RootMeanSquaredError(name='rmse'),
                           MeanAbsolutePercentageError(name='mape')]) 
    print("Model compiled.")
    model.summary(print_fn=print)
    return model


#Model Training 
def train_model(x_train, y_train, x_val, y_val,
                input_shape, ticker: str, epochs=50, batch_size=32,
                scalers=None): 

    if scalers is None:
         raise ValueError("Scalers dictionary must be provided for evaluation.")

    x_train = np.asarray(x_train).astype('float32')
    y_train = np.asarray(y_train).astype('float32')
    x_val = np.asarray(x_val).astype('float32')
    y_val = np.asarray(y_val).astype('float32')

   
    model = build_model(input_shape)

    # Callbacks
    early_stopping = EarlyStopping(monitor='val_loss', patience=10, restore_best_weights=True, verbose=1)
    reduce_lr = ReduceLROnPlateau(monitor='val_loss', factor=0.2, patience=5, verbose=1, min_lr=1e-6)

    print(f"\n--- Starting Training for {ticker} ---")
   
    # Train the model 
    history = model.fit(
        x_train, y_train,            
        epochs=epochs,              
        batch_size=batch_size,      
        validation_data=(x_val, y_val),
        verbose=2,                   
        callbacks=[early_stopping, reduce_lr] 
    )

    print(f"\n--- Training Finished for {ticker} ---")

    
    y_pred_scaled = model.predict(x_val)
    close_scaler = scalers['close_scaler']
    
    y_pred_unscaled = close_scaler.inverse_transform(y_pred_scaled.reshape(-1, 1)).reshape(y_pred_scaled.shape)
    y_true_unscaled = close_scaler.inverse_transform(y_val.reshape(-1, 1)).reshape(y_val.shape)

    y_true_flat = y_true_unscaled.flatten()
    y_pred_flat = y_pred_unscaled.flatten()

    # Calculate MAE and RMSE
    mae_unscaled = mean_absolute_error(y_true_flat, y_pred_flat)
    mse_unscaled = mean_squared_error(y_true_flat, y_pred_flat)
    rmse_unscaled = np.sqrt(mse_unscaled)

    # Calculate MAPE
    mask = y_true_flat != 0 
    if np.sum(mask) == 0: 
        mape_unscaled = np.nan
        print("Warning: Cannot calculate unscaled MAPE as all true validation values are zero.")
    else:
        mape_unscaled = np.mean(np.abs((y_true_flat[mask] - y_pred_flat[mask]) / y_true_flat[mask])) * 100 # Gives numpy float

    print("\n--- Final Validation Metrics (Unscaled) ---")
    print(f"  Mean Absolute Error (MAE):  ${mae_unscaled:.4f}")
    print(f"  Root Mean Squared Error (RMSE): ${rmse_unscaled:.4f}")
    if not np.isnan(mape_unscaled):
        print(f"  Mean Absolute Percentage Error (MAPE): {mape_unscaled:.2f}%")
    else:
        print(f"  Mean Absolute Percentage Error (MAPE): N/A")
    print("---------------------------------------------")

    # 4. Save the trained model
    model_path = os.path.join(MODEL_DIR, f"{ticker}_model.h5")
    try:
        model.save(model_path)
        print(f"Model saved successfully to {model_path}")
    except Exception as e:
        print(f"Error saving model to {model_path}: {e}")
        print("Ensure 'h5py' library is installed (`pip install h5py`).", file=sys.stderr)
        raise e 
   
    metrics_dict = {
        "mae_unscaled": float(round(mae_unscaled, 4)),
        "rmse_unscaled": float(round(rmse_unscaled, 4)),
        "mape_unscaled_pct": float(round(mape_unscaled, 2)) if not np.isnan(mape_unscaled) else None # Handle potential NaN
    }

    return model, metrics_dict


# Model Loading 
def load_trained_model(ticker: str):
    """Loads a pre-trained Keras model (expecting .h5). Handles the custom Attention layer."""
    model_path = os.path.join(MODEL_DIR, f"{ticker}_model.h5")

    if not os.path.exists(model_path):
         raise FileNotFoundError(f"Model file not found for {ticker} at {model_path}.")

    print(f"Loading model from {model_path}")
    
    custom_objects = {'AdditiveAttention': AdditiveAttention} if model_path.endswith('.h5') else None

    try:
        model = load_model(model_path, custom_objects=custom_objects, compile=True)
        print("Model loaded successfully (compile=True).")
        return model
    except Exception as e:
        print(f"Warning: Error loading model with compile=True: {e}. Attempting with compile=False...")
        try:
            model = load_model(model_path, custom_objects=custom_objects, compile=False)
            print("Model loaded successfully (compile=False).")
            return model
        except Exception as e2:
            print(f"Error loading model with compile=False: {e2}")
            if model_path.endswith('.h5'):
                print("Ensure 'h5py' library is installed (`pip install h5py`).", file=sys.stderr)
            raise RuntimeError(f"Failed to load model from {model_path}") from e2