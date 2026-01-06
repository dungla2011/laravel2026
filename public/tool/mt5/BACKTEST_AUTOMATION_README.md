# MT5 Backtest Automation Script

Automates sequential backtests for **09-dca.mq5** with different configurations and generates HTML reports similar to MT5 Strategy Tester format.

## Features

✅ **Sequential Backtest Execution** - Runs multiple configs one after another  
✅ **HTML Report Generation** - Professional MT5-style backtest report  
✅ **Configuration Management** - Automatically updates EA config between tests  
✅ **Metrics Extraction** - Net Profit, Gross Profit, Win Rate, Drawdown, Sharpe Ratio, etc.  
✅ **Configuration Comparison** - Side-by-side comparison of all tested configs  

## Installation

### 1. Install Python Dependencies

```bash
pip install MetaTrader5
```

### 2. Configure MT5 Connection

Ensure your MT5 terminal is running on the same machine. The script will connect to it via the MetaTrader5 Python library.

### 3. Prepare Backtest Data

For best results:
- Ensure M1 tick data is available in MT5 for XAUUSD
- You can download historical data through MT5: Tools → History Center

## Usage

### Basic Usage

```bash
python mt5_backtest_automation.py
```

### Configuration

Edit these variables in the script:

```python
# Configs to test (format: MaxB-Lot-Step-TP-SL#MaxB-Lot-Step-TP-SL#...)
CONFIGS = [
    "25-0.01-1000-1000-0#20-0.01-3000-3000-0#10-0.01-6000-6000-0",
    "50-0.01-1000-1000-0"
]

# Backtest parameters
BACKTEST_PARAMS = {
    "symbol": "XAUUSD",
    "timeframe": "M1",
    "date_from": "2025.12.18",
    "date_to": "2025.12.18",
    "initial_deposit": 10000,
}
```

## Output

The script generates:

1. **HTML Report** - Professional backtest report with:
   - Configuration comparison table
   - Detailed metrics for each config
   - Best performing config highlighted
   - Styled for easy reading

2. **JSON Results** - Raw backtest data for further processing

Both files are saved in: `e:\Projects\laravel2022-01\laravel01\public\tool\mt5\backtest_results\`

## How It Works

1. **Connect to MT5** - Establishes connection via MetaTrader5 Python API
2. **Modify EA Config** - Updates `09-dca-config.json` for each test config
3. **Run Backtest** - Executes backtest and collects results
4. **Generate Report** - Creates HTML report with all metrics and comparison
5. **Save Results** - Stores both HTML and JSON output

## Report Contents

The generated HTML report includes:

### Test Configuration Section
- Symbol, Timeframe, Period, Initial Deposit

### Configuration Comparison Table
- All configs side-by-side
- Key metrics: Net Profit, Win Rate, Profit Factor, Max Drawdown
- Best performing config marked

### Detailed Results Per Config
- Summary statistics cards
- Comprehensive metrics table:
  - Gross Profit / Gross Loss
  - Profit Factor
  - Expected Value
  - Winning/Losing Trades
  - Average Win/Loss
  - Max Consecutive Wins/Losses
  - Max Drawdown
  - Sharpe Ratio

### Summary & Recommendations
- Best performing configuration
- Key statistics

## Example Output

```
[2025-12-19 10:30:45] INFO: MT5 BACKTEST AUTOMATION - 09-DCA Strategy
[2025-12-19 10:30:45] INFO: ======================================================================
[2025-12-19 10:30:45] INFO: Connecting to MT5...
[2025-12-19 10:30:46] INFO: ✅ MT5 connected
[2025-12-19 10:30:46] INFO: ============================================================
[2025-12-19 10:30:46] INFO: Running backtest 1 / 2
[2025-12-19 10:30:46] INFO: Config: 25-0.01-1000-1000-0#20-0.01-3000-3000-0#10-0.01-6000-6000-0
[2025-12-19 10:30:47] INFO: ✅ Config updated
[2025-12-19 10:30:47] INFO: ⏳ Running backtest simulation...
[2025-12-19 10:30:47] INFO: ✅ Backtest 1 completed

[2025-12-19 10:30:49] INFO: ============================================================
[2025-12-19 10:30:49] INFO: Running backtest 2 / 2
[2025-12-19 10:30:49] INFO: Config: 50-0.01-1000-1000-0
[2025-12-19 10:30:50] INFO: ✅ Config updated
[2025-12-19 10:30:50] INFO: ⏳ Running backtest simulation...
[2025-12-19 10:30:50] INFO: ✅ Backtest 2 completed

[2025-12-19 10:30:50] INFO: Generating HTML report...
[2025-12-19 10:30:50] INFO: ✅ HTML report saved: e:\...\backtest_report_20251219_103050.html
[2025-12-19 10:30:50] INFO: ✅ JSON results saved: e:\...\backtest_results_20251219_103050.json
[2025-12-19 10:30:50] INFO: ======================================================================
[2025-12-19 10:30:50] INFO: ✅ Backtest automation completed in 5.2s
[2025-12-19 10:30:50] INFO: 📊 Report: e:\...\backtest_report_20251219_103050.html
```

## Metrics Explained

| Metric | Description |
|--------|-------------|
| **Net Profit** | Total profit/loss from all trades |
| **Gross Profit** | Sum of all winning trades |
| **Gross Loss** | Sum of all losing trades |
| **Profit Factor** | Gross Profit / Gross Loss (>1.0 is profitable) |
| **Total Trades** | Number of opened trades |
| **Win Rate** | Percentage of winning trades |
| **Max Drawdown** | Largest decline from peak equity |
| **Sharpe Ratio** | Risk-adjusted return metric (>1.0 is good) |
| **Expected Value** | Average profit per trade |

## Advanced Usage

### Modify Test Period

```python
BACKTEST_PARAMS = {
    "date_from": "2025.01.01",
    "date_to": "2025.12.31",
}
```

### Add More Configurations

```python
CONFIGS = [
    "25-0.01-1000-1000-0#20-0.01-3000-3000-0#10-0.01-6000-6000-0",
    "50-0.01-1000-1000-0",
    "30-0.01-2000-2000-0#20-0.01-4000-4000-0",  # New config
    "40-0.01-1500-1500-0#15-0.01-4500-4500-0",  # Another config
]
```

### Change Symbol or Timeframe

```python
BACKTEST_PARAMS = {
    "symbol": "EURUSD",      # Different symbol
    "timeframe": "H1",        # Hourly instead of M1
}
```

## Troubleshooting

### "Failed to initialize MT5"
- Ensure MT5 terminal is running
- Check if terminal data path is correct in the script
- Try restarting MT5

### "No tick data available"
- Download historical data in MT5: Tools → History Center → Select symbol → Download
- Ensure the test date has available data

### "Cannot find EA file"
- Verify EA path: `C:\Users\pc2\AppData\Roaming\MetaQuotes\Terminal\{TERMINAL_ID}\MQL5\Experts\09-dca.ex5`
- Check that 09-dca.ex5 exists

### "Config file not found"
- The script automatically creates the config file in: `{TERMINAL}\MQL5\Files\09-dca-config.json`
- If not found, manually create it with content: `{"config":"...", "start":true}`

## Future Enhancements

- [ ] Direct MT5 Strategy Tester integration via COM
- [ ] Parallel backtest execution
- [ ] Equity curve visualization
- [ ] Monte Carlo analysis
- [ ] Walk-forward optimization
- [ ] Real-time backtest monitoring

## License

Internal tool - For authorized use only

## Support

For issues or questions, contact development team.

---

**Last Updated:** December 19, 2025  
**Version:** 1.0
