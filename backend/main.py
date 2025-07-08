from datetime import datetime, timezone
from fastapi import FastAPI, Query, HTTPException, Body 
from pydantic import BaseModel 
import numpy as np
import pandas as pd
import traceback 
import os

# Import functions from local modules
try:
    from data import (
        calculate_technical_indicators,
        fetch_stock_data,
        preprocess_data,
        save_scaler,
        load_scaler,
        FEATURES 
    )
    from model import train_model, load_trained_model
except ImportError as e:
    print(f"Error importing local modules (model.py, data.py): {e}")
    print("Make sure they are in the same folder or your Python path is set up correctly.")
    import sys
    sys.exit(1) 


app = FastAPI(title="Stock Prediction API", version="1.0.0")
SCALER_DIR = "scalers"
MODEL_DIR = "/Users/ishaanshetty/Documents/stock-frontend/models" 
os.makedirs(SCALER_DIR, exist_ok=True)
os.makedirs(MODEL_DIR, exist_ok=True)


class PredictionRequest(BaseModel):
    ticker: str 

class TrainResponse(BaseModel):
    message: str
    last_trained_utc: str
    validation_metrics: dict 

class PredictionResponse(BaseModel):
    ticker: str
    predicted_prices: list[float] 

class HistoryDataPoint(BaseModel): 
    date: str
    close: float

class HistoryResponse(BaseModel):
    ticker: str
    history: list[HistoryDataPoint]

class ErrorResponse(BaseModel): 
    detail: str 

class IndicatorData(BaseModel): 
    date: str
    MACD: float | None = None 
    Signal: float | None = None
    RSI: float | None = None
    OBV: float | None = None
    MA10: float | None = None
    MA50: float | None = None
    Volatility: float | None = None
    ROC_5: float | None = None

class IndicatorResponse(BaseModel): 
    ticker: str
    macd: list[IndicatorData]
    rsi: list[IndicatorData]
    obv: list[IndicatorData]
    ma: list[IndicatorData]
    volatility: list[IndicatorData]
    roc: list[IndicatorData]
    last_updated: str

class HistoricalPredictionRequest(BaseModel):
    ticker: str

class HistoricalPredictionDataPoint(BaseModel):
    date: str
    actual_close: float
    predicted_close: float | None 

class HistoricalPredictionResponse(BaseModel):
    ticker: str
    comparison: list[HistoricalPredictionDataPoint]
    last_updated: str

class OHLCDataPoint(BaseModel):
    """Represents one period (e.g., day) of OHLC data."""
    date: str 
    o: float  
    h: float   
    l: float   
    c: float   

class OHLCResponse(BaseModel):
    """Response model for the OHLC history endpoint."""
    ticker: str
    ohlc_data: list[OHLCDataPoint]
    last_updated: str


@app.post("/train",
          summary="Train model for a stock ticker",
          response_model=TrainResponse 
         )
def train(ticker: str = Query(..., description="Stock ticker symbol (e.g., AAPL, MSFT)")):
    """
    Trains the LSTM model using 3 years of data for the given ticker.
    Saves the model and scalers, then returns validation metrics.
    """
    ticker = ticker.upper().strip()
    if not ticker:
         raise HTTPException(status_code=400, detail="Ticker symbol is required.")

    try:
        print(f"\n--- Training request for {ticker} ---")
      
        df = fetch_stock_data(ticker, period='3y') 
        if df.empty:
            raise HTTPException(status_code=404, detail=f"Could not fetch data for {ticker}.")

        # Calculate Indicators
        df_indicators = calculate_technical_indicators(df)
        if df_indicators.empty:
            # Use 400 if indicators result in no data (maybe bad source data?)
            raise HTTPException(status_code=400, detail="No data left after indicator calculation.")

        sequence_length = 60 
        x_train, y_train, x_val, y_val, scalers = preprocess_data(
            df_indicators,
            sequence_length=sequence_length,
            validation_split=0.2 
        )


        input_shape=(x_train.shape[1], x_train.shape[2]) 

        model, metrics_dict = train_model(
            x_train, y_train, x_val, y_val,
            input_shape=input_shape,
            ticker=ticker,
            scalers=scalers 
        )

        save_scaler(ticker, scalers)

        completion_time = datetime.now(timezone.utc).isoformat()
        print(f"Training completed for {ticker} at {completion_time}")

        return TrainResponse(
            message=f"Model trained successfully for {ticker}",
            last_trained_utc=completion_time,
            validation_metrics=metrics_dict
        )

    except FileNotFoundError as e:
        print(f"ERROR [Train {ticker}]: File Not Found. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=404, detail=f"File not found error: {e}")
    except ValueError as e: 
        print(f"ERROR [Train {ticker}]: ValueError. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=400, detail=f"Data processing error: {e}")
    except RuntimeError as e: 
        print(f"ERROR [Train {ticker}]: RuntimeError. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=500, detail=f"Runtime error during training: {e}")
    except HTTPException as e: 
        raise e
    except Exception as e:
        print(f"ERROR [Train {ticker}]: Unexpected Error. {type(e).__name__}: {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=500, detail=f"An unexpected server error occurred: {type(e).__name__}")


@app.post("/predict",
          summary="Predict stock prices for the next 1-10 days",
          response_model=PredictionResponse
         )
def predict(req: PredictionRequest, days: int = Query(10, ge=1, le=10, description="Number of future days (1-10)")):
    """
    Loads the trained model and scalers for the ticker, gets recent data,
    and predicts the closing price for the requested number of future days.
    """
    ticker = req.ticker.upper().strip()
    if not ticker:
         raise HTTPException(status_code=400, detail="Ticker symbol is required.")

    try:
        print(f"\n--- Prediction request for {ticker}, {days} days ---")
        try:
            scalers = load_scaler(ticker)
            model = load_trained_model(ticker)
        except FileNotFoundError as e:
            print(f"ERROR [Predict {ticker}]: Model/Scaler not found. {e}")
            raise HTTPException(status_code=404, detail=f"Model/scalers not found for {ticker}. Please train first.")

        feature_scaler = scalers['feature_scaler']
        close_scaler = scalers['close_scaler']

        sequence_length = 60 
        fetch_period = f'{sequence_length + 70}d' # Fetch ~130 days
        df_hist = fetch_stock_data(ticker, period=fetch_period)
        if df_hist.empty or df_hist.shape[0] < sequence_length:
            raise ValueError(f"Not enough recent data ({df_hist.shape[0]}/{sequence_length}) for {ticker}.")

        df_indicators = calculate_technical_indicators(df_hist)
        if df_indicators.shape[0] < sequence_length:
             raise ValueError(f"Not enough data ({df_indicators.shape[0]}/{sequence_length}) after indicators.")

        missing_features = [f for f in FEATURES if f not in df_indicators.columns]
        if missing_features:
            raise ValueError(f"Prediction data missing features: {missing_features}")

        df_features = df_indicators[FEATURES].tail(sequence_length)
        recent_data_features = df_features.values

        scaled_input_sequence = feature_scaler.transform(recent_data_features)
        input_seq = scaled_input_sequence.reshape(1, sequence_length, len(FEATURES))
        input_seq = np.asarray(input_seq).astype('float32') 

        predicted_scaled_10_steps = model.predict(input_seq)[0] 


        predicted_scaled_reshaped = predicted_scaled_10_steps.reshape(-1, 1)
        predicted_prices_unscaled = close_scaler.inverse_transform(predicted_scaled_reshaped)
        predicted_prices_10_days = predicted_prices_unscaled.flatten() 
        print(f"Predicted prices (raw 10 days): {[f'{p:.2f}' for p in predicted_prices_10_days]}")

        num_predictions = min(days, len(predicted_prices_10_days))
        predictions_output = [round(float(p), 2) for p in predicted_prices_10_days[:num_predictions]]

        return PredictionResponse(ticker=ticker, predicted_prices=predictions_output)

    except FileNotFoundError as e: 
        print(f"ERROR [Predict {ticker}]: File Not Found. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=404, detail=f"File not found error: {e}")
    except ValueError as e: 
        print(f"ERROR [Predict {ticker}]: ValueError. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=400, detail=f"Data error during prediction: {e}")
    except HTTPException as e: 
        raise e
    except Exception as e: 
        print(f"ERROR [Predict {ticker}]: Unexpected Error. {type(e).__name__}: {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=500, detail=f"An unexpected server error occurred: {type(e).__name__}")


@app.get("/history",
         summary="Get recent historical closing prices",
         response_model=HistoryResponse,
         responses={404: {"model": ErrorResponse}} 
         )
def get_history(ticker: str = Query(..., description="Stock ticker symbol"),
                days: int = Query(60, ge=1, description="Number of past trading days")):
    ticker = ticker.upper().strip()
    if not ticker:
        raise HTTPException(status_code=400, detail="Ticker symbol is required.")
    try:
        print(f"\n--- History request for {ticker}, last {days} days ---")
        fetch_days = int(days * 1.5) + 10
        df = fetch_stock_data(ticker, period=f'{fetch_days}d')

        if df.empty or 'Close' not in df.columns:
             raise HTTPException(status_code=404, detail=f"No history or 'Close' price found for {ticker}.")
        recent = df[['Close']].tail(days).copy()
        if recent.empty:
            raise HTTPException(status_code=404, detail=f"Not enough history available for {ticker} ({days} days).")
        if len(recent) < days:
             print(f"Warning: Only found {len(recent)}/{days} days of history for {ticker}.")

        # Get date from index
        recent.reset_index(inplace=True)
        date_col = 'index' 
        if 'Date' in recent.columns: date_col = 'Date' 

      
        history_list = []
        for _, row in recent.iterrows():
            if pd.notna(row[date_col]) and pd.notna(row['Close']):
                history_list.append(HistoryDataPoint( 
                    date=pd.to_datetime(row[date_col]).strftime('%Y-%m-%d'),
                    close=round(float(row['Close']), 2)
                ))

        return HistoryResponse(ticker=ticker, history=history_list)

    except HTTPException as e: 
         raise e
    except Exception as e:
        print(f"ERROR [History {ticker}]: {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=500, detail=f"Failed to get history: {str(e)}")


@app.get("/indicators",
         summary="Get recent technical indicator values",
         response_model=IndicatorResponse,
         responses={404: {"model": ErrorResponse}, 400: {"model": ErrorResponse}}
        )
def get_indicators(ticker: str = Query(..., description="Stock ticker symbol"),
                   days: int = Query(100, ge=1, description="Number of recent days for indicators")):

    ticker = ticker.upper().strip()
    if not ticker:
        raise HTTPException(status_code=400, detail="Ticker symbol is required.")
    try:
        print(f"\n--- Indicators request for {ticker}, last {days} days ---")
        buffer_days = 70 
        fetch_period = f'{days + buffer_days}d'
        df = fetch_stock_data(ticker, period=fetch_period)
        if df.empty:
            raise HTTPException(status_code=404, detail=f"No data found for {ticker}.")

        df_indicators = calculate_technical_indicators(df)

        if df_indicators.empty or len(df_indicators) < days:
             raise HTTPException(status_code=400, detail=f"Not enough data ({len(df_indicators)}/{days}) after indicators.")

        df_recent = df_indicators.tail(days).copy()
        df_recent.reset_index(inplace=True)
        date_col = 'index'
        if 'Date' in df_recent.columns: date_col = 'Date'
        df_recent[date_col] = pd.to_datetime(df_recent[date_col])

        dates_str = df_recent[date_col].dt.strftime('%Y-%m-%d').tolist()
        indicator_values = {}
        cols = ['MACD', 'Signal', 'RSI', 'OBV', 'MA10', 'MA50', 'Volatility', 'ROC_5']
        for col in cols:
             indicator_values[col] = [float(x) if pd.notna(x) else None for x in df_recent[col]]

        # Build lists of IndicatorData objects
        macd_list, rsi_list, obv_list, ma_list, vol_list, roc_list = [], [], [], [], [], []
        for i, date_str in enumerate(dates_str):
            macd_list.append(IndicatorData(date=date_str, MACD=indicator_values['MACD'][i], Signal=indicator_values['Signal'][i]))
            rsi_list.append(IndicatorData(date=date_str, RSI=indicator_values['RSI'][i]))
            obv_list.append(IndicatorData(date=date_str, OBV=indicator_values['OBV'][i]))
            ma_list.append(IndicatorData(date=date_str, MA10=indicator_values['MA10'][i], MA50=indicator_values['MA50'][i]))
            vol_list.append(IndicatorData(date=date_str, Volatility=indicator_values['Volatility'][i]))
            roc_list.append(IndicatorData(date=date_str, ROC_5=indicator_values['ROC_5'][i]))

        return IndicatorResponse(
            ticker=ticker,
            macd=macd_list,
            rsi=rsi_list,
            obv=obv_list,
            ma=ma_list,
            volatility=vol_list,
            roc=roc_list,
            last_updated=datetime.now(timezone.utc).isoformat()
        )
    except HTTPException as e:
        raise e
    except ValueError as e:
        print(f"ERROR [Indicators {ticker}]: ValueError. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=400, detail=f"Data error for indicators: {e}")
    except Exception as e:
        print(f"ERROR [Indicators {ticker}]: Unexpected. {type(e).__name__}: {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=500, detail=f"Failed to get indicators: {str(e)}")

@app.post("/predict_historical",
          summary="Compare historical actual prices vs model's historical predictions",
          response_model=HistoricalPredictionResponse,
          responses={404: {"model": ErrorResponse}, 400: {"model": ErrorResponse}}
         )
def predict_historical(
    req: HistoricalPredictionRequest,
    days: int = Query(60, ge=10, le=200, description="Number of past days to compare (10-200)")
):
   
    ticker = req.ticker.upper().strip()
    if not ticker:
        raise HTTPException(status_code=400, detail="Ticker symbol is required.")

    try:
        print(f"\n--- Historical Prediction Comparison request for {ticker}, last {days} days ---")
       
        try:
            scalers = load_scaler(ticker)
            model = load_trained_model(ticker)
        except FileNotFoundError as e:
            print(f"ERROR [PredictHist {ticker}]: Model/Scaler not found. {e}")
            raise HTTPException(status_code=404, detail=f"Model/scalers not found for {ticker}. Please train first.")

        feature_scaler = scalers['feature_scaler']
        close_scaler = scalers['close_scaler']

        sequence_length = 60 
        buffer_days = 70 
        fetch_period = f'{days + sequence_length + buffer_days}d'
        df_hist = fetch_stock_data(ticker, period=fetch_period)
        if df_hist.empty:
             raise HTTPException(status_code=404, detail=f"No data found for {ticker} to run historical comparison.")

        # Calculate technical indicators on the fetched data
        df_indicators = calculate_technical_indicators(df_hist)
       
        if df_indicators.empty or df_indicators.shape[0] < sequence_length + days:
             raise HTTPException(status_code=400, detail=f"Not enough data ({df_indicators.shape[0]}/{sequence_length + days}) after indicators calculation.")

        missing_features = [f for f in FEATURES if f not in df_indicators.columns]
        if missing_features:
            raise ValueError(f"Historical comparison data missing features: {missing_features}")

        # --- Generate historical sequences and predict ---
        X_hist_list = [] 
        dates_list = [] 
        actual_close_list = [] 
        num_features = len(FEATURES)
        end_index = df_indicators.shape[0] - 10 + 1
        start_index = max(sequence_length, end_index - days) 

        df_features_only = df_indicators[FEATURES]
        data_np = df_features_only.values

        print(f"Generating historical sequences from index {start_index} to {end_index-1}...")

        # Loop through the determined range to create input sequences and store corresponding dates
        for i in range(start_index, end_index):
            input_seq_np = data_np[i-sequence_length : i, :] 
            if input_seq_np.shape == (sequence_length, num_features): 
                X_hist_list.append(input_seq_np)
               
                dates_list.append(pd.to_datetime(df_indicators.index[i]).strftime('%Y-%m-%d'))
                actual_close_list.append(df_indicators['Close'].iloc[i])
            else:
                 print(f"Warning: Skipping sequence at index {i} due to shape mismatch: {input_seq_np.shape}")

       
        if not X_hist_list:
            raise ValueError("No valid historical sequences generated for the requested period.")

        X_hist_np = np.array(X_hist_list).astype('float32')
        print(f"Generated {X_hist_np.shape[0]} historical sequences.")

        X_hist_scaled = feature_scaler.transform(X_hist_np.reshape(-1, num_features)).reshape(X_hist_np.shape)
        y_pred_scaled_hist = model.predict(X_hist_scaled) 
       
        first_pred_scaled = y_pred_scaled_hist[:, 0] 
        predicted_prices_hist = close_scaler.inverse_transform(first_pred_scaled.reshape(-1, 1)).flatten() # Shape: (num_sequences,)

        comparison_list = []
        num_predictions = len(predicted_prices_hist)
        num_actuals = len(actual_close_list)
        num_dates = len(dates_list)

        if not (num_predictions == num_actuals == num_dates):
            print(f"Warning: Mismatch in lengths! Preds={num_predictions}, Actuals={num_actuals}, Dates={num_dates}")
            min_len = min(num_predictions, num_actuals, num_dates)
            predicted_prices_hist = predicted_prices_hist[:min_len]
            actual_close_list = actual_close_list[:min_len]
            dates_list = dates_list[:min_len]

        for i in range(len(dates_list)):
            comparison_list.append(HistoricalPredictionDataPoint(
                date=dates_list[i],
                actual_close=round(float(actual_close_list[i]), 2),
                predicted_close=round(float(predicted_prices_hist[i]), 2) if pd.notna(predicted_prices_hist[i]) else None
            ))

        print(f"Generated comparison data for {len(comparison_list)} dates.")

        return HistoricalPredictionResponse(
            ticker=ticker,
            comparison=comparison_list,
            last_updated=datetime.now(timezone.utc).isoformat()
        )
    
    except FileNotFoundError as e:
        print(f"ERROR [PredictHist {ticker}]: File Not Found Error. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=404, detail=f"File not found error during historical prediction: {e}")
    except ValueError as e:
        print(f"ERROR [PredictHist {ticker}]: ValueError. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=400, detail=f"Data error during historical prediction: {e}")
    except HTTPException as e: 
        raise e
    except Exception as e: 
        print(f"ERROR [PredictHist {ticker}]: Unexpected Error. {type(e).__name__}: {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=500, detail=f"An unexpected server error occurred: {type(e).__name__}")
    
@app.get("/ohlc_history",
         summary="Get recent OHLC data for candlestick charts",
         response_model=OHLCResponse,
         responses={404: {"model": ErrorResponse}, 400: {"model": ErrorResponse}}
         )
    
def get_ohlc_history(
    ticker: str = Query(..., description="Stock ticker symbol"),
    days: int = Query(100, ge=10, le=365*2, description="Number of past trading days for OHLC data (10-730)") # Allow up to 2 years
):
   
    ticker = ticker.upper().strip()
    if not ticker:
        raise HTTPException(status_code=400, detail="Ticker symbol is required.")

    try:
        print(f"\n--- OHLC History request for {ticker}, last {days} days ---")
        fetch_days_calendar = int(days * 1.6) + 20 
        df_raw = fetch_stock_data(ticker, period=f'{fetch_days_calendar}d')

        required_cols = ['Open', 'High', 'Low', 'Close']
        if df_raw.empty:
             raise HTTPException(status_code=404, detail=f"No data found for ticker {ticker}.")
        missing_cols = [col for col in required_cols if col not in df_raw.columns]
        if missing_cols:
             raise HTTPException(status_code=400, detail=f"Fetched data missing required columns: {missing_cols}")

       
        df_ohlc = df_raw[required_cols].tail(days).copy()

        if df_ohlc.empty:
            raise HTTPException(status_code=404, detail=f"Not enough OHLC history available for {ticker} ({days} days requested).")
        if len(df_ohlc) < days:
             print(f"Warning: Only found {len(df_ohlc)}/{days} days of OHLC history for {ticker}.")

        df_ohlc.reset_index(inplace=True)
        date_col = 'index' 
        if 'Date' in df_ohlc.columns: date_col = 'Date' 
       
        ohlc_list = []
        for _, row in df_ohlc.iterrows():
            if pd.notna(row[date_col]) and \
               pd.notna(row['Open']) and \
               pd.notna(row['High']) and \
               pd.notna(row['Low']) and \
               pd.notna(row['Close']):
                ohlc_list.append(OHLCDataPoint(
                    date=pd.to_datetime(row[date_col]).strftime('%Y-%m-%d'),
                    o=round(float(row['Open']), 2),
                    h=round(float(row['High']), 2),
                    l=round(float(row['Low']), 2),
                    c=round(float(row['Close']), 2)
                ))
            else:
                print(f"Warning: Skipping row for date {row[date_col]} due to NaN values.")

        if not ohlc_list:
             raise HTTPException(status_code=400, detail="Failed to format any OHLC data points, check for NaNs.")

       
        return OHLCResponse(
            ticker=ticker,
            ohlc_data=ohlc_list,
            last_updated=datetime.now(timezone.utc).isoformat()
        )

   
    except HTTPException as e: 
         raise e
    except ValueError as e:
        print(f"ERROR [OHLC History {ticker}]: ValueError. {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=400, detail=f"Data processing error for OHLC history: {e}")
    except Exception as e: 
        print(f"ERROR [OHLC History {ticker}]: Unexpected Error. {type(e).__name__}: {e}\n{traceback.format_exc()}")
        raise HTTPException(status_code=500, detail=f"An unexpected server error occurred while fetching OHLC data: {type(e).__name__}")


