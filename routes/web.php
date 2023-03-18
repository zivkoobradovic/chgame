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

Route::get('ui/game', function () {
    return view('game');
});
Route::post('/get-image', function (Request $request) {

    $ch = curl_init();

    $data = [
        'prompt' => $request->input('description'),
        'n' => 1,
        'size' => '256x256'
    ];

    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: Bearer ' . env('OPENAI_API_KEY');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $generated_image_result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    $generated_image_result = json_decode($generated_image_result, true);
    // dd($generated_image_result);

    curl_close($ch);
    return response()->json([
        'image' => $generated_image_result['data'][0]['url']
    ]);
});
Route::post('chat', function (Request $request) {
    set_time_limit(120);
    $messages = $request->session()->get('messages',
        [['role' => 'system', 'content' => "
        Please perform the function of a text adventure game, following the rules listed below:
        Presentation Rules:
        1. Play the game in turns, starting with you.
        2. The game output will always show 'turn_number', 'time_period_of_the_day', 'current_day_number', 'weather', 'health', 'xp', ‘ac’, 'level’, location', 'description', ‘gold’, 'inventory', 'quest', 'abilities', 'possible_commands', 'message', 'question', 'image_description', 'd20_result'.
        3. Always wait for the player’s next command.
        4. Stay in character as a text adventure game and respond to commands the way a text adventure game should.
        5. Wrap all game output in JSON format.
        6. The ‘Description’ must stay between 3 to 10 sentences.
        7. Increase the value for ‘Turn number’ by +1 every time it’s your turn.
        8. ‘Time period of day’ must progress naturally after a few turns.
        9. Once ‘Time period of day’ reaches or passes midnight, then add 1 to ‘Current day number’.
        10. Change the ‘Weather’ to reflect ‘Description’ and whatever environment the player is in the game.
        11. JSON keys need to be lowercase.
        12. Generate the description for creating the images of the current situation in the 'image_description' field of the JSON output.
        13. The whole response MUST be wraped in the JSON format.
        14. All content MUST be in JSON format.
        Fundamental Game Mechanics:
        1. Determine ‘ac’ using Dungeons and Dragons 5e rules.
        2. Generate ‘abilities’ before the game starts. ‘abilities’ include: ‘Persuasion', 'Strength', 'Intelligence', ‘Dexterity’, and 'Luck', all determined by d20 rolls when the game starts for the first time.
        3. Start the game with 20/20 for ‘health’, with 20 being the maximum health. Eating food, drinking water, or sleeping will restore health.
        4. Always show what the player is wearing and wielding (as ‘Wearing’ and ‘Wielding’).
        5. Display ‘Game Over’ if ‘Health’ falls to 0 or lower.
        6. The player must choose all commands, and the game will list 7 of them at all times under ‘possible_ommands’, and assign them a number 1-7 that I can type to choose that option, and vary the possible selection depending on the actual scene and characters being interacted with. Put the commands in the 'possible_commands' JSON key as an array of commands.
        7. If any of the commands will cost money, then the game will display the cost in parenthesis.
        8. Before a command is successful, the game must roll a d20 with a bonus from a relevant ‘Trait’ to see how successful it is. Determine the bonus by dividing the trait by 3.
        9. If an action is unsuccessful, respond with a relevant consequence.
        10. Always display the result of a d20 in a 'd20_result' JSON key.
        11. The player can obtain a ‘Quest’ by interacting with the world and other people. The ‘Quest’ will also show what needs to be done to complete it.
        12. The only currency in this game is Gold.
        13. The value of ‘Gold’ must never be a negative integer.
        14. The player can not spend more than the total value of ‘Gold’.
        Rules for Setting:
        1. Use the world of Pirates of the Caribbean movie as inspiration for the game world. Import whatever beasts, monsters, and items that Pirates of the Caribbean has.
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

    $messages[] = ['role' => 'user', 'content' => $request->input('message')];
    $response = OpenAI::chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages,
    ]);


    $messages[] = ['role' => 'assistant', 'content' => $response->choices[0]->message->content];
    $request->session()->put('messages', $messages);
    $messages = collect(session('messages', []))->reject(fn ($message) => $message['role'] === 'system');


    $contentJson = json_decode($response->choices[0]->message->content);
    return response()->json([
        'messages' => $contentJson
    ]);


});


Route::post('/', function (Request $request) {
    set_time_limit(120);
    $messages = $request->session()->get('messages',
        [['role' => 'system', 'content' => "
        Please perform the function of a text adventure game, following the rules listed below:
        Presentation Rules:
        1. Play the game in turns, starting with you.
        2. The game output will always show 'turn_number', 'time_period_of_the_day', 'current_day_number', 'weather', 'health', 'xp', ‘ac’, 'level’, location', 'description', ‘gold’, 'inventory', 'quest', 'abilities', 'possible_commands', 'message', 'question', 'image_description', 'd20_result'.
        3. Always wait for the player’s next command.
        4. Stay in character as a text adventure game and respond to commands the way a text adventure game should.
        5. Wrap all game output in JSON format.
        6. The ‘Description’ must stay between 3 to 10 sentences.
        7. Increase the value for ‘Turn number’ by +1 every time it’s your turn.
        8. ‘Time period of day’ must progress naturally after a few turns.
        9. Once ‘Time period of day’ reaches or passes midnight, then add 1 to ‘Current day number’.
        10. Change the ‘Weather’ to reflect ‘Description’ and whatever environment the player is in the game.
        11. JSON keys need to be lowercase.
        12. Replace white space in JSON keys with underscore.
        13. Generate the description for creating the images of the current situation in the 'image_description' field of the JSON output.
        14. The whole response MUST be wraped in the JSON format.
        Fundamental Game Mechanics:
        1. Determine ‘ac’ using Dungeons and Dragons 5e rules.
        2. Generate ‘abilities’ before the game starts. ‘abilities’ include: ‘Persuasion', 'Strength', 'Intelligence', ‘Dexterity’, and 'Luck', all determined by d20 rolls when the game starts for the first time.
        3. Start the game with 20/20 for ‘health’, with 20 being the maximum health. Eating food, drinking water, or sleeping will restore health.
        4. Always show what the player is wearing and wielding (as ‘Wearing’ and ‘Wielding’).
        5. Display ‘Game Over’ if ‘Health’ falls to 0 or lower.
        6. The player must choose all commands, and the game will list 7 of them at all times under ‘possible_ommands’, and assign them a number 1-7 that I can type to choose that option, and vary the possible selection depending on the actual scene and characters being interacted with. Put the commands in the 'possible_commands' JSON key as an array of commands.
        7. The 7th command should be ‘Other’, which allows me to type in a custom command.
        8. If any of the commands will cost money, then the game will display the cost in parenthesis.
        9. Before a command is successful, the game must roll a d20 with a bonus from a relevant ‘Trait’ to see how successful it is. Determine the bonus by dividing the trait by 3.
        10. If an action is unsuccessful, respond with a relevant consequence.
        11. Always display the result of a d20 in a 'd20_result' JSON key.
        12. The player can obtain a ‘Quest’ by interacting with the world and other people. The ‘Quest’ will also show what needs to be done to complete it.
        13. The only currency in this game is Gold.
        14. The value of ‘Gold’ must never be a negative integer.
        15. The player can not spend more than the total value of ‘Gold’.
        Rules for Setting:
        1. Use the world of Pirates of the Caribbean movie as inspiration for the game world. Import whatever beasts, monsters, and items that Pirates of the Caribbean has.
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

    $content = json_decode($response->choices[0]->message->content, true);



if(!empty($content['image_description'])){
        $ch = curl_init();

        $data = [
            'prompt' => $content['image_description'],
            'n' => 1,
            'size' => '512x512'
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . env('OPENAI_API_KEY');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $generated_image_result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        //  dd($generated_image_result);
        // dd($generated_image_result);
        $generated_image_result = json_decode($generated_image_result, true);
        // dd($generated_image_result);
        $request->session()->put('image_url', $generated_image_result['data'][0]['url']);
}

    $request->session()->put('messages', $messages);
    $messages = collect(session('messages', []))->reject(fn ($message) => $message['role'] === 'system');



    return view('welcome', ['messages' => $messages]);

});

Route::get('reset', function (Request $request) {

    $request->session()->forget('messages');
    $request->session()->forget('image_url');

    return redirect('/ui/game');
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

    $generated_image_result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

     return view('image', ['result_image' => json_decode($generated_image_result, true)]);
});

Route::get('/image', function () {
    return view('image');
});
