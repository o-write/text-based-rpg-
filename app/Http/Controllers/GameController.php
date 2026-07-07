<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GameController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Game Constants
    |--------------------------------------------------------------------------
    */
    private const DEFAULT_HP       = 100;
    private const MAX_HP           = 100;
    private const PLAYER_ATK_MIN   = 10;
    private const PLAYER_ATK_MAX   = 20;
    private const ENEMY_ATK_MIN    = 5;
    private const ENEMY_ATK_MAX    = 15;
    private const HEAL_AMOUNT      = 20;
    private const QUOTES_API_URL   = 'http://127.0.0.1:8001/api/quotes';
    private const RANDOM_API_URL   = 'http://127.0.0.1:8001/api/quote/random';
    private const MAX_LOG_ENTRIES  = 10;

    /*
    |--------------------------------------------------------------------------
    | index() — Show the main game screen
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        // Bootstrap session state on first visit
        if (! $request->session()->has('playerHp')) {
            $this->initSession($request);
        }

        return view('game', $this->getState($request));
    }

    /*
    |--------------------------------------------------------------------------
    | attack() — Player attacks the enemy, enemy counter-attacks
    |--------------------------------------------------------------------------
    */
    public function attack(Request $request)
    {
        // Do nothing if the game is already over
        if ($this->gameIsOver($request)) {
            return redirect()->route('game.index');
        }

        $playerHp = $request->session()->get('playerHp');
        $enemyHp  = $request->session()->get('enemyHp');
        $log      = $request->session()->get('battleLog', []);

        // --- Player's Turn ---
        $playerDmg = rand(self::PLAYER_ATK_MIN, self::PLAYER_ATK_MAX);
        $enemyHp   = max(0, $enemyHp - $playerDmg);

        $turnLog = "⚔️ You attacked the enemy for <span class='log-dmg'>{$playerDmg} DMG</span>!";

        // Check if enemy died before counter-attack
        if ($enemyHp <= 0) {
            $request->session()->put('enemyHp', 0);
            $request->session()->put('playerHp', $playerHp);
            $request->session()->put('gameState', 'victory');
            $turnLog .= " 💀 <span class='log-victory'>The enemy has fallen! VICTORY!</span>";
            array_unshift($log, $turnLog);
            $request->session()->put('battleLog', array_slice($log, 0, self::MAX_LOG_ENTRIES));
            return redirect()->route('game.index');
        }

        // --- Enemy Counter-Attack ---
        $enemyDmg = rand(self::ENEMY_ATK_MIN, self::ENEMY_ATK_MAX);
        $playerHp = max(0, $playerHp - $enemyDmg);
        $turnLog .= " The enemy retaliated and dealt <span class='log-enemy-dmg'>{$enemyDmg} DMG</span> to you!";

        // Check if player died
        $gameState = 'playing';
        if ($playerHp <= 0) {
            $playerHp  = 0;
            $gameState = 'gameover';
            $turnLog  .= " 💔 <span class='log-gameover'>You have been defeated!</span>";
        }

        // Persist state
        $request->session()->put('playerHp', $playerHp);
        $request->session()->put('enemyHp', $enemyHp);
        $request->session()->put('gameState', $gameState);
        array_unshift($log, $turnLog);
        $request->session()->put('battleLog', array_slice($log, 0, self::MAX_LOG_ENTRIES));

        return redirect()->route('game.index');
    }

    /*
    |--------------------------------------------------------------------------
    | getMotivation() — Fetch a quote, heal player, enemy counter-attacks
    |--------------------------------------------------------------------------
    */
    public function getMotivation(Request $request)
    {
        // Do nothing if the game is already over
        if ($this->gameIsOver($request)) {
            return redirect()->route('game.index');
        }

        $playerHp = $request->session()->get('playerHp');
        $enemyHp  = $request->session()->get('enemyHp');
        $log      = $request->session()->get('battleLog', []);

        // --- Fetch Quote from Quotes API ---
        $quoteText = null;
        $author    = null;

        try {
            // Try the shared favorites list first
            $response = Http::timeout(5)->get(self::QUOTES_API_URL);

            if ($response->successful()) {
                $body = $response->json();

                if (
                    isset($body['success']) && $body['success'] === true
                    && is_array($body['data']) && count($body['data']) > 0
                ) {
                    // Pick a random quote from the saved favorites
                    $randomQuote = $body['data'][array_rand($body['data'])];
                    $quoteText   = $randomQuote['quote_text'] ?? null;
                    $author      = $randomQuote['author'] ?? null;
                }
            }

            // Fallback to the random endpoint if the favorites list is empty or failed
            if (! $quoteText) {
                $fallback = Http::timeout(5)->get(self::RANDOM_API_URL);
                if ($fallback->successful()) {
                    $fbBody    = $fallback->json();
                    $quoteText = $fbBody['data']['quote_text'] ?? null;
                    $author    = $fbBody['data']['author'] ?? null;
                }
            }
        } catch (\Exception $e) {
            // API unavailable — still allow the heal, but no quote
            $quoteText = null;
            $author    = null;
        }

        // --- Heal Player ---
        $healedHp = min(self::MAX_HP, $playerHp + self::HEAL_AMOUNT);
        $actualHeal = $healedHp - $playerHp;
        $playerHp = $healedHp;

        $turnLog = "✨ You drew strength from a quote and restored <span class='log-heal'>{$actualHeal} HP</span>!";

        // Prepend the new quote to the session history list
        if ($quoteText) {
            $history = $request->session()->get('motivation', []) ?? [];
            array_unshift($history, [
                'text'   => $quoteText,
                'author' => $author ?? 'Unknown',
            ]);
            $request->session()->put('motivation', $history);
        }

        // --- Enemy Counter-Attack ---
        $enemyDmg = rand(self::ENEMY_ATK_MIN, self::ENEMY_ATK_MAX);
        $playerHp = max(0, $playerHp - $enemyDmg);
        $turnLog .= " The enemy struck while you were distracted, dealing <span class='log-enemy-dmg'>{$enemyDmg} DMG</span>!";

        // Check if player died from enemy counter (edge case)
        $gameState = 'playing';
        if ($playerHp <= 0) {
            $playerHp  = 0;
            $gameState = 'gameover';
            $turnLog  .= " 💔 <span class='log-gameover'>You have been defeated!</span>";
        }

        // Persist state
        $request->session()->put('playerHp', $playerHp);
        $request->session()->put('enemyHp', $enemyHp);
        $request->session()->put('gameState', $gameState);
        array_unshift($log, $turnLog);
        $request->session()->put('battleLog', array_slice($log, 0, self::MAX_LOG_ENTRIES));

        return redirect()->route('game.index');
    }

    /*
    |--------------------------------------------------------------------------
    | reset() — Clear game session and restart
    |--------------------------------------------------------------------------
    */
    public function reset(Request $request)
    {
        $request->session()->forget([
            'playerHp',
            'enemyHp',
            'battleLog',
            'motivation',
            'gameState',
        ]);

        return redirect()->route('game.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helpers
    |--------------------------------------------------------------------------
    */

    /** Bootstrap a fresh game session. */
    private function initSession(Request $request): void
    {
        $request->session()->put('playerHp',   self::DEFAULT_HP);
        $request->session()->put('enemyHp',    self::DEFAULT_HP);
        $request->session()->put('battleLog',  []);
        $request->session()->put('motivation', []);
        $request->session()->put('gameState',  'playing');
    }

    /** Return all view-level state as an array. */
    private function getState(Request $request): array
    {
        return [
            'playerHp'   => $request->session()->get('playerHp',   self::DEFAULT_HP),
            'enemyHp'    => $request->session()->get('enemyHp',    self::DEFAULT_HP),
            'battleLog'  => $request->session()->get('battleLog',  []),
            'motivation' => $request->session()->get('motivation', []) ?? [],
            'gameState'  => $request->session()->get('gameState',  'playing'),
            'maxHp'      => self::MAX_HP,
        ];
    }

    /** Return true if the game has ended (victory or game over). */
    private function gameIsOver(Request $request): bool
    {
        $state = $request->session()->get('gameState', 'playing');
        return in_array($state, ['victory', 'gameover'], true);
    }
}
