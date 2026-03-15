import pandas as pd
import numpy as np

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

def calculate_stochastic(df, k_period=21, d_period=5, slowing=3):
    """
    Calculate Stochastic Oscillator (Slow)
    %K = SMA(100 * (Close - LL) / (HH - LL), slowing)
    %D = SMA(%K, d_period)
    """
    lowest_low = df['low'].rolling(window=k_period).min()
    highest_high = df['high'].rolling(window=k_period).max()
    
    # Raw %K
    raw_k = 100 * (df['close'] - lowest_low) / (highest_high - lowest_low)
    
    # %K Main (smoothed by slowing)
    df['stoch_k'] = raw_k.rolling(window=slowing).mean()
    
    # %D Signal (SMA of %K)
    df['stoch_d'] = df['stoch_k'].rolling(window=d_period).mean()
    
    return df
