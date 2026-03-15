import pandas as pd
import numpy as np

def identify_trend(df):
    """Identify trend for trend following strategy"""
    df['trend'] = 'neutral'
    
    for i in range(4, len(df)):
        current_tenkan = df['tenkan_sen'].iloc[i]
        current_kijun = df['kijun_sen'].iloc[i]
        stoch_k = df['stoch_k'].iloc[i]
        stoch_d = df['stoch_d'].iloc[i]
        
        # Check for cross in the previous 4 candles (indices i-3 to i) 
        # Actually i-4 to i covers the last 4 intervals where a cross could occur relative to the current bar
        
        upcross_found = False
        downcross_found = False
        
        for j in range(i-3, i+1): # Current bar and 3 previous bars
            if j <= 0: continue
            prev_tenkan = df['tenkan_sen'].iloc[j-1]
            prev_kijun = df['kijun_sen'].iloc[j-1]
            this_tenkan = df['tenkan_sen'].iloc[j]
            this_kijun = df['kijun_sen'].iloc[j]
            
            if prev_tenkan < prev_kijun and this_tenkan >= this_kijun:
                upcross_found = True
            if prev_tenkan > prev_kijun and this_tenkan <= this_kijun:
                downcross_found = True

        # Uptrend: current tenkan > kijun AND upcross found in last 4 AND stoch_k > stoch_d
        if current_tenkan > current_kijun and upcross_found and stoch_k > stoch_d:
            df.loc[df.index[i], 'trend'] = 'uptrend'
        
        # Downtrend: current tenkan < kijun AND downcross found in last 4 AND stoch_k < stoch_d
        elif current_tenkan < current_kijun and downcross_found and stoch_k < stoch_d:
            df.loc[df.index[i], 'trend'] = 'downtrend'
        
        # Trend is ONLY active when conditions are met (no persistence beyond 4-candle window plus conditions)
        # Note: 'neutral' is default at start of function
    
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
        trend_1 = df['trend'].iloc[i-1]
        
        if pd.isna(atr_1) or pd.isna(tenkan_1) or pd.isna(kijun_1):
            continue
        
        if not check_trend_entry_filter(close_1, tenkan_1, kijun_1):
            continue
        
        # Buy/Sell logic only allowed in corresponding trend
        
        # BUY signal
        if trend_1 == 'uptrend' and close_1 > tenkan_1:
            entry_price = high_1 + (config.pending_offset * config.pip_value)
            stop_loss = entry_price - (config.sl_multiplier * atr_1)
            take_profit = entry_price + (config.tp_multiplier * atr_1)
            
            df.loc[df.index[i], 'signal'] = 'buy'
            df.loc[df.index[i], 'entry_price'] = entry_price
            df.loc[df.index[i], 'stop_loss'] = stop_loss
            df.loc[df.index[i], 'take_profit'] = take_profit
        
        # SELL signal
        elif trend_1 == 'downtrend' and close_1 < tenkan_1:
            entry_price = low_1 - (config.pending_offset * config.pip_value)
            stop_loss = entry_price + (config.sl_multiplier * atr_1)
            take_profit = entry_price - (config.tp_multiplier * atr_1)
            
            df.loc[df.index[i], 'signal'] = 'sell'
            df.loc[df.index[i], 'entry_price'] = entry_price
            df.loc[df.index[i], 'stop_loss'] = stop_loss
            df.loc[df.index[i], 'take_profit'] = take_profit
    
    return df

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
