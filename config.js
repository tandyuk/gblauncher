{
	"overrides": {
		"james": {
			"REQUIRES": "stepgain",
                        "PERIOD": 5,
                        "BUYLVL1": 1.5,
                        "BUYLVL2": 2.5,
                        "BUYLVL3": 4,
                        "SELLLVL1": 0.9,
                        "SELLLVL2": 1.2,
                        "SELLLVL3": 1.9,
                        "BUYLVL": 3,
                        "SELLLVL": 2,
                        "LASTPOINTS": 6,
                        "AVGPOINTS": 30,
                        "AVGMINIMUM": 1e-8,
                        "EMA1": 8,
                        "EMA2": 4
		},
		"lostinspace": {
			"REQUIRES": "stepgain",
                        "PERIOD": 7,
                        "BUYLVL1": 1.295,
                        "BUYLVL2": 2.496,
                        "BUYLVL3": 13.845,
                        "SELLLVL1": 0.834,
                        "SELLLVL2": 1.593,
                        "SELLLVL3": 6.753,
                        "BUYLVL": 2,
                        "SELLLVL": 2,
                        "LASTPOINTS": 5,
                        "AVGPOINTS": 24,
                        "AVGMINIMUM": 1e-8,
                        "EMA1": 5,
                        "EMA2": 3
		}
	},
	"pairs": {
		"bittrex": {
			"BTC-1ST": {
				"strategy": "stepgain",
				"override": {
					"CUSTOM": "james"
				}
			},
			"BTC-ARK": {
				"strategy": "stepgain",
				"override": {
					"CUSTOM": "lostinspace"
				}
			},
			"JAM-FAKE": {
				"strategy": "jamessteps"
			},
			"BTC-BCC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-BTS": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-CVC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-DASH": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-DGB": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-EDG": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-EGC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-ETC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-ETH": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-KMD": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-LSK": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-LTC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-LUN": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-MCO": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-MTL": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-NAV": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-NEO": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-NXS": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-OK": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-OMG": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-PAY": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-PTOY": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-QTUM": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-RISE": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-STRAT": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-SYS": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-TRIG": {
				"strategy": "stepgain",
				"override": {
					"DOUBLE_UP": false
				}
			},
			"BTC-VRC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-WAVES": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-XEL": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-XEM": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-XMR": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-XRP": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-XVC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-XVG": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-ZEC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-BAT": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-ADX": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-STORJ": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-THC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-SC": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-SNT": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-BURST": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-REP": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-GAME": {
				"strategy": "stepgain",
				"override": {}
			},
			"BTC-WINGS": {
				"strategy": "stepgain",
				"override": {}
			}
		}
	},
	"exchanges": {
		"poloniex": {
			"key": "",
			"secret": ""
		},
		"kraken": {
			"key": "",
			"secret": ""
		},
		"bittrex": {
			"key": "",
			"secret": ""
		}
	},
	"bot": {
		"debug": false,
		"period_storage_ticker": 500,
		"interval_ticker_update": 15000,
		"timeout_buy": 60000,
		"timeout_sell": 60000,
		"WATCH_MODE": false,
		"VERBOSE": true,
		"TV_GAIN": 0.6,
		"TV_TRADING_LIMIT": 0.001,
		"BOT_DELAY": 60
	},
	"ws": {
		"port": 5001
	},
	"imap_listener": {
		"enabled": false,
		"authorized_froms": [
			"noreply@tradingview.com"
		],
		"user": "YOUR_EMAIL_HERE",
		"password": "YOUR_PASSWORD_HERE",
		"host": "imap.gmail.com",
		"port": 993,
		"tls": true,
		"tlsOptions": {
			"rejectUnauthorized": false
		}
	},
	"strategies": {
		"bb": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"EMA1": 8,
			"EMA2": 4,
			"GAIN": 2,
			"HIGH_BB": 40,
			"LOW_BB": 40,
			"STDV": 2,
			"SMAPERIOD": 50,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.0001,
			"MIN_VOLUME_TO_SELL": 0.005
		},
		"gain": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"BUY_LEVEL": 2,
			"GAIN": 2,
			"EMA1": 200,
			"EMA2": 50,
			"HIGH_BB": 45,
			"LOW_BB": 45,
			"STDV": 2,
			"SMAPERIOD": 50,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 99,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"pp": {
			"BTC_TRADING_LIMIT": 0.01,
			"PP_BUY": 1e-8,
			"PP_SELL": 0.12345678,
			"PANIC_SELL": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"stepgain": {
			"BTC_TRADING_LIMIT": 0.02,
			"PERIOD": 5,
			"BUYLVL1": 1.5,
			"BUYLVL2": 2.5,
			"BUYLVL3": 4,
			"SELLLVL1": 0.9,
			"SELLLVL2": 1.2,
			"SELLLVL3": 1.9,
			"BUYLVL": 3,
			"SELLLVL": 2,
			"LASTPOINTS": 6,
			"AVGPOINTS": 30,
			"AVGMINIMUM": 1e-8,
			"EMA1": 8,
			"EMA2": 4,
			"PANIC_SELL": false,
			"STOP_LIMIT": 90,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"jamessteps": {
			"REQUIRES": "stepgain",
			"BTC_TRADING_LIMIT": 0.00,
			"PERIOD": 5,
			"BUYLVL1": 1.5,
			"BUYLVL2": 2.5,
			"BUYLVL3": 50,
			"SELLLVL1": 0.9,
			"SELLLVL2": 1.2,
			"SELLLVL3": 100,
			"BUYLVL": 3,
			"SELLLVL": 2,
			"LASTPOINTS": 6,
			"AVGPOINTS": 30,
			"AVGMINIMUM": 1e-8,
			"EMA1": 8,
			"EMA2": 4,
			"PANIC_SELL": false,
			"STOP_LIMIT": 90,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"bbgain": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"GAIN": 0.6,
			"HIGH_BB": 45,
			"LOW_BB": 45,
			"STDV": 2,
			"SMAPERIOD": 150,
			"EMA1": 200,
			"EMA2": 50,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 85,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"gainbb": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"BUY_LEVEL": 2,
			"GAIN": 0.6,
			"HIGH_BB": 45,
			"LOW_BB": 20,
			"STDV": 2,
			"SMAPERIOD": 150,
			"EMA1": 200,
			"EMA2": 50,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 85,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"bbstepgain": {
			"BTC_TRADING_LIMIT": 0.001,
			"PERIOD": 15,
			"HIGH_BB": 45,
			"LOW_BB": 45,
			"STDV": 2,
			"SMAPERIOD": 150,
			"SELLLVL1": 0.9,
			"SELLLVL2": 2.5,
			"SELLLVL3": 5,
			"SELLLVL": 3,
			"LASTPOINTS": 4,
			"AVGPOINTS": 20,
			"AVGMINIMUM": 1e-8,
			"EMA1": 200,
			"EMA2": 50,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 85,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.00001
		},
		"stepgainbb": {
			"BTC_TRADING_LIMIT": 0.001,
			"PERIOD": 15,
			"HIGH_BB": 49,
			"LOW_BB": 40,
			"STDV": 2,
			"SMAPERIOD": 150,
			"BUYLVL1": 0.6,
			"BUYLVL2": 2,
			"BUYLVL3": 70,
			"BUYLVL": 2,
			"GAIN": 0.6,
			"LASTPOINTS": 5,
			"AVGPOINTS": 250,
			"AVGMINIMUM": 1e-8,
			"EMA1": 2000,
			"EMA2": 1000,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 85,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"bbpp": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"GAIN": 0.1,
			"HIGH_BB": 40,
			"LOW_BB": 40,
			"STDV": 2,
			"SMAPERIOD": 150,
			"PP_SELL": 0.12345678,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 85,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"ppbb": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"GAIN": 0.1,
			"HIGH_BB": 40,
			"LOW_BB": 40,
			"STDV": 2,
			"SMAPERIOD": 150,
			"PP_BUY": 1e-8,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"gainstepgain": {
			"BTC_TRADING_LIMIT": 0.01,
			"HIGH_BB": 45,
			"LOW_BB": 45,
			"STDV": 2,
			"SMAPERIOD": 150,
			"PERIOD": 15,
			"SELLLVL1": 0.6,
			"SELLLVL2": 2,
			"SELLLVL3": 70,
			"SELLLVL": 2,
			"BUY_LEVEL": 2,
			"LASTPOINTS": 5,
			"AVGPOINTS": 250,
			"AVGMINIMUM": 1e-8,
			"EMA1": 200,
			"EMA2": 50,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"stepgaingain": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"BUYLVL1": 0.6,
			"BUYLVL2": 2,
			"BUYLVL3": 70,
			"BUYLVL": 2,
			"GAIN": 2,
			"LASTPOINTS": 5,
			"AVGPOINTS": 250,
			"AVGMINIMUM": 1e-8,
			"EMA1": 2000,
			"EMA2": 1000,
			"PANIC_SELL": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"gainpp": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"BUY_LEVEL": 0.1,
			"PP_SELL": 0.12345678,
			"EMA1": 2000,
			"EMA2": 1000,
			"PANIC_SELL": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"ppgain": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"GAIN": 2,
			"PP_BUY": 0.12345678,
			"EMA1": 200,
			"EMA2": 50,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"stepgainpp": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"BUYLVL1": 1,
			"BUYLVL2": 3,
			"BUYLVL3": 5,
			"BUYLVL": 2,
			"PP_SELL": 0.1,
			"LASTPOINTS": 5,
			"AVGPOINTS": 250,
			"AVGMINIMUM": 1e-8,
			"EMA1": 2000,
			"EMA2": 1000,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		},
		"ppstepgain": {
			"BTC_TRADING_LIMIT": 0.01,
			"PERIOD": 15,
			"SELLLVL1": 2,
			"SELLLVL2": 5,
			"SELLLVL3": 10,
			"SELLLVL": 2,
			"PP_BUY": 1e-8,
			"LASTPOINTS": 5,
			"AVGPOINTS": 250,
			"AVGMINIMUM": 1e-8,
			"EMA1": 2000,
			"EMA2": 1000,
			"PANIC_SELL": false,
			"DOUBLE_UP": false,
			"STOP_LIMIT": 60,
			"BUY_ENABLED": true,
			"MIN_VOLUME_TO_BUY": 0.001,
			"MIN_VOLUME_TO_SELL": 0.001
		}
	},
	"optionals": {
		"toOverride": {}
	}
}
