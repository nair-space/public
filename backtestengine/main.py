import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import os
import warnings
from datetime import datetime

# Import modular components
from config import DATA_FOLDER, RESULT_FOLDER, TradingConfig
from indicators import calculate_atr, calculate_ichimoku, calculate_bollinger_bands, calculate_williams_r, calculate_stochastic
from strategies import identify_trend, generate_trend_signals, generate_mean_reversion_signals
from backtest import run_backtest
from analytics import analyze_performance
from visualization import plot_backtest_results
from strategy_finder import find_best_strategy

warnings.filterwarnings('ignore')

def load_and_validate_data(filename):
    """
    Load and validate CSV data
    """
    try:
        df = pd.read_csv(filename)
        
        # Clean column names: lowercase and strip whitespace
        df.columns = [col.lower().strip() for col in df.columns]
        
        # Check required columns
        required_cols = ['date', 'open', 'high', 'low', 'close']
        missing_cols = [col for col in required_cols if col not in df.columns]
        
        if missing_cols:
            raise ValueError(f"Missing required columns: {missing_cols}")
        
        # Convert date column
        df['date'] = pd.to_datetime(df['date'])
        
        # Sort by date
        df = df.sort_values('date').reset_index(drop=True)
        
        # Check for missing values
        if df[required_cols].isnull().any().any():
            print("WARNING: Missing values detected. Filling with forward fill method.")
            df = df.fillna(method='ffill')
        
        print(f"\n✓ Data loaded successfully!")
        print(f"Total bars: {len(df)}")
        print(f"Date range: {df['date'].min().date()} to {df['date'].max().date()}")
        print(f"\nFirst 5 rows:")
        print(df.head())
        
        return df
        
    except Exception as e:
        raise ValueError(f"Error loading data: {str(e)}")

def main():
    print("="*80)
    print("MULTI-PAIR TRADING SYSTEM BACKTEST")
    print("="*80)

    # 1. LOAD DATA FILE
    if not os.path.exists(DATA_FOLDER):
        print(f"Error: '{DATA_FOLDER}' folder not found.")
        return

    csv_files = [f for f in os.listdir(DATA_FOLDER) if f.endswith('.csv')]

    if csv_files:
        print(f"\nAvailable Data Files in '{DATA_FOLDER}':")
        for i, f in enumerate(csv_files, 1):
            print(f"{i}. {f}")
        
        file_choice = input("\nEnter file number or filename: ").strip()
        if file_choice.isdigit() and 1 <= int(file_choice) <= len(csv_files):
            data_filename = csv_files[int(file_choice)-1]
        else:
            data_filename = file_choice
    else:
        data_filename = input(f"\nEnter CSV filename in '{DATA_FOLDER}' (e.g., AUDUSD.csv): ").strip()

    full_data_path = os.path.join(DATA_FOLDER, data_filename)

    if not os.path.exists(full_data_path):
        print(f"Error: File '{full_data_path}' not found.")
        return

    # Automatically determine pair name from filename
    basename = os.path.splitext(data_filename)[0].upper()
    if '_' in basename:
        selected_pair = basename.split('_')[0]
    else:
        selected_pair = basename

    print(f"\n✓ Detected Pair: {selected_pair}")

    # 2. SELECT STRATEGY
    print("\nSelect Strategy Logic:")
    print("1. MEAN REVERSION")
    print("2. TREND FOLLOWING")
    print("3. STRATEGY FINDER")
    strat_choice = input("\nEnter strategy number (1, 2, or 3): ").strip()

    if strat_choice == '1':
        strategy_type = 'MEAN_REVERSION'
    elif strat_choice == '2':
        strategy_type = 'TREND_FOLLOWING'
    elif strat_choice == '3':
        strategy_type = 'STRATEGY_FINDER'
    else:
        print("Invalid choice, defaulting to MEAN REVERSION")
        strategy_type = 'MEAN_REVERSION'

    print(f"\n✓ Running {strategy_type} on {selected_pair}")

    # 3. SELECT ATR MULTIPLIER
    # Get current default settings to display to user
    temp_config = TradingConfig(selected_pair, strategy_type)
    
    print("\nSelect ATR Multiplier:")
    print("1. select ATR multiplier")
    print("2. modify the ATR multiplier")
    print(f"\ncurrent ATR multiplier: SL={temp_config.sl_multiplier}, TP={temp_config.tp_multiplier}")
    atr_choice = input("\nEnter choice (1 or 2): ").strip()

    custom_sl = None
    custom_tp = None

    if atr_choice == '2':
        try:
            custom_sl = float(input("1. input the ATR multiplier for SL (e.g., 1.23): ").strip())
            custom_tp = float(input("2. input the ATR multiplier for TP (e.g., 1.23): ").strip())
        except ValueError:
            print("Invalid input, using default multipliers.")
            custom_sl = None
            custom_tp = None

    # 4. CHART TOGGLE (Skip if Strategy Finder)
    generate_chart = False
    if strategy_type != 'STRATEGY_FINDER':
        generate_chart = input("\nGenerate PNG visualization chart? (y/n): ").lower().strip() == 'y'

    # 5. INITIALIZE CONFIGURATION OR RUN STRATEGY FINDER
    if strategy_type == 'STRATEGY_FINDER':
        try:
            custom_sl = float(input("Select SL ATR Multiplier: ").strip())
            custom_tp = float(input("Select TP ATR Multiplier: ").strip())
        except ValueError:
            print("Invalid input, using default multipliers (SL=1.5, TP=2.0).")
            custom_sl = 1.5
            custom_tp = 2.0
            
        # Run strategy finder
        best_strat = find_best_strategy(load_and_validate_data(full_data_path), selected_pair, custom_sl, custom_tp)
        if best_strat == 'ML_STRATEGY':
            # ML Strategy Finder already ran its own backtest, exit here
            print("\n" + "="*80)
            print("ML STRATEGY FINDER SESSION COMPLETE!")
            print("="*80)
            return
        elif best_strat:
            strategy_type = best_strat
            print(f"Continuing with winners choice: {strategy_type}")
        else:
            return
            
    # Regular flow continues with the chosen (or found) strategy
    config = TradingConfig(selected_pair, strategy_type, sl_multiplier=custom_sl, tp_multiplier=custom_tp)
    config.display_config()

    # 6. LOAD DATA
    df = load_and_validate_data(full_data_path)

    # 7. CALCULATE INDICATORS
    print("\nCalculating indicators...")
    df = calculate_atr(df, config.atr_period)

    if strategy_type == 'TREND_FOLLOWING':
        df = calculate_ichimoku(df, config.tenkan_period, config.kijun_period)
        df = calculate_stochastic(df, config.stoch_k_period, config.stoch_d_period, config.stoch_slowing)
        print(f"✓ Ichimoku calculated (Tenkan: {config.tenkan_period}, Kijun: {config.kijun_period})")
        print(f"✓ Stochastic calculated (K: {config.stoch_k_period}, D: {config.stoch_d_period}, Slowing: {config.stoch_slowing})")
    else:
        df = calculate_bollinger_bands(df, config.bollinger_period, config.bollinger_std)
        df = calculate_williams_r(df, config.williams_period)
        print(f"✓ Bollinger Bands calculated (Period: {config.bollinger_period})")
        print(f"✓ Williams %R calculated (Period: {config.williams_period})")

    print(f"✓ ATR calculated (Period: {config.atr_period})")

    # 8. GENERATE SIGNALS
    print("\nGenerating trading signals...")
    if strategy_type == 'TREND_FOLLOWING':
        df = identify_trend(df)
        df = generate_trend_signals(df, config)
    else:
        df = generate_mean_reversion_signals(df, config)

    signals_df = df[df['signal'].notna()]
    print(f"✓ Generated {len(signals_df)} signals")

    # 9. RUN BACKTEST
    print("\nRunning backtest...")
    trades, equity_curve = run_backtest(df, config)
    print(f"✓ Backtest complete! Executed {len(trades)} trades")

    # 10. ANALYZE PERFORMANCE
    trades_df, metrics_summary = analyze_performance(trades, equity_curve, config.initial_equity, selected_pair)

    # 11. VISUALIZATION
    if trades_df is not None:
        if generate_chart:
            print("\nGenerating charts...")
            plot_backtest_results(df, trades_df, equity_curve, config, metrics_summary)
        else:
            print("\n✓ Skipping chart generation as requested.")

        # 12. EXPORT RESULTS
        # Save trade log
        trade_log_filename = f'{selected_pair}_trades.csv'
        trade_log_path = os.path.join(RESULT_FOLDER, trade_log_filename)
        trades_df.to_csv(trade_log_path, index=False)
        print(f"\n✓ Trade log saved: {trade_log_path}")

        # Save equity curve
        equity_df = pd.DataFrame({
            'bar': range(len(equity_curve)),
            'equity': equity_curve
        })
        equity_filename = f'{selected_pair}_equity_curve.csv'
        equity_path = os.path.join(RESULT_FOLDER, equity_filename)
        equity_df.to_csv(equity_path, index=False)
        print(f"✓ Equity curve saved: {equity_path}")

        print("\n" + "="*80)
        print("BACKTEST COMPLETE!")
        print("="*80)
        print(f"\nFiles generated in '{RESULT_FOLDER}':")
        print(f"1. {trade_log_filename}")
        print(f"2. {equity_filename}")
        if generate_chart:
            print(f"3. {selected_pair}_backtest_results.png")
        print("\n" + "="*80)

if __name__ == "__main__":
    main()
