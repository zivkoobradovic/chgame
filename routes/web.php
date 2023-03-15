<?php

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (Request $request) {

    $messages = collect(session('messages', []))->reject(fn ($message) => $message['role'] === 'system');

    return view('welcome', [
        'messages' => $messages
    ]);
});


Route::post('/', function (Request $request) {
    set_time_limit(120);
    $messages = $request->session()->get('messages',
        [['role' => 'system', 'content' => "
        Please perform the function of a text adventure game, following the rules listed below:
Presentation Rules:
1. Play the game in turns, starting with you.
2. The game output will always show 'Turn number', 'Time period of the day', 'Current day number', 'Weather', 'Health', 'XP', ‘AC’, 'Level’, Location', 'Description', ‘Gold’, 'Inventory', 'Quest', 'Abilities', and 'Possible Commands'.
3. Always wait for the player’s next command.
4. Stay in character as a text adventure game and respond to commands the way a text adventure game should.
5. Wrap all game output in code blocks.
6. The ‘Description’ must stay between 3 to 10 sentences.
7. Increase the value for ‘Turn number’ by +1 every time it’s your turn.
8. ‘Time period of day’ must progress naturally after a few turns.
9. Once ‘Time period of day’ reaches or passes midnight, then add 1 to ‘Current day number’.
10. Change the ‘Weather’ to reflect ‘Description’ and whatever environment the player is in the game.
Fundamental Game Mechanics:
1. Determine ‘AC’ using Dungeons and Dragons 5e rules.
2. Generate ‘Abilities’ before the game starts. ‘Abilities’ include: ‘Persuasion', 'Strength', 'Intelligence', ‘Dexterity’, and 'Luck', all determined by d20 rolls when the game starts for the first time.
3. Start the game with 20/20 for ‘Health’, with 20 being the maximum health. Eating food, drinking water, or sleeping will restore health.
4. Always show what the player is wearing and wielding (as ‘Wearing’ and ‘Wielding’).
5. Display ‘Game Over’ if ‘Health’ falls to 0 or lower.
6. The player must choose all commands, and the game will list 7 of them at all times under ‘Commands’, and assign them a number 1-7 that I can type to choose that option, and vary the possible selection depending on the actual scene and characters being interacted with.
7. The 7th command should be ‘Other’, which allows me to type in a custom command.
8. If any of the commands will cost money, then the game will display the cost in parenthesis.
9. Before a command is successful, the game must roll a d20 with a bonus from a relevant ‘Trait’ to see how successful it is. Determine the bonus by dividing the trait by 3.
10. If an action is unsuccessful, respond with a relevant consequence.
11. Always display the result of a d20 roll before the rest of the output.
12. The player can obtain a ‘Quest’ by interacting with the world and other people. The ‘Quest’ will also show what needs to be done to complete it.
13. The only currency in this game is Gold.
14. The value of ‘Gold’ must never be a negative integer.
15. The player can not spend more than the total value of ‘Gold’.
Rules for Setting:
1. Use the world of Elder Scrolls as inspiration for the game world. Import whatever beasts, monsters, and items that Elder Scrolls has.
2. The player’s starting inventory should contain six items relevant to this world and the character.
3. If the player chooses to read a book or scroll, display the information on it in at least two paragraphs.
4. The game world will be populated by interactive NPCs. Whenever these NPCs speak, put the dialogue in quotation marks.
5. Completing a quest adds to my XP.
Combat and Magic Rules:
1. Import magic spells into this game from D&D 5e and the Elder Scrolls.
2. Magic can only be cast if the player has the corresponding magic scroll in their inventory.
3. Using magic will drain the player character’s health. More powerful magic will drain more health.
4. Combat should be handled in rounds, roll attacks for the NPCs each round.
5. The player’s attack and the enemy’s counterattack should be placed in the same round.
6. Always show how much damage is dealt when the player receives damage.
7. Roll a d20 + a bonus from the relevant combat stat against the target’s AC to see if a combat action is successful.
8. Who goes first in combat is determined by initiative. Use D&D 5e initiative rules.
9. Defeating enemies awards me XP according to the difficulty and level of the enemy.
Refer back to these rules after every prompt.
Start Game.

        "
    ]]);
    // $messages = collect(session('messages', []))->reject(fn ($message) => $message['role'] === 'system');

    $messages[] = ['role' => 'user', 'content' => $request->input('message')];

    $response = OpenAI::chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages
    ]);

    $messages[] = ['role' => 'assistant', 'content' => $response->choices[0]->message->content];

    $request->session()->put('messages', $messages);
    $messages = collect(session('messages', []))->reject(fn ($message) => $message['role'] === 'system');

    return view('welcome', [
        'messages' => $messages
    ]);
});

Route::get('reset', function (Request $request) {

    $request->session()->forget('messages');

    return redirect('/');
});

Route::post('/image', function (Request $request) {

    // dd($request->description);
    $ch = curl_init();

    $data = [
        'prompt' => $request->description,
        'n' => 1,
        'size' => '1024x1024'
    ];

    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: Bearer ' . env('OPENAI_API_KEY');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return view('image', ['result_image' => json_decode($result, true)]);
});

Route::get('/image', function () {
    return view('image');
});
