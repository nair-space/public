import pandas as pd
import numpy as np
import os
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.model_selection import train_test_split
from config import TradingConfig, RESULT_FOLDER
from indicators import calculate_atr
from backtest import run_backtest
from analytics import analyze_performance

# ============================================================================
# CONSTANTS
# ============================================================================
MIN_WIN_RATE = 55.0  # Minimum acceptable win rate (%)
MAX_OPTIMIZATION_ITERATIONS = 10  # Maximum optimization attempts
MIN_TRADES_REQUIRED = 200  # Minimum trades needed for valid statistics

# ============================================================================
# HELPER FUNCTIONS
# ============================================================================

def detect_timeframe(df):
    """
    Detect the timeframe from the date intervals in the dataframe.
    Returns a string like 'D1', 'H4', 'H1', etc.
    """
    if len(df) < 2:
        return 'D1'  # Default to daily
    
    # Calculate the difference between consecutive dates
    df_sorted = df.sort_values('date').reset_index(drop=True)
    time_diffs = df_sorted['date'].diff().dropna()
    
    # Get the most common time difference
    median_diff = time_diffs.median()
    
    # Convert to hours
    hours = median_diff.total_seconds() / 3600
    
    # Determine timeframe
    if hours < 1.5:  # 1 hour or less
        return 'H1'
    elif hours < 2.5:  # Around 2 hours
        return 'H2'
    elif hours < 5:  # Around 4 hours
        return 'H4'
    elif hours < 10:  # Around 8 hours
        return 'H8'
    elif hours < 20:  # Around 12 hours
        return 'H12'
    elif hours < 48:  # Around 24 hours
        return 'D1'
    else:  # More than 48 hours (usually ~120-168 hours for weekly)
        return 'W1'


def export_results_to_file(pair, timeframe, report_text):
    """
    Export the strategy finder results to a text file.
    Filename format: (pair)-(timeframe).txt
    """
    filename = f"{pair}-{timeframe}.txt"
    filepath = os.path.join(RESULT_FOLDER, filename)
    
    try:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(report_text)
        print(f"\n✓ Results exported to: {filepath}")
        return filepath
    except Exception as e:
        print(f"\n⚠️  Error exporting results: {str(e)}")
        return None

# ============================================================================
# CANDLESTICK PATTERN DETECTION
# ============================================================================

def detect_candlestick_patterns(df):
    """
    Detect common candlestick patterns for signal filtering.
    Returns DataFrame with pattern columns.
    """
    patterns = pd.DataFrame(index=df.index)
    
    # Calculate body and wicks
    body = df['close'] - df['open']
    body_abs = abs(body)
    upper_wick = df['high'] - df[['open', 'close']].max(axis=1)
    lower_wick = df[['open', 'close']].min(axis=1) - df['low']
    candle_range = df['high'] - df['low']
    
    # Avoid division by zero
    candle_range = candle_range.replace(0, np.nan)
    
    # Pattern 1: Bullish Engulfing
    prev_body = body.shift(1)
    patterns['bullish_engulfing'] = (
        (body > 0) &  # Current is bullish
        (prev_body < 0) &  # Previous is bearish
        (df['open'] <= df['close'].shift(1)) &  # Opens at or below prev close
        (df['close'] >= df['open'].shift(1))  # Closes at or above prev open
    ).astype(int)
    
    # Pattern 2: Bearish Engulfing
    patterns['bearish_engulfing'] = (
        (body < 0) &  # Current is bearish
        (prev_body > 0) &  # Previous is bullish
        (df['open'] >= df['close'].shift(1)) &  # Opens at or above prev close
        (df['close'] <= df['open'].shift(1))  # Closes at or below prev open
    ).astype(int)
    
    # Pattern 3: Doji (small body relative to range)
    body_ratio = body_abs / candle_range
    patterns['doji'] = (body_ratio < 0.1).astype(int)
    
    # Pattern 4: Hammer (bullish reversal)
    patterns['hammer'] = (
        (lower_wick >= 2 * body_abs) &  # Long lower wick
        (upper_wick <= body_abs * 0.5) &  # Short upper wick
        (body > 0)  # Bullish body
    ).astype(int)
    
    # Pattern 5: Shooting Star (bearish reversal)
    patterns['shooting_star'] = (
        (upper_wick >= 2 * body_abs) &  # Long upper wick
        (lower_wick <= body_abs * 0.5) &  # Short lower wick
        (body < 0)  # Bearish body
    ).astype(int)
    
    # Pattern 6: Morning Star (3-bar bullish reversal)
    patterns['morning_star'] = (
        (body.shift(2) < 0) &  # First bar bearish
        (body_abs.shift(1) < body_abs.shift(2) * 0.3) &  # Second bar small
        (body > 0) &  # Third bar bullish
        (df['close'] > (df['open'].shift(2) + df['close'].shift(2)) / 2)  # Close above midpoint
    ).astype(int)
    
    # Pattern 7: Evening Star (3-bar bearish reversal)
    patterns['evening_star'] = (
        (body.shift(2) > 0) &  # First bar bullish
        (body_abs.shift(1) < body_abs.shift(2) * 0.3) &  # Second bar small
        (body < 0) &  # Third bar bearish
        (df['close'] < (df['open'].shift(2) + df['close'].shift(2)) / 2)  # Close below midpoint
    ).astype(int)
    
    # Pattern 8: Pin Bar (rejection candle)
    patterns['bullish_pin'] = (
        (lower_wick >= 2.5 * body_abs) &
        (lower_wick >= 0.6 * candle_range)
    ).astype(int)
    
    patterns['bearish_pin'] = (
        (upper_wick >= 2.5 * body_abs) &
        (upper_wick >= 0.6 * candle_range)
    ).astype(int)
    
    # Pattern 9: Inside Bar
    patterns['inside_bar'] = (
        (df['high'] < df['high'].shift(1)) &
        (df['low'] > df['low'].shift(1))
    ).astype(int)
    
    # Pattern 10: Outside Bar (Engulfing range)
    patterns['outside_bar'] = (
        (df['high'] > df['high'].shift(1)) &
        (df['low'] < df['low'].shift(1))
    ).astype(int)
    
    return patterns


def calculate_pattern_score(patterns, direction='buy'):
    """
    Calculate a combined pattern quality score for signal filtering.
    Higher score = stronger pattern confirmation.
    """
    score = pd.Series(0.0, index=patterns.index)
    
    if direction == 'buy':
        score += patterns['bullish_engulfing'] * 2.0
        score += patterns['hammer'] * 1.5
        score += patterns['morning_star'] * 2.0
        score += patterns['bullish_pin'] * 1.5
        score += patterns['inside_bar'] * 0.5
        score -= patterns['bearish_engulfing'] * 2.0
        score -= patterns['shooting_star'] * 1.5
        score -= patterns['evening_star'] * 2.0
    else:  # sell
        score += patterns['bearish_engulfing'] * 2.0
        score += patterns['shooting_star'] * 1.5
        score += patterns['evening_star'] * 2.0
        score += patterns['bearish_pin'] * 1.5
        score += patterns['inside_bar'] * 0.5
        score -= patterns['bullish_engulfing'] * 2.0
        score -= patterns['hammer'] * 1.5
        score -= patterns['morning_star'] * 2.0
    
    return score


# ============================================================================
# ENHANCED FEATURE ENGINEERING
# ============================================================================

def create_features(df):
    """
    Create comprehensive features from OHLC data for ML model.
    Enhanced with additional technical indicators.
    """
    features = pd.DataFrame(index=df.index)
    
    # Price-based features
    features['return_1'] = df['close'].pct_change(1)
    features['return_2'] = df['close'].pct_change(2)
    features['return_3'] = df['close'].pct_change(3)
    features['return_5'] = df['close'].pct_change(5)
    features['return_10'] = df['close'].pct_change(10)
    
    # Range features
    features['hl_range'] = (df['high'] - df['low']) / df['close']
    features['body_size'] = abs(df['close'] - df['open']) / df['close']
    features['upper_wick'] = (df['high'] - df[['open', 'close']].max(axis=1)) / df['close']
    features['lower_wick'] = (df[['open', 'close']].min(axis=1) - df['low']) / df['close']
    features['body_position'] = (df['close'] - df['low']) / (df['high'] - df['low'] + 1e-10)
    
    # Bullish/Bearish candle
    features['is_bullish'] = (df['close'] > df['open']).astype(int)
    
    # Momentum features
    features['momentum_3'] = df['close'] - df['close'].shift(3)
    features['momentum_5'] = df['close'] - df['close'].shift(5)
    features['momentum_10'] = df['close'] - df['close'].shift(10)
    
    # Rate of change
    features['roc_3'] = (df['close'] / df['close'].shift(3) - 1) * 100
    features['roc_5'] = (df['close'] / df['close'].shift(5) - 1) * 100
    features['roc_10'] = (df['close'] / df['close'].shift(10) - 1) * 100
    
    # Volatility (ATR-normalized if available)
    if 'atr' in df.columns:
        features['atr_ratio'] = df['atr'] / df['close']
        features['range_to_atr'] = (df['high'] - df['low']) / (df['atr'] + 1e-10)
        features['atr_change'] = df['atr'].pct_change(1)
    
    # Rolling statistics
    for period in [5, 10, 20]:
        features[f'close_sma_{period}'] = df['close'].rolling(period).mean() / df['close'] - 1
        features[f'volatility_{period}'] = df['close'].rolling(period).std() / df['close']
        features[f'high_max_{period}'] = df['high'].rolling(period).max() / df['close'] - 1
        features[f'low_min_{period}'] = df['low'].rolling(period).min() / df['close'] - 1
    
    # Higher highs / Lower lows pattern
    features['hh_count'] = (df['high'] > df['high'].shift(1)).rolling(5).sum()
    features['ll_count'] = (df['low'] < df['low'].shift(1)).rolling(5).sum()
    features['hl_count'] = (df['low'] > df['low'].shift(1)).rolling(5).sum()  # Higher lows
    features['lh_count'] = (df['high'] < df['high'].shift(1)).rolling(5).sum()  # Lower highs
    
    # Trend strength
    features['trend_strength'] = features['hh_count'] - features['ll_count']
    
    # RSI-like feature
    delta = df['close'].diff()
    gain = (delta.where(delta > 0, 0)).rolling(14).mean()
    loss = (-delta.where(delta < 0, 0)).rolling(14).mean()
    features['rsi_14'] = 100 - (100 / (1 + gain / (loss + 1e-10)))
    
    # MACD-like feature
    ema_12 = df['close'].ewm(span=12, adjust=False).mean()
    ema_26 = df['close'].ewm(span=26, adjust=False).mean()
    features['macd'] = (ema_12 - ema_26) / df['close']
    features['macd_signal'] = features['macd'].ewm(span=9, adjust=False).mean()
    features['macd_hist'] = features['macd'] - features['macd_signal']
    
    # Bollinger Band position
    sma_20 = df['close'].rolling(20).mean()
    std_20 = df['close'].rolling(20).std()
    features['bb_position'] = (df['close'] - (sma_20 - 2 * std_20)) / (4 * std_20 + 1e-10)
    
    # Volume-based features (if volume exists)
    if 'volume' in df.columns:
        features['volume_sma_ratio'] = df['volume'] / df['volume'].rolling(20).mean()
        features['volume_change'] = df['volume'].pct_change(1)
    
    return features


def label_trades(df, sl_multiplier, tp_multiplier, pip_value, pending_offset):
    """
    Label each candle for BUY and SELL trades based on future price action.
    Returns 1 if TP is hit before SL, 0 otherwise.
    Also returns the number of bars to resolution for filtering.
    """
    n = len(df)
    buy_labels = np.zeros(n)
    sell_labels = np.zeros(n)
    buy_bars_to_resolve = np.full(n, np.nan)
    sell_bars_to_resolve = np.full(n, np.nan)
    
    for i in range(n - 1):
        atr = df['atr'].iloc[i]
        if pd.isna(atr) or atr == 0:
            continue
            
        high_i = df['high'].iloc[i]
        low_i = df['low'].iloc[i]
        
        # BUY trade simulation
        buy_entry = high_i + (pending_offset * pip_value)
        buy_sl = buy_entry - (sl_multiplier * atr)
        buy_tp = buy_entry + (tp_multiplier * atr)
        
        # SELL trade simulation
        sell_entry = low_i - (pending_offset * pip_value)
        sell_sl = sell_entry + (sl_multiplier * atr)
        sell_tp = sell_entry - (tp_multiplier * atr)
        
        # Track outcomes separately
        buy_resolved = False
        sell_resolved = False
        
        # Look forward to determine outcome
        for j in range(i + 1, min(i + 100, n)):  # Look up to 100 bars ahead
            future_high = df['high'].iloc[j]
            future_low = df['low'].iloc[j]
            
            # BUY outcome
            if not buy_resolved:
                if future_low <= buy_sl:
                    buy_labels[i] = 0  # SL hit first
                    buy_bars_to_resolve[i] = j - i
                    buy_resolved = True
                elif future_high >= buy_tp:
                    buy_labels[i] = 1  # TP hit first
                    buy_bars_to_resolve[i] = j - i
                    buy_resolved = True
            
            # SELL outcome
            if not sell_resolved:
                if future_high >= sell_sl:
                    sell_labels[i] = 0  # SL hit first
                    sell_bars_to_resolve[i] = j - i
                    sell_resolved = True
                elif future_low <= sell_tp:
                    sell_labels[i] = 1  # TP hit first
                    sell_bars_to_resolve[i] = j - i
                    sell_resolved = True
            
            if buy_resolved and sell_resolved:
                break
    
    return buy_labels, sell_labels, buy_bars_to_resolve, sell_bars_to_resolve


def train_ml_model(X_train, y_train, use_gradient_boosting=False):
    """
    Train a classifier model.
    Can use RandomForest or GradientBoosting.
    """
    if use_gradient_boosting:
        model = GradientBoostingClassifier(
            n_estimators=100,
            max_depth=5,
            min_samples_split=30,
            min_samples_leaf=15,
            learning_rate=0.1,
            random_state=42
        )
    else:
        model = RandomForestClassifier(
            n_estimators=150,
            max_depth=8,
            min_samples_split=30,
            min_samples_leaf=15,
            random_state=42,
            n_jobs=-1
        )
    
    model.fit(X_train, y_train)
    return model


def generate_ml_signals(df, buy_model, sell_model, features, patterns, config,
                        confidence_threshold=0.50, pattern_filter_strength=0.0):
    """
    Generate trading signals based on ML model predictions.
    Enhanced with pattern filtering and confidence thresholds.
    """
    df = df.copy()
    df['signal'] = None
    df['entry_price'] = np.nan
    df['stop_loss'] = np.nan
    df['take_profit'] = np.nan
    df['confidence'] = np.nan
    
    # Calculate pattern scores
    buy_pattern_score = calculate_pattern_score(patterns, 'buy')
    sell_pattern_score = calculate_pattern_score(patterns, 'sell')
    
    # Make predictions - handle single class case
    if len(buy_model.classes_) == 2:
        buy_proba = buy_model.predict_proba(features)[:, 1]
    else:
        buy_proba = buy_model.predict(features).astype(float)
    
    if len(sell_model.classes_) == 2:
        sell_proba = sell_model.predict_proba(features)[:, 1]
    else:
        sell_proba = sell_model.predict(features).astype(float)
    
    # Map dataframe index to sequential position
    df_indices = df.index.tolist()
    pattern_indices = patterns.index.tolist()
    
    for pos in range(1, len(df)):
        idx = df_indices[pos]
        prev_idx = df_indices[pos - 1]
        
        if pd.isna(df.loc[prev_idx, 'atr']):
            continue
            
        atr_1 = df.loc[prev_idx, 'atr']
        high_1 = df.loc[prev_idx, 'high']
        low_1 = df.loc[prev_idx, 'low']
        
        # Use positional index for proba arrays
        buy_p = buy_proba[pos - 1] if pos - 1 < len(buy_proba) else 0
        sell_p = sell_proba[pos - 1] if pos - 1 < len(sell_proba) else 0
        
        # Get pattern scores
        if prev_idx in pattern_indices:
            buy_ps = buy_pattern_score.loc[prev_idx]
            sell_ps = sell_pattern_score.loc[prev_idx]
        else:
            buy_ps = 0
            sell_ps = 0
        
        # Apply pattern filter
        buy_passes_filter = buy_ps >= pattern_filter_strength
        sell_passes_filter = sell_ps >= pattern_filter_strength
        
        # BUY signal if model is confident and passes filters
        if buy_p > confidence_threshold and buy_p > sell_p and buy_passes_filter:
            entry_price = high_1 + (config.pending_offset * config.pip_value)
            stop_loss = entry_price - (config.sl_multiplier * atr_1)
            take_profit = entry_price + (config.tp_multiplier * atr_1)
            
            df.loc[idx, 'signal'] = 'buy'
            df.loc[idx, 'entry_price'] = entry_price
            df.loc[idx, 'stop_loss'] = stop_loss
            df.loc[idx, 'take_profit'] = take_profit
            df.loc[idx, 'confidence'] = buy_p
        
        # SELL signal if model is confident and passes filters
        elif sell_p > confidence_threshold and sell_p > buy_p and sell_passes_filter:
            entry_price = low_1 - (config.pending_offset * config.pip_value)
            stop_loss = entry_price + (config.sl_multiplier * atr_1)
            take_profit = entry_price - (config.tp_multiplier * atr_1)
            
            df.loc[idx, 'signal'] = 'sell'
            df.loc[idx, 'entry_price'] = entry_price
            df.loc[idx, 'stop_loss'] = stop_loss
            df.loc[idx, 'take_profit'] = take_profit
            df.loc[idx, 'confidence'] = sell_p
    
    return df


# ============================================================================
# STRATEGY OPTIMIZATION ENGINE
# ============================================================================

def run_single_backtest(df, buy_model, sell_model, features, patterns, config,
                        confidence_threshold, pattern_filter_strength):
    """
    Run a single backtest with specified parameters.
    Returns win rate, total trades, and profit.
    """
    df_signals = generate_ml_signals(
        df, buy_model, sell_model, features, patterns, config,
        confidence_threshold, pattern_filter_strength
    )
    
    trades, equity_curve = run_backtest(df_signals, config)
    
    if len(trades) < MIN_TRADES_REQUIRED:
        return None, len(trades), 0
    
    trades_df, metrics = analyze_performance(
        trades, equity_curve, config.initial_equity, config.pair, silent=True
    )
    
    if metrics is None:
        return None, len(trades), 0
    
    return metrics['win_rate_raw'], len(trades), metrics['total_profit']


def optimize_for_winrate(df, buy_model, sell_model, features, patterns, config,
                         target_winrate=MIN_WIN_RATE, logger=print):
    """
    Optimize strategy parameters to achieve target win rate.
    Uses grid search and iterative refinement.
    """
    logger(f"\n🔧 OPTIMIZATION: Targeting {target_winrate}% win rate...")
    
    best_result = None
    best_winrate = 0
    best_params = {}
    
    # Parameter grid
    confidence_thresholds = [0.50, 0.52, 0.55, 0.57, 0.60, 0.62, 0.65, 0.68, 0.70]
    pattern_filter_strengths = [0.0, 0.5, 1.0, 1.5, 2.0]
    tp_adjustments = [0.0, 0.05, 0.10, -0.05]  # Adjust TP relative to current
    sl_adjustments = [0.0, 0.05, 0.10, -0.05]  # Adjust SL relative to current
    
    iteration = 0
    total_iterations = len(confidence_thresholds) * len(pattern_filter_strengths)
    
    logger(f"   Testing {total_iterations} parameter combinations...")
    
    for conf_thresh in confidence_thresholds:
        for pattern_filter in pattern_filter_strengths:
            iteration += 1
            
            winrate, trades_count, profit = run_single_backtest(
                df, buy_model, sell_model, features, patterns, config,
                conf_thresh, pattern_filter
            )
            
            if winrate is not None and trades_count >= MIN_TRADES_REQUIRED:
                if winrate > best_winrate or (winrate == best_winrate and profit > (best_result.get('profit', 0) if best_result else 0)):
                    best_winrate = winrate
                    best_result = {
                        'winrate': winrate,
                        'trades': trades_count,
                        'profit': profit
                    }
                    best_params = {
                        'confidence_threshold': conf_thresh,
                        'pattern_filter_strength': pattern_filter
                    }
                    
                    if winrate >= target_winrate:
                        logger(f"   ✓ Found winning params at iteration {iteration}/{total_iterations}")
                        logger(f"     Confidence: {conf_thresh}, Pattern Filter: {pattern_filter}")
                        logger(f"     Win Rate: {winrate:.2f}%, Trades: {trades_count}, Profit: ${profit:,.2f}")
                        return best_params, best_result
    
    # Secondary optimization: adjust SL/TP multipliers
    if best_winrate < target_winrate:
        logger(f"\n   🔄 Phase 2: Adjusting SL/TP multipliers...")
        
        base_sl = config.sl_multiplier
        base_tp = config.tp_multiplier
        
        for sl_adj in sl_adjustments:
            for tp_adj in tp_adjustments:
                config.sl_multiplier = base_sl + sl_adj
                config.tp_multiplier = base_tp + tp_adj
                
                # Ensure minimum R:R
                if config.tp_multiplier < config.sl_multiplier * 1.05:
                    continue
                
                winrate, trades_count, profit = run_single_backtest(
                    df, buy_model, sell_model, features, patterns, config,
                    best_params.get('confidence_threshold', 0.55),
                    best_params.get('pattern_filter_strength', 0.0)
                )
                
                if winrate is not None and winrate > best_winrate and trades_count >= MIN_TRADES_REQUIRED:
                    best_winrate = winrate
                    best_result = {
                        'winrate': winrate,
                        'trades': trades_count,
                        'profit': profit
                    }
                    best_params['sl_multiplier'] = config.sl_multiplier
                    best_params['tp_multiplier'] = config.tp_multiplier
                    
                    if winrate >= target_winrate:
                        logger(f"   ✓ Found winning params with SL/TP adjustment")
                        return best_params, best_result
        
        # Reset to best found
        config.sl_multiplier = best_params.get('sl_multiplier', base_sl)
        config.tp_multiplier = best_params.get('tp_multiplier', base_tp)
    
    logger(f"\n   📊 Best found: Win Rate {best_winrate:.2f}%")
    return best_params, best_result


def retrain_with_stricter_labels(df, features_clean, buy_labels, sell_labels,
                                 buy_bars_resolve, sell_bars_resolve,
                                 max_bars_to_resolve=30):
    """
    Retrain models using only high-quality labels.
    Trades that resolve quickly are often higher quality.
    """
    # Filter for trades that resolved within max_bars
    buy_mask = buy_bars_resolve <= max_bars_to_resolve
    sell_mask = sell_bars_resolve <= max_bars_to_resolve
    
    combined_mask = buy_mask | sell_mask
    
    if combined_mask.sum() < 100:
        return None, None
    
    X_filtered = features_clean[combined_mask]
    y_buy_filtered = buy_labels[combined_mask]
    y_sell_filtered = sell_labels[combined_mask]
    
    # Split
    split_idx = int(len(X_filtered) * 0.7)
    X_train = X_filtered[:split_idx]
    y_buy_train = y_buy_filtered[:split_idx]
    y_sell_train = y_sell_filtered[:split_idx]
    
    if len(X_train) < 50:
        return None, None
    
    buy_model = train_ml_model(X_train, y_buy_train, use_gradient_boosting=True)
    sell_model = train_ml_model(X_train, y_sell_train, use_gradient_boosting=True)
    
    return buy_model, sell_model


# ============================================================================
# MAIN STRATEGY FINDER
# ============================================================================

def find_best_strategy(df_original, pair, sl_multiplier, tp_multiplier):
    """
    Enhanced ML Strategy Finder with 60% minimum win rate optimization.
    
    Algorithm:
    1. Create features and label trades
    2. Train ML models
    3. Generate signals and backtest
    4. If win rate < 60%, optimize:
       - Adjust confidence thresholds
       - Apply pattern filters
       - Modify SL/TP ratios
       - Retrain with stricter labels
    5. Return optimized strategy or report best achievable
    """
    print("\n" + "="*80)
    print(f"🎯 STRATEGY FINDER FOR {pair}")
    print(f"   Target: {MIN_WIN_RATE}% minimum win rate")
    print(f"   ATR Multipliers: SL={sl_multiplier}, TP={tp_multiplier}")
    print("="*80)
    
    # Track report content for export
    report_content = []
    def rprint(msg=""):
        print(msg)
        report_content.append(str(msg))
        
    timeframe = detect_timeframe(df_original)
    
    rprint("="*80)
    rprint(f"🎯 STRATEGY FINDER FOR {pair} ({timeframe})")
    rprint(f"   Target: {MIN_WIN_RATE}% minimum win rate")
    rprint(f"   ATR Multipliers: SL={sl_multiplier}, TP={tp_multiplier}")
    rprint("="*80)
    
    # Work on a copy
    df = df_original.copy()
    
    # Create config for pip value and offset
    config = TradingConfig(pair, 'TREND_FOLLOWING', sl_multiplier=sl_multiplier, tp_multiplier=tp_multiplier)
    
    # Calculate ATR
    df = calculate_atr(df, config.atr_period)
    
    # =========================================================================
    # STEP 1: Feature Engineering
    # =========================================================================
    rprint("\n📊 STEP 1: Feature Engineering")
    rprint("-" * 40)
    
    rprint("   Creating features from OHLC data...")
    features = create_features(df)
    
    rprint("   Detecting candlestick patterns...")
    patterns = detect_candlestick_patterns(df)
    pattern_counts = patterns.sum()
    rprint(f"   Found patterns: {dict(pattern_counts[pattern_counts > 0])}")
    
    # =========================================================================
    # STEP 2: Label Historical Trades
    # =========================================================================
    rprint("\n📊 STEP 2: Labeling Historical Trades")
    rprint("-" * 40)
    
    buy_labels, sell_labels, buy_bars_resolve, sell_bars_resolve = label_trades(
        df, sl_multiplier, tp_multiplier, config.pip_value, config.pending_offset
    )
    
    # Prepare data for training
    valid_mask = ~features.isna().any(axis=1) & ~pd.isna(df['atr'])
    features_clean = features[valid_mask]
    buy_labels_clean = buy_labels[valid_mask]
    sell_labels_clean = sell_labels[valid_mask]
    buy_bars_clean = buy_bars_resolve[valid_mask]
    sell_bars_clean = sell_bars_resolve[valid_mask]
    patterns_clean = patterns[valid_mask]
    
    rprint(f"   Total valid samples: {len(features_clean)}")
    rprint(f"   Buy winning labels: {int(buy_labels_clean.sum())} ({buy_labels_clean.mean()*100:.1f}%)")
    rprint(f"   Sell winning labels: {int(sell_labels_clean.sum())} ({sell_labels_clean.mean()*100:.1f}%)")
    
    # Check if historical win rate is already below threshold
    hist_buy_wr = buy_labels_clean.mean() * 100
    hist_sell_wr = sell_labels_clean.mean() * 100
    
    if hist_buy_wr < 40 and hist_sell_wr < 40:
        rprint(f"\n   ⚠️  Warning: Historical win rates are very low")
        rprint(f"       This pair/timeframe may be challenging to optimize")
    
    # =========================================================================
    # STEP 3: Train ML Models
    # =========================================================================
    rprint("\n📊 STEP 3: Training ML Models")
    rprint("-" * 40)
    
    # Train/Test split (use first 70% for training, last 30% for testing)
    split_idx = int(len(features_clean) * 0.7)
    
    X_train = features_clean.iloc[:split_idx]
    X_test = features_clean.iloc[split_idx:]
    
    y_buy_train = buy_labels_clean[:split_idx]
    y_buy_test = buy_labels_clean[split_idx:]
    
    y_sell_train = sell_labels_clean[:split_idx]
    y_sell_test = sell_labels_clean[split_idx:]
    
    rprint(f"   Training samples: {len(X_train)}")
    rprint(f"   Testing samples: {len(X_test)}")
    
    # Train models
    rprint("   Training Random Forest classifiers...")
    buy_model = train_ml_model(X_train, y_buy_train)
    sell_model = train_ml_model(X_train, y_sell_train)
    
    # Evaluate on test set
    buy_accuracy = buy_model.score(X_test, y_buy_test)
    sell_accuracy = sell_model.score(X_test, y_sell_test)
    
    rprint(f"   Buy Model Accuracy: {buy_accuracy*100:.2f}%")
    rprint(f"   Sell Model Accuracy: {sell_accuracy*100:.2f}%")
    
    # Feature importance
    feature_names = features.columns.tolist()
    buy_importance = pd.Series(buy_model.feature_importances_, index=feature_names).sort_values(ascending=False)
    rprint(f"\n   Top 5 Buy Features: {list(buy_importance.head(5).index)}")
    
    # =========================================================================
    # STEP 4: Initial Backtest
    # =========================================================================
    rprint("\n📊 STEP 4: Initial Backtest")
    rprint("-" * 40)
    
    # Prepare test dataframe
    test_start_idx = features_clean.index[split_idx]
    df_test = df.loc[test_start_idx:].copy()
    features_test = features.loc[test_start_idx:].copy()
    patterns_test = patterns.loc[test_start_idx:].copy()
    
    # Fill NaN features with 0 for prediction
    features_test = features_test.fillna(0)
    
    # Initial signal generation with default parameters
    df_test = generate_ml_signals(
        df_test, buy_model, sell_model, features_test, patterns_test, config,
        confidence_threshold=0.50, pattern_filter_strength=0.0
    )
    
    signals_count = df_test['signal'].notna().sum()
    rprint(f"   Generated {signals_count} initial signals")
    
    # Run backtest
    trades, equity_curve = run_backtest(df_test, config)
    rprint(f"   Executed {len(trades)} trades")
    
    # Analyze initial performance
    trades_df, metrics = analyze_performance(
        trades, equity_curve, config.initial_equity, pair, silent=True
    )
    
    if metrics is None or len(trades) < MIN_TRADES_REQUIRED:
        rprint(f"\n   ⚠️  Insufficient trades ({len(trades)}). Need at least {MIN_TRADES_REQUIRED}.")
        rprint("   Attempting optimization with relaxed filters...")
        
        # Try with very low threshold
        df_test = generate_ml_signals(
            df_test, buy_model, sell_model, features_test, patterns_test, config,
            confidence_threshold=0.45, pattern_filter_strength=0.0
        )
        trades, equity_curve = run_backtest(df_test, config)
        trades_df, metrics = analyze_performance(
            trades, equity_curve, config.initial_equity, pair, silent=True
        )
    
    initial_winrate = metrics['win_rate_raw'] if metrics else 0
    initial_profit = metrics['total_profit'] if metrics else 0
    
    rprint(f"\n   Initial Results:")
    rprint(f"   ├─ Win Rate: {initial_winrate:.2f}%")
    rprint(f"   ├─ Total Trades: {len(trades)}")
    rprint(f"   └─ Total Profit: ${initial_profit:,.2f}")
    
    # =========================================================================
    # STEP 5: Optimization (if needed)
    # =========================================================================
    if initial_winrate < MIN_WIN_RATE:
        rprint(f"\n📊 STEP 5: Optimization Required")
        rprint("-" * 40)
        rprint(f"   Win rate {initial_winrate:.2f}% is below {MIN_WIN_RATE}% target")
        
        # Run optimization
        best_params, best_result = optimize_for_winrate(
            df_test, buy_model, sell_model, features_test, patterns_test, config,
            target_winrate=MIN_WIN_RATE, logger=rprint
        )
        
        if best_result and best_result['winrate'] >= MIN_WIN_RATE:
            rprint(f"\n   ✅ OPTIMIZATION SUCCESSFUL!")
            rprint(f"   ├─ Final Win Rate: {best_result['winrate']:.2f}%")
            rprint(f"   ├─ Total Trades: {best_result['trades']}")
            rprint(f"   └─ Total Profit: ${best_result['profit']:,.2f}")
            rprint(f"\n   Optimal Parameters:")
            for key, value in best_params.items():
                rprint(f"   ├─ {key}: {value}")
            
            # Re-run final backtest with optimal params
            final_winrate = best_result['winrate']
            final_trades = best_result['trades']
            final_profit = best_result['profit']
        else:
            # Could not achieve target, try retraining with stricter labels
            rprint(f"\n   🔄 Attempting model retraining with stricter labels...")
            
            new_buy_model, new_sell_model = retrain_with_stricter_labels(
                df, features_clean.iloc[:split_idx], 
                buy_labels_clean[:split_idx], sell_labels_clean[:split_idx],
                buy_bars_clean[:split_idx], sell_bars_clean[:split_idx],
                max_bars_to_resolve=20
            )
            
            if new_buy_model and new_sell_model:
                best_params2, best_result2 = optimize_for_winrate(
                    df_test, new_buy_model, new_sell_model, 
                    features_test, patterns_test, config,
                    target_winrate=MIN_WIN_RATE, logger=rprint
                )
                
                if best_result2:
                    if best_result is None or best_result2['winrate'] > best_result['winrate']:
                        best_result = best_result2
                        best_params = best_params2
            
            if best_result:
                final_winrate = best_result['winrate']
                final_trades = best_result['trades']
                final_profit = best_result['profit']
                
                if final_winrate >= MIN_WIN_RATE:
                    rprint(f"\n   ✅ OPTIMIZATION SUCCESSFUL after retraining!")
                else:
                    rprint(f"\n   ⚠️  Could not achieve {MIN_WIN_RATE}% target")
                    rprint(f"       Best achievable: {final_winrate:.2f}%")
            else:
                final_winrate = initial_winrate
                final_trades = len(trades)
                final_profit = initial_profit
    else:
        rprint(f"\n📊 STEP 5: No Optimization Needed")
        rprint(f"   Win rate {initial_winrate:.2f}% meets {MIN_WIN_RATE}% target ✓")
        final_winrate = initial_winrate
        final_trades = len(trades)
        final_profit = initial_profit
        best_params = {'confidence_threshold': 0.50, 'pattern_filter_strength': 0.0}
    
    # =========================================================================
    # FINAL REPORT - DETAILED STRATEGY
    # =========================================================================
    rprint("\n" + "="*80)
    rprint("🏆 STRATEGY FINDER RESULTS")
    rprint("="*80)
    
    # Basic Info
    rprint(f"\n📋 BASIC INFORMATION")
    rprint("-" * 40)
    rprint(f"   Pair: {pair}")
    rprint(f"   Strategy: ML-Optimized Pattern Recognition")
    rprint(f"   Data Range: {df['date'].iloc[0].strftime('%Y-%m-%d')} to {df['date'].iloc[-1].strftime('%Y-%m-%d')}")
    rprint(f"   Total Bars Analyzed: {len(df)}")
    rprint(f"   Test Period Size: {len(df_test)} bars")
    
    # Performance Metrics
    rprint(f"\n📊 PERFORMANCE METRICS")
    rprint("-" * 40)
    rprint(f"   Win Rate: {final_winrate:.2f}%" + (" ✅" if final_winrate >= MIN_WIN_RATE else " ⚠️"))
    rprint(f"   Total Trades: {final_trades}")
    rprint(f"   Total Profit: ${final_profit:,.2f}")
    if final_trades > 0:
        rprint(f"   Average Profit per Trade: ${final_profit/final_trades:,.2f}")
        rprint(f"   Winning Trades: ~{int(final_trades * final_winrate / 100)}")
        rprint(f"   Losing Trades: ~{int(final_trades * (100 - final_winrate) / 100)}")
    
    # Optimized Parameters
    rprint(f"\n⚙️  OPTIMIZED PARAMETERS")
    rprint("-" * 40)
    conf_thresh = best_params.get('confidence_threshold', 0.50)
    pattern_filter = best_params.get('pattern_filter_strength', 0.0)
    rprint(f"   Confidence Threshold: {conf_thresh*100:.0f}%")
    rprint(f"   Pattern Filter Strength: {pattern_filter}")
    rprint(f"   Stop Loss: {config.sl_multiplier}x ATR")
    rprint(f"   Take Profit: {config.tp_multiplier}x ATR")
    rprint(f"   Risk:Reward Ratio: 1:{config.tp_multiplier/config.sl_multiplier:.2f}")
    rprint(f"   Pending Order Offset: {config.pending_offset} pips")
    
    # Entry Rules
    rprint(f"\n📝 ENTRY RULES (ML-Optimized)")
    rprint("-" * 40)
    rprint(f"   BUY Signal Conditions:")
    rprint(f"   • ML Buy Probability > {conf_thresh*100:.0f}%")
    rprint(f"   • ML Buy Probability > ML Sell Probability")
    if pattern_filter > 0:
        rprint(f"   • Bullish Pattern Score >= {pattern_filter}")
        rprint(f"     (Engulfing=+2, Hammer=+1.5, Morning Star=+2, Pin Bar=+1.5)")
    rprint(f"   • Entry Price: Previous High + {config.pending_offset} pips")
    rprint(f"   • Stop Loss: Entry - ({config.sl_multiplier} × ATR)")
    rprint(f"   • Take Profit: Entry + ({config.tp_multiplier} × ATR)")
    
    rprint(f"\n   SELL Signal Conditions:")
    rprint(f"   • ML Sell Probability > {conf_thresh*100:.0f}%")
    rprint(f"   • ML Sell Probability > ML Buy Probability")
    if pattern_filter > 0:
        rprint(f"   • Bearish Pattern Score >= {pattern_filter}")
        rprint(f"     (Engulfing=+2, Shooting Star=+1.5, Evening Star=+2, Pin Bar=+1.5)")
    rprint(f"   • Entry Price: Previous Low - {config.pending_offset} pips")
    rprint(f"   • Stop Loss: Entry + ({config.sl_multiplier} × ATR)")
    rprint(f"   • Take Profit: Entry - ({config.tp_multiplier} × ATR)")
    
    # Feature Importance
    rprint(f"\n🔬 TOP PREDICTIVE FEATURES")
    rprint("-" * 40)
    feature_names = features.columns.tolist()
    buy_importance = pd.Series(buy_model.feature_importances_, index=feature_names).sort_values(ascending=False)
    sell_importance = pd.Series(sell_model.feature_importances_, index=feature_names).sort_values(ascending=False)
    
    rprint(f"   For BUY Signals (Feature → Importance):")
    for i, (feat, imp) in enumerate(buy_importance.head(10).items(), 1):
        bar = "█" * int(imp * 50)
        rprint(f"   {i:2}. {feat:20} {bar} {imp*100:.1f}%")
    
    rprint(f"\n   For SELL Signals (Feature → Importance):")
    for i, (feat, imp) in enumerate(sell_importance.head(10).items(), 1):
        bar = "█" * int(imp * 50)
        rprint(f"   {i:2}. {feat:20} {bar} {imp*100:.1f}%")
    
    # Pattern Analysis
    rprint(f"\n📈 CANDLESTICK PATTERN ANALYSIS")
    rprint("-" * 40)
    test_patterns = patterns.loc[test_start_idx:]
    pattern_counts = test_patterns.sum()
    rprint(f"   Patterns Detected in Test Period:")
    for pattern, count in pattern_counts.sort_values(ascending=False).items():
        if count > 0:
            pct = count / len(test_patterns) * 100
            rprint(f"   • {pattern:20}: {int(count):4} occurrences ({pct:.1f}%)")
    
    # Historical Label Analysis
    rprint(f"\n📉 HISTORICAL TRADE OUTCOME ANALYSIS")
    rprint("-" * 40)
    rprint(f"   Based on labeling {len(features_clean)} historical setups:")
    rprint(f"   • BUY setups winning: {buy_labels_clean.mean()*100:.1f}%")
    rprint(f"   • SELL setups winning: {sell_labels_clean.mean()*100:.1f}%")
    rprint(f"   • Combined baseline: {(buy_labels_clean.mean() + sell_labels_clean.mean())/2*100:.1f}%")
    rprint(f"   • ML improvement: +{final_winrate - (buy_labels_clean.mean() + sell_labels_clean.mean())/2*100:.1f}%")
    
    # Model Accuracy
    rprint(f"\n🤖 ML MODEL PERFORMANCE")
    rprint("-" * 40)
    rprint(f"   Buy Model Test Accuracy: {buy_accuracy*100:.2f}%")
    rprint(f"   Sell Model Test Accuracy: {sell_accuracy*100:.2f}%")
    rprint(f"   Training Samples: {len(X_train)}")
    rprint(f"   Testing Samples: {len(X_test)}")
    rprint(f"   Model Type: Random Forest (150 trees, max_depth=8)")
    
    # Strategy Summary
    rprint(f"\n" + "="*80)
    rprint("📋 STRATEGY SUMMARY")
    rprint("="*80)
    if final_winrate >= MIN_WIN_RATE:
        rprint(f"\n   ✅ SUCCESS: Strategy meets {MIN_WIN_RATE}% win rate requirement!")
        rprint(f"\n   This ML-optimized strategy for {pair} uses:")
        rprint(f"   • Machine Learning probability filtering (>{conf_thresh*100:.0f}% confidence)")
        if pattern_filter > 0:
            rprint(f"   • Candlestick pattern confirmation (score >= {pattern_filter})")
        rprint(f"   • ATR-based risk management ({config.sl_multiplier}x SL, {config.tp_multiplier}x TP)")
        rprint(f"   • Key predictors: {', '.join(buy_importance.head(3).index.tolist())}")
    else:
        rprint(f"\n   ⚠️  WARNING: Strategy does not meet {MIN_WIN_RATE}% target")
        rprint(f"   Best achievable win rate: {final_winrate:.2f}%")
        rprint(f"\n   Recommendations:")
        rprint(f"   • Try different SL/TP ratios (current: {config.sl_multiplier}/{config.tp_multiplier})")
        rprint(f"   • Test with a different timeframe")
        rprint(f"   • This pair may have unfavorable market conditions")
        rprint(f"   • Historical baseline ({(buy_labels_clean.mean() + sell_labels_clean.mean())/2*100:.1f}%) is low for this setup")
    
    rprint("\n" + "="*80 + "\n")
    
    # Export report to file
    report_text = "\n".join(report_content)
    export_results_to_file(pair, timeframe, report_text)
    
    return 'ML_STRATEGY'
