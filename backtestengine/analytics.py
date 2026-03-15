import pandas as pd
import numpy as np

def analyze_performance(trades, equity_curve, initial_equity, pair, silent=False):
    """Calculate and display performance metrics"""
    
    if len(trades) == 0:
        if not silent:
            print("\n⚠ No trades executed!")
        return None, None
    
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
    if not silent:
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
        'win_rate_raw': win_rate,  # Use for strategy finder
        'total_profit': total_profit, # Use for strategy finder
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
