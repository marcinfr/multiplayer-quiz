<?php

namespace App\Controllers;

class SelectGame extends AbstractController
{
    public function execute()
    {

    }
}

$db = app(\App\Db::class);
$connection = $db->getConnection();
$sql = "select id,creator from game where round = 0";
$activeGames = $connection->query($sql);

?>


<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="pub/css/quiz.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="container">
            <a href="." class=back>Wróć</a>
            <form id="form" method="post" action="createGame">
                <input type="hidden" name="game_id" id="game_id"/>
                <div class="row">
                    <div class="col-25 text-right">
                        <label for="name">Twoje imie</label>
                    </div>
                    <div class="col-75">
                        <input type="text" id="name" name="name" placeholder="Twoje imię ..." required>
                    </div>
                    <table>
                        <tr>
                            <td class="center-container">
                                <a href="#" onclick="joinGame(0)" class="primary">Nowa Gra</a>
                            </td>
                        </tr>
                        <?php foreach($activeGames as $game): ?>
                            <tr>
                                <td class="center-container">
                                    <a href="#" onclick="joinGame(<?= $game['id'] ?>)" class="primary"><?= $game['creator'] ?></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                </div>
            </form>
        </div>
        <script>
            function joinGame(gameId)
            {
                let form = $("#form");
                if (form[0].checkValidity()) {
                    $("#game_id").val(gameId);
                    form[0].submit();
                } else {
                    form[0].reportValidity();
                }
            }
        </script>
    </body>
</html>