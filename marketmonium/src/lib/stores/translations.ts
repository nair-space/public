import { writable, derived } from 'svelte/store';

export const currentLang = writable<'en' | 'id'>('en');

const translations = {
    en: {
        nav: {
            brand: "Trading Mastery",
            tradingPlan: "Trading Plan",
            tradingJournal: "Trading Journal",
            quiz: "Quiz"
        },
        actions: {
            explore: "Explore Module",
            useTool: "Use Tool",
            startLearning: "Start Learning",
            backHome: "Back to Home",
            downloadPlan: "Download Trading Plan (PDF)",
            downloadJournal: "Download Editable Log (.doc)",
            retakeQuiz: "Retake Quiz",
            flipCoin: "FLIP COIN",
            resetStats: "Reset Stats",
            exitSimulator: "Exit Simulator"
        },
        landing: {
            featuresTitle: "Your Learning Path",
            featuresSubtitle: "Four essential pillars to build your trading foundation",
            toolsTitle: "Powerful Tools",
            toolsSubtitle: "Practice your skills with our interactive utility modules",
            cardPlanTitle: "Trading Plan",
            cardPlanDesc: "Build your fractal horizon with our systematic trading plan template.",
            cardJournalTitle: "Trading Journal",
            cardJournalDesc: "Reflect on your journey with our structured daily reflection log.",
            cardQuizTitle: "Trading Quiz",
            cardQuizDesc: "Find your personality type and test your trading knowledge.",
            cardMmTitle: "Money Management",
            cardMmDesc: "Calculate your risk and position size with our easy-to-use tool.",
            cardCoinTitle: "Coin Flip Sim",
            cardCoinDesc: "Experience how edge and variance work in real-time.",
            benefitsTitle: "Why Learn Trading?",
            benefits: [
                {
                    title: "Beginner-Friendly",
                    description: "Start from zero with clear, step-by-step guidance designed for total beginners."
                },
                {
                    title: "Practical Learning",
                    description: "Apply real-world trading principles with actionable tools and frameworks."
                },
                {
                    title: "Risk-First Approach",
                    description: "Learn to protect your capital before chasing profits—the trader's golden rule."
                }
            ],
            seoTitle: "Trading Mastery for Beginners | Learn the Fundamentals",
            seoDesc: "Master trading fundamentals with our comprehensive beginner's guide. Learn trading plans, journaling, money management, and test your knowledge with interactive quizzes."
        },
        coinFlip: {
            title: "Probability Simulator",
            subtitle: "Experience how edge and variance work in real-time",
            settingsTitle: "Game Settings",
            rewardScenario: "Reward Scenario",
            statEdge: "Statistical Edge (%)",
            helperText: "Add your \"edge\" to the base win probability.",
            flipping: "Flipping...",
            pressFlip: "Press Flip",
            stats: {
                winProb: "Win Probability:",
                rewardWin: "Reward if Win:",
                costLoss: "Cost if Loss:",
                ev: "Expected Value (EV):",
                base: "Base"
            },
            results: {
                title: "Session Results",
                totalPnL: "Total PnL",
                heads: "Heads",
                tails: "Tails",
                recentFlips: "Recent Flips",
                noFlips: "No flips yet",
                points: "Points"
            }
        },
        quiz: {
            title: "Trader Personality Test",
            subtitle: "Which Trader Are You?",
            questionCount: "Question {current} of {total}",
            results: {
                youAre: "You are a",
                trendTitle: "Trend Follower / Breakout Trader",
                trendDesc1: "You naturally seek safety, structure, and confirmation. You prefer to follow the path of least resistance and are comfortable \"going with the flow.\"",
                trendDesc2: "<strong>Your Strengths:</strong> Patience, discipline, and the ability to ride big moves.<br /><strong>Your Style:</strong> Breakout Trading, Trend Following.<br /><strong>Advice:</strong> Focus on strategies that capitalize on momentum. Don't try to catch falling knives. Wait for the market to show its hand before you enter.",
                reversionTitle: "Mean Reversion Trader",
                reversionDesc1: "You are a contrarian at heart. You like to challenge the norm, question the crowd, and find value where others see fear. You thrive on independence.",
                reversionDesc2: "<strong>Your Strengths:</strong> Independent thinking, spotting overextended markets, and finding turning points.<br /><strong>Your Style:</strong> Mean Reversion, Swing Trading, Counter-Trend.<br /><strong>Advice:</strong> Focus on buying low and selling high in ranges. Be careful not to fight specific strong trends too early. Use strict risk management as you are often catching knives."
            },
            questions: [
                {
                    question: "When buying a new gadget, what do you usually do?",
                    options: [
                        { text: "Buy the most popular one everyone is talking about", type: "trend" },
                        { text: "Wait for detailed reviews from trusted sources", type: "trend" },
                        { text: "Look for a hidden gem with unique features", type: "reversion" },
                        { text: "Buy an older model that everyone is selling off", type: "reversion" }
                    ]
                },
                {
                    question: "You see a long line outside a restaurant. What is your first thought?",
                    options: [
                        { text: "The food must be amazing, I'll join the line", type: "trend" },
                        { text: "It's popular for a reason, I'll come back later", type: "trend" },
                        { text: "I wonder if the place next door is better", type: "reversion" },
                        { text: "Crowds are overrated, I'll avoid this place forever", type: "reversion" }
                    ]
                },
                {
                    question: "In a group project, how do you prefer to work?",
                    options: [
                        { text: "Follow the group consensus to move forward efficiently", type: "trend" },
                        { text: "Stick to the plan we agreed upon initially", type: "trend" },
                        { text: "Challenge ideas if I think they are wrong", type: "reversion" },
                        { text: "Propose a completely different approach from the start", type: "reversion" }
                    ]
                },
                {
                    question: "How do you handle rules and regulations?",
                    options: [
                        { text: "They exist for safety and order, I follow them", type: "trend" },
                        { text: "I generally follow them to avoid trouble", type: "trend" },
                        { text: "I question them if they don't make sense", type: "reversion" },
                        { text: "Rules are meant to be broken or bent", type: "reversion" }
                    ]
                },
                {
                    question: "When a stock market crashes, what is your instinct?",
                    options: [
                        { text: "Sell everything before it drops further", type: "trend" },
                        { text: "Wait until the market stabilizes", type: "trend" },
                        { text: "Look for opportunities to buy cheap", type: "reversion" },
                        { text: "Get excited about the volatility", type: "reversion" }
                    ]
                },
                {
                    question: "Which quote resonates with you more?",
                    options: [
                        { text: "Slow and steady wins the race", type: "trend" },
                        { text: "Don't fight the current", type: "trend" },
                        { text: "Be fearful when others are greedy", type: "reversion" },
                        { text: "Fortune favors the bold", type: "reversion" }
                    ]
                },
                {
                    question: "You're driving and everyone starts slowing down. You:",
                    options: [
                        { text: "Slow down immediately, something must be wrong", type: "trend" },
                        { text: "Coast along with the traffic flow", type: "trend" },
                        { text: "Look for a lane that might be moving faster", type: "reversion" },
                        { text: "Wonder if I can take a shortcut through a side street", type: "reversion" }
                    ]
                },
                {
                    question: "Your favorite fashion style is:",
                    options: [
                        { text: "Whatever is currently trending", type: "trend" },
                        { text: "Classic and timeless", type: "trend" },
                        { text: "Vintage or retro", type: "reversion" },
                        { text: "Unique, avant-garde, or DIY", type: "reversion" }
                    ]
                },
                {
                    question: "How do you view 'Risk'?",
                    options: [
                        { text: "Something to be minimized and managed carefully", type: "trend" },
                        { text: "Acceptable only when the path is clear", type: "trend" },
                        { text: "A necessary cost for high rewards", type: "reversion" },
                        { text: "An exciting challenge to overcome", type: "reversion" }
                    ]
                },
                {
                    question: "When planning a vacation, you prefer:",
                    options: [
                        { text: "Top-rated destinations on TripAdvisor", type: "trend" },
                        { text: "A guided tour to see all the highlights", type: "trend" },
                        { text: "An off-the-beaten-path location", type: "reversion" },
                        { text: "Going without a plan and exploring", type: "reversion" }
                    ]
                },
                {
                    question: "If 9 out of 10 people agree on something, you think:",
                    options: [
                        { text: "They are probably right", type: "trend" },
                        { text: "It's safe to agree with them", type: "trend" },
                        { text: "Let's see what the 10th person thinks", type: "reversion" },
                        { text: "The majority is often wrong", type: "reversion" }
                    ]
                },
                {
                    question: "In a debate, you usually:",
                    options: [
                        { text: "Seek common ground and agreement", type: "trend" },
                        { text: "Support the most logical, established argument", type: "trend" },
                        { text: "Play devil's advocate", type: "reversion" },
                        { text: "Defend the unpopular opinion", type: "reversion" }
                    ]
                },
                {
                    question: "When you lose money on a small bet/investment, you:",
                    options: [
                        { text: "Stop and cut my losses immediately", type: "trend" },
                        { text: "Wait to see if things improve", type: "trend" },
                        { text: "Double down to win it back", type: "reversion" },
                        { text: "Analyze why I was wrong and try a different angle", type: "reversion" }
                    ]
                },
                {
                    question: "You prefer games that involve:",
                    options: [
                        { text: "Steady progress and leveling up", type: "trend" },
                        { text: "Teamwork and cooperation", type: "trend" },
                        { text: "Bluffing and psychology", type: "reversion" },
                        { text: "High stakes and big swings", type: "reversion" }
                    ]
                },
                {
                    question: "Which graph looks more appealing to you?",
                    options: [
                        { text: "A steady line going up deeply", type: "trend" },
                        { text: "A smooth curve with little volatility", type: "trend" },
                        { text: "A zigzag line with high peaks and lows", type: "reversion" },
                        { text: "A chart that just crashed and might bounce", type: "reversion" }
                    ]
                },
                {
                    question: "When learning a new skill, you:",
                    options: [
                        { text: "Follow the standard curriculum step-by-step", type: "trend" },
                        { text: "Watch tutorials from experts", type: "trend" },
                        { text: "Experiment and learn by making mistakes", type: "reversion" },
                        { text: "Try to find a hack or shortcut", type: "reversion" }
                    ]
                },
                {
                    question: "Your approach to tradition is:",
                    options: [
                        { text: "Respect and uphold it", type: "trend" },
                        { text: "Follow it unless it's harmful", type: "trend" },
                        { text: "Question its relevance today", type: "reversion" },
                        { text: "Break it to create something new", type: "reversion" }
                    ]
                },
                {
                    question: "If prices of houses in your area skyrocket, you:",
                    options: [
                        { text: "Think it's a good investment and buy in", type: "trend" },
                        { text: "Wish I had bought earlier", type: "trend" },
                        { text: "Think it's a bubble and wait for the crash", type: "reversion" },
                        { text: "Look for a cheaper area that hasn't boomed yet", type: "reversion" }
                    ]
                },
                {
                    question: "Success to you means:",
                    options: [
                        { text: "Stability and security", type: "trend" },
                        { text: "Following a proven career path", type: "trend" },
                        { text: "Innovating and disrupting", type: "reversion" },
                        { text: "Beating the system", type: "reversion" }
                    ]
                },
                {
                    question: "When you face a problem, you:",
                    options: [
                        { text: "Use a proven solution", type: "trend" },
                        { text: "Ask for advice", type: "trend" },
                        { text: "Invent a new solution", type: "reversion" },
                        { text: "Turn the problem into an opportunity", type: "reversion" }
                    ]
                },
                {
                    question: "Regarding change, you:",
                    options: [
                        { text: "Prefer gradual, predictable change", type: "trend" },
                        { text: "Adapt when necessary", type: "trend" },
                        { text: "Thrive in chaos", type: "reversion" },
                        { text: "Initiate change yourself", type: "reversion" }
                    ]
                },
                {
                    question: "Your ideal work environment is:",
                    options: [
                        { text: "Structured with clear hierarchy", type: "trend" },
                        { text: "Collaborative and harmonized", type: "trend" },
                        { text: "Flexible and independent", type: "reversion" },
                        { text: "Competitive and fast-paced", type: "reversion" }
                    ]
                },
                {
                    question: "When you hear a rumor, you:",
                    options: [
                        { text: "Wait for official confirmation", type: "trend" },
                        { text: "Assume it might be true if many say so", type: "trend" },
                        { text: "Investigate the source skeptically", type: "reversion" },
                        { text: "Bet against it being straightforward", type: "reversion" }
                    ]
                },
                {
                    question: "Patience is:",
                    options: [
                        { text: "A virtue essential for success", type: "trend" },
                        { text: "Necessary to avoid mistakes", type: "trend" },
                        { text: "Sometimes a missed opportunity", type: "reversion" },
                        { text: "Boring, I prefer action", type: "reversion" }
                    ]
                },
                {
                    question: "Finally, how do you see the world?",
                    options: [
                        { text: "As a place of order and patterns", type: "trend" },
                        { text: "Generally predictable", type: "trend" },
                        { text: "Cyclical and repetitive", type: "reversion" },
                        { text: "Random and full of surprises", type: "reversion" }
                    ]
                }
            ]
        },
        tradingPlan: {
            title: "Trading Plan",
            subtitle: "The Fractal Horizon System",
            marketSetup: {
                title: "Market & Setup",
                marketTimeframe: {
                    title: "Market and Timeframe",
                    content: "<strong>XAUUSD</strong> (Gold) using <strong>Daily</strong> Time Frame"
                },
                tradingSession: {
                    title: "Trading Session",
                    content: "Check the market daily at <strong>08:00 A.M</strong>"
                },
                entryIdeas: {
                    title: "Entry Ideas (Bill Williams' Fractal Breakout)",
                    long: "<strong>Long Setup:</strong> Identify the most recent Up Fractal (a high surrounded by two lower highs on each side). Place a pending Buy Stop order above this fractal high.",
                    short: "<strong>Short Setup:</strong> Identify the most recent Down Fractal (a low surrounded by two higher lows on each side). Place a pending Sell Stop order below this fractal low.",
                    logic: "<strong>Logic:</strong> A break of a fractal level indicates the market has overcome a previous behavioral turning point, signalling a potential resumption of the trend or a powerful breakout."
                },
                entryMethod: {
                    title: "Entry Method",
                    content: "Use <strong>+/- 20 pips (200 points)</strong> pending stop order from the Fractal price level."
                }
            },
            execution: {
                title: "Execution Rules",
                stopLoss: {
                    title: "Stop Loss",
                    content: "Use fixed stop loss order based on recent structural support/resistance or previous opposite fractal."
                },
                exitStrategy: {
                    title: "Exit Strategy",
                    content: "Use fixed take profit order."
                },
                stopOutStrategy: {
                    title: "Stop-out Strategy",
                    content: "Re-enter if price is still valid according to the rules."
                },
                runningTrade: {
                    title: "Running Trade Strategy",
                    content: "Enter the market again <strong>max 2 times</strong> if price runs in our favor (pyramiding)."
                },
                haltProtocol: {
                    title: "Trading Halt Protocol",
                    content: "If the maximum life remaining on each pair is depleted or maximum risk percent reached in a month is <strong>10%</strong>, then <span class=\"halt-warning\">HALT</span> the trading for that month."
                },
                indicators: {
                    title: "Indicators",
                    content: "Bill Williams' Fractal (default settings)"
                }
            },
            moneyManagement: {
                title: "Money Management",
                drawdown: {
                    title: "Drawdown Protocol",
                    content: "<strong>Reducing Notional Size:</strong> When equity falls by 10% of equity capital, notional equity falls by 20%, and so on. Protect the core capital."
                },
                onProfit: {
                    title: "On-Profit Protocol",
                    content: "<strong>Profit Buffer:</strong> Use the same risk per trade if equity profit is still &lt; 20% from starting equity. Otherwise, check at the beginning of every month to adjust the risk."
                },
                riskReward: {
                    title: "Risk-Reward Target",
                    content: "<strong>1 : 1.1 RR</strong>",
                    note: "(The extra 0.1 is for spread, commission, and swap costs)"
                },
                riskAllocation: {
                    title: "Risk Allocation",
                    content: "<strong>Max 1%</strong> of total equity on each trade."
                },
                positionSizing: {
                    title: "Position Sizing",
                    content: "Categorize risk based on volatility. Adjust lot size so that 1% risk aligns with the stop loss distance."
                },
                riskExposure: {
                    title: "Risk Exposure (Monthly)",
                    content: "<strong>Max 10%</strong> of total equity. (Adjust monthly based on Drawdown and On-Profit Protocol rules)."
                }
            },
            mindset: {
                title: "Trader's Mindset",
                points: [
                    "Keep your losses small",
                    "No supermarket mentality in CFD, we never know which direction the market goes",
                    "No expectation, follow the rule",
                    "Adapt to the market, zero ego",
                    "Always humble, or the market will humble you",
                    "Use realistic equity and risk management model",
                    "There are old traders and there are bold traders but there are no old bold traders, so always use stop loss",
                    "There is total randomness in each and every trade, but there is order in 100 trades that have an edge",
                    "There is no correlation in past events with future events, every moment in the market is unique, focus in the now and execute the plan flawlessly",
                    "Anything can happen in the market, literally anything",
                    "Trader is a risk and portfolio manager, no get rich quick scheme",
                    "You can not control the market, but you can control how you trade",
                    "Be agnostic when entering the market, you are here to make money, not to tell some fortunes",
                    "Be arrogant and believe on your own journey will lead you to success"
                ]
            },
            affirmation: {
                title: "Daily Affirmation (Mark Douglas)",
                points: [
                    "I am completely reconciled to spend the amount of money my edge says is necessary to find out if this next trade turns out to be a winner.",
                    "The outcome of each individual edge in a series is a unique event that has no discernible relationship with the outcome of any previous or future outcome.",
                    "The diversity of the intention of other traders who are about to submit buy and sell orders to the flow can cause anything to happen.",
                    "Since I don't know what anything might be coming, I am going to execute the signal my edge calls for and make myself available to either win and collect my profits or pay my expenses, either way I am fine.",
                    "The risk of an edge not working always exists. Each prediction that results from my analysis is always a guess.",
                    "Capturing the favorable odds built into my edge over a series of trades has nothing to do with being right or wrong.",
                    "Because I did not know my reward or the outcome of the opportunity will be, I completely adhere to execute my trading plan flawlessly and manage my risk before entering my trade."
                ]
            }
        },
        moneyManagement: {
            title: "Money Management",
            subtitle: "Protect your capital with proper position sizing",
            seoTitle: "Money Management Calculator | Trading Mastery",
            accountSettings: "Account Settings",
            totalEquity: "Total Equity ($)",
            riskPerTrade: "Risk per Trade (%)",
            stopLossSize: "Stop Loss Size (%)",
            stopLossTooltip: "Distance to your invalidation point",
            helperPosSize: "Required to calculate Position Size",
            monthlyRiskLimit: "Total Risk Limit per Month (%)",
            positionSize: "Position Size (Entry Amount)",
            posSizeSubtext: "With a {sl}% stop loss, buy this amount to risk exactly ${risk}.",
            riskAmount: "Risk Amount",
            riskSubtext: "Max loss per trade",
            monthlyLimit: "Monthly Limit",
            monthlySubtext: "Max loss per month",
            drawdownAnalysis: "Drawdown Analysis",
            tradesToMonthlyLimit: "Trades to hit Monthly Limit:",
            tradesToExhaust: "Trades to Exhaust Equity:",
            tradesUnit: "{count} trades",
            disclaimer: "*Assuming fixed dollar risk based on starting equity."
        },
        tradingJournal: {
            title: "Trading Journal",
            subtitle: "Daily Reflection Log",
            reminders: {
                title: "Reminder to Myself",
                points: [
                    "Always agnostically arrogant, adaptable, humble.",
                    "Plan and manage your risk.",
                    "Trade like a casino house, let your odds play.",
                    "Diversify.",
                    "Edge can be form from certain pattern that has statistical advantage.",
                    "Learn math and you will be learn the market.",
                    "Nothing is true in the market - every strategy can work.",
                    "Do not trade the noise.",
                    "Make the system simple, mechanical and easily repeatable.",
                    "Discipline and patience, only enter according to the system.",
                    "Fortis fortuna adiuvat, fortune favors the brave.",
                    "Expect nothing, appreciate everything (especially what lies in the present)."
                ]
            },
            logEntries: [
                {
                    date: "31 Dec",
                    sections: [
                        {
                            title: "What Do I Feel When Entering The Market",
                            content: "A bit hesitate. I guess this is because we could not lose the momentum"
                        },
                        {
                            title: "What Do I Feel When The SL Position Got Hit",
                            content: "A bit sad but it is okay"
                        },
                        {
                            title: "What Do I Feel When The TP Position Got Hit",
                            content: "Glad. But do not get caught in the good feeling"
                        },
                        {
                            title: "What I Learn / Improve Today",
                            content: "I think I could get to the 'analysis paralysis' phase."
                        },
                        {
                            title: "Changes I Need To Make From Today",
                            content: "Keep the rules simply stupid and detailed"
                        },
                        {
                            title: "What To Do Tomorrow",
                            content: "Just see how the strategy rolled out"
                        },
                        { title: "Overview", content: "Good trade week" }
                    ]
                }
            ]
        },
        footer: {
            tagline: "Building Better Traders, One Lesson at a Time",
            modules: "Learning Modules",
            rights: "Trading Mastery. All rights reserved. Educational purposes only."
        }
    },
    id: {
        nav: {
            brand: "Master Trading",
            tradingPlan: "Rencana Trading",
            tradingJournal: "Jurnal Trading",
            quiz: "Kuis"
        },
        actions: {
            explore: "Jelajahi Modul",
            useTool: "Gunakan Alat",
            startLearning: "Mulai Belajar",
            backHome: "Kembali ke Beranda",
            downloadPlan: "Unduh Rencana Trading (PDF)",
            downloadJournal: "Unduh Log yang Dapat Diedit (.doc)",
            retakeQuiz: "Ulangi Kuis",
            flipCoin: "LEMPAR KOIN",
            resetStats: "Reset Statistik",
            exitSimulator: "Keluar Simulator"
        },
        landing: {
            featuresTitle: "Jalur Belajar Anda",
            featuresSubtitle: "Empat pilar utama untuk membangun fondasi trading Anda",
            toolsTitle: "Alat Canggih",
            toolsSubtitle: "Latih keterampilan Anda dengan modul utilitas interaktif kami",
            cardPlanTitle: "Rencana Trading",
            cardPlanDesc: "Bangun horison fraktal Anda dengan template rencana trading sistematis kami.",
            cardJournalTitle: "Jurnal Trading",
            cardJournalDesc: "Refleksikan perjalanan Anda dengan log refleksi harian terstruktur kami.",
            cardQuizTitle: "Kuis Trading",
            cardQuizDesc: "Temukan tipe kepribadian Anda dan uji pengetahuan trading Anda.",
            cardMmTitle: "Manajemen Uang",
            cardMmDesc: "Hitung risiko dan ukuran posisi Anda dengan alat yang mudah digunakan.",
            cardCoinTitle: "Simulasi Koin",
            cardCoinDesc: "Rasakan bagaimana keunggulan dan varians bekerja secara real-time.",
            benefitsTitle: "Mengapa Belajar Trading?",
            benefits: [
                {
                    title: "Ramah Pemula",
                    description: "Mulai dari nol dengan panduan langkah demi langkah yang jelas untuk pemula."
                },
                {
                    title: "Pembelajaran Praktis",
                    description: "Terapkan prinsip trading dunia nyata dengan alat dan kerangka kerja yang dapat ditindaklanjuti."
                },
                {
                    title: "Sikap Utamakan Risiko",
                    description: "Belajarlah untuk melindungi modal Anda sebelum mengejar keuntungan—aturan emas trader."
                }
            ],
            seoTitle: "Penguasaan Trading untuk Pemula | Pelajari Dasarnya",
            seoDesc: "Kuasai dasar-dasar trading dengan panduan lengkap kami untuk pemula. Pelajari rencana trading, jurnal, manajemen uang, dan uji pengetahuan Anda dengan kuis interaktif."
        },
        coinFlip: {
            title: "Simulator Probabilitas",
            subtitle: "Rasakan bagaimana edge dan varians bekerja secara real-time",
            settingsTitle: "Pengaturan Game",
            rewardScenario: "Skenario Hadiah",
            statEdge: "Keunggulan Statistik (%)",
            helperText: "Tambahkan \"keunggulan\" Anda ke probabilitas kemenangan dasar.",
            flipping: "Memutar...",
            pressFlip: "Tekan Putar",
            stats: {
                winProb: "Probabilitas Menang:",
                rewardWin: "Hadiah jika Menang:",
                costLoss: "Biaya jika Kalah:",
                ev: "Nilai Harapan (EV):",
                base: "Dasar"
            },
            results: {
                title: "Hasil Sesi",
                totalPnL: "Total PnL",
                heads: "Head",
                tails: "Tail",
                recentFlips: "Lemparan Terakhir",
                noFlips: "Belum ada lemparan",
                points: "Poin"
            }
        },
        quiz: {
            title: "Tes Kepribadian Trader",
            subtitle: "Tipe Trader Apakah Anda?",
            questionCount: "Pertanyaan {current} dari {total}",
            results: {
                youAre: "Anda adalah",
                trendTitle: "Pengikut Tren / Breakout Trader",
                trendDesc1: "Anda secara alami mencari keamanan, struktur, dan konfirmasi. Anda lebih suka mengikuti jalan yang paling sedikit hambatannya dan nyaman \"mengikuti arus.\"",
                trendDesc2: "<strong>Kekuatan Anda:</strong> Kesabaran, disiplin, dan kemampuan menaiki pergerakan besar.<br /><strong>Gaya Anda:</strong> Breakout Trading, Trend Following.<br /><strong>Saran:</strong> Fokus pada strategi yang memanfaatkan momentum. Jangan mencoba menangkap pisau jatuh. Tunggu pasar menunjukkan tangannya sebelum Anda masuk.",
                reversionTitle: "Mean Reversion Trader",
                reversionDesc1: "Anda berjiwa kontrarian. Anda suka menantang norma, mempertanyakan kerumunan, dan menemukan nilai di mana orang lain melihat ketakutan. Anda berkembang dengan kemandirian.",
                reversionDesc2: "<strong>Kekuatan Anda:</strong> Pemikiran independen, melihat pasar yang jenuh, dan menemukan titik balik.<br /><strong>Gaya Anda:</strong> Mean Reversion, Swing Trading, Counter-Trend.<br /><strong>Saran:</strong> Fokus beli rendah dan jual tinggi dalam range. Hati-hati jangan melawan tren kuat terlalu dini. Gunakan manajemen risiko ketat karena Anda sering menangkap pisau."
            },
            questions: [
                {
                    question: "Saat membeli gadget baru, apa yang biasanya Anda lakukan?",
                    options: [
                        { text: "Beli yang paling populer yang dibicarakan semua orang", type: "trend" },
                        { text: "Tunggu ulasan mendetail dari sumber terpercaya", type: "trend" },
                        { text: "Cari permata tersembunyi dengan fitur unik", type: "reversion" },
                        { text: "Beli model lama yang dijual murah orang lain", type: "reversion" }
                    ]
                },
                {
                    question: "Anda melihat antrean panjang di luar restoran. Apa pikiran pertama Anda?",
                    options: [
                        { text: "Makanannya pasti enak, saya ikut antre", type: "trend" },
                        { text: "Pasti populer karena alasan bagus, saya kembali nanti", type: "trend" },
                        { text: "Saya penasaran apakah tempat di sebelahnya lebih baik", type: "reversion" },
                        { text: "Keramaian itu berlebihan, saya hindari tempat ini selamanya", type: "reversion" }
                    ]
                },
                {
                    question: "Dalam proyek kelompok, bagaimana cara kerja pilihan Anda?",
                    options: [
                        { text: "Ikuti konsensus kelompok agar efisien", type: "trend" },
                        { text: "Patuhi rencana yang kita sepakati di awal", type: "trend" },
                        { text: "Tantang ide jika menurut saya salah", type: "reversion" },
                        { text: "Usulkan pendekatan yang sama sekali berbeda dari awal", type: "reversion" }
                    ]
                },
                {
                    question: "Bagaimana Anda menangani aturan dan regulasi?",
                    options: [
                        { text: "Itu ada demi keamanan dan ketertiban, saya ikuti", type: "trend" },
                        { text: "Saya umumnya ikuti untuk hindari masalah", type: "trend" },
                        { text: "Saya pertanyakan jika tidak masuk akal", type: "reversion" },
                        { text: "Aturan dibuat untuk dilanggar atau ditekuk", type: "reversion" }
                    ]
                },
                {
                    question: "Saat pasar saham jatuh, apa insting Anda?",
                    options: [
                        { text: "Jual semuanya sebelum turun lebih jauh", type: "trend" },
                        { text: "Tunggu sampai pasar stabil", type: "trend" },
                        { text: "Cari peluang beli murah", type: "reversion" },
                        { text: "Merasa bersemangat dengan volatilitasnya", type: "reversion" }
                    ]
                },
                {
                    question: "Kutipan mana yang lebih cocok dengan Anda?",
                    options: [
                        { text: "Pelan tapi pasti memenangkan perlombaan", type: "trend" },
                        { text: "Jangan melawan arus", type: "trend" },
                        { text: "Takutlah saat orang lain serakah", type: "reversion" },
                        { text: "Keberuntungan memihak yang berani", type: "reversion" }
                    ]
                },
                {
                    question: "Anda mengemudi dan semua orang mulai melambat. Anda:",
                    options: [
                        { text: "Segera melambat, pasti ada yang salah", type: "trend" },
                        { text: "Ikuti arus lalu lintas", type: "trend" },
                        { text: "Cari jalur yang mungkin bergerak lebih cepat", type: "reversion" },
                        { text: "Berpikir apa bisa ambil jalan pintas lewat jalan kecil", type: "reversion" }
                    ]
                },
                {
                    question: "Gaya busana favorit Anda adalah:",
                    options: [
                        { text: "Apa pun yang sedang tren", type: "trend" },
                        { text: "Klasik dan tak lekang waktu", type: "trend" },
                        { text: "Vintage atau retro", type: "reversion" },
                        { text: "Unik, avant-garde, atau DIY", type: "reversion" }
                    ]
                },
                {
                    question: "Bagaimana Anda memandang 'Risiko'?",
                    options: [
                        { text: "Sesuatu yang harus diminimalkan dan dikelola hati-hati", type: "trend" },
                        { text: "Dapat diterima hanya jika jalannya jelas", type: "trend" },
                        { text: "Biaya yang perlu untuk imbalan tinggi", type: "reversion" },
                        { text: "Tantangan menarik untuk ditaklukkan", type: "reversion" }
                    ]
                },
                {
                    question: "Saat merencanakan liburan, Anda lebih suka:",
                    options: [
                        { text: "Destinasi teratas di TripAdvisor", type: "trend" },
                        { text: "Tur terpadu untuk melihat semua sorotan", type: "trend" },
                        { text: "Lokasi yang jarang dikunjungi", type: "reversion" },
                        { text: "Pergi tanpa rencana dan menjelajah", type: "reversion" }
                    ]
                },
                {
                    question: "Jika 9 dari 10 orang setuju akan sesuatu, Anda berpikir:",
                    options: [
                        { text: "Mereka mungkin benar", type: "trend" },
                        { text: "Aman untuk setuju dengan mereka", type: "trend" },
                        { text: "Mari cek apa pendapat orang ke-10", type: "reversion" },
                        { text: "Mayoritas seringkali salah", type: "reversion" }
                    ]
                },
                {
                    question: "Dalam debat, Anda biasanya:",
                    options: [
                        { text: "Cari titik temu dan kesepakatan", type: "trend" },
                        { text: "Dukung argumen yang paling logis dan mapan", type: "trend" },
                        { text: "Bermain sebagai devil's advocate", type: "reversion" },
                        { text: "Membela pendapat yang tidak populer", type: "reversion" }
                    ]
                },
                {
                    question: "Saat Anda rugi uang dalam taruhan/investasi kecil, Anda:",
                    options: [
                        { text: "Berhenti dan cut loss segera", type: "trend" },
                        { text: "Tunggu untuk melihat apakah membaik", type: "trend" },
                        { text: "Lipat gandakan untuk menang balik", type: "reversion" },
                        { text: "Analisis kenapa saya salah dan coba sudut pandang lain", type: "reversion" }
                    ]
                },
                {
                    question: "Anda suka game yang melibatkan:",
                    options: [
                        { text: "Kemajuan stabil dan naik level", type: "trend" },
                        { text: "Kerja tim dan kooperasi", type: "trend" },
                        { text: "Menggertak dan psikologi", type: "reversion" },
                        { text: "Taruhan tinggi dan ayunan besar", type: "reversion" }
                    ]
                },
                {
                    question: "Grafik mana yang lebih menarik bagi Anda?",
                    options: [
                        { text: "Garis stabil naik tajam", type: "trend" },
                        { text: "Kurva mulus dengan sedikit volatilitas", type: "trend" },
                        { text: "Garis zigzag dengan puncak dan lembah tinggi", type: "reversion" },
                        { text: "Grafik yang baru jatuh dan mungkin memantul", type: "reversion" }
                    ]
                },
                {
                    question: "Saat belajar keahlian baru, Anda:",
                    options: [
                        { text: "Ikuti kurikulum standar langkah demi langkah", type: "trend" },
                        { text: "Tonton tutorial dari para ahli", type: "trend" },
                        { text: "Eksperimen dan belajar dari kesalahan", type: "reversion" },
                        { text: "Coba cari cara cepat atau jalan pintas", type: "reversion" }
                    ]
                },
                {
                    question: "Pendekatan Anda terhadap tradisi adalah:",
                    options: [
                        { text: "Hormati dan pertahankan", type: "trend" },
                        { text: "Ikuti kecuali berbahaya", type: "trend" },
                        { text: "Pertanyakan relevansinya saat ini", type: "reversion" },
                        { text: "Hancurkan untuk ciptakan sesuatu yang baru", type: "reversion" }
                    ]
                },
                {
                    question: "Jika harga rumah di daerah Anda meroket, Anda:",
                    options: [
                        { text: "Pikir itu investasi bagus dan beli", type: "trend" },
                        { text: "Berharap sudah beli lebih awal", type: "trend" },
                        { text: "Pikir itu gelembung dan tunggu jatuh", type: "reversion" },
                        { text: "Cari daerah lebih murah yang belum booming", type: "reversion" }
                    ]
                },
                {
                    question: "Sukses bagi Anda berarti:",
                    options: [
                        { text: "Stabilitas dan keamanan", type: "trend" },
                        { text: "Mengikuti jalur karir yang terbukti", type: "trend" },
                        { text: "Berinovasi dan mendisrupsi", type: "reversion" },
                        { text: "Mengalahkan sistem", type: "reversion" }
                    ]
                },
                {
                    question: "Saat hadapi masalah, Anda:",
                    options: [
                        { text: "Gunakan solusi terbukti", type: "trend" },
                        { text: "Minta nasihat", type: "trend" },
                        { text: "Ciptakan solusi baru", type: "reversion" },
                        { text: "Ubah masalah jadi peluang", type: "reversion" }
                    ]
                },
                {
                    question: "Mengenai perubahan, Anda:",
                    options: [
                        { text: "Suka perubahan bertahap dan terprediksi", type: "trend" },
                        { text: "Beradaptasi bila perlu", type: "trend" },
                        { text: "Berkembang dalam kekacauan", type: "reversion" },
                        { text: "Memulai perubahan sendiri", type: "reversion" }
                    ]
                },
                {
                    question: "Lingkungan kerja ideal Anda adalah:",
                    options: [
                        { text: "Terstruktur dengan hierarki jelas", type: "trend" },
                        { text: "Kolaboratif dan harmonis", type: "trend" },
                        { text: "Fleksibel dan mandiri", type: "reversion" },
                        { text: "Kompetitif dan serba cepat", type: "reversion" }
                    ]
                },
                {
                    question: "Saat mendengar rumor, Anda:",
                    options: [
                        { text: "Tunggu konfirmasi resmi", type: "trend" },
                        { text: "Asumsikan mungkin benar jika banyak yang bilang", type: "trend" },
                        { text: "Investigasi sumbernya dengan skeptis", type: "reversion" },
                        { text: "Bertaruh melawannya menjadi lugas", type: "reversion" }
                    ]
                },
                {
                    question: "Kesabaran adalah:",
                    options: [
                        { text: "Kebajikan penting untuk sukses", type: "trend" },
                        { text: "Perlu untuk hindari kesalahan", type: "trend" },
                        { text: "Kadang peluang yang terlewatkan", type: "reversion" },
                        { text: "Membosankan, saya lebih suka aksi", type: "reversion" }
                    ]
                },
                {
                    question: "Terakhir, bagaimana Anda melihat dunia?",
                    options: [
                        { text: "Sebagai tempat keteraturan dan pola", type: "trend" },
                        { text: "Umumnya dapat diprediksi", type: "trend" },
                        { text: "Siklus dan repetitif", type: "reversion" },
                        { text: "Acak dan penuh kejutan", type: "reversion" }
                    ]
                }
            ]
        },
        tradingPlan: {
            title: "Rencana Trading",
            subtitle: "Sistem Fractal Horizon",
            marketSetup: {
                title: "Pasar & Setup",
                marketTimeframe: {
                    title: "Pasar dan Timeframe",
                    content: "<strong>XAUUSD</strong> (Emas) menggunakan Time Frame <strong>Harian</strong>"
                },
                tradingSession: {
                    title: "Sesi Trading",
                    content: "Cek pasar setiap hari jam <strong>08:00 WIB</strong>"
                },
                entryIdeas: {
                    title: "Ide Masuk (Bill Williams' Fractal Breakout)",
                    long: "<strong>Setup Long:</strong> Identifikasi Fraktal Naik terbaru (puncak yang dikelilingi oleh dua puncak lebih rendah di setiap sisi). Tempatkan order Buy Stop pending di atas fraktal ini.",
                    short: "<strong>Setup Short:</strong> Identifikasi Fraktal Turun terbaru (lembah yang dikelilingi oleh dua lembah lebih tinggi di setiap sisi). Tempatkan order Sell Stop pending di bawah fraktal ini.",
                    logic: "<strong>Logika:</strong> Penembusan level fraktal menunjukkan pasar telah melampaui titik balik perilaku sebelumnya, menandakan potensi dimulainya tren atau breakout yang kuat."
                },
                entryMethod: {
                    title: "Metode Masuk",
                    content: "Gunakan pending stop order <strong>+/- 20 pips (200 poin)</strong> dari level harga Fraktal."
                }
            },
            execution: {
                title: "Aturan Eksekusi",
                stopLoss: {
                    title: "Stop Loss",
                    content: "Gunakan fixed stop loss berdasarkan support/resistance struktural terbaru atau fraktal berlawanan sebelumnya."
                },
                exitStrategy: {
                    title: "Strategi Keluar",
                    content: "Gunakan fixed take profit."
                },
                stopOutStrategy: {
                    title: "Strategi Stop-out",
                    content: "Masuk kembali jika harga masih valid sesuai aturan."
                },
                runningTrade: {
                    title: "Strategi Trade Berjalan",
                    content: "Masuk pasar lagi <strong>maksimal 2 kali</strong> jika harga bergerak menguntungkan kita (pyramiding)."
                },
                haltProtocol: {
                    title: "Protokol Hentikan Trading",
                    content: "Jika 'nyawa' maksimal pada setiap pair habis atau persentase risiko maksimal tercapai dalam sebulan sebesar <strong>10%</strong>, maka <strong>HENTIKAN</strong> trading untuk bulan tersebut."
                },
                indicators: {
                    title: "Indikator",
                    content: "Bill Williams' Fractal (pengaturan default)"
                }
            },
            moneyManagement: {
                title: "Manajemen Uang",
                drawdown: {
                    title: "Protokol Drawdown",
                    content: "<strong>Mengurangi Ukuran Notional:</strong> Ketika ekuitas turun 10% dari modal, notional turun 20%, dan seterusnya. Lindungi modal inti."
                },
                onProfit: {
                    title: "Protokol Saat Profit",
                    content: "<strong>Buffer Profit:</strong> Gunakan risiko yang sama per trade jika profit ekuitas masih &lt; 20% dari ekuitas awal. Jika tidak, cek di awal setiap bulan untuk menyesuaikan risiko."
                },
                riskReward: {
                    title: "Target Risk-Reward",
                    content: "<strong>1 : 1.1 RR</strong>",
                    note: "(Ekstra 0.1 adalah untuk biaya spread, komisi, dan swap)"
                },
                riskAllocation: {
                    title: "Alokasi Risiko",
                    content: "<strong>Maksimal 1%</strong> dari total ekuitas pada setiap trade."
                },
                positionSizing: {
                    title: "Ukuran Posisi",
                    content: "Kategorikan risiko berdasarkan volatilitas. Sesuaikan ukuran lot agar risiko 1% selaras dengan jarak stop loss."
                },
                riskExposure: {
                    title: "Eksposur Risiko (Bulanan)",
                    content: "<strong>Maksimal 10%</strong> dari total ekuitas. (Sesuaikan setiap bulan berdasarkan aturan Protokol Drawdown dan Saat Profit)."
                }
            },
            mindset: {
                title: "Pola Pikir Trader",
                points: [
                    "Jaga kerugian Anda tetap kecil",
                    "Tidak ada mentalitas supermarket di CFD, kita tidak pernah tahu ke mana arah pasar",
                    "Tanpa ekspektasi, ikuti aturan",
                    "Beradaptasi dengan pasar, nol ego",
                    "Selalu rendah hati, atau pasar akan merendahkan Anda",
                    "Gunakan model ekuitas dan manajemen risiko yang realistis",
                    "Ada trader tua dan ada trader berani, tapi tidak ada trader tua yang berani, jadi selalu gunakan stop loss",
                    "Ada keacakan total dalam setiap trade, tapi ada keteraturan dalam 100 trade yang memiliki keunggulan",
                    "Tidak ada korelasi peristiwa masa lalu dengan masa depan, setiap momen di pasar adalah unik, fokus sekarang dan eksekusi rencana tanpa cela",
                    "Apa pun bisa terjadi di pasar, benar-benar apa pun",
                    "Trader adalah manajer risiko dan portofolio, bukan skema cepat kaya",
                    "Anda tidak bisa mengendalikan pasar, tapi Anda bisa mengendalikan cara Anda trading",
                    "Bersikaplah agnostik saat masuk pasar, Anda di sini untuk cari uang, bukan ramal nasib",
                    "Jadilah arogan dan percaya pada perjalanan Anda sendiri akan membawa kesuksesan"
                ]
            },
            affirmation: {
                title: "Afirmasi Harian (Mark Douglas)",
                points: [
                    "Saya sepenuhnya ikhlas mengeluarkan uang yang dikatakan oleh keunggulan saya untuk mengetahui apakah trade berikutnya ini akan menjadi pemenang.",
                    "Hasil dari setiap keunggulan individu dalam satu seri adalah peristiwa unik yang tidak memiliki hubungan nyata dengan hasil sebelumnya atau masa depan.",
                    "Keberagaman niat trader lain yang akan mengirimkan order beli dan jual ke aliran pasar dapat menyebabkan apa pun terjadi.",
                    "Karena saya tidak tahu apa yang akan terjadi, saya akan mengeksekusi sinyal yang dipanggil oleh keunggulan saya dan siap untuk menang dan mengambil profit atau membayar biaya, saya baik-baik saja dengan keduanya.",
                    "Risiko keunggulan tidak berhasil selalu ada. Setiap prediksi dari analisis saya selalu merupakan tebakan.",
                    "Menangkap peluang menguntungkan dalam keunggulan saya selama serangkaian trade tidak ada hubungannya dengan menjadi benar atau salah.",
                    "Karena saya tidak tahu imbalan atau hasil dari peluang tersebut, saya sepenuhnya patuh untuk mengeksekusi rencana trading saya tanpa cela dan mengelola risiko sebelum masuk ke trade saya."
                ]
            }
        },
        moneyManagement: {
            title: "Manajemen Uang",
            subtitle: "Lindungi modal Anda dengan pengaturan ukuran posisi yang tepat",
            seoTitle: "Kalkulator Manajemen Uang | Penguasaan Trading",
            accountSettings: "Pengaturan Akun",
            totalEquity: "Total Ekuitas ($)",
            riskPerTrade: "Risiko per Trade (%)",
            stopLossSize: "Ukuran Stop Loss (%)",
            stopLossTooltip: "Jarak ke titik pembatalan Anda",
            helperPosSize: "Diperlukan untuk menghitung Ukuran Posisi",
            monthlyRiskLimit: "Batas Risiko Total per Bulan (%)",
            positionSize: "Ukuran Posisi (Jumlah Entri)",
            posSizeSubtext: "Dengan stop loss {sl}%, beli jumlah ini untuk merisiko tepat ${risk}.",
            riskAmount: "Jumlah Risiko",
            riskSubtext: "Kerugian maksimal per trade",
            monthlyLimit: "Batas Bulanan",
            monthlySubtext: "Kerugian maksimal per bulan",
            drawdownAnalysis: "Analisis Drawdown",
            tradesToMonthlyLimit: "Trade untuk mencapai Batas Bulanan:",
            tradesToExhaust: "Trade untuk Menghabiskan Ekuitas:",
            tradesUnit: "{count} trade",
            disclaimer: "*Asumsi risiko dolar tetap berdasarkan ekuitas awal."
        },
        tradingJournal: {
            title: "Jurnal Trading",
            subtitle: "Log Refleksi Harian",
            reminders: {
                title: "Pengingat Diri",
                points: [
                    "Selalu arogan secara agnostik, mudah beradaptasi, rendah hati.",
                    "Rencanakan dan kelola risiko Anda.",
                    "Bermainlah seperti bandar kasino, biarkan probabilitas bekerja.",
                    "Diversifikasi.",
                    "Keunggulan dapat terbentuk dari pola tertentu yang memiliki keuntungan statistik.",
                    "Pelajari matematika dan Anda akan mempelajari pasar.",
                    "Tidak ada yang benar di pasar - setiap strategi bisa berhasil.",
                    "Jangan trading di noise.",
                    "Buat sistem sederhana, mekanis, dan mudah diulang.",
                    "Disiplin dan sabar, hanya masuk sesuai sistem.",
                    "Fortis fortuna adiuvat, keberuntungan memihak yang berani.",
                    "Jangan harapkan apa pun, hargai segalanya (terutama yang ada saat ini)."
                ]
            },
            logEntries: [
                {
                    date: "31 Des",
                    sections: [
                        {
                            title: "Apa yang Saya Rasakan Saat Masuk Pasar",
                            content: "Sedikit ragu. Saya rasa ini karena kita tidak boleh kehilangan momentum"
                        },
                        {
                            title: "Apa yang Saya Rasakan Saat SL Terkena",
                            content: "Agak sedih tapi tidak apa-apa"
                        },
                        {
                            title: "Apa yang Saya Rasakan Saat TP Terkena",
                            content: "Senang. Tapi jangan terjebak dalam perasaan senang itu"
                        },
                        {
                            title: "Apa yang Saya Pelajari / Tingkatkan Hari Ini",
                            content: "Saya rasa saya mulai masuk ke fase 'analysis paralysis'."
                        },
                        {
                            title: "Perubahan yang Perlu Saya Buat Mulai Hari Ini",
                            content: "Jaga aturan tetap sederhana dan mendetail"
                        },
                        {
                            title: "Apa yang Dilakukan Besok",
                            content: "Lihat saja bagaimana strategi berjalan"
                        },
                        { title: "Ikhtisar", content: "Minggu trading yang bagus" }
                    ]
                }
            ]
        },
        footer: {
            tagline: "Membangun Trader yang Lebih Baik, Satu Pelajaran Sekaligus",
            modules: "Modul Pembelajaran",
            rights: "Trading Mastery. Hak cipta dilindungi undang-undang. Hanya untuk tujuan edukasi."
        }
    }
};

export const t = derived(currentLang, ($lang) => translations[$lang]);
