# ============================================================================
# MULTI-PAIR TRADING SYSTEM BACKTEST - GOOGLE COLAB
# Supports: Trend Following & Mean Reversion Strategies
# ============================================================================

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from datetime import datetime
import warnings
warnings.filterwarnings('ignore')

# ============================================================================
# CELL 1: PAIR SELECTION & CONFIGURATION
# ============================================================================

# ============================================================================
# STEP 1: LOAD DATA FILE
# ============================================================================

import os

# Define folders
DATA_FOLDER = 'candle_data'
RESULT_FOLDER = 'backtest_result'

# Ensure results folder exists
if not os.path.exists(RESULT_FOLDER):
    os.makedirs(RESULT_FOLDER)

print("="*80)
print("MULTI-PAIR TRADING SYSTEM BACKTEST")
print("="*80)

# Try to find CSV files in the data directory
if not os.path.exists(DATA_FOLDER):
    print(f"Error: '{DATA_FOLDER}' folder not found.")
    exit(1)

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
    exit(1)

# Automatically determine pair name from filename
# For filenames like "audusd_d.csv", we want "AUDUSD"
basename = os.path.splitext(data_filename)[0].upper()
if '_' in basename:
    selected_pair = basename.split('_')[0]
else:
    selected_pair = basename

print(f"\n✓ Detected Pair: {selected_pair}")

# ============================================================================
# STEP 2: SELECT STRATEGY
# ============================================================================

print("\nSelect Strategy Logic:")
print("1. MEAN REVERSION")
print("2. TREND FOLLOWING")
strat_choice = input("\nEnter strategy number (1 or 2): ").strip()

if strat_choice == '1':
    strategy_type = 'MEAN_REVERSION'
elif strat_choice == '2':
    strategy_type = 'TREND_FOLLOWING'
else:
    print("Invalid choice, defaulting to MEAN REVERSION")
    strategy_type = 'MEAN_REVERSION'

print(f"\n✓ Running {strategy_type} on {selected_pair}")

# ============================================================================
# CELL 2: PAIR-SPECIFIC CONFIGURATION
# ============================================================================

class TradingConfig:
    """
    Configuration class with pair-specific settings
    """
    def __init__(self, pair, strategy_type):
        self.pair = pair
        self.strategy_type = strategy_type
        self.timeframe = 'D1'
        self.initial_equity = 10000
        self.risk_per_trade = 0.01  # 1% per trade
        
        # Set pip value based on pair (Always calculated automatically)
        self.pip_value = self._get_pip_value()
        
        # Set parameters
        if strategy_type == 'TREND_FOLLOWING':
            self._set_trend_following_params()
            self.tenkan_period = 5
            self.kijun_period = 13
        else:
            self._set_mean_reversion_params()
            self.bollinger_period = 21
            self.bollinger_std = 2
            self.williams_period = 21
            self.williams_overbought = -30
            self.williams_oversold = -70
        
        # ENFORCE MINIMUM RISK:REWARD (1:1.05)
        min_tp_multiplier = self.sl_multiplier * 1.05
        if self.tp_multiplier < min_tp_multiplier:
            self.tp_multiplier = min_tp_multiplier
            
        self.atr_period = 14
    
    def _get_pip_value(self):
        """
        Get pip value based on pair
        """
        if 'JPY' in self.pair:
            return 0.01  # For JPY pairs, 1 pip = 0.01
        elif self.pair == 'BTCUSD':
            return 1.0   # For BTC, 1 pip = 1.0
        else:
            return 0.0001  # For most pairs, 1 pip = 0.0001
    
    def _set_trend_following_params(self):
        """
        Set parameters for trend following. Has defaults if pair not in list.
        """
        # Default settings if pair is not optimized for trend following
        default_settings = {
            'pending_offset': 20,
            'sl_multiplier': 1.5,
            'tp_multiplier': 2.0,  # Increased from 1.5 for better default RR
            'allow_scale_in': False,
            'scale_in_offset': 0
        }

        params = {
            'XAGUSD': {
                'pending_offset': 20,
                'sl_multiplier': 1.5,
                'tp_multiplier': 1.77,
                'allow_scale_in': True,
                'scale_in_offset': 20
            },
            'XAUUSD': {
                'pending_offset': 30,
                'sl_multiplier': 1.05,
                'tp_multiplier': 1.12,
                'allow_scale_in': True,
                'scale_in_offset': 20
            },
            'BTCUSD': {
                'pending_offset': 20,
                'sl_multiplier': 1.35,
                'tp_multiplier': 1.45,
                'allow_scale_in': True,
                'scale_in_offset': 20
            },
            'USDJPY': {
                'pending_offset': 20,
                'sl_multiplier': 1.55,
                'tp_multiplier': 1.66,
                'allow_scale_in': False,
                'scale_in_offset': 0
            },
            'USDCHF': {
                'pending_offset': 20,
                'sl_multiplier': 1.3,
                'tp_multiplier': 1.44,
                'allow_scale_in': False,
                'scale_in_offset': 0
            }
        }
        
        settings = params.get(self.pair, default_settings)
        self.pending_offset = settings['pending_offset']
        self.sl_multiplier = settings['sl_multiplier']
        self.tp_multiplier = settings['tp_multiplier']
        self.allow_scale_in = settings['allow_scale_in']
        self.scale_in_offset = settings['scale_in_offset']
    
    def _set_mean_reversion_params(self):
        """
        Set parameters for mean reversion. Has defaults if pair not in list.
        """
        # Default settings if pair is not optimized for mean reversion
        default_settings = {
            'pending_offset': 20,
            'sl_multiplier': 1.5,
            'tp_multiplier': 2.0   # Increased from 1.5 for better default RR
        }

        params = {
            'NZDJPY': {
                'pending_offset': 20,
                'sl_multiplier': 1.35,
                'tp_multiplier': 1.45
            },
            'GBPJPY': {
                'pending_offset': 20,
                'sl_multiplier': 1.5,
                'tp_multiplier': 1.65
            },
            'EURGBP': {
                'pending_offset': 20,
                'sl_multiplier': 1.25,
                'tp_multiplier': 1.38
            },
            'AUDUSD': {
                'pending_offset': 20,
                'sl_multiplier': 1.2,
                'tp_multiplier': 1.33
            }
        }
        
        settings = params.get(self.pair, default_settings)
        self.pending_offset = settings['pending_offset']
        self.sl_multiplier = settings['sl_multiplier']
        self.tp_multiplier = settings['tp_multiplier']
        self.allow_scale_in = False
    
    def display_config(self):
        """
        Display configuration summary
        """
        print("\n" + "="*80)
        print("CONFIGURATION SUMMARY")
        print("="*80)
        print(f"Pair: {self.pair}")
        print(f"Strategy: {self.strategy_type}")
        print(f"Timeframe: {self.timeframe}")
        print(f"Pip Value: {self.pip_value}")
        print(f"Pending Offset: {self.pending_offset} pips")
        print(f"Stop Loss Multiplier: {self.sl_multiplier}x ATR")
        print(f"Take Profit Multiplier: {self.tp_multiplier:.2f}x ATR")
        print(f"Target Risk:Reward Ratio: 1:{(self.tp_multiplier/self.sl_multiplier):.2f}")
        print(f"Risk per Trade: {self.risk_per_trade*100}%")
        print(f"Initial Equity: ${self.initial_equity:,.2f}")
        
        if self.strategy_type == 'TREND_FOLLOWING':
            print(f"Scale-in Allowed: {self.allow_scale_in}")
            print(f"Ichimoku Tenkan: {self.tenkan_period}")
            print(f"Ichimoku Kijun: {self.kijun_period}")
        else:
            print(f"Bollinger Period: {self.bollinger_period}")
            print(f"Williams %R Period: {self.williams_period}")
        
        print(f"ATR Period: {self.atr_period}")
        print("="*80 + "\n")

# ============================================================================
# STEP 3: INITIALIZE CONFIGURATION
# ============================================================================

config = TradingConfig(selected_pair, strategy_type)
config.display_config()

# ============================================================================
# CELL 4: DATA LOADING & VALIDATION
# ============================================================================

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

# Load data
df = load_and_validate_data(full_data_path)

# ============================================================================
# CELL 5: INDICATOR CALCULATIONS
# ============================================================================

def calculate_atr(df, period=14):
    """Calculate Average True Range"""
    high_low = df['high'] - df['low']
    high_close = np.abs(df['high'] - df['close'].shift(1))
    low_close = np.abs(df['low'] - df['close'].shift(1))
    
    ranges = pd.concat([high_low, high_close, low_close], axis=1)
    true_range = ranges.max(axis=1)
    df['atr'] = true_range.rolling(window=period).mean()
    
    return df

def calculate_ichimoku(df, tenkan_period=5, kijun_period=13):
    """Calculate Ichimoku Tenkan-sen and Kijun-sen"""
    high_tenkan = df['high'].rolling(window=tenkan_period).max()
    low_tenkan = df['low'].rolling(window=tenkan_period).min()
    df['tenkan_sen'] = (high_tenkan + low_tenkan) / 2
    
    high_kijun = df['high'].rolling(window=kijun_period).max()
    low_kijun = df['low'].rolling(window=kijun_period).min()
    df['kijun_sen'] = (high_kijun + low_kijun) / 2
    
    return df

def calculate_bollinger_bands(df, period=21, std_dev=2):
    """Calculate Bollinger Bands"""
    sma = df['close'].rolling(window=period).mean()
    std = df['close'].rolling(window=period).std()
    
    df['bb_upper'] = sma + (std_dev * std)
    df['bb_middle'] = sma
    df['bb_lower'] = sma - (std_dev * std)
    
    return df

def calculate_williams_r(df, period=21):
    """Calculate Williams %R"""
    highest_high = df['high'].rolling(window=period).max()
    lowest_low = df['low'].rolling(window=period).min()
    
    df['williams_r'] = -100 * (highest_high - df['close']) / (highest_high - lowest_low)
    
    return df

print("\nCalculating indicators...")

# Calculate ATR for all strategies
df = calculate_atr(df, config.atr_period)

# Calculate strategy-specific indicators
if strategy_type == 'TREND_FOLLOWING':
    df = calculate_ichimoku(df, config.tenkan_period, config.kijun_period)
    print(f"✓ Ichimoku calculated (Tenkan: {config.tenkan_period}, Kijun: {config.kijun_period})")
else:
    df = calculate_bollinger_bands(df, config.bollinger_period, config.bollinger_std)
    df = calculate_williams_r(df, config.williams_period)
    print(f"✓ Bollinger Bands calculated (Period: {config.bollinger_period})")
    print(f"✓ Williams %R calculated (Period: {config.williams_period})")

print(f"✓ ATR calculated (Period: {config.atr_period})")

# ============================================================================
# CELL 6: TREND FOLLOWING STRATEGY LOGIC
# ============================================================================

def identify_trend(df):
    """Identify trend for trend following strategy"""
    df['trend'] = 'neutral'
    
    for i in range(1, len(df)):
        current_tenkan = df['tenkan_sen'].iloc[i]
        current_kijun = df['kijun_sen'].iloc[i]
        prev_tenkan = df['tenkan_sen'].iloc[i-1]
        prev_kijun = df['kijun_sen'].iloc[i-1]
        
        # Uptrend
        tenkan_upcross = (prev_tenkan < prev_kijun) and (current_tenkan >= current_kijun)
        if current_tenkan >= current_kijun and tenkan_upcross:
            df.loc[df.index[i], 'trend'] = 'uptrend'
        
        # Downtrend
        tenkan_downcross = (prev_tenkan > prev_kijun) and (current_tenkan <= current_kijun)
        if current_tenkan <= current_kijun and tenkan_downcross:
            df.loc[df.index[i], 'trend'] = 'downtrend'
        
        # Maintain previous trend
        if df['trend'].iloc[i] == 'neutral' and i > 1:
            df.loc[df.index[i], 'trend'] = df['trend'].iloc[i-1]
    
    return df

def check_trend_entry_filter(close_1, tenkan_1, kijun_1):
    """Filter for trend following: no entry if close between tenkan and kijun"""
    if min(tenkan_1, kijun_1) < close_1 < max(tenkan_1, kijun_1):
        return False
    return True

def generate_trend_signals(df, config):
    """Generate signals for trend following strategy"""
    df['signal'] = None
    df['entry_price'] = np.nan
    df['stop_loss'] = np.nan
    df['take_profit'] = np.nan
    
    for i in range(2, len(df)):
        close_1 = df['close'].iloc[i-1]
        high_1 = df['high'].iloc[i-1]
        low_1 = df['low'].iloc[i-1]
        tenkan_1 = df['tenkan_sen'].iloc[i-1]
        kijun_1 = df['kijun_sen'].iloc[i-1]
        atr_1 = df['atr'].iloc[i-1]
        
        if pd.isna(atr_1) or pd.isna(tenkan_1) or pd.isna(kijun_1):
            continue
        
        if not check_trend_entry_filter(close_1, tenkan_1, kijun_1):
            continue
        
        # BUY signal
        if close_1 > tenkan_1:
            entry_price = high_1 + (config.pending_offset * config.pip_value)
            stop_loss = entry_price - (config.sl_multiplier * atr_1)
            take_profit = entry_price + (config.tp_multiplier * atr_1)
            
            df.loc[df.index[i], 'signal'] = 'buy'
            df.loc[df.index[i], 'entry_price'] = entry_price
            df.loc[df.index[i], 'stop_loss'] = stop_loss
            df.loc[df.index[i], 'take_profit'] = take_profit
        
        # SELL signal
        elif close_1 < tenkan_1:
            entry_price = low_1 - (config.pending_offset * config.pip_value)
            stop_loss = entry_price + (config.sl_multiplier * atr_1)
            take_profit = entry_price - (config.tp_multiplier * atr_1)
            
            df.loc[df.index[i], 'signal'] = 'sell'
            df.loc[df.index[i], 'entry_price'] = entry_price
            df.loc[df.index[i], 'stop_loss'] = stop_loss
            df.loc[df.index[i], 'take_profit'] = take_profit
    
    return df

# ============================================================================
# CELL 7: MEAN REVERSION STRATEGY LOGIC
# ============================================================================

def check_mean_reversion_filter(williams_r_1):
    """Filter for mean reversion: no entry if Williams %R between -30 and -70"""
    if -70 < williams_r_1 < -30:
        return False
    return True

def generate_mean_reversion_signals(df, config):
    """Generate signals for mean reversion strategy"""
    df['signal'] = None
    df['entry_price'] = np.nan
    df['stop_loss'] = np.nan
    df['take_profit'] = np.nan
    
    for i in range(2, len(df)):
        close_1 = df['close'].iloc[i-1]
        close_2 = df['close'].iloc[i-2]
        high_1 = df['high'].iloc[i-1]
        low_1 = df['low'].iloc[i-1]
        bb_upper_1 = df['bb_upper'].iloc[i-1]
        bb_lower_1 = df['bb_lower'].iloc[i-1]
        williams_1 = df['williams_r'].iloc[i-1]
        atr_1 = df['atr'].iloc[i-1]
        
        if pd.isna(atr_1) or pd.isna(bb_upper_1) or pd.isna(williams_1):
            continue
        
        if not check_mean_reversion_filter(williams_1):
            continue
        
        # BUY signal (oversold)
        buy_condition_1 = close_1 > close_2
        buy_condition_2 = (high_1 < bb_lower_1) and (close_1 > bb_lower_1)
        
        if buy_condition_1 or buy_condition_2:
            entry_price = high_1 + (config.pending_offset * config.pip_value)
            stop_loss = entry_price - (config.sl_multiplier * atr_1)
            take_profit = entry_price + (config.tp_multiplier * atr_1)
            
            df.loc[df.index[i], 'signal'] = 'buy'
            df.loc[df.index[i], 'entry_price'] = entry_price
            df.loc[df.index[i], 'stop_loss'] = stop_loss
            df.loc[df.index[i], 'take_profit'] = take_profit
        
        # SELL signal (overbought)
        sell_condition_1 = close_1 < close_2
        sell_condition_2 = (high_1 > bb_upper_1) and (close_1 < bb_upper_1)
        
        if sell_condition_1 or sell_condition_2:
            entry_price = low_1 - (config.pending_offset * config.pip_value)
            stop_loss = entry_price + (config.sl_multiplier * atr_1)
            take_profit = entry_price - (config.tp_multiplier * atr_1)
            
            df.loc[df.index[i], 'signal'] = 'sell'
            df.loc[df.index[i], 'entry_price'] = entry_price
            df.loc[df.index[i], 'stop_loss'] = stop_loss
            df.loc[df.index[i], 'take_profit'] = take_profit
    
    return df

# ============================================================================
# CELL 8: GENERATE SIGNALS BASED ON STRATEGY
# ============================================================================

print("\nGenerating trading signals...")

if strategy_type == 'TREND_FOLLOWING':
    df = identify_trend(df)
    df = generate_trend_signals(df, config)
else:
    df = generate_mean_reversion_signals(df, config)

signals = df[df['signal'].notna()]
print(f"✓ Generated {len(signals)} signals")

if len(signals) > 0:
    print(f"\nSignal breakdown:")
    print(f"  Buy signals: {len(signals[signals['signal'] == 'buy'])}")
    print(f"  Sell signals: {len(signals[signals['signal'] == 'sell'])}")
    print(f"\nFirst 5 signals:")
    print(signals[['date', 'signal', 'entry_price', 'stop_loss', 'take_profit']].head())

# ============================================================================
# CELL 9: BACKTEST ENGINE
# ============================================================================

def calculate_position_size(equity, entry_price, stop_loss, risk_percent):
    """Calculate position size based on risk"""
    risk_amount = equity * risk_percent
    price_risk = abs(entry_price - stop_loss)
    
    if price_risk == 0:
        return 0
    
    position_size = risk_amount / price_risk
    return position_size

def run_backtest(df, config):
    """Execute backtest"""
    equity = config.initial_equity
    peak_equity = equity
    position = None
    trades = []
    equity_curve = [equity]
    
    for i in range(len(df)):
        current_bar = df.iloc[i]
        
        # Manage existing position
        if position is not None:
            current_high = current_bar['high']
            current_low = current_bar['low']
            
            position_closed = False
            exit_price = None
            exit_reason = None
            
            if position['direction'] == 'buy':
                if current_low <= position['stop_loss']:
                    exit_price = position['stop_loss']
                    exit_reason = 'stop_loss'
                    position_closed = True
                elif current_high >= position['take_profit']:
                    exit_price = position['take_profit']
                    exit_reason = 'take_profit'
                    position_closed = True
            
            elif position['direction'] == 'sell':
                if current_high >= position['stop_loss']:
                    exit_price = position['stop_loss']
                    exit_reason = 'stop_loss'
                    position_closed = True
                elif current_low <= position['take_profit']:
                    exit_price = position['take_profit']
                    exit_reason = 'take_profit'
                    position_closed = True
            
            if position_closed:
                if position['direction'] == 'buy':
                    profit = (exit_price - position['entry_price']) * position['size']
                else:
                    profit = (position['entry_price'] - exit_price) * position['size']
                
                equity += profit
                
                trade_record = {
                    'entry_date': position['entry_date'],
                    'exit_date': current_bar['date'],
                    'direction': position['direction'],
                    'entry_price': position['entry_price'],
                    'exit_price': exit_price,
                    'stop_loss': position['stop_loss'],
                    'take_profit': position['take_profit'],
                    'size': position['size'],
                    'profit': profit,
                    'profit_pct': (profit / position['equity_at_entry']) * 100,
                    'equity': equity,
                    'exit_reason': exit_reason
                }
                trades.append(trade_record)
                
                if equity > peak_equity:
                    peak_equity = equity
                
                position = None
        
        # Check for new signals
        if position is None and pd.notna(current_bar['signal']):
            entry_price = current_bar['entry_price']
            stop_loss = current_bar['stop_loss']
            take_profit = current_bar['take_profit']
            
            position_size = calculate_position_size(
                equity, entry_price, stop_loss, config.risk_per_trade
            )
            
            position = {
                'direction': current_bar['signal'],
                'entry_date': current_bar['date'],
                'entry_price': entry_price,
                'stop_loss': stop_loss,
                'take_profit': take_profit,
                'size': position_size,
                'equity_at_entry': equity
            }
        
        equity_curve.append(equity)
    
    # Close remaining position
    if position is not None:
        final_bar = df.iloc[-1]
        exit_price = final_bar['close']
        
        if position['direction'] == 'buy':
            profit = (exit_price - position['entry_price']) * position['size']
        else:
            profit = (position['entry_price'] - exit_price) * position['size']
        
        equity += profit
        
        trade_record = {
            'entry_date': position['entry_date'],
            'exit_date': final_bar['date'],
            'direction': position['direction'],
            'entry_price': position['entry_price'],
            'exit_price': exit_price,
            'stop_loss': position['stop_loss'],
            'take_profit': position['take_profit'],
            'size': position['size'],
            'profit': profit,
            'profit_pct': (profit / position['equity_at_entry']) * 100,
            'equity': equity,
            'exit_reason': 'end_of_data'
        }
        trades.append(trade_record)
    
    return trades, equity_curve

print("\nRunning backtest...")
trades, equity_curve = run_backtest(df, config)
print(f"✓ Backtest complete! Executed {len(trades)} trades")

# ============================================================================
# CELL 10: PERFORMANCE ANALYSIS
# ============================================================================

def analyze_performance(trades, equity_curve, initial_equity, pair):
    """Calculate and display performance metrics"""
    
    if len(trades) == 0:
        print("\n⚠ No trades executed!")
        return None
    
    trades_df = pd.DataFrame(trades)
    
    # Basic statistics
    total_trades = len(trades_df)
    winning_trades = len(trades_df[trades_df['profit'] > 0])
    losing_trades = len(trades_df[trades_df['profit'] < 0])
    win_rate = (winning_trades / total_trades) * 100 if total_trades > 0 else 0
    
    # Profit metrics
    total_profit = trades_df['profit'].sum()
    total_return_pct = ((equity_curve[-1] - initial_equity) / initial_equity) * 100
    avg_profit_pct = trades_df['profit_pct'].mean()
    avg_win_pct = trades_df[trades_df['profit'] > 0]['profit_pct'].mean() if winning_trades > 0 else 0
    avg_loss_pct = trades_df[trades_df['profit'] < 0]['profit_pct'].mean() if losing_trades > 0 else 0
    
    # Profit factor
    gross_profit = trades_df[trades_df['profit'] > 0]['profit'].sum()
    gross_loss = abs(trades_df[trades_df['profit'] < 0]['profit'].sum())
    profit_factor = gross_profit / gross_loss if gross_loss > 0 else float('inf')
    
    # Drawdown
    equity_series = pd.Series(equity_curve)
    running_max = equity_series.cummax()
    drawdown = (equity_series - running_max) / running_max * 100
    max_drawdown = drawdown.min()
    
    # Risk-reward (Calculated on percentage basis to show strategy effectiveness)
    avg_rr = abs(avg_win_pct / avg_loss_pct) if avg_loss_pct != 0 else 0
    
    # Sharpe Ratio (Annualized based on daily bars)
    equity_series = pd.Series(equity_curve)
    daily_returns = equity_series.pct_change().fillna(0)
    if daily_returns.std() != 0:
        sharpe_ratio = (daily_returns.mean() / daily_returns.std()) * np.sqrt(252)
    else:
        sharpe_ratio = 0
    
    # Largest win/loss
    largest_win = trades_df['profit'].max()
    largest_loss = trades_df['profit'].min()
    
    # Calculate streaks
    wins = (trades_df['profit'] > 0).astype(int)
    losses = (trades_df['profit'] < 0).astype(int)
    
    def get_max_streak(series):
        streak = 0
        max_streak = 0
        for val in series:
            if val == 1:
                streak += 1
                max_streak = max(max_streak, streak)
            else:
                streak = 0
        return max_streak
        
    max_win_streak = get_max_streak(wins)
    max_loss_streak = get_max_streak(losses)
    
    # Print report
    print("\n" + "="*80)
    print(f"{pair} BACKTEST PERFORMANCE REPORT")
    print("="*80)
    
    print("\n--- TRADE STATISTICS ---")
    print(f"Total Trades: {total_trades}")
    print(f"Winning Trades: {winning_trades} ({win_rate:.2f}%)")
    print(f"Losing Trades: {losing_trades} ({100-win_rate:.2f}%)")
    print(f"Max Consecutive Wins: {max_win_streak}")
    print(f"Max Consecutive Losses: {max_loss_streak}")
    
    print("\n--- PROFIT METRICS ---")
    print(f"Initial Equity: ${initial_equity:,.2f}")
    print(f"Final Equity: ${equity_curve[-1]:,.2f}")
    print(f"Total Profit: ${total_profit:,.2f}")
    print(f"Total Return: {total_return_pct:.2f}%")
    print(f"Average Profit per Trade: {avg_profit_pct:.2f}%")
    print(f"Average Win: {avg_win_pct:.2f}%")
    print(f"Average Loss: {avg_loss_pct:.2f}%")
    print(f"Largest Win: ${largest_win:,.2f} ({trades_df['profit_pct'].max():.2f}%)")
    print(f"Largest Loss: ${largest_loss:,.2f} ({trades_df['profit_pct'].min():.2f}%)")
    print(f"Profit Factor: {profit_factor:.2f}")
    print(f"Average Risk:Reward: 1:{avg_rr:.2f}")
    print(f"Annualized Sharpe Ratio: {sharpe_ratio:.2f}")
    
    print("\n--- RISK METRICS ---")
    print(f"Maximum Drawdown: {max_drawdown:.2f}%")
    
    print("\n--- EXIT REASONS ---")
    exit_reasons = trades_df['exit_reason'].value_counts()
    for reason, count in exit_reasons.items():
        print(f"{reason}: {count} ({count/total_trades*100:.1f}%)")
    
    print("\n" + "="*80)
    
    # Prepare metrics summary for PNG
    metrics_summary = {
        'Total Trades': total_trades,
        'Win Rate': f"{win_rate:.2f}%",
        'Max Win Streak': max_win_streak,
        'Max Loss Streak': max_loss_streak,
        'Total Return': f"{total_return_pct:.2f}%",
        'Avg Profit/Trade': f"{avg_profit_pct:.2f}%",
        'Sharpe Ratio': f"{sharpe_ratio:.2f}",
        'Profit Factor': f"{profit_factor:.2f}",
        'Avg Risk:Reward': f"1:{avg_rr:.2f}",
        'Max Drawdown': f"{max_drawdown:.2f}%"
    }
    
    return trades_df, metrics_summary

trades_df, metrics_summary = analyze_performance(trades, equity_curve, config.initial_equity, selected_pair)

# ============================================================================
# CELL 11: VISUALIZATION
# ============================================================================

def plot_backtest_results(df, trades_df, equity_curve, config, metrics):
    if trades_df is None or len(trades_df) == 0:
        print("No trades to plot!")
        return

    fig = plt.figure(figsize=(16, 12))

    # Create grid
    gs = fig.add_gridspec(4, 2, hspace=0.3, wspace=0.3)

    # Plot 1: Price chart with indicators and signals
    ax1 = fig.add_subplot(gs[0:2, :])
    ax1.plot(df['date'], df['close'], label='Close Price', color='black', linewidth=1.5, alpha=0.8)

    if config.strategy_type == 'TREND_FOLLOWING':
        ax1.plot(df['date'], df['tenkan_sen'], label='Tenkan-sen', color='red', linewidth=1, alpha=0.7)
        ax1.plot(df['date'], df['kijun_sen'], label='Kijun-sen', color='blue', linewidth=1, alpha=0.7)
    else:
        ax1.plot(df['date'], df['bb_upper'], label='BB Upper', color='gray', linewidth=1, linestyle='--', alpha=0.6)
        ax1.plot(df['date'], df['bb_middle'], label='BB Middle', color='orange', linewidth=1, alpha=0.6)
        ax1.plot(df['date'], df['bb_lower'], label='BB Lower', color='gray', linewidth=1, linestyle='--', alpha=0.6)

    # Plot trades
    for _, trade in trades_df.iterrows():
        if trade['direction'] == 'buy':
            entry_color = 'green'
            entry_marker = '^'
        else:
            entry_color = 'red'
            entry_marker = 'v'
        
        # Entry point
        ax1.scatter(trade['entry_date'], trade['entry_price'], 
                   color=entry_color, marker=entry_marker, s=150, zorder=5, 
                   edgecolors='black', linewidth=1)
        
        # Exit point
        exit_color = 'green' if trade['profit'] > 0 else 'red'
        ax1.scatter(trade['exit_date'], trade['exit_price'], 
                   color=exit_color, marker='x', s=150, zorder=5, linewidth=2)

    ax1.set_title(f'{config.pair} - {config.strategy_type} Strategy', fontsize=16, fontweight='bold')
    ax1.set_xlabel('Date', fontsize=12)
    ax1.set_ylabel('Price', fontsize=12)
    ax1.legend(loc='best', fontsize=10)
    ax1.grid(True, alpha=0.3)

    # Plot 2: Equity curve
    ax2 = fig.add_subplot(gs[2, :])
    ax2.plot(equity_curve, color='blue', linewidth=2)
    ax2.axhline(y=config.initial_equity, color='gray', linestyle='--', 
                label=f'Initial: ${config.initial_equity:,.0f}', linewidth=1)
    ax2.fill_between(range(len(equity_curve)), config.initial_equity, equity_curve, 
                     where=[e >= config.initial_equity for e in equity_curve], 
                     color='green', alpha=0.3, label='Profit')
    ax2.fill_between(range(len(equity_curve)), config.initial_equity, equity_curve, 
                     where=[e < config.initial_equity for e in equity_curve], 
                     color='red', alpha=0.3, label='Loss')
    ax2.set_title('Equity Curve', fontsize=14, fontweight='bold')
    ax2.set_xlabel('Bar Number', fontsize=12)
    ax2.set_ylabel('Equity ($)', fontsize=12)
    ax2.legend(loc='best', fontsize=10)
    ax2.grid(True, alpha=0.3)

    # Plot 3: Drawdown
    ax3 = fig.add_subplot(gs[3, 0])
    equity_series = pd.Series(equity_curve)
    running_max = equity_series.cummax()
    drawdown = (equity_series - running_max) / running_max * 100
    ax3.fill_between(range(len(drawdown)), drawdown, 0, color='red', alpha=0.5)
    ax3.set_title('Drawdown', fontsize=14, fontweight='bold')
    ax3.set_xlabel('Bar Number', fontsize=12)
    ax3.set_ylabel('Drawdown (%)', fontsize=12)
    ax3.grid(True, alpha=0.3)

    # Plot 4: Trade distribution
    ax4 = fig.add_subplot(gs[3, 1])
    profits = trades_df['profit_pct'].values
    colors = ['green' if p > 0 else 'red' for p in profits]
    ax4.bar(range(len(profits)), profits, color=colors, alpha=0.7, edgecolor='black')
    ax4.axhline(y=0, color='black', linestyle='-', linewidth=1)
    ax4.set_title('Individual Trade Results (%)', fontsize=14, fontweight='bold')
    ax4.set_xlabel('Trade Number', fontsize=12)
    ax4.set_ylabel('Profit/Loss (%)', fontsize=12)
    ax4.grid(True, alpha=0.3, axis='y')

    plt.suptitle(f'{config.pair} Backtest Results - {config.strategy_type}', 
                 fontsize=18, fontweight='bold', y=0.995)

    # ADD PERFORMANCE SUMMARY BOX ON PNG
    stats_text = "PERFORMANCE REPORT\n" + "-"*30 + "\n"
    for key, val in metrics.items():
        stats_text += f"{key}: {val}\n"
    
    # Add text box to the right of the drawdown chart
    fig.text(0.72, 0.28, stats_text, fontsize=12, family='monospace',
             bbox=dict(facecolor='white', alpha=0.8, edgecolor='black', boxstyle='round,pad=1'))
    
    output_image = os.path.join(RESULT_FOLDER, f'{config.pair}_backtest_results.png')
    plt.savefig(output_image, dpi=300, bbox_inches='tight')
    print(f"\n✓ Chart saved as '{output_image}'")
    plt.show()

print("\nGenerating charts...")
plot_backtest_results(df, trades_df, equity_curve, config, metrics_summary)

# ============================================================================
# CELL 12: EXPORT RESULTS
# ============================================================================
if trades_df is not None and len(trades_df) > 0:
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

    # Files saved locally
    print("\n" + "="*80)
    print("BACKTEST COMPLETE!")
    print("="*80)
    print(f"\nFiles generated in '{RESULT_FOLDER}':")
    print(f"1. {trade_log_filename}")
    print(f"2. {equity_filename}")
    print(f"3. {selected_pair}_backtest_results.png")
    print("\n" + "="*80)
else:
    print("\n⚠ No trades executed - no files to export")

# ============================================================================
# CELL 13: TRADE LOG DISPLAY
# ============================================================================
if trades_df is not None and len(trades_df) > 0:
    print("\n" + "="*80)
    print("DETAILED TRADE LOG")
    print("="*80)

    # Format display
    pd.options.display.float_format = '{:,.4f}'.format
    pd.options.display.max_columns = None
    pd.options.display.width = None

    print(trades_df.to_string(index=False))
    print("\n" + "="*80)

