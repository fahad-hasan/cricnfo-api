## CricInfo API and front-end demo using AngularJS
This API uses cricinfo XML and JSON data and gives you a simpler livescore API. The API has been written with Laravel 5.  

###Usage
#####/api/matches
Gives you a list of live matches and their GUID

#####/api/matches/{guid}
Gives you the scorecard for the match having the {guid}

This repo comes with a bundled demo of a live cricket scorecard built with AngularJS.

###License
Do whatever you want.

###Notes
Thsi API will only return data for live matches. If a match has not started yet, it will fail. I will try to update the source code so that the API returns something meaningful instead of an exception. Feel free to contribute yourself if you have the time :)
