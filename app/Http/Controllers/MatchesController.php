<?php namespace App\Http\Controllers;

class MatchesController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$rss_url = "http://static.cricinfo.com/rss/livescores.xml";
		$xml = simplexml_load_file($rss_url) or die("feed not loading");

		$matches = [];

		foreach($xml->channel->item as $item) {
			$match = (object) null;
			$match->title = (string) $item->title;
			$link = (string) $item->guid;
			preg_match("/\d*(?=.html)/", $link, $guid);
			$match->guid = $guid[0];

			$matches[] = $match;
		}

		return json_encode($matches);
	}

	public function getMatchData($guid)
	{
		$json_url = "http://www.espncricinfo.com/netstorage/".$guid.".json?xhr=1";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$json_url);
		$json_result = curl_exec($ch);
		curl_close($ch);
		$obj = json_decode($json_result);

		//GLOBAL SCORE OBJECT
		$score = (object) null;

		//PLACEHOLDER FOR IMAGES
		$thumbnails = array();

		//MATCH SUMMARY
		$summary = (object) null;
		$summary->match_title = $obj->match->current_summary;
		$summary->match_state = $obj->match->live_state;
		$summary->date = $obj->match->date_string;
		$summary->floodlit = $obj->match->floodlit_name;
		$summary->ground = $obj->match->ground_name.", ".$obj->match->country_abbreviation;

		$score->match_info = $summary;

		//CURRENT BATTING INFO
		if (property_exists($obj->centre, 'batting')) {
			foreach($obj->centre->batting as $batting) {
				$batsman = (object) null;
				$batsman->player_id = $batting->player_id;
				$batsman->name = $batting->known_as;
				$batsman->nick_name = $batting->popular_name;
				$batsman->batting_style = $batting->batting_style;
				$batsman->on_strike = $batting->live_current_name == "striker" ? true : false;
				$batsman->runs = $batting->runs;
				$batsman->balls_faced = $batting->balls_faced;
				$batsman->strike_rate = $batting->strike_rate;

				foreach($obj->live->batting as $career) {
					if ($career->player_id == $batsman->player_id) {
						$average = (object) null;
						$average->class_card = $career->batting_averages->class_card;
						$average->batting_average = $career->batting_averages->batting_average;
						$average->strike_rate = $career->batting_averages->batting_strike_rate;
						$average->matches = $career->batting_averages->matches;
						$average->num_innings = $career->batting_averages->innings;
						$average->runs = $career->batting_averages->runs;
						$average->highest_score = $career->batting_averages->high_score;
						$average->hundreds = $career->batting_averages->hundreds;
						
						$batsman->career = $average;
					}
				}

				$score->batting->current[] = $batsman;
			}
		}

		//PREVIOUS BATTING INFO
		if (property_exists($obj->centre, 'common')) {
			foreach($obj->centre->common->batting as $batting) {
				$batsman = (object) null;
				$batsman->player_id = $batting->player_id;
				$batsman->name = $batting->known_as;
				$batsman->nick_name = $batting->popular_name;
				//$batsman->thumbnail = "http://p.imgci.com".$batting->image_path;
				$thumbnails['p_'.$batsman->player_id] = "http://p.imgci.com".$batting->image_path;
				$batsman->batting_style = $batting->hand;
				$batsman->position = $batting->position;
				$batsman->out = $batting->notout == "0" ? true : false;
				$batsman->runs = $batting->runs;
				$batsman->balls_faced = $batting->balls_faced;

				$score->batting->previous[] = $batsman;
			}
		}
		

		//CURRENT BOWLING INFO
		if (property_exists($obj->centre, 'bowling')) {
			foreach($obj->centre->bowling as $bowling) {
				$bowler = (object) null;
				$bowler->player_id = $bowling->player_id;
				$bowler->name = $bowling->known_as;
				$bowler->nick_name = $bowling->popular_name;
				$bowler->bowling_style = $bowling->bowling_style;
				$bowler->on_strike = $bowling->live_current_name == "current bowler" ? true : false;
				$bowler->overs = $bowling->overs;
				$bowler->runs_conceded = $bowling->conceded;
				$bowler->wickets = $bowling->wickets;
				$bowler->economy_rate = $bowling->economy_rate;

				foreach($obj->live->bowling as $career) {
					if ($career->player_id == $bowler->player_id) {
						$average = (object) null;
						$average->class_card = $career->bowling_averages->class_card;
						$average->bowling_average = $career->bowling_averages->bowling_average;
						$average->economy_rate = $career->bowling_averages->economy_rate;
						$average->matches = $career->bowling_averages->matches;
						$average->num_overs = $career->bowling_averages->overs;
						$average->wickets = $career->bowling_averages->wickets;
						$average->best_score = $career->bowling_averages->bbi;
						$average->five_wickets = $career->bowling_averages->five_wickets;
						
						$bowler->career = $average;
					}
				}

				$score->bowling->current[] = $bowler;
			}
		}

		//PREVIOUS BOWLING INFO
		if (property_exists($obj->centre, 'common')) {
			foreach($obj->centre->common->bowling as $bowling) {
				$bowler = (object) null;
				$bowler->player_id = $bowling->player_id;
				$bowler->name = $bowling->known_as;
				$bowler->nick_name = $bowling->popular_name;
				//$bowler->thumbnail =  "http://p.imgci.com".$bowling->image_path;
				$thumbnails['p_'.$bowler->player_id] = "http://p.imgci.com".$bowling->image_path;
				$bowler->bowling_style = $bowling->hand;
				$bowler->position = $bowling->position;
				if (property_exists($bowling, 'live_current_name')) {
					$bowler->on_spell = true;
					$bowler->on_strike = $bowling->live_current_name == "current bowler" ? true : false;
				} else {
					$bowler->on_spell = false;
					$bowler->on_strike = false;
				}
				$bowler->overs = $bowling->overs;
				$bowler->runs_conceded = $bowling->conceded;
				$bowler->wickets = $bowling->wickets;

				$score->bowling->previous[] = $bowler;
			}
		}

		$score->player_thumbs = $thumbnails;

		return json_encode($score);
	}

	public function getMatches() {
		return view('matches');
	}

}
