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
                <a href="#" onclick="action('startGame')" class="primary">Start</a>
            </div>
            <div id="section-wait" class="section" style="display:none">
                Zaczekaj na start gry
            </div>
            <div id="section-players" class="section" style="display:none">
                <h3><label>Gracze</label></h3>
                <table id="list"></table>
            </div>
            <div id="section-round-result" class="section" style="display:none">
                <h3>Tutaj bedzie wynik rundy ale jeszcze ni ma</h3>
                <a href="#" onclick="action('setPlayerReady')" class="primary">Gotowy</a>
            </div>
            <div id="section-question" class="section" style="display:none">
                <div id="question-header">Pytanie numer: <span id="question-number"></span></div>
                <div id="question-text"></div>
            </div>
            <div id="section-answers" class="section" style="display:none">
                <div id="answers-list"></div>
            </div>
        </div>
        <script>
            function action(actionUrl)
            {
                $.ajax({
                    type : 'GET',
                    url : actionUrl,
                });
            }
        </script>
    </body>
</html>