
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    

    <title>Skill Test</title>

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/cover.css" rel="stylesheet">
  </head>

  <body>

<div class="container">
    <div class="page-header">
        <h1>Queue test task</h1>
    </div>
    @if (isset($identified))
    <div class="panel panel-default" style="height:473px;">

        <div class="panel-heading">
            Queue Monitor 
            <div id="clockdiv"></div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>Token</th>
                <th>Time</th>
                <th>Amount</th>
                <th>Interest</th>
                <th>Total amount</th>
            </tr>
            </thead>
            <tbody id="monitor">

            </tbody>
        </table>
    </div>
    @endif
    
    <div class="panel panel-default">
        <div class="panel-heading">
            Task
        </div>
        <div class="panel-body">
            <h2>Task</h2>

            <p>Create a small service which does the following:</p>

            <ol>
                <li>Connects to AMQP (RabbitMQ) server (details below)</li>
                <li>Listens on <b>'interest-queue'</b> queue in default exchange for messages</li>
                <li>For each message it calculates the "interest" and total sum by formula given below</li>
                <li>Broadcast the new messages to <b>'solved-interest-queue'</b> in the same exchange</li>
            </ol>

            <p>If everything is done correctly you should be able to see the messages popping up in the display table
                above.</p>

            <h2>Interest formula</h2>

            <ol>
                <li>Interest is calculated based on <i>sum</i> and <i>days</i> fields</li>
                <li>Interest is calculated per day as a percentage from the original amount</li>
                <li>If day is...
                    <ol>
                        <li>divisible by three, the interest is: <b>1%</b></li>
                        <li>divisible by five, the interest is: <b>2%</b></li>
                        <li>divisible by both three and five, the interest is: <b>3%</b></li>
                        <li>not divisible by either three or five, interest is: <b>4%</b></li>
                    </ol>
                </li>
                <li>Each day interest amount is rounded to two digits</li>
                <li>Final interest is a sum of all days interests</li>
                <li>Total sum is the sum of original amount and total interest</li>
            </ol>


            <h2>Message Format</h2>

            <p>Messages are transmitted as JSON.</p>

            <p>Incoming messages will look like following:<code>{ sum: 123, days: 5 }</code></p>

            <p>Outgoing messages should look like following:<code>{ sum: 123, days: 5, interest: 18.45, totalSum:
                141.45, token: "myIdentifier" }</code></p>

            <p>Token will be displayed on the monitor above for clarity when several services are running at the same time.
                Use your name, nick, or something else clever.</p>

            <h2>AMQP server (RabbitMQ) details</h2>
            @if (isset($identified))
                <p>
                    <b>Server:</b> {{ Config::get('impact.host')}}<br>
                    <b>User:</b> {{ Config::get('impact.user')}}<br>
                    <b>Password:</b> {{ Config::get('impact.password')}}<br>
                </p>


            @else
            <p>
                <form method="post" >

                <fieldset class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input name="email" type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                    <small class="text-muted">We'll never share your email with anyone else.</small>
                  </fieldset>

                <fieldset class="form-group">
                  <span class="input-group-addon" id="basic-addon1">@</span>
                  <input 
                        name="name"
                        type="text" 
                        class="form-control" 
                        placeholder="Name" 
                        aria-describedby="basic-addon1">
                </fieldset>

                <fieldset class="form-group">
                <label for="basic-url">GitHub Repozitory URL</label>
                <div class="input-group">
                  <span class="input-group-addon" id="basic-addon3">https://github.com/</span>
                  <input type="text" name="github" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                </fieldset>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <button type="submit" class="btn btn-danger">Identify Your Self and Start Test</button>
            </form>
            </p>
            @endif

            <h2>References</h2>

            <ul>
                <li><a href="https://www.rabbitmq.com/">https://www.rabbitmq.com/</a></li>
            </ul>

            <p align="right">Guess contacts of original author:
                <b><a href="skype:myjar.jaan.pullerits">Jaan Pullerits</a></b>
            </p>

            <p align="right">Bad person who took this idea:
                <b><a href="skype:icw.work">Ronalds Sovas</a></b>
                <!-- https://www.google.lv/?gws_rd=cr,ssl&ei=R6L_VqHwO4SzsQGmkoC4AQ#q=site:http:%2F%2Fimpact.ccat.eu%2F site did not have any coopyright notices and it is possibl to find it in Google-->
            </p>
        </div>

    </div>
</div>
<br><br>

</div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        @if (isset($deadline))

            function initializeClock(id, endtime){
              var clock = document.getElementById(id);
              var timeinterval = setInterval(function(){
                var t = getTimeRemaining(endtime);
                clock.innerHTML = 'days: ' + t.days + '<br>' +
                                  'hours: '+ t.hours + '<br>' +
                                  'minutes: ' + t.minutes + '<br>' +
                                  'seconds: ' + t.seconds;
                if(t.total<=0){
                  clearInterval(timeinterval);
                }
              },1000);
            }

            $( document ).ready(function() {
                initializeClock('clockdiv', "{{explode(" " , $deadline)[0] }}");

                setInterval(function(){
                $.getJSON("/ajax", function(result){

                randomClassName = Math.floor((Math.random() * 100) + 1);

                if (result.status > 0){
                    $("#monitor").prepend(
                        $("<tr>").addClass('classic_' + randomClassName).addClass(result.status ==1 ? "valid" : "invalid")
                            
                            .append($("<td>").text(result.token))
                            .append($("<td>").text(result.days))
                            .append($("<td>").text(result.sum))
                            .append($("<td>").text(result.interest))
                            .append($("<td>").text(result.totalSum))
                            .delay(7000).queue(function(next){
                                $(this).remove();
                                next();
                            })
                        );
                    }
                });
                

                }, 1000);
            });


        @endif
    </script>
  </body>
</html>
