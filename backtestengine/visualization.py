import pandas as pd
import matplotlib.pyplot as plt
import os
from config import RESULT_FOLDER

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
