//+------------------------------------------------------------------+
//|                                     Auto TP SL Scalping EA.mq5    |
//|                                  Copyright 2024, MetaQuotes Ltd.  |
//|                                             https://www.mql5.com  |
//+------------------------------------------------------------------+
#property copyright "Copyright 2026, GPLv3"
#property link "https://marketmonium.com"
#property version "1.0"
#property strict
#property description "Auto ATR-based SL/TP for Positions and Pending Orders"

#include <Trade\AccountInfo.mqh>
#include <Trade\OrderInfo.mqh>
#include <Trade\PositionInfo.mqh>
#include <Trade\Trade.mqh>

//--- Input parameters
input int InpATRPeriod = 14;        // ATR Period
input double InpRiskPercent = 1.0;  // Maximum Risk % per Trade
input double InpTPMultiplier = 2.0; // Take Profit ATR Multiplier
input double InpSLMultiplier = 1.5; // Stop Loss ATR Multiplier
input long InpMagicNum = 123456;    // EA Magic Number

//--- Global variables
int handleATR;
CTrade trade;
CPositionInfo posInfo;
COrderInfo orderInfo;
CAccountInfo account;

//+------------------------------------------------------------------+
//| Create UI Buttons                                                |
//+------------------------------------------------------------------+
void CreateButtons() {
  ObjectCreate(0, "BtnBuy", OBJ_BUTTON, 0, 0, 0);
  ObjectSetInteger(0, "BtnBuy", OBJPROP_XDISTANCE, 20);
  ObjectSetInteger(0, "BtnBuy", OBJPROP_YDISTANCE, 50);
  ObjectSetInteger(0, "BtnBuy", OBJPROP_XSIZE, 100);
  ObjectSetInteger(0, "BtnBuy", OBJPROP_YSIZE, 30);
  ObjectSetString(0, "BtnBuy", OBJPROP_TEXT, "BUY ATR");
  ObjectSetInteger(0, "BtnBuy", OBJPROP_BGCOLOR, clrGreen);
  ObjectSetInteger(0, "BtnBuy", OBJPROP_COLOR, clrWhite);

  ObjectCreate(0, "BtnSell", OBJ_BUTTON, 0, 0, 0);
  ObjectSetInteger(0, "BtnSell", OBJPROP_XDISTANCE, 130);
  ObjectSetInteger(0, "BtnSell", OBJPROP_YDISTANCE, 50);
  ObjectSetInteger(0, "BtnSell", OBJPROP_XSIZE, 100);
  ObjectSetInteger(0, "BtnSell", OBJPROP_YSIZE, 30);
  ObjectSetString(0, "BtnSell", OBJPROP_TEXT, "SELL ATR");
  ObjectSetInteger(0, "BtnSell", OBJPROP_BGCOLOR, clrRed);
  ObjectSetInteger(0, "BtnSell", OBJPROP_COLOR, clrWhite);

  ChartRedraw();
}

//+------------------------------------------------------------------+
//| Expert initialization function                                   |
//+------------------------------------------------------------------+
int OnInit() {
  trade.SetExpertMagicNumber(InpMagicNum);

  handleATR = iATR(_Symbol, _Period, InpATRPeriod);
  if (handleATR == INVALID_HANDLE) {
    Print("Failed to create ATR handle");
    return (INIT_FAILED);
  }

  CreateButtons();
  return (INIT_SUCCEEDED);
}

//+------------------------------------------------------------------+
//| Expert deinitialization function                                 |
//+------------------------------------------------------------------+
void OnDeinit(const int reason) {
  IndicatorRelease(handleATR);
  ObjectDelete(0, "BtnBuy");
  ObjectDelete(0, "BtnSell");
}

//+------------------------------------------------------------------+
//| Chart Event Handler                                              |
//+------------------------------------------------------------------+
void OnChartEvent(const int id, const long &lparam, const double &dparam,
                  const string &sparam) {
  if (id == CHARTEVENT_OBJECT_CLICK) {
    double atr = GetATR();
    if (atr <= 0)
      return;

    double slRange = atr * InpSLMultiplier;
    double lots = CalculateLotSize(slRange);

    if (sparam == "BtnBuy") {
      double price = SymbolInfoDouble(_Symbol, SYMBOL_ASK);
      double sl = price - slRange;
      double tp = price + (atr * InpTPMultiplier);

      int digits = (int)SymbolInfoInteger(_Symbol, SYMBOL_DIGITS);
      trade.Buy(lots, _Symbol, price, NormalizeDouble(sl, digits),
                NormalizeDouble(tp, digits));

      ObjectSetInteger(0, "BtnBuy", OBJPROP_STATE, false);
    } else if (sparam == "BtnSell") {
      double price = SymbolInfoDouble(_Symbol, SYMBOL_BID);
      double sl = price + slRange;
      double tp = price - (atr * InpTPMultiplier);

      int digits = (int)SymbolInfoInteger(_Symbol, SYMBOL_DIGITS);
      trade.Sell(lots, _Symbol, price, NormalizeDouble(sl, digits),
                 NormalizeDouble(tp, digits));

      ObjectSetInteger(0, "BtnSell", OBJPROP_STATE, false);
    }
    ChartRedraw();
  }
}

//+------------------------------------------------------------------+
//| Expert tick function                                             |
//+------------------------------------------------------------------+
void OnTick() {
  // 1. Process Active Positions
  for (int i = PositionsTotal() - 1; i >= 0; i--) {
    if (posInfo.SelectByIndex(i)) {
      if (posInfo.Symbol() == _Symbol) {
        if (posInfo.StopLoss() == 0 || posInfo.TakeProfit() == 0) {
          ApplyAtrToPosition(posInfo.Ticket());
        }
      }
    }
  }

  // 2. Process Pending Orders (Buy Limit, Sell Stop, etc.)
  for (int i = OrdersTotal() - 1; i >= 0; i--) {
    if (orderInfo.SelectByIndex(i)) {
      if (orderInfo.Symbol() == _Symbol) {
        if (orderInfo.StopLoss() == 0 || orderInfo.TakeProfit() == 0) {
          ApplyAtrToOrder(orderInfo.Ticket());
        }
      }
    }
  }
}

//+------------------------------------------------------------------+
//| Calculate ATR Value                                              |
//+------------------------------------------------------------------+
double GetATR() {
  double buffer[1];
  if (CopyBuffer(handleATR, 0, 0, 1, buffer) < 1)
    return 0;
  return buffer[0];
}

//+------------------------------------------------------------------+
//| Calculate Lot Size based on risk                                 |
//+------------------------------------------------------------------+
double CalculateLotSize(double slRange) {
  double equity = account.Equity();
  double riskAmount = equity * (InpRiskPercent / 100.0);
  double tickValue = SymbolInfoDouble(_Symbol, SYMBOL_TRADE_TICK_VALUE);
  double tickSize = SymbolInfoDouble(_Symbol, SYMBOL_TRADE_TICK_SIZE);

  if (slRange <= 0 || tickValue <= 0)
    return SymbolInfoDouble(_Symbol, SYMBOL_VOLUME_MIN);

  double lotStep = SymbolInfoDouble(_Symbol, SYMBOL_VOLUME_STEP);
  double lots = riskAmount / (slRange / tickSize * tickValue);

  lots = MathFloor(lots / lotStep) * lotStep;

  double minLot = SymbolInfoDouble(_Symbol, SYMBOL_VOLUME_MIN);
  double maxLot = SymbolInfoDouble(_Symbol, SYMBOL_VOLUME_MAX);

  if (lots < minLot)
    lots = minLot;
  if (lots > maxLot)
    lots = maxLot;

  return lots;
}

//+------------------------------------------------------------------+
//| Apply ATR SL/TP to existing position                             |
//+------------------------------------------------------------------+
void ApplyAtrToPosition(ulong ticket) {
  if (!posInfo.SelectByTicket(ticket))
    return;

  double atr = GetATR();
  if (atr <= 0)
    return;

  double entryPrice = posInfo.PriceOpen();
  double sl = 0, tp = 0;

  if (posInfo.PositionType() == POSITION_TYPE_BUY) {
    sl = entryPrice - (atr * InpSLMultiplier);
    tp = entryPrice + (atr * InpTPMultiplier);
  } else if (posInfo.PositionType() == POSITION_TYPE_SELL) {
    sl = entryPrice + (atr * InpSLMultiplier);
    tp = entryPrice - (atr * InpTPMultiplier);
  }

  int digits = (int)SymbolInfoInteger(_Symbol, SYMBOL_DIGITS);
  sl = NormalizeDouble(sl, digits);
  tp = NormalizeDouble(tp, digits);

  if (!trade.PositionModify(ticket, sl, tp))
    Print("Error modifying position #", ticket, ": ", GetLastError());
}

//+------------------------------------------------------------------+
//| Apply ATR SL/TP to pending order                                 |
//+------------------------------------------------------------------+
void ApplyAtrToOrder(ulong ticket) {
  if (!orderInfo.Select(ticket))
    return;

  double atr = GetATR();
  if (atr <= 0)
    return;

  double entryPrice = orderInfo.PriceOpen();
  double sl = 0, tp = 0;
  ENUM_ORDER_TYPE orderType = orderInfo.OrderType();

  // Buy Limit, Buy Stop, Buy Stop Limit
  if (orderType == ORDER_TYPE_BUY_LIMIT || orderType == ORDER_TYPE_BUY_STOP ||
      orderType == ORDER_TYPE_BUY_STOP_LIMIT) {
    sl = entryPrice - (atr * InpSLMultiplier);
    tp = entryPrice + (atr * InpTPMultiplier);
  }
  // Sell Limit, Sell Stop, Sell Stop Limit
  else if (orderType == ORDER_TYPE_SELL_LIMIT ||
           orderType == ORDER_TYPE_SELL_STOP ||
           orderType == ORDER_TYPE_SELL_STOP_LIMIT) {
    sl = entryPrice + (atr * InpSLMultiplier);
    tp = entryPrice - (atr * InpTPMultiplier);
  } else
    return; // Not a relevant pending order type

  int digits = (int)SymbolInfoInteger(_Symbol, SYMBOL_DIGITS);
  sl = NormalizeDouble(sl, digits);
  tp = NormalizeDouble(tp, digits);

  if (!trade.OrderModify(ticket, entryPrice, sl, tp, orderInfo.TypeTime(),
                         orderInfo.TimeExpiration(),
                         orderInfo.PriceStopLimit()))
    Print("Error modifying order #", ticket, ": ", GetLastError());
}

//+------------------------------------------------------------------+
//| Trade Transaction Event                                          |
//+------------------------------------------------------------------+
void OnTradeTransaction(const MqlTradeTransaction &trans,
                        const MqlTradeRequest &request,
                        const MqlTradeResult &result) {
  // 1. Detect New Deals (Immediate Position SL/TP)
  if (trans.type == TRADE_TRANSACTION_DEAL_ADD) {
    if (HistoryDealSelect(trans.deal)) {
      if (HistoryDealGetInteger(trans.deal, DEAL_ENTRY) == DEAL_ENTRY_IN) {
        ApplyAtrToPosition(HistoryDealGetInteger(trans.deal, DEAL_POSITION_ID));
      }
    }
  }
  // 2. Detect New Orders (Pending/Stop-Limit SL/TP)
  if (trans.type == TRADE_TRANSACTION_ORDER_ADD) {
    ApplyAtrToOrder(trans.order);
  }
}
