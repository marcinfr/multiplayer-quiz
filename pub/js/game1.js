var update = function() {
    $.ajax({
       type : 'GET',
       url : 'gameJson',
       success : function(data){
            let parsedData = JSON.parse(data);
            if (gameHash == parsedData['hash']) {
                return;
            }
            $(".section").hide();
            gameHash = parsedData['hash'];
            for (const [key, content] of Object.entries(parsedData)) {
                console.log(key);
                console.log(content);
                $('#' + key).html(content);
                $('#' + key).show();
            }
            $('.answer').on('click', function() {
                const answer_id = $(this).data('index');
                $.ajax({
                    type : 'GET',
                    url : 'selectAnswer?id=' + answer_id,
                });
            });
       },
   });
};
update();
var gameHash = "";
var refInterval = window.setInterval('update()', 300); 