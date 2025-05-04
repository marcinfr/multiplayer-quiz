<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="pub/css/quiz.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <script src="pub/js/game.js"></script>
    </head>
    <body>
        <div class="container">
            <div id="section-start" class="section" style="display:none">
                <a href="#" onclick="startGame()" class="primary">Start</a>
            </div>
            <div id="section-wait" class="section" style="display:none">
                Zaczekaj na start gry
            </div>
            <div id="section-players" class="section" style="display:none">
                <h3><label>Gracze</label></h3>
                <table id="list"></table>
            </div>
            <div id="section-question" class="section" style="display:none">
                <h3>Pytanie numer: <span id="question-number"></span></h3>
                <div id="question-text"></div>
            </div>
            <div id="section-answers" class="section" style="display:none">
                <div class="col-50">
                    <a href="#" class="answer"><span id="answer-0"></span></a>
                </div>
                <div class="col-50">
                    <a href="#" class="answer"><span id="answer-1"></span></a>
                </div>
                <div class="col-50">
                    <a href="#" class="answer"><span id="answer-2"></span></a>
                </div>
                <div class="col-50">
                    <a href="#" class="answer"><span id="answer-3"></span></a>
                </div>
            </div>
        </div>
        <script>
            function startGame()
            {
                $.ajax({
                    type : 'GET',
                    url : 'startGame',
                });
            }
        </script>
    </body>
</html>