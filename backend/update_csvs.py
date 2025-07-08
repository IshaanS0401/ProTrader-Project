import yfinance as yf
import pandas as pd
import os
import time

# --- Configuration ---
# List of tickers to download (You could load this from a file or use the list from your datalist)
TICKERS = [
    "AAPL", "MSFT", "AMZN", "GOOGL", "GOOG", "META", "TSLA", "BRK-B", "JNJ", "V",
    "JPM", "WMT", "PG", "MA", "UNH", "HD", "NVDA", "PFE", "BAC", "DIS",
    "XOM", "VZ", "ADBE", "CRM", "CSCO", "KO", "PEP", "NFLX", "CMCSA", "ABT",
    "TMO", "ABBV", "DHR", "ACN", "NKE", "T", "PM", "LIN", "AVGO", "ORCL",
    "LLY", "CVX", "MRK", "BMY", "AMD", "QCOM", "INTU", "HON", "AMGN", "SBUX"
    # Add any other tickers you want to support
]

# Directory to save the CSV files
CSV_DIR = "stock_data_csv"

# Data period to download (e.g., '10y' for 10 years, 'max' for all available)
DOWNLOAD_PERIOD = "10y"

# --- Main Logic ---
def update_stock_csvs():
    """Downloads historical data for tickers and saves them as CSV files."""
    print(f"Starting CSV update process for {len(TICKERS)} tickers...")
    os.makedirs(CSV_DIR, exist_ok=True) # Create directory if it doesn't exist

    success_count = 0
    error_count = 0
    error_tickers = []

    for ticker in TICKERS:
        print(f"Processing: {ticker}...")
        filepath = os.path.join(CSV_DIR, f"{ticker}.csv")
        try:
            # Download data using yfinance
            data = yf.download(ticker, period=DOWNLOAD_PERIOD, progress=False, auto_adjust=True, timeout=20)

            if data.empty:
                print(f"  WARN: No data returned for {ticker}. Skipping.")
                error_count += 1
                error_tickers.append(ticker + " (No data)")
                continue # Skip to the next ticker

            # Ensure the index is a DatetimeIndex
            if not isinstance(data.index, pd.DatetimeIndex):
                 data.index = pd.to_datetime(data.index)

            # Sort by date just in case
            data.sort_index(inplace=True)

            # Save to CSV (overwrite existing file)
            # index=True saves the date index to the CSV
            data.to_csv(filepath, index=True)
            print(f"  SUCCESS: Saved data to {filepath}")
            success_count += 1

        except Exception as e:
            print(f"  ERROR processing {ticker}: {e}")
            error_count += 1
            error_tickers.append(ticker + f" ({type(e).__name__})")

        # Optional: Add a small delay between requests to be polite to the API
        time.sleep(0.5)

    print("\n--- CSV Update Summary ---")
    print(f"Successfully updated: {success_count}")
    print(f"Errors encountered: {error_count}")
    if error_tickers:
        print("Tickers with errors:", ", ".join(error_tickers))
    print("--------------------------")

if __name__ == "__main__":
    # Run the update function when the script is executed directly
    update_stock_csvs()
