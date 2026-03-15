import pandas as pd

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
