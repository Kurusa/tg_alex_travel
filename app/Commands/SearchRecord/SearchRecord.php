<?php

namespace App\Commands\SearchRecord;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Models\RecordSearch;
use Illuminate\Database\Capsule\Manager as DB;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class SearchRecord extends BaseCommand
{

    function processCommand()
    {
        $search_record = RecordSearch::where('user_id', $this->user->id)->get();
        $query = 'SELECT record_location.departure_city_name AS departure_city_name, record_location.arrival_city_name AS arrival_city_name, record.id AS id, user.verified AS verified, user.name AS name
        FROM record 
        INNER JOIN record_location ON record_location.record_id = record.id
        INNER JOIN user ON user.id = record.user_id
        WHERE record.user_id != ' . $this->user->id . '
        AND record_location.arrival_city_id = "' . $search_record[0]->arrival_city_id . '"
        AND record_location.departure_city_id = "' . $search_record[0]->departure_city_id . '"
        AND record.date = "' . $search_record[0]->date . '"
        AND record.free_spots >= ' . $search_record[0]->spots;

        if ($search_record[0]->arrival_street_id) {
            $query .= ' AND record_location.arrival_street_id = "' . $search_record[0]->arrival_street_id . '"';
        }
        if ($search_record[0]->departure_street_id) {
            $query .= ' AND record_location.departure_street_id = "' . $search_record[0]->departure_street_id . '"';
        }

        if ($search_record[0]->arrival_street_number) {
            $query .= ' AND record_location.arrival_street_number = "' . $search_record[0]->arrival_street_number . '"';
        }
        if ($search_record[0]->departure_street_number) {
            $query .= ' AND record_location.departure_street_number = "' . $search_record[0]->departure_street_number . '"';
        }

        $possible_records = DB::select($query);
        if ($possible_records) {
            $buttons = [];
            foreach ($possible_records as $record) {
                $buttons[] = [
                    'text' => $record->name . ($record->verified ? ' ✅' : ' ❌'),
                    'callback_data' => json_encode(['a' => 'record', 'id' => $record->id])
                ];
            }
            $this->triggerCommand(MainMenu::class);
            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['results_for'] . $possible_records[0]->arrival_city_name . ' у ' . $possible_records[0]->departure_city_name, new InlineKeyboardMarkup([$buttons]));
        } else {
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['nothing_found']);
        }
    }

}