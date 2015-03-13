<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CricInfo - LiveScore API Example</title>

  <!-- Bootstrap -->
  <link href="https://bootswatch.com/yeti/bootstrap.min.css" rel="stylesheet">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.26/angular-route.min.js"></script>
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
      <script src="js/clscore.js"></script>
      <script src="js/matchesController.js"></script>
   </head>
   <body ng-app="clscore">
      <div class="container">
         <nav class="navbar navbar-default">
            <div class="container">
               <form class="navbar-form navbar-left" role="search">
                  <div class="form-group">
                     <input type="text" class="form-control" placeholder="Search matches..." ng-model="matchSearch">
                  </div>
               </form>
            </div>
         </nav>
         <div class="row">
            <div class="col-md-4">
               <div ng-controller="matchListController">
                  <table class="table table-condensed table-striped table-hover">
                     <tr>
                        <th>Title</th>
                        <th>GUID</th>
                     </tr>
                     <tr ng-repeat="match in matches | filter:matchSearch | orderBy:'guid'">
                        <td><a href="#/details/{{ match.guid }}">{{ match.title }}</a></td>
                        <td>{{ match.guid }}</td>
                     </tr>
                  </table>
               </div>
            </div>
            <div class="col-md-8">
               <div ng-view></div>
               <script type="text/ng-template" id="matchDetails.htm">
                  <div class="row">
                     <div class="col-md-12">
                        <h3>{{ score.match_info.match_title }}</h3>
                        <h4>{{ score.match_info.ground }}&nbsp;<small>{{ score.match_info.date }}</small></h4>
                        <table class="table table-condensed">
                           <tr>
                              <th>Batting</th>
                              <th>R</th>
                              <th>B</th>
                              <th>S/R</th>
                           <tr>
                           <tbody ng-repeat="batsman in score.batting.current | orderBy:'on_strike':true">
                              <tr>
                                 <td><span class="{{ batsman.on_strike == true ? 'text-danger' : '' }}">{{ batsman.name }}</span>&nbsp;&nbsp;<span class="badge">{{ batsman.batting_style }}</span></td>
                                 <td>{{ batsman.runs }}</td>
                                 <td>{{ batsman.balls_faced }}</td>
                                 <td>{{ batsman.strike_rate }}</td>
                              </tr>
                              <!--tr>
                                 <td colspan="4">
                                    <div class="media">
                                       <div class="media-left">
                                          <img class="media-object" src="" /> 
                                       </div>
                                       <div class="media-body">
                                          <h4 class="media-heading">{{ batsman.name }} {{ score.player_thumbs.p_{{ batsman.player_id }} }}</h4>
                                       </div>
                                    </div>      
                                 </td>
                              </tr-->
                           </tbody>
                        </table>
                        <table class="table table-condensed">
                           <tr>
                              <th>Previous</th>
                              <th>R</th>
                              <th>B</th>
                           <tr>
                           <tr ng-repeat="batsman in score.batting.previous | orderBy:'position':true" ng-show="batsman.out">
                              <td>{{ batsman.name }}</td>
                              <td>{{ batsman.runs }}</td>
                              <td>{{ batsman.balls_faced }}</td>
                           </tr>
                        </table>
                        <table class="table table-condensed">
                           <tr>
                              <th>Bowling</th>
                              <th>O</th>
                              <th>R</th>
                              <th>W</th>
                              <th>E/R</th>
                           <tr>
                           <tr ng-repeat="bowler in score.bowling.current | orderBy:'on_strike':true">
                              <td><span class="{{ bowler.on_strike == true ? 'text-danger' : '' }}">{{ bowler.name }}</span>&nbsp;&nbsp;<span class="badge">{{ bowler.bowling_style }}</span></td>
                              <td>{{ bowler.overs }}</td>
                              <td>{{ bowler.runs_conceded }}</td>
                              <td>{{ bowler.wickets }}</td>
                              <td>{{ bowler.economy_rate }}</td>
                           </tr>
                        </table>
                        <table class="table table-condensed">
                           <tr>
                              <th>Previous</th>
                              <th>O</th>
                              <th>R</th>
                              <th>W</th>
                           <tr>
                           <tr ng-repeat="bowler in score.bowling.previous | orderBy:'position':true" ng-show="!bowler.on_spell">
                              <td>{{ bowler.name }}</td>
                              <td>{{ bowler.overs }}</td>
                              <td>{{ bowler.runs_conceded }}</td>
                              <td>{{ bowler.wickets }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </script>
            </div>
         </div>
      </div>
</body>
</html>