import os

# Define folders
DATA_FOLDER = 'candle_data'
RESULT_FOLDER = 'backtest_result'

# Ensure results folder exists
if not os.path.exists(RESULT_FOLDER):
    os.makedirs(RESULT_FOLDER)

class TradingConfig:
    """
    Configuration class with pair-specific settings
    """
    def __init__(self, pair, strategy_type, sl_multiplier=None, tp_multiplier=None):
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

        # Override with custom multipliers if provided
        if sl_multiplier is not None:
            self.sl_multiplier = sl_multiplier
        if tp_multiplier is not None:
            self.tp_multiplier = tp_multiplier
        
        # ENFORCE MINIMUM RISK:REWARD (1:1.05)
        min_tp_multiplier = self.sl_multiplier * 1.05
        if self.tp_multiplier < min_tp_multiplier:
            self.tp_multiplier = min_tp_multiplier
            
        self.atr_period = 14
        
        # Stochastic parameters
        self.stoch_k_period = 21
        self.stoch_d_period = 5
        self.stoch_slowing = 3
    
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
            'tp_multiplier': 2.0,
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
            'tp_multiplier': 2.0
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
