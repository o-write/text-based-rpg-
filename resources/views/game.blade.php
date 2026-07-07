<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Text-Based RPG Game — Battle enemies, draw strength from wisdom, and emerge victorious." />
    <title>⚔️ Chronicles of the Abyss — RPG Game</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&family=Crimson+Text:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           CSS RESET & CUSTOM PROPERTIES
           ============================================================ */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --clr-bg:           #0a0a0f;
            --clr-surface:      #12121a;
            --clr-surface-2:    #1a1a28;
            --clr-surface-3:    #21213a;
            --clr-border:       rgba(255,255,255,0.07);
            --clr-border-glow:  rgba(138,92,246,0.35);

            --clr-primary:      #8a5cf6;
            --clr-primary-dark: #6d3ed4;
            --clr-accent:       #f59e0b;
            --clr-accent-dark:  #d97706;

            --clr-player:       #22d3ee;
            --clr-player-dark:  #0891b2;
            --clr-enemy:        #f43f5e;
            --clr-enemy-dark:   #be123c;
            --clr-heal:         #4ade80;
            --clr-heal-dark:    #16a34a;

            --clr-text:         #e2e8f0;
            --clr-text-muted:   #94a3b8;
            --clr-text-dim:     #64748b;

            --radius-sm:        6px;
            --radius-md:        12px;
            --radius-lg:        18px;
            --radius-xl:        24px;

            --shadow-glow-purple: 0 0 20px rgba(138,92,246,0.4), 0 0 60px rgba(138,92,246,0.1);
            --shadow-glow-cyan:   0 0 20px rgba(34,211,238,0.4),  0 0 60px rgba(34,211,238,0.1);
            --shadow-glow-red:    0 0 20px rgba(244,63,94,0.4),   0 0 60px rgba(244,63,94,0.1);
            --shadow-glow-gold:   0 0 20px rgba(245,158,11,0.4),  0 0 60px rgba(245,158,11,0.1);
        }

        /* ============================================================
           BASE
           ============================================================ */
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--clr-bg);
            color: var(--clr-text);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated star-field background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(138,92,246,0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(34,211,238,0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 60% 80%, rgba(244,63,94,0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* Subtle scanline overlay */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,0,0,0.03) 2px,
                rgba(0,0,0,0.03) 4px
            );
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 20px 60px;
        }

        /* ============================================================
           HEADER / TITLE
           ============================================================ */
        .game-header {
            text-align: center;
            padding: 40px 0 32px;
            position: relative;
        }

        .game-header::after {
            content: '';
            display: block;
            width: 220px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--clr-primary), transparent);
            margin: 20px auto 0;
        }

        .game-title {
            font-family: 'Cinzel', serif;
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 900;
            letter-spacing: 0.04em;
            background: linear-gradient(135deg, #c4b5fd 0%, #8a5cf6 40%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            line-height: 1.15;
        }

        .game-subtitle {
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
            letter-spacing: 0.25em;
            color: var(--clr-text-muted);
            text-transform: uppercase;
            margin-top: 8px;
        }

        /* ============================================================
           GLASS CARD BASE
           ============================================================ */
        .glass-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-lg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* ============================================================
           BATTLE ARENA
           ============================================================ */
        .battle-arena {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 16px;
            align-items: center;
            margin-bottom: 24px;
        }

        /* Fighter Cards */
        .fighter-card {
            padding: 28px 24px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--clr-border);
            background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .fighter-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .fighter-card.player::before { background: linear-gradient(90deg, transparent, var(--clr-player), transparent); }
        .fighter-card.enemy::before  { background: linear-gradient(90deg, transparent, var(--clr-enemy),  transparent); }

        .fighter-card.player { border-color: rgba(34,211,238,0.2); }
        .fighter-card.enemy  { border-color: rgba(244,63,94,0.2); }

        .fighter-card.player:hover { box-shadow: var(--shadow-glow-cyan); border-color: rgba(34,211,238,0.4); }
        .fighter-card.enemy:hover  { box-shadow: var(--shadow-glow-red);  border-color: rgba(244,63,94,0.4); }

        .fighter-avatar {
            font-size: 4rem;
            text-align: center;
            display: block;
            margin-bottom: 12px;
            filter: drop-shadow(0 0 8px currentColor);
            animation: float 3s ease-in-out infinite;
        }

        .fighter-card.enemy .fighter-avatar { animation-delay: -1.5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-6px); }
        }

        .fighter-name {
            font-family: 'Cinzel', serif;
            font-size: 1rem;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        .fighter-card.player .fighter-name { color: var(--clr-player); }
        .fighter-card.enemy  .fighter-name { color: var(--clr-enemy); }

        .fighter-title {
            font-size: 0.7rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--clr-text-dim);
            text-align: center;
            margin-bottom: 20px;
        }

        /* HP Display */
        .hp-display {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
        }

        .hp-label {
            font-size: 0.7rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--clr-text-dim);
        }

        .hp-value {
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .fighter-card.player .hp-value { color: var(--clr-player); }
        .fighter-card.enemy  .hp-value { color: var(--clr-enemy); }

        .hp-max {
            font-size: 0.8rem;
            color: var(--clr-text-dim);
        }

        /* HP Bar */
        .hp-bar-track {
            width: 100%;
            height: 10px;
            background: rgba(255,255,255,0.06);
            border-radius: 99px;
            overflow: hidden;
            border: 1px solid var(--clr-border);
        }

        .hp-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .hp-bar-fill::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 40%;
            background: rgba(255,255,255,0.3);
            border-radius: 99px;
        }

        .fighter-card.player .hp-bar-fill {
            background: linear-gradient(90deg, var(--clr-player-dark), var(--clr-player));
            box-shadow: 0 0 8px rgba(34,211,238,0.6);
        }

        .fighter-card.enemy .hp-bar-fill {
            background: linear-gradient(90deg, var(--clr-enemy-dark), var(--clr-enemy));
            box-shadow: 0 0 8px rgba(244,63,94,0.6);
        }

        /* HP bar color changes as it depletes */
        .hp-bar-fill.warning {
            background: linear-gradient(90deg, #d97706, #f59e0b) !important;
            box-shadow: 0 0 8px rgba(245,158,11,0.6) !important;
        }

        .hp-bar-fill.critical {
            background: linear-gradient(90deg, #9f1239, #f43f5e) !important;
            box-shadow: 0 0 12px rgba(244,63,94,0.8) !important;
        }

        .hp-bar-fill.dead {
            background: #374151 !important;
            box-shadow: none !important;
        }

        /* VS Badge */
        .vs-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .vs-text {
            font-family: 'Cinzel', serif;
            font-size: 1.6rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--clr-primary), var(--clr-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.05em;
        }

        .vs-orb {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 1px solid var(--clr-border-glow);
            background: radial-gradient(circle at 35% 35%, rgba(138,92,246,0.3), rgba(0,0,0,0.5));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            animation: pulse-orb 2s ease-in-out infinite;
        }

        @keyframes pulse-orb {
            0%, 100% { box-shadow: 0 0 10px rgba(138,92,246,0.4); }
            50%       { box-shadow: 0 0 25px rgba(138,92,246,0.7), 0 0 50px rgba(138,92,246,0.2); }
        }

        /* ============================================================
           ACTION BUTTONS
           ============================================================ */
        .action-panel {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            border: none;
            border-radius: var(--radius-md);
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
            transition: left 0.4s ease;
        }

        .btn:hover::before { left: 100%; }

        .btn:active { transform: scale(0.97); }

        .btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn:disabled::before { display: none; }

        /* Attack Button */
        .btn-attack {
            background: linear-gradient(135deg, #9f1239, var(--clr-enemy));
            color: #fff;
            border: 1px solid rgba(244,63,94,0.4);
            box-shadow: 0 4px 15px rgba(244,63,94,0.25);
        }

        .btn-attack:not(:disabled):hover {
            box-shadow: var(--shadow-glow-red);
            transform: translateY(-2px);
            border-color: var(--clr-enemy);
        }

        /* Motivation Button */
        .btn-motivation {
            background: linear-gradient(135deg, var(--clr-primary-dark), var(--clr-primary));
            color: #fff;
            border: 1px solid rgba(138,92,246,0.4);
            box-shadow: 0 4px 15px rgba(138,92,246,0.25);
        }

        .btn-motivation:not(:disabled):hover {
            box-shadow: var(--shadow-glow-purple);
            transform: translateY(-2px);
            border-color: var(--clr-primary);
        }

        /* Reset Button */
        .btn-reset {
            background: transparent;
            color: var(--clr-text-muted);
            border: 1px solid var(--clr-border);
            font-size: 0.75rem;
            padding: 10px 20px;
        }

        .btn-reset:hover {
            background: rgba(255,255,255,0.04);
            color: var(--clr-text);
            border-color: rgba(255,255,255,0.15);
            transform: none;
        }

        .btn-icon { font-size: 1.1rem; }

        /* ============================================================
           BOTTOM GRID (Battle Log + Motivation Dashboard)
           ============================================================ */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        /* ============================================================
           BATTLE LOG
           ============================================================ */
        .battle-log {
            padding: 20px 24px;
        }

        .section-title {
            font-family: 'Cinzel', serif;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--clr-text-dim);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--clr-border);
        }

        .log-entries {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 260px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .log-entries::-webkit-scrollbar { width: 3px; }
        .log-entries::-webkit-scrollbar-track { background: transparent; }
        .log-entries::-webkit-scrollbar-thumb {
            background: var(--clr-primary);
            border-radius: 99px;
        }

        .log-entry {
            font-size: 0.82rem;
            line-height: 1.6;
            color: var(--clr-text-muted);
            padding: 10px 14px;
            background: rgba(255,255,255,0.025);
            border-radius: var(--radius-sm);
            border-left: 2px solid var(--clr-border);
            animation: slide-in 0.3s ease;
        }

        .log-entry:first-child {
            border-left-color: var(--clr-primary);
            background: rgba(138,92,246,0.06);
            color: var(--clr-text);
        }

        @keyframes slide-in {
            from { opacity: 0; transform: translateX(-10px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .log-empty {
            font-size: 0.82rem;
            color: var(--clr-text-dim);
            text-align: center;
            padding: 30px 0;
            font-style: italic;
        }

        /* Inline coloured spans in log messages */
        .log-dmg       { color: var(--clr-enemy);  font-weight: 600; }
        .log-enemy-dmg { color: #fb923c;            font-weight: 600; }
        .log-heal      { color: var(--clr-heal);    font-weight: 600; }
        .log-victory   { color: var(--clr-accent);  font-weight: 700; }
        .log-gameover  { color: var(--clr-enemy);   font-weight: 700; }

        /* ============================================================
           MOTIVATION DASHBOARD
           ============================================================ */
        .motivation-dashboard {
            padding: 24px;
            border-color: rgba(138,92,246,0.2);
            position: relative;
        }

        .motivation-dashboard::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 50% 50%, rgba(138,92,246,0.05) 0%, transparent 60%);
            pointer-events: none;
        }

        .motivation-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
            gap: 10px;
            color: var(--clr-text-dim);
        }

        .motivation-empty-icon { font-size: 2rem; opacity: 0.4; }

        .motivation-empty-text {
            font-size: 0.8rem;
            text-align: center;
            font-style: italic;
            line-height: 1.6;
        }

        /* Scrollable quote list */
        .motivation-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 290px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .motivation-list::-webkit-scrollbar { width: 3px; }
        .motivation-list::-webkit-scrollbar-track { background: transparent; }
        .motivation-list::-webkit-scrollbar-thumb {
            background: var(--clr-primary);
            border-radius: 99px;
        }

        /* Individual quote card in the list */
        .motivation-quote-card {
            background: rgba(138,92,246,0.06);
            border: 1px solid rgba(138,92,246,0.15);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            position: relative;
            animation: fade-rise 0.4s ease;
        }

        .motivation-quote-card:first-child {
            border-color: rgba(138,92,246,0.35);
            background: rgba(138,92,246,0.1);
            box-shadow: 0 0 12px rgba(138,92,246,0.15);
        }

        .motivation-quote-card:first-child::after {
            content: 'LATEST';
            position: absolute;
            top: 10px; right: 12px;
            font-size: 0.6rem;
            letter-spacing: 0.12em;
            font-weight: 700;
            color: var(--clr-primary);
            background: rgba(138,92,246,0.15);
            padding: 2px 7px;
            border-radius: 99px;
            border: 1px solid rgba(138,92,246,0.3);
        }

        @keyframes fade-rise {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .quote-marks {
            font-family: 'Crimson Text', serif;
            font-size: 3rem;
            line-height: 0.5;
            color: var(--clr-primary);
            opacity: 0.35;
            display: block;
            margin-bottom: 6px;
        }

        .quote-text {
            font-family: 'Crimson Text', serif;
            font-size: 0.97rem;
            font-style: italic;
            color: var(--clr-text);
            line-height: 1.65;
            margin-bottom: 10px;
        }

        .quote-author {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--clr-primary);
            font-weight: 600;
        }

        .quote-author::before {
            content: '';
            display: inline-block;
            width: 18px;
            height: 1px;
            background: var(--clr-primary);
        }

        /* ============================================================
           WIN / LOSS OVERLAYS
           ============================================================ */
        .game-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: overlay-in 0.5s ease;
        }

        @keyframes overlay-in {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .game-overlay.victory-overlay {
            background: radial-gradient(ellipse at center, rgba(245,158,11,0.15) 0%, rgba(10,10,15,0.92) 70%);
            backdrop-filter: blur(6px);
        }

        .game-overlay.gameover-overlay {
            background: radial-gradient(ellipse at center, rgba(244,63,94,0.15) 0%, rgba(10,10,15,0.95) 70%);
            backdrop-filter: blur(6px);
        }

        .overlay-content {
            text-align: center;
            padding: 60px 48px;
            border-radius: var(--radius-xl);
            border: 1px solid;
            max-width: 500px;
            width: 90%;
        }

        .victory-overlay .overlay-content {
            border-color: rgba(245,158,11,0.4);
            background: linear-gradient(135deg, rgba(245,158,11,0.08), rgba(10,10,15,0.9));
            box-shadow: var(--shadow-glow-gold);
        }

        .gameover-overlay .overlay-content {
            border-color: rgba(244,63,94,0.4);
            background: linear-gradient(135deg, rgba(244,63,94,0.08), rgba(10,10,15,0.9));
            box-shadow: var(--shadow-glow-red);
        }

        .overlay-emoji {
            font-size: 5rem;
            display: block;
            margin-bottom: 16px;
            animation: bounce-in 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) 0.2s both;
        }

        @keyframes bounce-in {
            0%   { transform: scale(0); }
            60%  { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .overlay-title {
            font-family: 'Cinzel', serif;
            font-size: clamp(1.8rem, 5vw, 2.8rem);
            font-weight: 900;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .victory-overlay .overlay-title {
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #fcd34d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gameover-overlay .overlay-title {
            background: linear-gradient(135deg, #fb7185, #f43f5e, #e11d48);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .overlay-subtitle {
            font-size: 0.9rem;
            color: var(--clr-text-muted);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        /* ============================================================
           BOTTOM RESET STRIP
           ============================================================ */
        .reset-strip {
            text-align: center;
            padding-top: 8px;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 700px) {
            .battle-arena {
                grid-template-columns: 1fr;
                grid-template-rows: auto auto auto;
            }

            .vs-badge { flex-direction: row; }

            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ============================================================
           UTILITY
           ============================================================ */
        form { display: contents; }
    </style>
</head>
<body>

{{-- ================================================================
     WIN / LOSS OVERLAYS (rendered on top of everything)
     ================================================================ --}}
@if($gameState === 'victory')
<div class="game-overlay victory-overlay" id="victory-overlay" role="dialog" aria-modal="true" aria-label="Victory">
    <div class="overlay-content">
        <span class="overlay-emoji">🏆</span>
        <h2 class="overlay-title">Victory!</h2>
        <p class="overlay-subtitle">You have vanquished the enemy.<br>Your legend shall be sung for ages.</p>
        <form action="{{ route('game.reset') }}" method="POST">
            @csrf
            <button id="btn-play-again" type="submit" class="btn btn-motivation">
                <span class="btn-icon">⚔️</span> Play Again
            </button>
        </form>
    </div>
</div>
@endif

@if($gameState === 'gameover')
<div class="game-overlay gameover-overlay" id="gameover-overlay" role="dialog" aria-modal="true" aria-label="Game Over">
    <div class="overlay-content">
        <span class="overlay-emoji">💀</span>
        <h2 class="overlay-title">Game Over</h2>
        <p class="overlay-subtitle">You have fallen in battle.<br>The darkness claims another soul.</p>
        <form action="{{ route('game.reset') }}" method="POST">
            @csrf
            <button id="btn-try-again" type="submit" class="btn btn-attack">
                <span class="btn-icon">🔄</span> Try Again
            </button>
        </form>
    </div>
</div>
@endif

{{-- ================================================================
     MAIN WRAPPER
     ================================================================ --}}
<div class="wrapper">

    {{-- HEADER --}}
    <header class="game-header">
        <h1 class="game-title">⚔️ Chronicles of the Abyss</h1>
        <p class="game-subtitle">Text-Based RPG &nbsp;·&nbsp; Turn-Based Combat</p>
    </header>

    {{-- ============================================================
         BATTLE ARENA
         ============================================================ --}}
    @php
        $playerPct = $maxHp > 0 ? round(($playerHp / $maxHp) * 100) : 0;
        $enemyPct  = $maxHp > 0 ? round(($enemyHp  / $maxHp) * 100) : 0;

        $playerBarClass = $playerPct <= 0 ? 'dead' : ($playerPct <= 25 ? 'critical' : ($playerPct <= 50 ? 'warning' : ''));
        $enemyBarClass  = $enemyPct  <= 0 ? 'dead' : ($enemyPct  <= 25 ? 'critical' : ($enemyPct  <= 50 ? 'warning' : ''));

        $gameOver = in_array($gameState, ['victory', 'gameover']);
    @endphp

    <section class="battle-arena" aria-label="Battle Arena">

        {{-- PLAYER CARD --}}
        <div class="fighter-card glass-card player">
            <span class="fighter-avatar" role="img" aria-label="Player character">🧙‍♂️</span>
            <div class="fighter-name">Hero</div>
            <div class="fighter-title">Wandering Sage</div>

            <div class="hp-display">
                <span class="hp-label">HP</span>
                <span class="hp-value" id="player-hp">{{ $playerHp }}</span>
                <span class="hp-max">/ {{ $maxHp }}</span>
            </div>
            <div class="hp-bar-track" role="progressbar" aria-valuenow="{{ $playerHp }}" aria-valuemin="0" aria-valuemax="{{ $maxHp }}" aria-label="Player HP">
                <div class="hp-bar-fill {{ $playerBarClass }}" id="player-hp-bar" style="width: {{ $playerPct }}%"></div>
            </div>
        </div>

        {{-- VS BADGE --}}
        <div class="vs-badge" aria-hidden="true">
            <div class="vs-orb">⚡</div>
            <span class="vs-text">VS</span>
        </div>

        {{-- ENEMY CARD --}}
        <div class="fighter-card glass-card enemy">
            <span class="fighter-avatar" role="img" aria-label="Enemy character">🐉</span>
            <div class="fighter-name">Shadow Drake</div>
            <div class="fighter-title">Ancient Warlord</div>

            <div class="hp-display">
                <span class="hp-label">HP</span>
                <span class="hp-value" id="enemy-hp">{{ $enemyHp }}</span>
                <span class="hp-max">/ {{ $maxHp }}</span>
            </div>
            <div class="hp-bar-track" role="progressbar" aria-valuenow="{{ $enemyHp }}" aria-valuemin="0" aria-valuemax="{{ $maxHp }}" aria-label="Enemy HP">
                <div class="hp-bar-fill {{ $enemyBarClass }}" id="enemy-hp-bar" style="width: {{ $enemyPct }}%"></div>
            </div>
        </div>

    </section>

    {{-- ============================================================
         ACTION BUTTONS
         ============================================================ --}}
    <section class="action-panel" aria-label="Battle Actions">

        <form action="{{ route('game.attack') }}" method="POST">
            @csrf
            <button id="btn-attack" type="submit" class="btn btn-attack" {{ $gameOver ? 'disabled' : '' }}>
                <span class="btn-icon">⚔️</span> Attack
            </button>
        </form>

        <form action="{{ route('game.motivation') }}" method="POST">
            @csrf
            <button id="btn-motivation" type="submit" class="btn btn-motivation" {{ $gameOver ? 'disabled' : '' }}>
                <span class="btn-icon">✨</span> Get Motivation
            </button>
        </form>

    </section>

    {{-- ============================================================
         BOTTOM GRID — Battle Log & Motivation Dashboard
         ============================================================ --}}
    <div class="bottom-grid">

        {{-- BATTLE LOG --}}
        <div class="battle-log glass-card" role="log" aria-live="polite" aria-label="Battle Log">
            <h2 class="section-title">📜 Battle Log</h2>
            <div class="log-entries" id="battle-log-entries">
                @forelse($battleLog as $entry)
                    <div class="log-entry">{!! $entry !!}</div>
                @empty
                    <div class="log-empty">The battle has not yet begun…<br>Choose your action above.</div>
                @endforelse
            </div>
        </div>

        {{-- MOTIVATION DASHBOARD --}}
        <div class="motivation-dashboard glass-card" role="complementary" aria-label="Motivation Dashboard">
            <h2 class="section-title">💫 Motivation Dashboard</h2>

            @if(!empty($motivation))
                <div class="motivation-list" id="motivation-list" aria-live="polite">
                    @foreach($motivation as $quote)
                        <div class="motivation-quote-card">
                            <span class="quote-marks" aria-hidden="true">&ldquo;</span>
                            <p class="quote-text">{{ $quote['text'] }}</p>
                            <div class="quote-author">{{ $quote['author'] }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="motivation-empty" id="motivation-empty">
                    <span class="motivation-empty-icon" aria-hidden="true">📖</span>
                    <p class="motivation-empty-text">
                        Use <strong>Get Motivation</strong> to draw a quote from the ancient scrolls and restore your spirit.
                    </p>
                </div>
            @endif
        </div>

    </div>

    {{-- ============================================================
         RESET STRIP
         ============================================================ --}}
    <div class="reset-strip">
        <form action="{{ route('game.reset') }}" method="POST">
            @csrf
            <button id="btn-reset" type="submit" class="btn btn-reset">
                🔄 Reset Game
            </button>
        </form>
    </div>

</div>{{-- /.wrapper --}}

</body>
</html>
